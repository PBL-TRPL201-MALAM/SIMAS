<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $primaryKey = 'dokumen_id';

    protected $fillable = [
        'jenis_dokumen',
        'pemohon_id',
        'status_dokumen',
        'verification_token',
        'published_at',
        'file_final_path',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id', 'user_id');
    }

    public function dokumenFiles(): HasMany
    {
        return $this->hasMany(DokumenFile::class, 'dokumen_id', 'dokumen_id');
    }

    public function suratBiasa(): HasOne
    {
        return $this->hasOne(SuratBiasa::class, 'dokumen_id', 'dokumen_id');
    }

    public function suratKeputusan(): HasOne
    {
        return $this->hasOne(SuratKeputusan::class, 'dokumen_id', 'dokumen_id');
    }

    public function verifikasi(): HasMany
    {
        return $this->hasMany(Verifikasi::class, 'dokumen_id', 'dokumen_id');
    }

    public function riwayatDokumen(): HasMany
    {
        return $this->hasMany(RiwayatDokumen::class, 'dokumen_id', 'dokumen_id');
    }

    public function posisiElemenDokumen(): HasMany
    {
        return $this->hasMany(PosisiElemenDokumen::class, 'dokumen_id', 'dokumen_id');
    }
}
