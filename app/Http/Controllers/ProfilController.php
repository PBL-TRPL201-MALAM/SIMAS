<?php

namespace App\Http\Controllers;

use App\Support\UserReferenceOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Controller ini menangani halaman "Profil Saya" untuk Admin Surat, Pemohon, dan Verifikator/Penandatangan.
// Super Admin TIDAK memakai edit()/update() di sini, karena edit profil Super Admin tetap memakai UserController
// (route super-admin.profil & super-admin.users.update). Controller ini hanya menyediakan ganti password untuk Super Admin.
//
// Semua role (kecuali Super Admin) menggunakan satu view tunggal 'profile.index'.
// Perbedaan sidebar, label, dan route prefix ditentukan oleh konfigurasi per-role di bawah.
class ProfilController extends Controller
{
    // Konfigurasi per-role: sidebar view, label tampilan, route prefix, dan subtitle halaman.
    private const ROLE_CONFIG = [
        'ADMIN_SURAT' => [
            'sidebarView'  => 'template.sidebar.admin',
            'roleLabel'    => 'Admin Surat',
            'routePrefix'  => 'admin',
            'pageSubtitle' => 'Informasi akun Admin Surat',
        ],
        'PEMOHON' => [
            'sidebarView'  => 'template.sidebar.pemohon',
            'roleLabel'    => 'Pemohon',
            'routePrefix'  => 'pemohon',
            'pageSubtitle' => 'Informasi akun pemohon.',
        ],
        'VERIFIKATOR' => [
            'sidebarView'  => 'template.sidebar.verifikator',
            'roleLabel'    => 'Verifikator',
            'routePrefix'  => 'verifikator',
            'pageSubtitle' => 'Informasi akun verifikator.',
        ],
        'PENANDATANGAN' => [
            'sidebarView'  => 'template.sidebar.penandatangan',
            'roleLabel'    => 'Penandatangan',
            'routePrefix'  => 'penandatangan',
            'pageSubtitle' => 'Informasi akun penandatangan.',
        ],
    ];

    // Method ini menampilkan halaman profil menggunakan view tunggal 'profile.index'.
    // Data konfigurasi per-role (sidebar, label, dll.) dikirim sebagai variabel view.
    public function edit(Request $request): View
    {
        $user = $request->user();
        $config = $this->resolveConfig($user->role);

        return view('profile.index', array_merge($config, [
            'user' => $user,
            'jabatans' => UserReferenceOptions::jabatans(),
            'signerJabatans' => UserReferenceOptions::signerJabatans(),
            'unitKerjas' => UserReferenceOptions::unitKerjas(),
        ]));
    }

    // Method ini menyimpan perubahan data diri (nama, username, email, unit kerja, NIP/NIK, jabatan).
    // Role dan status aktif sengaja tidak bisa diubah lewat sini karena itu wewenang Super Admin.
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validJabatans = $user->role === 'PENANDATANGAN'
            ? UserReferenceOptions::signerJabatans()
            : UserReferenceOptions::jabatans();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => [
                'required', 'string', 'max:100',
                Rule::unique('users', 'username')->ignore($user->user_id, 'user_id'),
            ],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            'unit_kerja' => ['nullable', Rule::in(UserReferenceOptions::unitKerjas())],
            'nip_nik' => ['nullable', 'string', 'max:50'],
            'jabatan' => ['nullable', Rule::in($validJabatans)],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah dipakai user lain.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah dipakai user lain.',
        ]);

        $user->update($validated);

        return redirect()
            ->route($this->resolveRoute($user->role))
            ->with('status', 'Profil berhasil diperbarui.');
    }

    // Method ini khusus mengganti password; dipakai oleh semua role termasuk Super Admin.
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Password lama diverifikasi dulu sebelum diizinkan mengganti.
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route($this->resolveRoute($user->role))
            ->with('status', 'Password berhasil diperbarui.');
    }

    /**
     * Ambil konfigurasi view per-role; fallback ke PEMOHON jika role tidak dikenali.
     *
     * @return array{sidebarView: string, roleLabel: string, routePrefix: string, pageSubtitle: string}
     */
    private function resolveConfig(string $role): array
    {
        return self::ROLE_CONFIG[$role] ?? self::ROLE_CONFIG['PEMOHON'];
    }

    private function resolveRoute(string $role): string
    {
        return match ($role) {
            'ADMIN_SURAT' => 'admin.profil',
            'PEMOHON' => 'pemohon.profil',
            'VERIFIKATOR' => 'verifikator.profil',
            'PENANDATANGAN' => 'penandatangan.profil',
            'SUPER_ADMIN' => 'super-admin.profil',
            default => 'home',
        };
    }
}
