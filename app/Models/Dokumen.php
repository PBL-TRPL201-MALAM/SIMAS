<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Model Dokumen adalah induk utama alur surat di SIMAS.
// Semua jenis surat menyimpan status globalnya di tabel ini, lalu detailnya disambungkan lewat relasi Eloquent.
class Dokumen extends Model
{
    use HasFactory;

    // Tabel dokumen adalah induk utama seluruh alur surat, dari pengajuan awal sampai publish final.
    protected $table = 'dokumen';

    // Primary key custom mengikuti struktur database SIMAS, sehingga Eloquent tidak mencari kolom default id.
    protected $primaryKey = 'dokumen_id';

    // Kolom fillable ini adalah data yang boleh diisi lewat mass assignment saat pengajuan, proses, dan publish.
    protected $fillable = [
        'jenis_dokumen',
        'pemohon_id',
        'penandatangan_id',
        'status_dokumen',
        'verification_token',
        'published_at',
        'file_final_path',
    ];

    protected function casts(): array
    {
        // Cast membantu Laravel otomatis mengubah nilai database menjadi objek tanggal Carbon.
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

    // Penandatangan dipilih Admin Surat sebagai metadata utama dokumen sebelum masuk verifikasi.
    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penandatangan_id', 'user_id');
    }

    // Relasi one-to-many: satu dokumen dapat memiliki banyak versi file sepanjang prosesnya.
    public function dokumenFiles(): HasMany
    {
        return $this->hasMany(DokumenFile::class, 'dokumen_id', 'dokumen_id');
    }

    // Detail surat biasa dipisah ke tabel sendiri agar struktur dokumen tetap fleksibel untuk beberapa jenis surat.
    public function suratBiasa(): HasOne
    {
        return $this->hasOne(SuratBiasa::class, 'dokumen_id', 'dokumen_id');
    }

    // Relasi one-to-one untuk detail Surat Keputusan ketika jenis dokumen adalah SURAT_KEPUTUSAN.
    public function suratKeputusan(): HasOne
    {
        return $this->hasOne(SuratKeputusan::class, 'dokumen_id', 'dokumen_id');
    }

    // Relasi one-to-many: satu dokumen melewati beberapa level verifikasi.
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
