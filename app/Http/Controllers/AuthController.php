<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        // User yang sudah login tidak perlu melihat form lagi dan langsung diarahkan ke dashboard sesuai peran.
        if (Auth::check()) {
            return redirect()->route($this->dashboardRoute(Auth::user()));
        }

        return view('Login');
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ], [
            'login.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Login SIMAS menerima email atau username sebagai identitas akun.
        $user = User::query()
            ->where('email', $credentials['login'])
            ->orWhere('username', $credentials['login'])
            ->first();

        // Akun nonaktif tetap ditolak walaupun password benar.
        if (! $user || ! $user->is_active || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Email/username atau password tidak valid, atau akun Anda tidak aktif.',
            ]);
        }

        // Redirect akhir selalu mengikuti role user agar 1 form login bisa dipakai semua aktor.
        Auth::login($user, (bool) ($credentials['remember'] ?? false));
        $request->session()->regenerate();

        return redirect()->intended(route($this->dashboardRoute($user)));
    }

    public function logout(Request $request): RedirectResponse
    {
        // Logout membersihkan sesi aktif agar user benar-benar keluar dari area SIMAS dan tidak mewariskan token lama.
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda berhasil logout.');
    }

    private function dashboardRoute(User $user): string
    {
        // Satu pintu redirect dipakai ulang agar mapping role ke dashboard tetap konsisten di seluruh alur auth.
        return match ($user->role) {
            'SUPER_ADMIN' => 'super-admin.dashboard',
            'ADMIN_TU' => 'admin.dashboard',
            'PEMOHON' => 'pemohon.dashboard',
            'VERIFIKATOR' => 'verifikator.dashboard',
            default => 'home',
        };
    }
}
