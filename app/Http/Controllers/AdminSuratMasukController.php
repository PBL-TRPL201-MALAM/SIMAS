<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini menangani pintu awal pekerjaan Admin Surat untuk surat biasa yang baru diajukan Pemohon.
// Alurnya: ambil dokumen berstatus DIAJUKAN -> tampilkan di tabel -> Admin Surat cek PDF pemohon atau mulai proses surat.
class AdminSuratMasukController extends Controller
{
    // Halaman ini menjadi pintu kerja awal Admin Surat untuk melihat pengajuan baru yang masih berstatus DIAJUKAN.
    public function index(): View
    {
        // Query hanya mengambil SURAT_BIASA yang masih DIAJUKAN agar halaman ini fokus pada antrean yang belum diproses.
        // Relasi pemohon, suratBiasa, dan DRAFT_PDF diload sekaligus supaya Blade tidak menjalankan query tambahan per baris.
        $pengajuan = Dokumen::query()
            ->with([
                'pemohon',
                'suratBiasa',
                'dokumenFiles' => fn ($query) => $query
                    ->whereIn('file_type', ['DRAFT_PDF', 'LAMPIRAN'])
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

    // Method ini menerima Dokumen dari route model binding dan menampilkan PDF yang diunggah Pemohon.
    public function previewDraftPdf(Dokumen $dokumen): BinaryFileResponse
    {
        // Guard ini memastikan PDF draft hanya bisa dicek untuk surat biasa yang masih ada pada fase pengajuan masuk.
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->status_dokumen === 'DIAJUKAN',
            404
        );

        // Admin Surat mengecek DRAFT_PDF dari Pemohon; file ini juga menjadi sumber preview pada halaman proses surat.
        $draftFile = $dokumen->dokumenFiles()
            ->where('file_type', 'DRAFT_PDF')
            ->latest('file_id')
            ->firstOrFail();

        abort_unless(Storage::disk('public')->exists($draftFile->file_path), 404);

        return response()->file(
            Storage::disk('public')->path($draftFile->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $draftFile->file_name . '"',
            ]
        );
    }
}
