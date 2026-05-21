<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\View\View;

// Controller publik ini menangani halaman validasi dokumen dari QR.
// Route ini tidak memakai auth karena penerima surat perlu bisa memindai QR tanpa login ke SIMAS.
class PublicDokumenVerificationController extends Controller
{
    // Method ini mencari dokumen berdasarkan token dan hanya menganggap valid jika dokumen sudah PUBLISHED.
    public function show(string $token): View
    {
        // Query mengambil dokumen beserta relasi yang dibutuhkan halaman validasi publik.
        $dokumen = Dokumen::query()
            ->with([
                'pemohon',
                'penandatangan',
                'suratBiasa',
                'suratKeputusan',
                'verifikasi.verifikator',
            ])
            ->where('verification_token', $token)
            ->first();

        // Validasi publik berlaku untuk Surat Biasa dan SK selama dokumen sudah resmi PUBLISHED.
        $isValid = $dokumen
            && in_array($dokumen->jenis_dokumen, ['SURAT_BIASA', 'SURAT_KEPUTUSAN'], true)
            && $dokumen->status_dokumen === 'PUBLISHED';

        $verifiedBy = collect();

        if ($isValid) {
            // Nama verifikator diurutkan berdasarkan level agar alur persetujuan terlihat sesuai proses aslinya.
            $verifiedBy = $dokumen->verifikasi
                ->where('status_verifikasi', 'DISETUJUI')
                ->sortBy('level')
                ->map(fn ($verifikasi) => $verifikasi->verifikator?->nama)
                ->filter()
                ->values();
        }

        return view('verifikasi.public', [
            'dokumen' => $isValid ? $dokumen : null,
            'isValid' => (bool) $isValid,
            'verifiedBy' => $verifiedBy,
        ]);
    }
}
