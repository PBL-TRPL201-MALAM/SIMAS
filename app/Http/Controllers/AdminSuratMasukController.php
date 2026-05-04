<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini menangani pintu awal pekerjaan Admin/TU untuk surat biasa yang baru diajukan Pemohon.
// Alurnya: ambil dokumen berstatus DIAJUKAN -> tampilkan di tabel -> Admin/TU unduh draft DOCX atau mulai proses surat.
class AdminSuratMasukController extends Controller
{
    // Halaman ini menjadi pintu kerja awal Admin/TU untuk melihat pengajuan baru yang masih berstatus DIAJUKAN.
    public function index(): View
    {
        // Query hanya mengambil SURAT_BIASA yang masih DIAJUKAN agar halaman ini fokus pada antrean yang belum diproses.
        // Relasi pemohon, suratBiasa, dan file draft diload sekaligus supaya Blade tidak menjalankan query tambahan per baris.
        $pengajuan = Dokumen::query()
            ->with([
                'pemohon',
                'suratBiasa',
                'dokumenFiles' => fn ($query) => $query
                    ->where('file_type', 'DRAFT_DOCX')
                    ->latest('file_id'),
            ])
            ->where('jenis_dokumen', 'SURAT_BIASA')
            ->where('status_dokumen', 'DIAJUKAN')
            ->latest('created_at')
            ->get();

        return view('admin.pengajuan-masuk', [
            'pengajuan' => $pengajuan,
        ]);
    }

    // Method ini menerima Dokumen dari route model binding dan mengirim file DOCX asli ke browser Admin/TU.
    public function downloadDraft(Dokumen $dokumen): BinaryFileResponse
    {
        // Guard ini memastikan draft hanya bisa diunduh untuk surat biasa yang masih ada pada fase pengajuan masuk.
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->status_dokumen === 'DIAJUKAN',
            404
        );

        // Admin/TU mengunduh draft DOCX asli dari pemohon sebagai bahan pemeriksaan sebelum membuat PDF hasil review.
        $draftFile = $dokumen->dokumenFiles()
            ->where('file_type', 'DRAFT_DOCX')
            ->latest('file_id')
            ->firstOrFail();

        return response()->download(
            Storage::disk('public')->path($draftFile->file_path),
            $draftFile->file_name
        );
    }
}
