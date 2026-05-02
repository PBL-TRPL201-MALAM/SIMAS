<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Models\SuratBiasa;
use App\Support\SuratPdfDownloadName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini menangani alur Pemohon untuk membuat pengajuan surat biasa, melihat suratnya, dan mengunduh hasil publish.
// Dalam alur MVC Laravel, controller ini menjadi jembatan antara form/view Pemohon dan model Dokumen/SuratBiasa/DokumenFile.
class PemohonSuratController extends Controller
{
    // Method ini hanya menampilkan form pengajuan surat biasa untuk pemohon.
    public function create(): View
    {
        return view('pemohon.buat-surat');
    }

    // Method ini menangani pengajuan surat biasa dari Pemohon.
    // Data utama dokumen disimpan ke tabel dokumen, detail surat ke surat_biasa, dan file DOCX ke dokumen_file.
    public function store(Request $request): RedirectResponse
    {
        // Validasi request memastikan file yang masuk benar-benar DOCX dan data wajib surat sudah lengkap.
        $validated = $request->validate([
            'draft_surat' => ['required', 'file', 'mimes:docx', 'max:10240'],
            'perihal' => ['required', 'string', 'max:255'],
            'ringkasan' => ['required', 'string'],
        ], [
            'draft_surat.required' => 'File draft surat wajib diunggah.',
            'draft_surat.mimes' => 'File draft harus berformat DOCX.',
            'draft_surat.max' => 'Ukuran file draft maksimal 10 MB.',
            'perihal.required' => 'Perihal wajib diisi.',
            'ringkasan.required' => 'Ringkasan wajib diisi.',
        ]);

        $user = $request->user();
        $file = $validated['draft_surat'];
        // Draft awal pemohon selalu disimpan sebagai DOCX agar admin masih bisa memeriksa dokumen sumbernya.
        $storedPath = $file->store('dokumen/draft', 'public');

        // Transaction dipakai karena 3 tabel harus berhasil bersama: dokumen, detail surat, dan file draft.
        // Jika salah satu gagal, Laravel akan rollback agar data pengajuan tidak setengah tersimpan.
        DB::transaction(function () use ($validated, $user, $file, $storedPath): void {
            // Dokumen utama dibuat lebih dulu sebagai pusat relasi semua proses surat berikutnya.
            $dokumen = Dokumen::create([
                'jenis_dokumen' => 'SURAT_BIASA',
                'pemohon_id' => $user->user_id,
                'status_dokumen' => 'DIAJUKAN',
            ]);

            // Pemohon hanya mengisi perihal dan ringkasan; metadata resmi surat baru dilengkapi oleh Admin/TU.
            SuratBiasa::create([
                'dokumen_id' => $dokumen->dokumen_id,
                'hal' => $validated['perihal'],
                'ringkasan_isi' => $validated['ringkasan'],
            ]);

            DokumenFile::create([
                'dokumen_id' => $dokumen->dokumen_id,
                'file_type' => 'DRAFT_DOCX',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'uploaded_by' => $user->user_id,
                'uploaded_at' => now(),
            ]);
        });

        // Setelah pengajuan tersimpan, pemohon diarahkan ke daftar Surat Saya untuk memantau statusnya.
        return redirect()
            ->route('pemohon.surat-saya')
            ->with('status', 'Pengajuan surat berhasil dikirim.');
    }

    // Method ini mengambil daftar surat biasa milik pemohon login untuk halaman Surat Saya.
    public function index(Request $request): View
    {
        // Halaman Surat Saya hanya menampilkan surat biasa milik pemohon yang sedang login beserta file terakhirnya.
        $suratSaya = Dokumen::query()
            ->with(['suratBiasa', 'dokumenFiles' => fn ($query) => $query->orderByDesc('file_id')])
            ->where('pemohon_id', $request->user()->user_id)
            ->where('jenis_dokumen', 'SURAT_BIASA')
            ->latest('created_at')
            ->get();

        return view('pemohon.surat-saya', [
            'suratSaya' => $suratSaya,
        ]);
    }

    // Method ini mengirim file PDF hasil publish ke browser pemohon.
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

    // Helper ini mencari file publish yang paling layak diunduh pemohon.
    protected function resolvePublishedFile(Dokumen $dokumen): ?DokumenFile
    {
        // Setelah published, file final diutamakan. Preview verifikasi dipakai sebagai fallback aman.
        foreach (['FINAL_PDF', 'PREVIEW_VERIFIKASI_PDF'] as $fileType) {
            $file = $dokumen->dokumenFiles()
                ->where('file_type', $fileType)
                ->latest('file_id')
                ->first();

            if ($file) {
                return $file;
            }
        }

        return null;
    }

    // Helper ini membangun nama file unduhan agar tidak bergantung pada nama file di storage.
    protected function buildPublishedDownloadFileName(Dokumen $dokumen): string
    {
        // Nama unduhan dipisahkan dari nama file storage agar pemohon selalu menerima nama file yang rapi dan konsisten.
        return SuratPdfDownloadName::forDokumen($dokumen);
    }
}
