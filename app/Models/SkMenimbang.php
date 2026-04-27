<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkMenimbang extends Model
{
    use HasFactory;

    protected $table = 'sk_menimbang';

    protected $primaryKey = 'sk_menimbang_id';

    protected $fillable = [
        'sk_id',
        'urutan',
        'isi_menimbang',
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
