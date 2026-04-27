<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatDokumen extends Model
{
    use HasFactory;

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

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id', 'user_id');
    }
}
