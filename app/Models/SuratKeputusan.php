<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratKeputusan extends Model
{
    use HasFactory;

    protected $table = 'surat_keputusan';

    protected $primaryKey = 'sk_id';

    protected $fillable = [
        'dokumen_id',
        'nomor_sk',
        'tanggal_sk',
        'judul_sk',
        'tentang',
        'tempat_penetapan',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
        ];
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }

    public function skDasarHukum(): HasMany
    {
        return $this->hasMany(SkDasarHukum::class, 'sk_id', 'sk_id');
    }

    public function skMenimbang(): HasMany
    {
        return $this->hasMany(SkMenimbang::class, 'sk_id', 'sk_id');
    }

    public function skMemutuskan(): HasMany
    {
        return $this->hasMany(SkMemutuskan::class, 'sk_id', 'sk_id');
    }

    public function dasarHukum(): BelongsToMany
    {
        return $this->belongsToMany(
            DasarHukum::class,
            'sk_dasar_hukum',
            'sk_id',
            'dasar_hukum_id',
            'sk_id',
            'dasar_hukum_id'
        )->withPivot(['sk_dasar_hukum_id', 'urutan'])->withTimestamps();
    }
}
