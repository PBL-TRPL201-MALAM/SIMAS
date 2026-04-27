<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Models\SuratBiasa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PemohonSuratController extends Controller
{
    public function create(): View
    {
        return view('pemohon.buat-surat');
    }

    public function store(Request $request): RedirectResponse
    {
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

        DB::transaction(function () use ($validated, $user, $file, $storedPath): void {
            $dokumen = Dokumen::create([
                'jenis_dokumen' => 'SURAT_BIASA',
                'pemohon_id' => $user->user_id,
                'status_dokumen' => 'DIAJUKAN',
            ]);

            SuratBiasa::create([
                'dokumen_id' => $dokumen->dokumen_id,
                'jenis_surat' => 'Surat Biasa',
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

        return redirect()
            ->route('pemohon.surat-saya')
            ->with('status', 'Pengajuan surat berhasil dikirim.');
    }

    public function index(Request $request): View
    {
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

    public function download(Request $request, Dokumen $dokumen): BinaryFileResponse|RedirectResponse
    {
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->pemohon_id === $request->user()->user_id,
            403
        );

        // Pemohon hanya boleh mengunduh dokumen yang sudah benar-benar dipublish.
        abort_unless($dokumen->status_dokumen === 'PUBLISHED', 404);

        $file = $this->resolvePublishedFile($dokumen);

        if (! $file) {
            return redirect()
                ->route('pemohon.surat-saya')
                ->with('error', 'File dokumen belum tersedia untuk diunduh.');
        }

        if (! Storage::disk('public')->exists($file->file_path)) {
            return redirect()
                ->route('pemohon.surat-saya')
                ->with('error', 'File PDF dokumen tidak ditemukan di penyimpanan.');
        }

        return response()->download(
            Storage::disk('public')->path($file->file_path),
            $this->buildPublishedDownloadFileName($dokumen)
        );
    }

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

    protected function buildPublishedDownloadFileName(Dokumen $dokumen): string
    {
        $dokumen->loadMissing('suratBiasa');

        // Nama file unduhan dibuat konsisten agar tidak mengikuti nama upload mentah dari pengguna.
        $nomorSurat = $dokumen->suratBiasa?->nomor_surat ?: 'tanpa-nomor';
        $nomorSurat = preg_replace('/[^A-Za-z0-9]+/', '-', $nomorSurat) ?? 'tanpa-nomor';
        $nomorSurat = trim($nomorSurat, '-');
        $nomorSurat = Str::limit($nomorSurat !== '' ? $nomorSurat : 'tanpa-nomor', 120, '');

        return sprintf('SIMAS-Surat-%d-%s.pdf', $dokumen->dokumen_id, $nomorSurat);
    }
}
