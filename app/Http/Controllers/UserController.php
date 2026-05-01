<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\UserReferenceOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $usersQuery = User::query()->orderByDesc('user_id');

        if ($status === 'active') {
            $usersQuery->where('is_active', true);
        }

        if ($status === 'inactive') {
            $usersQuery->where('is_active', false);
        }

        $roleStats = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $topRole = $roleStats->sortDesc()->keys()->first();

        return view('super-admin.semua-user', [
            'users' => $usersQuery->get(),
            'activeCount' => User::query()->where('is_active', true)->count(),
            'inactiveCount' => User::query()->where('is_active', false)->count(),
            'topRole' => $topRole,
            'currentStatus' => $status,
        ]);
    }

    public function create(): View
    {
        return view('super-admin.tambah-user', [
            'roles' => $this->roles(),
            'jabatans' => $this->jabatans(),
            'unitKerjas' => $this->unitKerjas(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

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

        return redirect()
            ->route('super-admin.users.index')
            ->with('status', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('super-admin.profil', [
            'user' => $user,
            'roles' => $this->roles(),
            'jabatans' => $this->jabatans(),
            'unitKerjas' => $this->unitKerjas(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate($this->rules($user));

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

        return redirect()
            ->route('super-admin.users.edit', $user)
            ->with('status', 'Data user berhasil diperbarui.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if (auth()->id() === $user->user_id && $user->is_active) {
            return redirect()
                ->route('super-admin.users.index')
                ->withErrors(['status' => 'Super Admin yang sedang login tidak bisa menonaktifkan akun sendiri.']);
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
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $rules;
    }

    /**
     * @return array<int, string>
     */
    private function roles(): array
    {
        return UserReferenceOptions::roles();
    }

    /**
     * @return array<int, string>
     */
    private function jabatans(): array
    {
        return UserReferenceOptions::jabatans();
    }

    /**
     * @return array<int, string>
     */
    private function unitKerjas(): array
    {
        return UserReferenceOptions::unitKerjas();
    }
}
