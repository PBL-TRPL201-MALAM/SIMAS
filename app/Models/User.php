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
            'password' => 'hashed',
        ];
    }

    public function dokumenDiajukan(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'pemohon_id', 'user_id');
    }

    public function dokumenFilesDiunggah(): HasMany
    {
        return $this->hasMany(DokumenFile::class, 'uploaded_by', 'user_id');
    }

    public function verifikasiDitangani(): HasMany
    {
        return $this->hasMany(Verifikasi::class, 'verifikator_id', 'user_id');
    }

    public function riwayatAksi(): HasMany
    {
        return $this->hasMany(RiwayatDokumen::class, 'actor_id', 'user_id');
    }
}
