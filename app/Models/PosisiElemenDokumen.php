<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosisiElemenDokumen extends Model
{
    use HasFactory;

    protected $table = 'posisi_elemen_dokumen';

    protected $primaryKey = 'posisi_id';

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
        return [
            'halaman' => 'integer',
            'posisi_x' => 'decimal:2',
            'posisi_y' => 'decimal:2',
            'lebar' => 'decimal:2',
            'tinggi' => 'decimal:2',
        ];
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }
}
