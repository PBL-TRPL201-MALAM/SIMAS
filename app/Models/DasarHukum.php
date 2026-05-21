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
        'judul_hukum',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Label ini dipakai di bagian Mengingat SK agar formatnya seragam di form, review admin, dan verifikator.
    public function labelMengingat(): string
    {
        return trim(collect([$this->judul_hukum, $this->keterangan])->filter()->implode(' '));
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
