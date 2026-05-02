<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model SuratBiasa menyimpan metadata dan isi ringkas khusus untuk dokumen jenis SURAT_BIASA.
// Dokumen induk tetap ada di model Dokumen, sedangkan nomor, hal, sifat, dan penandatangan ada di model ini.
class SuratBiasa extends Model
{
    use HasFactory;

    // Tabel ini menyimpan detail isi surat biasa yang melengkapi dokumen induk pada sistem.
    protected $table = 'surat_biasa';

    protected $primaryKey = 'surat_biasa_id';

    // Fillable ini membuat controller bisa mengisi metadata surat secara mass assignment dengan aman.
    protected $fillable = [
        'dokumen_id',
        'jenis_surat',
        'nomor_surat',
        'tanggal_surat',
        'penandatangan',
        'hal',
        'ringkasan_isi',
        'sifat_surat',
        'lampiran',
        'kepada_tujuan',
        'tembusan',
        'keterangan_tambahan',
        'catatan_admin',
        'unit_kerja',
        'catatan',
    ];

    protected function casts(): array
    {
        // Cast tanggal_surat membuat format tanggal lebih mudah dikelola saat ditampilkan di view atau PDF.
        return [
            // Tanggal surat tetap disimpan sebagai date agar mudah diformat ulang saat render PDF.
            'tanggal_surat' => 'date',
        ];
    }

    // Setiap surat biasa selalu terhubung ke satu dokumen utama.
    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }
}
