<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model RiwayatDokumen adalah audit trail untuk perubahan status atau aksi penting pada dokumen.
// Jika di CodeIgniter biasanya ditulis manual lewat query log, di Laravel ini dibungkus sebagai model Eloquent.
class RiwayatDokumen extends Model
{
    use HasFactory;

    // Model ini dipakai sebagai jejak audit untuk menjelaskan perubahan status dokumen sepanjang alurnya.
    protected $table = 'riwayat_dokumen';

    // Primary key custom sesuai struktur tabel riwayat_dokumen di database SIMAS.
    protected $primaryKey = 'riwayat_id';

    // Fillable ini berisi informasi aksi, perubahan status, catatan, dan user yang menjalankan aksi.
    protected $fillable = [
        'dokumen_id',
        'aksi',
        'status_lama',
        'status_baru',
        'catatan',
        'actor_id',
    ];

    // Dokumen yang dicatat pada riwayat ini.
    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }

    // Actor menunjukkan user yang melakukan aksi pada momen perubahan tersebut.
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id', 'user_id');
    }
}
