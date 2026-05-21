<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model PosisiElemenDokumen menyimpan koordinat elemen yang ditempelkan ke PDF.
// Data ini diisi dari UI Admin Surat lalu dipakai service generator untuk menaruh nomor surat, tanggal, dan QR/TTE.
class PosisiElemenDokumen extends Model
{
    use HasFactory;

    // Koordinat di tabel ini menjadi dasar penempelan nomor surat, tanggal, dan QR/TTE ke PDF hasil generate.
    protected $table = 'posisi_elemen_dokumen';

    // Primary key custom dipakai agar Eloquent mengenali posisi_id sebagai identitas koordinat elemen.
    protected $primaryKey = 'posisi_id';

    // Fillable berisi koordinat relatif dari kanvas preview yang aman diisi melalui endpoint storePosisiElemen.
    protected $fillable = [
        'dokumen_id',
        'elemen',
        'halaman',
        'posisi_x',
        'posisi_y',
        'lebar',
        'tinggi',
    ];

    protected function casts(): array
    {
        // Cast menjaga angka koordinat konsisten saat dibaca dari database untuk proses generate PDF.
        return [
            'halaman' => 'integer',
            // Nilai desimal dipakai agar hasil drag posisi elemen tetap presisi saat dipindahkan ke ukuran PDF asli.
            'posisi_x' => 'decimal:2',
            'posisi_y' => 'decimal:2',
            'lebar' => 'decimal:2',
            'tinggi' => 'decimal:2',
        ];
    }

    // Posisi elemen selalu menempel ke satu dokumen yang sedang diproses oleh Admin Surat.
    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }
}
