<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DasarHukum extends Model
{
    use HasFactory;

    protected $table = 'dasar_hukum';

    protected $primaryKey = 'dasar_hukum_id';

    public $timestamps = true;

    protected $fillable = [
        'jenis_peraturan',
        'nomor_peraturan',
        'tahun_peraturan',
        'judul_peraturan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function skDasarHukum(): HasMany
    {
        return $this->hasMany(SkDasarHukum::class, 'dasar_hukum_id', 'dasar_hukum_id');
    }

    public function suratKeputusan(): BelongsToMany
    {
        return $this->belongsToMany(
            SuratKeputusan::class,
            'sk_dasar_hukum',
            'dasar_hukum_id',
            'sk_id',
            'dasar_hukum_id',
            'sk_id'
        )->withPivot(['sk_dasar_hukum_id', 'urutan'])->withTimestamps();
    }
}
