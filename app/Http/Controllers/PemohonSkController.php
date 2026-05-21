<?php

namespace App\Http\Controllers;

use App\Models\DasarHukum;
use App\Models\Dokumen;
use App\Models\RiwayatDokumen;
use App\Models\SuratKeputusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini menangani MVP pengajuan Surat Keputusan dari sisi Pemohon.
// Dasar hukum yang dipilih selalu berasal dari master dasar_hukum yang masih aktif.
class PemohonSkController extends Controller
{
    // Method ini menampilkan form Buat Pengajuan SK dengan pilihan Mengingat dari tabel dasar_hukum.
    public function create(): View
    {
        $dasarHukumAktif = DasarHukum::query()
            ->where('is_active', true)
            ->orderBy('judul_hukum')
            ->get();

        return view('pemohon.buat-sk', [
            'dasarHukumAktif' => $dasarHukumAktif,
        ]);
    }

    // Method ini menyimpan pengajuan SK baru beserta relasi dasar hukum, menimbang, dan memutuskan.
    public function store(Request $request): RedirectResponse
    {
        if (! $request->has('dasar_hukum_ids') && $request->has('dasar_hukum_id')) {
            // Form Mengingat mengirim dasar_hukum_id[] sesuai urutan pilihan; disalin ke nama validasi internal.
            $request->merge([
                'dasar_hukum_ids' => $request->input('dasar_hukum_id'),
            ]);
        }

        $validated = $request->validate([
            'judul_sk' => ['required', 'string', 'max:255'],
            'tentang' => ['required', 'string'],
            'tanggal_sk' => ['nullable', 'date'],
            'menetapkan' => ['nullable', 'string'],
            'menimbang' => ['required', 'array', 'min:1'],
            'menimbang.*' => ['nullable', 'string'],
            'memutuskan' => ['required', 'array', 'min:1'],
            'memutuskan.*' => ['nullable', 'string'],
            'dasar_hukum_ids' => ['required', 'array', 'min:1'],
            'dasar_hukum_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('dasar_hukum', 'dasar_hukum_id')->where('is_active', true),
            ],
        ], [
            'judul_sk.required' => 'Judul SK wajib diisi.',
            'tentang.required' => 'Tentang wajib diisi.',
            'menimbang.required' => 'Menimbang wajib diisi.',
            'memutuskan.required' => 'Memutuskan wajib diisi.',
            'dasar_hukum_ids.required' => 'Pilih minimal satu dasar hukum aktif.',
            'dasar_hukum_ids.*.exists' => 'Dasar hukum yang dipilih tidak aktif atau tidak tersedia.',
        ]);

        $user = $request->user();
        $menimbangItems = $this->filterListItems($validated['menimbang']);
        $memutuskanItems = $this->filterListItems($validated['memutuskan']);

        if ($menimbangItems === []) {
            throw ValidationException::withMessages([
                'menimbang' => 'Isi minimal satu butir Menimbang.',
            ]);
        }

        if ($memutuskanItems === []) {
            throw ValidationException::withMessages([
                'memutuskan' => 'Isi minimal satu diktum Memutuskan.',
            ]);
        }

        DB::transaction(function () use ($validated, $user, $menimbangItems, $memutuskanItems): void {
            // Dokumen utama dibuat lebih dulu sebagai induk alur SK dari pengajuan sampai verifikasi/publish.
            $dokumen = Dokumen::query()->create([
                'jenis_dokumen' => 'SURAT_KEPUTUSAN',
                'pemohon_id' => $user->user_id,
                'status_dokumen' => 'DIAJUKAN',
            ]);

            $suratKeputusan = SuratKeputusan::query()->create([
                'dokumen_id' => $dokumen->dokumen_id,
                'judul_sk' => $validated['judul_sk'],
                'tentang' => $validated['tentang'],
                // Tanggal SK belum diisi dari UI saat pengajuan awal, jadi boleh tersimpan null.
                'tanggal_sk' => $validated['tanggal_sk'] ?? null,
            ]);

            // Setiap baris textarea disimpan sebagai butir agar urutan Menimbang tetap bisa dirender ulang.
            foreach ($menimbangItems as $index => $isiMenimbang) {
                $suratKeputusan->skMenimbang()->create([
                    'urutan' => $index + 1,
                    'isi_menimbang' => $isiMenimbang,
                ]);
            }

            // Diktum Memutuskan juga disimpan berurutan untuk kebutuhan review dan generator dokumen berikutnya.
            foreach ($memutuskanItems as $index => $isiMemutuskan) {
                $suratKeputusan->skMemutuskan()->create([
                    'urutan' => $index + 1,
                    'isi_memutuskan' => $isiMemutuskan,
                ]);
            }

            $dasarHukumPayload = collect($validated['dasar_hukum_ids'])
                ->values()
                ->mapWithKeys(fn (int $dasarHukumId, int $index) => [
                    $dasarHukumId => ['urutan' => $index + 1],
                ])
                ->all();

            // Relasi Mengingat disimpan di pivot sk_dasar_hukum agar tetap menunjuk master dasar_hukum.
            $suratKeputusan->dasarHukum()->attach($dasarHukumPayload);

            RiwayatDokumen::query()->create([
                'dokumen_id' => $dokumen->dokumen_id,
                'aksi' => 'PEMOHON_AJUKAN_SK',
                'status_lama' => null,
                'status_baru' => 'DIAJUKAN',
                'catatan' => 'Pemohon mengajukan Surat Keputusan.',
                'actor_id' => $user->user_id,
            ]);
        });

        return redirect()
            ->route('pemohon.sk-saya')
            ->with('status', 'Pengajuan SK berhasil dikirim.');
    }

    // Method ini mengambil daftar SK milik pemohon login dari database.
    public function index(Request $request): View
    {
        $skSaya = Dokumen::query()
            ->with([
                'suratKeputusan.dasarHukum',
                'suratKeputusan.skMenimbang',
                'suratKeputusan.skMemutuskan',
                'dokumenFiles' => fn ($query) => $query->orderByDesc('file_id'),
                'verifikasi' => fn ($query) => $query->orderByDesc('verified_at')->orderByDesc('verifikasi_id'),
                'riwayatDokumen' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('riwayat_id'),
            ])
            ->where('pemohon_id', $request->user()->user_id)
            ->where('jenis_dokumen', 'SURAT_KEPUTUSAN')
            ->latest('created_at')
            ->get();

        return view('pemohon.sk-saya', [
            'skSaya' => $skSaya,
        ]);
    }

    // Method ini membuka FINAL_PDF SK secara inline ketika dokumen sudah resmi published.
    public function previewPublished(Request $request, Dokumen $dokumen): BinaryFileResponse|RedirectResponse
    {
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN' && $dokumen->pemohon_id === $request->user()->user_id,
            403
        );
        abort_unless($dokumen->status_dokumen === 'PUBLISHED', 404);

        $file = $this->resolvePublishedFile($dokumen);

        if (! $file || ! Storage::disk('public')->exists($file->file_path)) {
            return redirect()
                ->route('pemohon.sk-saya')
                ->with('error', 'File final SK belum tersedia untuk dilihat.');
        }

        return response()->file(
            Storage::disk('public')->path($file->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . addslashes($this->buildPublishedDownloadFileName($dokumen)) . '"',
            ]
        );
    }

    // Method ini mengunduh FINAL_PDF SK tanpa membuat atau mengubah PDF final.
    public function download(Request $request, Dokumen $dokumen): BinaryFileResponse|RedirectResponse
    {
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN' && $dokumen->pemohon_id === $request->user()->user_id,
            403
        );
        abort_unless($dokumen->status_dokumen === 'PUBLISHED', 404);

        $file = $this->resolvePublishedFile($dokumen);

        if (! $file || ! Storage::disk('public')->exists($file->file_path)) {
            return redirect()
                ->route('pemohon.sk-saya')
                ->with('error', 'File final SK belum tersedia untuk diunduh.');
        }

        return response()->download(
            Storage::disk('public')->path($file->file_path),
            $this->buildPublishedDownloadFileName($dokumen)
        );
    }

    // Pemohon hanya boleh melihat file FINAL_PDF; file preview verifikasi tidak dianggap dokumen terbit.
    private function resolvePublishedFile(Dokumen $dokumen)
    {
        return $dokumen->dokumenFiles()
            ->where('file_type', 'FINAL_PDF')
            ->latest('file_id')
            ->first();
    }

    // Nama unduhan SK dibuat dari nomor atau judul SK agar file yang diterima pemohon tetap rapi.
    private function buildPublishedDownloadFileName(Dokumen $dokumen): string
    {
        $dokumen->loadMissing('suratKeputusan');

        $sourceName = $dokumen->suratKeputusan?->nomor_sk
            ?: $dokumen->suratKeputusan?->judul_sk
            ?: 'tanpa-nomor';
        $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $sourceName) ?? 'tanpa-nomor';
        $safeName = trim(preg_replace('/-+/', '-', $safeName) ?? $safeName, '-');

        return 'SIMAS-SK-' . substr($safeName !== '' ? $safeName : 'tanpa-nomor', 0, 120) . '.pdf';
    }

    // Helper ini mempertahankan urutan input dinamis dan membuang baris kosong sebelum disimpan ke tabel detail SK.
    private function filterListItems(array $items): array
    {
        return collect($items)
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }
}
