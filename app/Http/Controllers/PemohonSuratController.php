<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Models\RiwayatDokumen;
use App\Models\SuratBiasa;
use App\Support\SuratPdfDownloadName;
use App\Support\UserReferenceOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini menangani alur Pemohon untuk membuat pengajuan surat biasa, melihat suratnya, dan mengunduh hasil publish.
// Dalam alur MVC Laravel, controller ini menjadi jembatan antara form/view Pemohon dan model Dokumen/SuratBiasa/DokumenFile.
class PemohonSuratController extends Controller
{
    // Method ini hanya menampilkan form pengajuan surat biasa untuk pemohon.
    public function create(): View
    {
        return view('pemohon.buat-surat', [
            'pageTitle' => 'Buat Surat Baru',
            'pageHeading' => 'Buat Surat Baru',
            'pageSubtitle' => 'Upload PDF dan lengkapi data surat.',
            'formAction' => route('pemohon.surat.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Ajukan Surat',
            'draftFileRequired' => true,
            'dokumen' => null,
            'existingLampiranFiles' => collect(),
            'jenisSuratOptions' => UserReferenceOptions::jenisSuratBiasa(),
        ]);
    }

    // Method ini menampilkan form perbaikan untuk dokumen milik Pemohon yang sedang menunggu revisi.
    public function edit(Request $request, Dokumen $dokumen): View
    {
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->pemohon_id === $request->user()->user_id,
            403
        );
        abort_unless(in_array($dokumen->status_dokumen, ['PERLU_REVISI', 'DITOLAK'], true), 404);

        $dokumen->loadMissing([
            'suratBiasa',
            'dokumenFiles' => fn ($query) => $query->latest('file_id'),
            'verifikasi' => fn ($query) => $query->orderByDesc('verified_at')->orderByDesc('verifikasi_id'),
            'riwayatDokumen' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('riwayat_id'),
        ]);

        $revisionNote = $this->resolveRevisionNotePayload($dokumen);

        return view('pemohon.buat-surat', [
            'pageTitle' => 'Perbaiki Pengajuan Surat',
            'pageHeading' => 'Perbaiki Pengajuan Surat',
            'pageSubtitle' => 'Perbarui PDF dan data surat tanpa membuat pengajuan baru.',
            'formAction' => route('pemohon.surat.update', $dokumen),
            'formMethod' => 'POST',
            'submitLabel' => 'Kirim Perbaikan',
            'draftFileRequired' => true,
            'dokumen' => $dokumen,
            'revisionNote' => $revisionNote,
            'existingLampiranFiles' => $dokumen->dokumenFiles->where('file_type', 'LAMPIRAN'),
            'jenisSuratOptions' => UserReferenceOptions::jenisSuratBiasa(),
        ]);
    }

    // Method ini menangani pengajuan surat biasa dari Pemohon.
    // Data utama dokumen disimpan ke tabel dokumen, detail surat ke surat_biasa, dan file PDF ke dokumen_file.
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePengajuan($request);

        $user = $request->user();
        $file = $validated['draft_surat'];
        $lampiranPendukung = $validated['lampiran_pendukung'] ?? [];
        // PDF dari Pemohon menjadi sumber utama proses Admin Surat, sehingga tidak ada upload ulang PDF di alur surat biasa baru.
        $storedPath = $file->store('dokumen/draft', 'public');

        // Transaction dipakai karena 3 tabel harus berhasil bersama: dokumen, detail surat, dan file draft.
        // Jika salah satu gagal, Laravel akan rollback agar data pengajuan tidak setengah tersimpan.
        DB::transaction(function () use ($validated, $user, $file, $storedPath, $lampiranPendukung): void {
            // Dokumen utama dibuat lebih dulu sebagai pusat relasi semua proses surat berikutnya.
            $dokumen = Dokumen::create([
                'jenis_dokumen' => 'SURAT_BIASA',
                'pemohon_id' => $user->user_id,
                'status_dokumen' => 'DIAJUKAN',
            ]);

            // Jenis surat disimpan dari pilihan Pemohon, tetapi Admin Surat tetap dapat mengubahnya saat proses jika diperlukan.
            SuratBiasa::create([
                'dokumen_id' => $dokumen->dokumen_id,
                'jenis_surat' => $validated['jenis_surat'],
                'hal' => $validated['perihal'],
                'ringkasan_isi' => $validated['ringkasan'],
            ]);

            DokumenFile::create([
                'dokumen_id' => $dokumen->dokumen_id,
                'file_type' => 'DRAFT_PDF',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'uploaded_by' => $user->user_id,
                'uploaded_at' => now(),
            ]);

            // Lampiran pendukung bersifat opsional dan disimpan sebagai file tambahan, bukan sumber preview/verifikasi/publish.
            foreach ($lampiranPendukung as $lampiran) {
                $lampiranPath = $lampiran->store('dokumen/lampiran', 'public');

                DokumenFile::create([
                    'dokumen_id' => $dokumen->dokumen_id,
                    'file_type' => 'LAMPIRAN',
                    'file_name' => $lampiran->getClientOriginalName(),
                    'file_path' => $lampiranPath,
                    'uploaded_by' => $user->user_id,
                    'uploaded_at' => now(),
                ]);
            }
        });

        // Setelah pengajuan tersimpan, pemohon diarahkan ke daftar Surat Saya untuk memantau statusnya.
        return redirect()
            ->route('pemohon.surat-saya')
            ->with('status', 'Pengajuan surat berhasil dikirim.');
    }

    // Method ini menyimpan perbaikan pengajuan ke dokumen yang sama saat status masih PERLU_REVISI.
    public function update(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->pemohon_id === $request->user()->user_id,
            403
        );
        abort_unless(in_array($dokumen->status_dokumen, ['PERLU_REVISI', 'DITOLAK'], true), 404);

        $validated = $this->validatePengajuan($request);
        $dokumen->loadMissing([
            'suratBiasa',
            'dokumenFiles',
            'verifikasi',
            'riwayatDokumen',
            'posisiElemenDokumen',
        ]);

        $user = $request->user();
        $file = $validated['draft_surat'];
        $lampiranPendukung = $validated['lampiran_pendukung'] ?? [];
        $storedPath = $file->store('dokumen/draft', 'public');
        $statusLama = $dokumen->status_dokumen;

        DB::transaction(function () use ($dokumen, $validated, $user, $file, $storedPath, $lampiranPendukung, $statusLama): void {
            $suratBiasa = $dokumen->suratBiasa ?? new SuratBiasa([
                'dokumen_id' => $dokumen->dokumen_id,
            ]);

            $suratBiasa->fill([
                'jenis_surat' => $validated['jenis_surat'],
                'hal' => $validated['perihal'],
                'ringkasan_isi' => $validated['ringkasan'],
                // Catatan revisi lama dibersihkan karena Pemohon sudah mengirim versi perbaikan terbaru.
                'catatan_admin' => null,
            ]);
            $suratBiasa->save();

            DokumenFile::query()->create([
                'dokumen_id' => $dokumen->dokumen_id,
                'file_type' => 'DRAFT_PDF',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'uploaded_by' => $user->user_id,
                'uploaded_at' => now(),
            ]);

            // Lampiran baru ditambahkan ke dokumen yang sama agar riwayat pendukung tetap lengkap.
            foreach ($lampiranPendukung as $lampiran) {
                $lampiranPath = $lampiran->store('dokumen/lampiran', 'public');

                DokumenFile::query()->create([
                    'dokumen_id' => $dokumen->dokumen_id,
                    'file_type' => 'LAMPIRAN',
                    'file_name' => $lampiran->getClientOriginalName(),
                    'file_path' => $lampiranPath,
                    'uploaded_by' => $user->user_id,
                    'uploaded_at' => now(),
                ]);
            }

            // Artefak proses lama dibersihkan supaya verifikasi berikutnya selalu memakai PDF revisi terbaru.
            $obsoleteFiles = $dokumen->dokumenFiles
                ->whereIn('file_type', ['PREVIEW_VERIFIKASI_PDF', 'FINAL_PDF'])
                ->values();

            foreach ($obsoleteFiles as $obsoleteFile) {
                if ($obsoleteFile->file_path && Storage::disk('public')->exists($obsoleteFile->file_path)) {
                    Storage::disk('public')->delete($obsoleteFile->file_path);
                }

                $obsoleteFile->delete();
            }

            // Posisi elemen lama ikut dihapus agar Admin Surat mengatur ulang terhadap PDF revisi yang baru.
            $dokumen->posisiElemenDokumen()->delete();

            // Verifikasi lama tidak dipakai lagi pada siklus revisi baru, jadi aman untuk dihapus.
            $dokumen->verifikasi()->delete();

            $dokumen->update([
                'status_dokumen' => 'DIAJUKAN',
                'verification_token' => null,
                'published_at' => null,
                'file_final_path' => null,
            ]);

            RiwayatDokumen::query()->create([
                'dokumen_id' => $dokumen->dokumen_id,
                'aksi' => 'PEMOHON_PERBAIKI_PENGAJUAN',
                'status_lama' => $statusLama,
                'status_baru' => 'DIAJUKAN',
                'catatan' => 'Pemohon mengirim ulang pengajuan setelah revisi.',
                'actor_id' => $user->user_id,
            ]);
        });

        return redirect()
            ->route('pemohon.surat-saya')
            ->with('status', 'Perbaikan pengajuan berhasil dikirim ulang.');
    }

    // Method ini mengambil daftar surat biasa milik pemohon login untuk halaman Surat Saya.
    public function index(Request $request): View
    {
        // Halaman Surat Saya memuat catatan revisi dari Admin Surat, Verifikator, atau riwayat agar modal detail tidak perlu query tambahan.
        $suratSaya = Dokumen::query()
            ->with([
                'suratBiasa',
                'dokumenFiles' => fn ($query) => $query->orderByDesc('file_id'),
                'verifikasi' => fn ($query) => $query->orderByDesc('verified_at')->orderByDesc('verifikasi_id'),
                'riwayatDokumen' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('riwayat_id'),
            ])
            ->where('pemohon_id', $request->user()->user_id)
            ->where('jenis_dokumen', 'SURAT_BIASA')
            ->latest('created_at')
            ->get();

        return view('pemohon.surat-saya', [
            'suratSaya' => $suratSaya,
        ]);
    }

    // Method ini menampilkan FINAL_PDF secara inline agar tombol "Lihat Dokumen" membuka file di tab baru.
    public function previewPublished(Request $request, Dokumen $dokumen): BinaryFileResponse|RedirectResponse
    {
        // Preview final hanya boleh dibuka oleh pemohon pemilik dokumen dan hanya setelah dokumen resmi published.
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->pemohon_id === $request->user()->user_id,
            403
        );

        abort_unless($dokumen->status_dokumen === 'PUBLISHED', 404);

        $file = $this->resolvePublishedFile($dokumen);

        if (! $file || ! Storage::disk('public')->exists($file->file_path)) {
            return redirect()
                ->route('pemohon.surat-saya')
                ->with('error', 'File dokumen belum tersedia untuk dilihat.');
        }

        return response()->file(
            Storage::disk('public')->path($file->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . addslashes($this->buildPublishedDownloadFileName($dokumen)) . '"',
            ]
        );
    }

    // Method ini mengunduh FINAL_PDF hasil publish ke browser pemohon.
    public function download(Request $request, Dokumen $dokumen): BinaryFileResponse|RedirectResponse
    {
        // abort_unless adalah guard Laravel: hentikan request jika dokumen bukan milik pemohon login.
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->pemohon_id === $request->user()->user_id,
            403
        );

        // Pemohon hanya boleh mengunduh dokumen yang sudah benar-benar dipublish.
        abort_unless($dokumen->status_dokumen === 'PUBLISHED', 404);

        $file = $this->resolvePublishedFile($dokumen);

        if (! $file) {
            // Redirect dengan flash error memberi feedback ke halaman Surat Saya tanpa menampilkan error teknis.
            return redirect()
                ->route('pemohon.surat-saya')
                ->with('error', 'File dokumen belum tersedia untuk diunduh.');
        }

        if (! Storage::disk('public')->exists($file->file_path)) {
            // File record ada di database, tetapi file fisik hilang di storage; user dikembalikan dengan pesan aman.
            return redirect()
                ->route('pemohon.surat-saya')
                ->with('error', 'File PDF dokumen tidak ditemukan di penyimpanan.');
        }

        // response()->download membuat browser mengunduh file dengan nama rapi dari helper SuratPdfDownloadName.
        return response()->download(
            Storage::disk('public')->path($file->file_path),
            $this->buildPublishedDownloadFileName($dokumen)
        );
    }

    // Method ini mengunduh lampiran pendukung milik Pemohon tanpa menjadikannya dokumen final terbit.
    public function downloadLampiran(Request $request, DokumenFile $file): BinaryFileResponse
    {
        $file->loadMissing('dokumen');
        $dokumen = $file->dokumen;

        abort_unless(
            $dokumen
                && $file->file_type === 'LAMPIRAN'
                && $dokumen->jenis_dokumen === 'SURAT_BIASA'
                && $dokumen->pemohon_id === $request->user()->user_id,
            404
        );

        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return response()->download(
            Storage::disk('public')->path($file->file_path),
            $file->file_name
        );
    }

    // Method ini membuka lampiran pendukung tertentu secara inline selama formatnya bisa dibaca browser.
    public function previewLampiran(Request $request, DokumenFile $file): BinaryFileResponse
    {
        $file->loadMissing('dokumen');
        $dokumen = $file->dokumen;

        abort_unless(
            $dokumen
                && $file->file_type === 'LAMPIRAN'
                && $dokumen->jenis_dokumen === 'SURAT_BIASA'
                && $dokumen->pemohon_id === $request->user()->user_id
                && $file->isPreviewableLampiran(),
            404
        );

        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return response()->file(
            Storage::disk('public')->path($file->file_path),
            [
                'Content-Type' => $file->lampiranPreviewContentType() ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . addslashes($file->file_name) . '"',
            ]
        );
    }

    // Helper ini mencari file publish final yang boleh diunduh pemohon.
    protected function resolvePublishedFile(Dokumen $dokumen): ?DokumenFile
    {
        // Pemohon hanya menerima FINAL_PDF; preview verifikasi tidak dianggap dokumen terbit.
        return $dokumen->dokumenFiles()
            ->where('file_type', 'FINAL_PDF')
            ->latest('file_id')
            ->first();
    }

    // Helper ini membangun nama file unduhan agar tidak bergantung pada nama file di storage.
    protected function buildPublishedDownloadFileName(Dokumen $dokumen): string
    {
        // Nama unduhan dipisahkan dari nama file storage agar pemohon selalu menerima nama file yang rapi dan konsisten.
        return SuratPdfDownloadName::forDokumen($dokumen);
    }

    // Helper ini memilih catatan revisi paling relevan untuk ditampilkan pada modal dan halaman revisi pemohon.
    public function resolveRevisionNotePayload(Dokumen $dokumen): array
    {
        if (filled($dokumen->suratBiasa?->catatan_admin)) {
            return [
                'source' => 'Catatan Admin Surat',
                'text' => $dokumen->suratBiasa->catatan_admin,
            ];
        }

        $latestVerificationNote = $dokumen->verifikasi
            ->first(fn ($verifikasi) => filled($verifikasi->catatan));

        if ($latestVerificationNote) {
            return [
                'source' => 'Catatan Verifikator',
                'text' => $latestVerificationNote->catatan,
            ];
        }

        $latestHistoryNote = $dokumen->riwayatDokumen
            ->first(fn ($riwayat) => filled($riwayat->catatan));

        if ($latestHistoryNote) {
            return [
                'source' => str_contains($latestHistoryNote->aksi ?? '', 'VERIFIKATOR')
                    ? 'Catatan Verifikator'
                    : 'Catatan Admin Surat',
                'text' => $latestHistoryNote->catatan,
            ];
        }

        return [
            'source' => 'Catatan Revisi',
            'text' => '',
        ];
    }

    // Helper ini dipakai bersama oleh form pengajuan baru dan form perbaikan agar aturan inputnya tetap konsisten.
    protected function validatePengajuan(Request $request): array
    {
        return $request->validate([
            'draft_surat' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'jenis_surat' => ['required', Rule::in(UserReferenceOptions::jenisSuratBiasa())],
            'perihal' => ['required', 'string', 'max:255'],
            'ringkasan' => ['required', 'string'],
            'lampiran_pendukung' => ['nullable', 'array'],
            'lampiran_pendukung.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ], [
            'draft_surat.required' => 'File draft surat wajib diunggah.',
            'draft_surat.mimes' => 'File draft harus berformat PDF.',
            'draft_surat.max' => 'Ukuran file draft maksimal 10 MB.',
            'jenis_surat.required' => 'Jenis surat wajib dipilih.',
            'jenis_surat.in' => 'Jenis surat yang dipilih tidak tersedia.',
            'perihal.required' => 'Perihal wajib diisi.',
            'ringkasan.required' => 'Ringkasan wajib diisi.',
            'lampiran_pendukung.*.mimes' => 'Lampiran hanya boleh berupa PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'lampiran_pendukung.*.max' => 'Ukuran setiap lampiran maksimal 10 MB.',
        ]);
    }
}
