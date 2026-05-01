<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatDokumen extends Model
{
    use HasFactory;

    // Model ini dipakai sebagai jejak audit untuk menjelaskan perubahan status dokumen sepanjang alurnya.
    protected $table = 'riwayat_dokumen';

    protected $primaryKey = 'riwayat_id';

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
