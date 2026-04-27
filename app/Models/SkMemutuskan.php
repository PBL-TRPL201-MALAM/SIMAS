<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkMemutuskan extends Model
{
    use HasFactory;

    protected $table = 'sk_memutuskan';

    protected $primaryKey = 'sk_memutuskan_id';

    protected $fillable = [
        'sk_id',
        'urutan',
        'isi_memutuskan',
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
}
