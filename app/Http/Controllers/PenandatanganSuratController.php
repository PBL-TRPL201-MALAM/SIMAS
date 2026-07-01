<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Controller ini menangani area Penandatangan, terpisah dari Verifikator.
// Logika bisnis inti (query, approve/reject, preview PDF) diwarisi dari VerifikatorSuratController
// agar tidak ada duplikasi kode. Hanya method view yang di-override untuk mengarahkan ke folder penandatangan.
class PenandatanganSuratController extends VerifikatorSuratController
{
    // Helper ini menentukan prefix route name berdasarkan role user aktif.
    // Karena controller ini khusus penandatangan, prefix selalu 'penandatangan'.
    protected function routePrefix(): string
    {
        return 'penandatangan';
    }

    // Menampilkan daftar surat biasa yang menunggu persetujuan penandatangan login.
    public function menunggu(Request $request): View
    {
        $status = $this->normalizeVerificationStatus($request->query('status'));

        $suratQuery = $status === 'MENUNGGU'
            ? $this->activePendingVerificationQuery($request, 'SURAT_BIASA')
            : $this->baseQuery($request)->where('status_verifikasi', $status);

        return view('penandatangan.surat-menunggu', [
            'suratMenunggu' => $suratQuery
                ->when(
                    $status === 'MENUNGGU',
                    fn ($query) => $query->orderBy('created_at'),
                    fn ($query) => $query->latest('verified_at')->latest('updated_at')
                )
                ->get(),
            'activeStatus' => strtolower($status),
            'statusCounts' => $this->assignedVerificationStatusCounts($request, 'SURAT_BIASA'),
        ]);
    }

    // Menampilkan daftar SK yang menunggu persetujuan penandatangan.
    public function skMenunggu(Request $request): View
    {
        $status = $this->normalizeVerificationStatus($request->query('status'));

        $skQuery = $status === 'MENUNGGU'
            ? $this->activePendingVerificationQuery($request, 'SURAT_KEPUTUSAN')
            : $this->baseSkQuery($request)->where('status_verifikasi', $status);

        return view('penandatangan.sk-menunggu', [
            'skMenunggu' => $skQuery
                ->when(
                    $status === 'MENUNGGU',
                    fn ($query) => $query->orderBy('created_at'),
                    fn ($query) => $query->latest('verified_at')->latest('updated_at')
                )
                ->get(),
            'activeStatus' => strtolower($status),
            'statusCounts' => $this->assignedVerificationStatusCounts($request, 'SURAT_KEPUTUSAN'),
        ]);
    }

    // Redirect ke tab disetujui pada halaman surat menunggu.
    public function disetujui(): RedirectResponse
    {
        return redirect()->route('penandatangan.surat-menunggu', ['status' => 'disetujui']);
    }

    // Redirect ke tab ditolak pada halaman surat menunggu.
    public function ditolak(): RedirectResponse
    {
        return redirect()->route('penandatangan.surat-menunggu', ['status' => 'ditolak']);
    }

    // Menampilkan seluruh surat biasa untuk monitoring penandatangan.
    public function semua(): View
    {
        return view('penandatangan.surat-semua', [
            'suratSemua' => $this->allDokumenQuery('SURAT_BIASA')
                ->latest('created_at')
                ->get(),
        ]);
    }

    // Menampilkan seluruh SK untuk monitoring penandatangan.
    public function skSemua(): View
    {
        return view('penandatangan.sk-semua', [
            'skSemua' => $this->allDokumenQuery('SURAT_KEPUTUSAN')
                ->latest('created_at')
                ->get(),
        ]);
    }

    // Menampilkan detail surat biasa untuk penandatangan.
    public function detailSurat(Request $request, \App\Models\Dokumen $dokumen): View
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        $dokumen->loadMissing([
            'pemohon',
            'suratBiasa',
            'dokumenFiles',
            'verifikasi.verifikator',
        ]);

        $verifikasi = $this->resolveAssignedVerifikasiForDokumen($request, $dokumen);
        $isReadOnly = $request->query('from') === 'semua' || ! $verifikasi;

        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);
        $previewPdfUrl = null;

        if ($pdfFile && \Illuminate\Support\Facades\Storage::disk('local')->exists($pdfFile->file_path)) {
            $previewPdfUrl = route('penandatangan.surat.preview-pdf', $dokumen);
        }

        return view('penandatangan.surat-detail', [
            'verifikasi' => $verifikasi,
            'dokumen' => $dokumen,
            'previewPdfUrl' => $previewPdfUrl,
            'downloadPdfUrl' => $pdfFile && $verifikasi && ! $isReadOnly ? route('penandatangan.surat.unduh-pdf', $dokumen) : null,
            'canProcess' => $verifikasi && ! $isReadOnly && $this->canProcessVerifikasi($verifikasi),
            'canAccessAssignedFiles' => $verifikasi && ! $isReadOnly,
            'isReadOnly' => $isReadOnly,
            'activePage' => $isReadOnly ? 'surat-semua' : 'surat-menunggu',
            'backUrl' => $isReadOnly ? route('penandatangan.surat-semua') : route('penandatangan.surat-menunggu'),
            'defaultDecision' => old('keputusan', $request->query('aksi') === 'tolak' ? 'tolak' : 'setuju'),
        ]);
    }

    // Menampilkan detail SK untuk penandatangan.
    public function detailSk(Request $request, \App\Models\Dokumen $dokumen): View
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN', 404);

        $dokumen->loadMissing([
            'pemohon',
            'penandatangan',
            'suratKeputusan.dasarHukum',
            'suratKeputusan.skMenimbang',
            'suratKeputusan.skMemutuskan',
            'dokumenFiles',
            'verifikasi.verifikator',
        ]);

        $verifikasi = $this->resolveAssignedVerifikasiForDokumen($request, $dokumen);
        $isReadOnly = $request->query('from') === 'semua' || ! $verifikasi;
        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);

        return view('penandatangan.sk-detail', [
            'verifikasi' => $verifikasi,
            'dokumen' => $dokumen,
            'suratKeputusan' => $dokumen->suratKeputusan,
            'previewPdfUrl' => $pdfFile ? route('penandatangan.sk.preview-pdf', $dokumen) : null,
            'downloadPdfUrl' => $pdfFile && $verifikasi && ! $isReadOnly ? route('penandatangan.sk.unduh-pdf', $dokumen) : null,
            'canProcess' => $verifikasi && ! $isReadOnly && $this->canProcessVerifikasi($verifikasi),
            'isReadOnly' => $isReadOnly,
            'activePage' => $isReadOnly ? 'sk-semua' : 'sk-menunggu',
            'backUrl' => $isReadOnly ? route('penandatangan.sk-semua') : route('penandatangan.sk-menunggu'),
            'defaultDecision' => old('keputusan', $request->query('aksi') === 'tolak' ? 'tolak' : 'setuju'),
        ]);
    }
}
