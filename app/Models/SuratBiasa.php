<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratBiasa extends Model
{
    use HasFactory;

    protected $table = 'surat_biasa';

    protected $primaryKey = 'surat_biasa_id';

    protected $fillable = [
        'dokumen_id',
        'jenis_surat',
        'nomor_surat',
        'tanggal_surat',
        'penandatangan',
        'hal',
        'ringkasan_isi',
        'sifat_surat',
        'lampiran',
        'kepada_tujuan',
        'tembusan',
        'keterangan_tambahan',
        'catatan_admin',
        'unit_kerja',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
        ];
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }
}
