<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model DokumenFile mencatat semua file yang berhubungan dengan dokumen.
// Pendekatan ini menjaga riwayat file tetap lengkap: draft PDF pemohon, preview verifikasi, sampai PDF final.
class DokumenFile extends Model
{
    use HasFactory;

    // Semua versi file pada satu dokumen dicatat di model ini, mulai dari draft pemohon sampai PDF final hasil publish.
    protected $table = 'dokumen_file';

    // Primary key custom mengikuti nama kolom file_id pada tabel dokumen_file.
    protected $primaryKey = 'file_id';

    // Fillable menentukan metadata file yang boleh disimpan dari controller/service.
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
        // Cast uploaded_at menjadi datetime agar bisa diurutkan dan diformat seperti objek tanggal Laravel.
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

    // Ekstensi lampiran dibaca dari nama asli file, lalu fallback ke path storage jika nama file tidak tersedia.
    public function lampiranExtension(): string
    {
        return strtolower(pathinfo($this->file_name ?: $this->file_path, PATHINFO_EXTENSION));
    }

    // Lampiran yang boleh dibuka lewat tombol Lihat hanya PDF dan gambar yang bisa dirender langsung oleh browser.
    public function isPreviewableLampiran(): bool
    {
        if ($this->file_type !== 'LAMPIRAN') {
            return false;
        }

        return in_array($this->lampiranExtension(), ['pdf', 'jpg', 'jpeg', 'png'], true);
    }

    // Content-Type inline disesuaikan dengan tipe lampiran agar tab baru menampilkan file, bukan memaksa unduhan.
    public function lampiranPreviewContentType(): ?string
    {
        return match ($this->lampiranExtension()) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => null,
        };
    }
}
