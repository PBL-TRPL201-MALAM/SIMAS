<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dokumen extends Model
{
    use HasFactory;

    // Tabel dokumen adalah induk utama seluruh alur surat, dari pengajuan awal sampai publish final.
    protected $table = 'dokumen';

    protected $primaryKey = 'dokumen_id';

    protected $fillable = [
        'jenis_dokumen',
        'pemohon_id',
        'status_dokumen',
        'verification_token',
        'published_at',
        'file_final_path',
    ];

    protected function casts(): array
    {
        return [
            // Waktu publish dipisahkan agar mudah membedakan kapan dokumen dibuat dan kapan resmi diterbitkan.
            'published_at' => 'datetime',
        ];
    }

    // Pemohon adalah user yang pertama kali mengajukan dokumen ke sistem.
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id', 'user_id');
    }

    public function dokumenFiles(): HasMany
    {
        return $this->hasMany(DokumenFile::class, 'dokumen_id', 'dokumen_id');
    }

    // Detail surat biasa dipisah ke tabel sendiri agar struktur dokumen tetap fleksibel untuk beberapa jenis surat.
    public function suratBiasa(): HasOne
    {
        return $this->hasOne(SuratBiasa::class, 'dokumen_id', 'dokumen_id');
    }

    public function suratKeputusan(): HasOne
    {
        return $this->hasOne(SuratKeputusan::class, 'dokumen_id', 'dokumen_id');
    }

    public function verifikasi(): HasMany
    {
        return $this->hasMany(Verifikasi::class, 'dokumen_id', 'dokumen_id');
    }

    // Riwayat dokumen dipakai sebagai jejak audit saat dokumen berpindah antar role dan status.
    public function riwayatDokumen(): HasMany
    {
        return $this->hasMany(RiwayatDokumen::class, 'dokumen_id', 'dokumen_id');
    }

    // Posisi elemen menyimpan koordinat nomor, tanggal, dan QR/TTE untuk keperluan generate preview/final PDF.
    public function posisiElemenDokumen(): HasMany
    {
        return $this->hasMany(PosisiElemenDokumen::class, 'dokumen_id', 'dokumen_id');
    }
}
