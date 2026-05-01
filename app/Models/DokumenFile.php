<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenFile extends Model
{
    use HasFactory;

    // Semua versi file pada satu dokumen dicatat di model ini, mulai dari draft pemohon sampai PDF final hasil publish.
    protected $table = 'dokumen_file';

    protected $primaryKey = 'file_id';

    protected $fillable = [
        'dokumen_id',
        'file_type',
        'file_name',
        'file_path',
        'uploaded_by',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            // Waktu upload membantu sistem menentukan file terbaru yang harus ditampilkan atau dijadikan fallback.
            'uploaded_at' => 'datetime',
        ];
    }

    // Setiap file selalu milik satu dokumen tertentu.
    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }

    // Uploader dipakai untuk mengetahui siapa yang menambahkan file: pemohon, admin, atau proses sistem tertentu.
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'user_id');
    }
}
