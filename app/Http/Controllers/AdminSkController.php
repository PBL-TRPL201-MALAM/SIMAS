<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Models\RiwayatDokumen;
use App\Models\User;
use App\Models\Verifikasi;
use App\Services\SuratKeputusanPdfGenerator;
use App\Support\UserReferenceOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as LaravelValidator;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini menangani MVP alur Admin Surat untuk dokumen Surat Keputusan.
// Surat Biasa tetap memakai controller terpisah agar alurnya tidak ikut berubah.
class AdminSkController extends Controller
{
    public function __construct(
        protected SuratKeputusanPdfGenerator $suratKeputusanPdfGenerator
    ) {
    }

    // Halaman antrean SK baru yang masih menunggu review Admin Surat.
    public function incoming(): View
    {
        return view('admin.pengajuan-sk', [
            'skList' => $this->baseSkQuery()
                ->where('status_dokumen', 'DIAJUKAN')
                ->latest('created_at')
                ->get(),
        ]);
    }

    // Halaman arsip seluruh SK dari database, apa pun statusnya.
    public function all(): View
    {
        return view('admin.semua-sk', [
            'skList' => $this->baseSkQuery()
                ->latest('created_at')
                ->get(),
        ]);
    }

    // Method ini menampilkan FINAL_PDF SK yang sudah dibuat saat publish.
    public function previewFinal(Dokumen $dokumen): BinaryFileResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN', 404);

        $file = $this->resolveFinalPdf($dokumen);
        abort_unless($file && Storage::disk('public')->exists($file->file_path), 404);

        return response()->file(
            Storage::disk('public')->path($file->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . addslashes($file->file_name) . '"',
            ]
        );
    }

    // Method ini mengunduh FINAL_PDF SK dari storage tanpa generate ulang.
    public function downloadFinal(Dokumen $dokumen): BinaryFileResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN', 404);

        $file = $this->resolveFinalPdf($dokumen);
        abort_unless($file && Storage::disk('public')->exists($file->file_path), 404);

        return response()->download(
            Storage::disk('public')->path($file->file_path),
            $file->file_name
        );
    }

    // Publish SK hanya boleh dilakukan setelah semua level verifikasi menyetujui dokumen.
    public function publish(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN' && $dokumen->status_dokumen === 'SIAP_PUBLISH',
            404
        );

        $dokumen->loadMissing([
            'suratKeputusan.skMenimbang',
            'suratKeputusan.skDasarHukum.dasarHukum',
            'suratKeputusan.skMemutuskan',
            'penandatangan',
        ]);

        if (! $dokumen->suratKeputusan?->nomor_sk || ! $dokumen->suratKeputusan?->tanggal_sk || ! $dokumen->penandatangan) {
            return redirect()
                ->route('admin.semua-sk')
                ->with('error', 'Metadata SK belum lengkap untuk dipublish.');
        }

        $verificationToken = $this->resolveVerificationToken($dokumen);
        // URL QR dibangun dari APP_URL agar isi QR konsisten dengan route publik /verifikasi/{token}.
        $validationUrl = rtrim((string) config('app.url'), '/') . route('verifikasi.public', $verificationToken, false);
        $finalFileName = $this->buildFinalFileName($dokumen);
        $finalFilePath = 'dokumen/final/' . $dokumen->dokumen_id . '/' . $finalFileName;

        Storage::disk('public')->makeDirectory(dirname($finalFilePath));

        try {
            // Generator SK merender PDF dari Blade dan data database, bukan dari PDF upload pemohon.
            $this->suratKeputusanPdfGenerator->generateFinal($dokumen, $validationUrl, $finalFilePath);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.semua-sk')
                ->with('error', 'PDF final SK belum berhasil dibuat. Silakan cek metadata SK dan data penandatangan.');
        }

        DB::transaction(function () use ($dokumen, $verificationToken, $finalFileName, $finalFilePath, $request): void {
            // Token disimpan setelah PDF berhasil dibuat agar QR langsung valid ketika status berubah PUBLISHED.
            $dokumen->update([
                'status_dokumen' => 'PUBLISHED',
                'published_at' => now(),
                'verification_token' => $verificationToken,
                'file_final_path' => $finalFilePath,
            ]);

            DokumenFile::query()->updateOrCreate(
                [
                    'dokumen_id' => $dokumen->dokumen_id,
                    'file_type' => 'FINAL_PDF',
                ],
                [
                    'file_name' => $finalFileName,
                    'file_path' => $finalFilePath,
                    'uploaded_by' => $request->user()?->user_id,
                    'uploaded_at' => now(),
                ]
            );
        });

        return redirect()
            ->route('admin.semua-sk')
            ->with('status', 'SK berhasil dipublish dan PDF final sudah dibuat.');
    }

    // Method ini menampilkan wizard review SK berdasarkan dokumen yang dipilih Admin Surat.
    public function show(Request $request): View|RedirectResponse
    {
        $dokumenId = $request->integer('dokumen');

        if (! $dokumenId) {
            return redirect()
                ->route('admin.pengajuan-sk')
                ->with('error', 'Dokumen SK yang akan diproses belum dipilih.');
        }

        $dokumen = $this->baseSkQuery()
            ->with(['verifikasi.verifikator'])
            ->findOrFail($dokumenId);

        // Calon penandatangan SK mengikuti daftar jabatan penandatangan resmi yang juga dipakai pada Surat Biasa.
        $penandatangans = User::query()
            ->where('role', 'VERIFIKATOR')
            ->where('is_active', true)
            ->whereIn('jabatan', UserReferenceOptions::signerJabatans())
            ->orderBy('jabatan')
            ->orderBy('nama')
            ->get(['user_id', 'nama', 'jabatan', 'unit_kerja']);

        $selectedPenandatanganId = old('penandatangan_id', $dokumen->penandatangan_id);

        $verifikators = User::query()
            ->where('role', 'VERIFIKATOR')
            ->where('is_active', true)
            // Penandatangan final tidak ditampilkan lagi sebagai opsi verifikator level pemeriksa.
            ->when($selectedPenandatanganId, fn ($query) => $query->where('user_id', '!=', $selectedPenandatanganId))
            ->orderBy('nama')
            ->get(['user_id', 'nama', 'jabatan', 'unit_kerja']);

        $selectedVerifikators = $dokumen->verifikasi
            ->sortBy('level')
            // Jika flow sudah pernah dibuat, penandatangan final tidak diisi ulang ke dropdown verifikator level 1/2/3.
            ->reject(fn (Verifikasi $verifikasi) => $selectedPenandatanganId && (int) $verifikasi->verifikator_id === (int) $selectedPenandatanganId)
            ->values()
            ->mapWithKeys(fn (Verifikasi $verifikasi, int $index) => [$index + 1 => $verifikasi->verifikator_id]);

        return view('admin.proses-sk', [
            'dokumen' => $dokumen,
            'suratKeputusan' => $dokumen->suratKeputusan,
            'verifikators' => $verifikators,
            'penandatangans' => $penandatangans,
            'selectedPenandatangan' => $penandatangans->firstWhere('user_id', (int) $selectedPenandatanganId),
            'selectedPenandatanganId' => $selectedPenandatanganId,
            'selectedVerifikators' => $selectedVerifikators,
            'initialStep' => min(3, max(1, $request->integer('step', 1))),
        ]);
    }

    // Method ini menyimpan metadata resmi SK sebelum Admin Surat memilih jalur verifikasi.
    public function storeMetadata(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN', 404);

        $validated = $request->validate($this->metadataRules(), [
            'nomor_sk.required' => 'Nomor SK wajib diisi sebelum memilih verifikator.',
            'tanggal_sk.required' => 'Tanggal SK wajib diisi sebelum memilih verifikator.',
            'penandatangan_id.required' => 'Penandatangan SK wajib dipilih sebelum memilih verifikator.',
        ]);

        DB::transaction(function () use ($dokumen, $validated): void {
            $dokumen->loadMissing('suratKeputusan');

            if ($dokumen->suratKeputusan) {
                // Nomor dan tanggal SK disimpan di detail surat_keputusan karena merupakan metadata resmi SK.
                $dokumen->suratKeputusan->update([
                    'nomor_sk' => $validated['nomor_sk'],
                    'tanggal_sk' => $validated['tanggal_sk'],
                    'catatan_admin' => $validated['catatan_admin'] ?? null,
                ]);
            }

            // Penandatangan disimpan di dokumen agar bisa dipakai ulang sebagai level final pada flow verifikasi.
            $dokumen->update([
                'penandatangan_id' => $validated['penandatangan_id'],
            ]);
        });

        return redirect()
            ->route('admin.proses-sk', [
                'dokumen' => $dokumen->dokumen_id,
                'step' => 2,
            ])
            ->with('status', 'Metadata SK berhasil disimpan. Silakan tentukan verifikator.');
    }

    // Method ini membuat ulang jalur verifikasi SK dan mengirim dokumen ke verifikator level pertama.
    public function sendToVerification(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN', 404);

        $dokumen->loadMissing('suratKeputusan');

        if (! $dokumen->suratKeputusan?->nomor_sk || ! $dokumen->suratKeputusan?->tanggal_sk || ! $dokumen->penandatangan_id) {
            // Metadata wajib sudah tersimpan lewat step 1 sebelum flow verifikasi SK dapat dibuat.
            return redirect()
                ->route('admin.proses-sk', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 1,
                ])
                ->with('error', 'Metadata SK belum lengkap. Silakan lengkapi nomor SK, tanggal SK, dan penandatangan terlebih dahulu.');
        }

        // Penandatangan final berasal dari metadata tersimpan, bukan dari pilihan bebas di step verifikator.
        $request->merge([
            'penandatangan_final' => $dokumen->penandatangan_id,
        ]);

        try {
            $validator = validator($request->all(), $this->verificationRules(), [
                'penandatangan_final.required' => 'Penandatangan final wajib dipilih di metadata SK.',
                'sk_verifikator_1.required' => 'Verifikator Level 1 wajib dipilih.',
            ]);

            $this->addUniqueVerifierFlowValidation($validator, $request, [
                'sk_verifikator_1' => 'Verifikator Level 1',
                'sk_verifikator_2' => 'Verifikator Level 2',
                'sk_verifikator_3' => 'Verifikator Level 3',
            ]);

            $validated = $validator->validate();
        } catch (ValidationException $exception) {
            return redirect()
                ->route('admin.proses-sk', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 2,
                ])
                ->withErrors($exception->validator)
                ->withInput();
        }

        $approvalFlow = collect([
            $validated['sk_verifikator_1'] ?? null,
            $validated['sk_verifikator_2'] ?? null,
            $validated['sk_verifikator_3'] ?? null,
        ])
            ->filter()
            ->values()
            ->map(fn ($id, $index) => [
                'level' => $index + 1,
                'verifikator_id' => (int) $id,
            ])
            ->all();

        // Penandatangan otomatis ditambahkan sebagai level terakhir setelah verifikator pemeriksa.
        $finalFlow = [
            ...$approvalFlow,
            [
                'level' => count($approvalFlow) + 1,
                'verifikator_id' => (int) $dokumen->penandatangan_id,
            ],
        ];

        $statusLama = $dokumen->status_dokumen;

        DB::transaction(function () use ($dokumen, $validated, $finalFlow, $statusLama, $request): void {
            $dokumen->loadMissing('suratKeputusan');

            if ($dokumen->suratKeputusan) {
                // Catatan admin tetap ikut diperbarui, sementara nomor/tanggal SK sudah disimpan pada step metadata.
                $dokumen->suratKeputusan->update([
                    'catatan_admin' => $validated['catatan_admin'] ?? null,
                ]);
            }

            // Jalur verifikasi dibuat ulang agar perubahan pilihan level tidak meninggalkan data lama.
            $dokumen->verifikasi()->delete();

            foreach ($finalFlow as $step) {
                Verifikasi::query()->create([
                    'dokumen_id' => $dokumen->dokumen_id,
                    'verifikator_id' => $step['verifikator_id'],
                    'level' => $step['level'],
                    'status_verifikasi' => 'MENUNGGU',
                ]);
            }

            $dokumen->update([
                'status_dokumen' => 'MENUNGGU_VERIFIKASI',
            ]);

            RiwayatDokumen::query()->create([
                'dokumen_id' => $dokumen->dokumen_id,
                'aksi' => 'ADMIN_KIRIM_VERIFIKASI_SK',
                'status_lama' => $statusLama,
                'status_baru' => 'MENUNGGU_VERIFIKASI',
                'catatan' => $validated['catatan_admin'] ?? null,
                'actor_id' => $request->user()?->user_id,
            ]);
        });

        return redirect()
            ->route('admin.semua-sk')
            ->with('status', 'SK berhasil dikirim ke verifikator.');
    }

    // Method ini mengembalikan SK ke Pemohon tanpa membuat record verifikasi baru.
    public function returnForRevision(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN', 404);

        $validated = $request->validate([
            'catatan_admin' => ['required', 'string'],
        ], [
            'catatan_admin.required' => 'Catatan revisi wajib diisi sebelum mengembalikan SK.',
        ]);

        $statusLama = $dokumen->status_dokumen;

        DB::transaction(function () use ($dokumen, $validated, $statusLama, $request): void {
            $dokumen->loadMissing('suratKeputusan');

            if ($dokumen->suratKeputusan) {
                $dokumen->suratKeputusan->update([
                    'catatan_admin' => $validated['catatan_admin'],
                ]);
            }

            $dokumen->verifikasi()->delete();
            $dokumen->update([
                'status_dokumen' => 'PERLU_REVISI',
            ]);

            RiwayatDokumen::query()->create([
                'dokumen_id' => $dokumen->dokumen_id,
                'aksi' => 'ADMIN_REVISI_SK',
                'status_lama' => $statusLama,
                'status_baru' => 'PERLU_REVISI',
                'catatan' => $validated['catatan_admin'],
                'actor_id' => $request->user()?->user_id,
            ]);
        });

        return redirect()
            ->route('admin.pengajuan-sk')
            ->with('status', 'SK dikembalikan ke Pemohon untuk revisi.');
    }

    // Query dasar SK selalu memuat relasi utama agar view tidak memakai data contoh statis atau query tambahan berulang.
    private function baseSkQuery()
    {
        return Dokumen::query()
            ->with([
                'pemohon',
                'penandatangan',
                'suratKeputusan.dasarHukum',
                'suratKeputusan.skDasarHukum.dasarHukum',
                'suratKeputusan.skMenimbang',
                'suratKeputusan.skMemutuskan',
                'dokumenFiles' => fn ($query) => $query->latest('file_id'),
            ])
            ->where('jenis_dokumen', 'SURAT_KEPUTUSAN');
    }

    // Helper ini mempertahankan token lama jika dokumen sudah memilikinya, atau membuat token baru yang unik.
    private function resolveVerificationToken(Dokumen $dokumen): string
    {
        if (filled($dokumen->verification_token)) {
            return $dokumen->verification_token;
        }

        do {
            $token = Str::random(40);
        } while (Dokumen::query()->where('verification_token', $token)->exists());

        return $token;
    }

    private function resolveFinalPdf(Dokumen $dokumen): ?DokumenFile
    {
        return $dokumen->dokumenFiles()
            ->where('file_type', 'FINAL_PDF')
            ->latest('file_id')
            ->first();
    }

    private function buildFinalFileName(Dokumen $dokumen): string
    {
        $dokumen->loadMissing('suratKeputusan');

        $sourceName = $dokumen->suratKeputusan?->nomor_sk
            ?: $dokumen->suratKeputusan?->judul_sk
            ?: 'tanpa-nomor';
        $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $sourceName) ?? 'tanpa-nomor';
        $safeName = trim(preg_replace('/-+/', '-', $safeName) ?? $safeName, '-');

        return 'SIMAS-SK-' . substr($safeName !== '' ? $safeName : 'tanpa-nomor', 0, 120) . '.pdf';
    }

    // Aturan validasi metadata memastikan penandatangan SK berasal dari daftar jabatan resmi.
    private function metadataRules(): array
    {
        $activePenandatanganRule = fn () => Rule::exists('users', 'user_id')->where(fn ($query) => $query
            ->where('role', 'VERIFIKATOR')
            ->where('is_active', true)
            ->whereIn('jabatan', UserReferenceOptions::signerJabatans()));

        return [
            'nomor_sk' => ['required', 'string', 'max:100'],
            'tanggal_sk' => ['required', 'date'],
            'penandatangan_id' => ['required', 'integer', $activePenandatanganRule()],
            'catatan_admin' => ['nullable', 'string'],
        ];
    }

    // Aturan validasi memastikan setiap level memakai user aktif dan tidak menduplikasi penandatangan final.
    private function verificationRules(): array
    {
        $activeVerifikatorRule = fn () => Rule::exists('users', 'user_id')->where(fn ($query) => $query
            ->where('role', 'VERIFIKATOR')
            ->where('is_active', true));

        $activePenandatanganRule = fn () => Rule::exists('users', 'user_id')->where(fn ($query) => $query
            ->where('role', 'VERIFIKATOR')
            ->where('is_active', true)
            ->whereIn('jabatan', UserReferenceOptions::signerJabatans()));

        return [
            'sk_verifikator_1' => ['required', 'integer', $activeVerifikatorRule()],
            'sk_verifikator_2' => ['nullable', 'integer', $activeVerifikatorRule()],
            'sk_verifikator_3' => ['nullable', 'integer', $activeVerifikatorRule()],
            'penandatangan_final' => ['required', 'integer', $activePenandatanganRule()],
            'catatan_admin' => ['nullable', 'string'],
        ];
    }

    private function addUniqueVerifierFlowValidation(LaravelValidator $validator, Request $request, array $levelLabels): void
    {
        $validator->after(function (LaravelValidator $validator) use ($request, $levelLabels): void {
            $penandatanganFinal = (string) $request->input('penandatangan_final', '');
            $selectedVerifierLevels = [];

            foreach ($levelLabels as $field => $label) {
                $value = $request->input($field);

                if ($value === null || $value === '') {
                    continue;
                }

                $verifikatorId = (string) $value;

                if ($penandatanganFinal !== '' && $verifikatorId === $penandatanganFinal) {
                    // Penandatangan final ditambahkan otomatis sebagai level terakhir, bukan opsi level pemeriksa.
                    $validator->errors()->add($field, "{$label} tidak boleh sama dengan penandatangan final.");
                }

                if (isset($selectedVerifierLevels[$verifikatorId])) {
                    // Jalur verifikasi harus berisi user yang unik agar satu orang tidak memeriksa lebih dari satu level.
                    $validator->errors()->add($field, "{$label} tidak boleh sama dengan {$selectedVerifierLevels[$verifikatorId]}.");
                    continue;
                }

                $selectedVerifierLevels[$verifikatorId] = $label;
            }
        });
    }
}
