<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminSuratMasukController extends Controller
{
    // Halaman ini menjadi pintu kerja awal Admin/TU untuk melihat pengajuan baru yang masih berstatus DIAJUKAN.
    public function index(): View
    {
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

    public function downloadDraft(Dokumen $dokumen): BinaryFileResponse
    {
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
