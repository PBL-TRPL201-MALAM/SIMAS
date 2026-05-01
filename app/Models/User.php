<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Model ini mewakili semua akun yang terlibat di SIMAS, baik pemohon, admin, verifikator, maupun super admin.
    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'role',
        'unit_kerja',
        'nip_nik',
        'jabatan',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            // Hash password dijaga di layer model agar tetap aman meskipun ada create/update lewat Eloquent.
            'password' => 'hashed',
        ];
    }

    // Relasi ini dipakai untuk menelusuri seluruh dokumen yang diajukan oleh seorang pemohon.
    public function dokumenDiajukan(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'pemohon_id', 'user_id');
    }

    // Semua file yang pernah diunggah user, mulai dari draft pemohon sampai PDF final, bisa ditelusuri dari relasi ini.
    public function dokumenFilesDiunggah(): HasMany
    {
        return $this->hasMany(DokumenFile::class, 'uploaded_by', 'user_id');
    }

    // User dengan role verifikator atau penandatangan final akan muncul sebagai penanggung jawab pada tabel verifikasi.
    public function verifikasiDitangani(): HasMany
    {
        return $this->hasMany(Verifikasi::class, 'verifikator_id', 'user_id');
    }

    // Riwayat aksi membantu menjawab siapa yang terakhir mengubah status atau memproses dokumen.
    public function riwayatAksi(): HasMany
    {
        return $this->hasMany(RiwayatDokumen::class, 'actor_id', 'user_id');
    }
}
