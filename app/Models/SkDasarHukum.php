<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkDasarHukum extends Model
{
    use HasFactory;

    protected $table = 'sk_dasar_hukum';

    protected $primaryKey = 'sk_dasar_hukum_id';

    protected $fillable = [
        'sk_id',
        'dasar_hukum_id',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function suratKeputusan(): BelongsTo
    {
        return $this->belongsTo(SuratKeputusan::class, 'sk_id', 'sk_id');
    }

    public function dasarHukum(): BelongsTo
    {
        return $this->belongsTo(DasarHukum::class, 'dasar_hukum_id', 'dasar_hukum_id');
    }
}
