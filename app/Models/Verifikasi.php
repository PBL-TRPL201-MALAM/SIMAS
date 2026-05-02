<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model Verifikasi menyimpan setiap tahap approval dokumen.
// Satu dokumen dapat memiliki beberapa baris verifikasi, dan kolom level menentukan urutan pemeriksa sampai penandatangan final.
class Verifikasi extends Model
{
    use HasFactory;

    // Satu baris verifikasi mewakili satu tahap approval pada dokumen, termasuk tahap penandatangan final.
    protected $table = 'verifikasi';

    protected $primaryKey = 'verifikasi_id';

    // Fillable ini dipakai saat Admin/TU membangun ulang flow verifikasi bertingkat.
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
        // Cast memastikan level menjadi integer dan waktu verifikasi menjadi datetime saat dipakai di PHP.
        return [
            // Level menentukan urutan proses verifikasi bertingkat yang harus dilalui dokumen.
            'level' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    // Dokumen yang sedang diperiksa pada tahap verifikasi ini.
    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }

    // Verifikator bisa berupa pemeriksa biasa atau penandatangan akhir, tergantung posisi level pada flow dokumen.
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id', 'user_id');
    }
}
