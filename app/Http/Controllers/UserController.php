<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\UserReferenceOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Controller ini menangani CRUD user untuk Super Admin.
// Di Laravel, route model binding otomatis mengisi parameter User $user dari ID di URL.
class UserController extends Controller
{
    // Method ini menyiapkan halaman manajemen user untuk Super Admin, termasuk filter status aktif/nonaktif.
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        // Query daftar user dibuat sebagai builder dulu supaya filter status bisa ditambahkan bertahap.
        $usersQuery = User::query()->orderByDesc('user_id');

        if ($status === 'active') {
            $usersQuery->where('is_active', true);
        }

        if ($status === 'inactive') {
            $usersQuery->where('is_active', false);
        }

        // Query agregasi ini menghitung jumlah user per role untuk ringkasan dashboard/list user.
        $roleStats = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $topRole = $roleStats->sortDesc()->keys()->first();

        return view('super-admin.semua-user', [
            'users' => $usersQuery->get(),
            'activeCount' => User::query()->where('is_active', true)->count(),
            'inactiveCount' => User::query()->where('is_active', false)->count(),
            // Role terbanyak dipakai sebagai ringkasan cepat komposisi akun di sistem.
            'topRole' => $topRole,
            'currentStatus' => $status,
        ]);
    }

    // Form tambah user memakai daftar referensi agar role, jabatan, dan unit kerja tetap konsisten di seluruh sistem.
    public function create(): View
    {
        return view('super-admin.tambah-user', [
            'roles' => $this->roles(),
            'jabatans' => $this->jabatans(),
            'unitKerjas' => $this->unitKerjas(),
        ]);
    }

    // Method ini menerima request form tambah user dan menyimpan akun baru ke tabel users.
    public function store(Request $request): RedirectResponse
    {
        // Validasi request melindungi tabel users dari email/username duplikat dan pilihan role yang tidak dikenal.
        $validated = $request->validate($this->rules());

        // Password selalu di-hash saat pembuatan akun agar tidak pernah tersimpan dalam bentuk plain text.
        User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'unit_kerja' => $validated['unit_kerja'] ?? null,
            'nip_nik' => $validated['nip_nik'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        // Setelah user dibuat, Super Admin dikembalikan ke daftar user agar langsung melihat hasil tambah data.
        return redirect()
            ->route('super-admin.users.index')
            ->with('status', 'User berhasil ditambahkan.');
    }

    // Method ini menampilkan form edit untuk user yang dipilih melalui route model binding.
    public function edit(User $user): View
    {
        // Halaman edit mengambil referensi dropdown yang sama dengan form tambah agar perubahan data tetap seragam.
        return view('super-admin.profil', [
            'user' => $user,
            'roles' => $this->roles(),
            'jabatans' => $this->jabatans(),
            'unitKerjas' => $this->unitKerjas(),
        ]);
    }

    // Method ini memproses perubahan data user tanpa mengubah password.
    public function update(Request $request, User $user): RedirectResponse
    {
        // Rule unique memakai ignore($userId) supaya user bisa menyimpan email/username miliknya sendiri.
        $validated = $request->validate($this->rules($user));

        if ($this->isSelfDeactivationAttempt($user, $validated)) {
            // Redirect balik ke form edit memberi pesan error tanpa membuang input yang sudah diketik.
            return redirect()
                ->route('super-admin.users.edit', $user)
                ->withErrors(['status' => 'Super Admin yang sedang login tidak bisa menonaktifkan akun sendiri.'])
                ->withInput();
        }

        if (! $this->canKeepAtLeastOneActiveSuperAdmin($user, $validated)) {
            // Validasi bisnis ini menjaga sistem tetap memiliki minimal satu akun pengelola aktif.
            return redirect()
                ->route('super-admin.users.edit', $user)
                ->withErrors(['status' => 'Minimal harus ada 1 user SUPER_ADMIN yang tetap aktif di sistem.'])
                ->withInput();
        }

        // Update profil user sengaja dipisahkan dari reset password agar perubahan akses lebih terkontrol.
        $user->update([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'unit_kerja' => $validated['unit_kerja'] ?? null,
            'nip_nik' => $validated['nip_nik'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        // Redirect ke halaman edit yang sama agar Super Admin tetap berada pada konteks user yang baru diperbarui.
        return redirect()
            ->route('super-admin.users.edit', $user)
            ->with('status', 'Data user berhasil diperbarui.');
    }

    // Method ini mengaktifkan atau menonaktifkan user tanpa membuka form edit penuh.
    public function toggleStatus(User $user): RedirectResponse
    {
        // Super Admin tidak boleh menonaktifkan dirinya sendiri agar tidak kehilangan akses pengelolaan sistem.
        if (auth()->id() === $user->user_id && $user->is_active) {
            return redirect()
                ->route('super-admin.users.index')
                ->withErrors(['status' => 'Super Admin yang sedang login tidak bisa menonaktifkan akun sendiri.']);
        }

        if ($user->role === 'SUPER_ADMIN' && $user->is_active && ! $this->hasAnotherActiveSuperAdmin($user)) {
            return redirect()
                ->route('super-admin.users.index')
                ->withErrors(['status' => 'Minimal harus ada 1 user SUPER_ADMIN yang tetap aktif di sistem.']);
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return redirect()
            ->route('super-admin.users.index')
            ->with('status', 'Status user berhasil diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?User $user = null): array
    {
        $userId = $user?->user_id;

        // Semua validasi pilihan mengacu ke helper referensi agar UI, validasi, dan database selalu sinkron.
        $rules = [
            'nama' => ['required', 'string', 'max:150'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($userId, 'user_id'),
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId, 'user_id'),
            ],
            'role' => ['required', Rule::in($this->roles())],
            'unit_kerja' => ['nullable', Rule::in($this->unitKerjas())],
            'nip_nik' => ['nullable', 'string', 'max:50'],
            'jabatan' => ['nullable', Rule::in($this->jabatans())],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($user === null) {
            // Password hanya wajib saat akun baru dibuat.
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $rules;
    }

    /**
     * @return array<int, string>
     */
    private function roles(): array
    {
        // Helper reference dipakai agar daftar role di controller sama dengan pilihan di UI.
        return UserReferenceOptions::roles();
    }

    /**
     * @return array<int, string>
     */
    private function jabatans(): array
    {
        // Jabatan diambil dari satu sumber agar validasi dan dropdown tidak berbeda.
        return UserReferenceOptions::jabatans();
    }

    /**
     * @return array<int, string>
     */
    private function unitKerjas(): array
    {
        // Unit kerja juga dipusatkan agar perubahan referensi cukup dilakukan di helper.
        return UserReferenceOptions::unitKerjas();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function isSelfDeactivationAttempt(User $user, array $validated): bool
    {
        // Cek ini mencegah Super Admin mengunci dirinya sendiri dari aplikasi.
        return auth()->id() === $user->user_id
            && $user->is_active
            && ! (bool) ($validated['is_active'] ?? true);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function canKeepAtLeastOneActiveSuperAdmin(User $user, array $validated): bool
    {
        // Method ini mengecek apakah perubahan role/status masih menyisakan minimal satu SUPER_ADMIN aktif.
        $currentIsActiveSuperAdmin = $user->role === 'SUPER_ADMIN' && $user->is_active;
        $willRemainActiveSuperAdmin = ($validated['role'] ?? $user->role) === 'SUPER_ADMIN'
            && (bool) ($validated['is_active'] ?? $user->is_active);

        if (! $currentIsActiveSuperAdmin || $willRemainActiveSuperAdmin) {
            return true;
        }

        return $this->hasAnotherActiveSuperAdmin($user);
    }

    private function hasAnotherActiveSuperAdmin(User $user): bool
    {
        // Query exists lebih ringan daripada get/count karena cukup mencari apakah ada satu admin aktif lain.
        return User::query()
            ->where('role', 'SUPER_ADMIN')
            ->where('is_active', true)
            ->where('user_id', '!=', $user->user_id)
            ->exists();
    }
}
