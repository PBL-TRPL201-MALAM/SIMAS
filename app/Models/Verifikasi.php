<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verifikasi extends Model
{
    use HasFactory;

    protected $table = 'verifikasi';

    protected $primaryKey = 'verifikasi_id';

    protected $fillable = [
        'dokumen_id',
        'verifikator_id',
        'level',
        'status_verifikasi',
        'catatan',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id', 'user_id');
    }
}
