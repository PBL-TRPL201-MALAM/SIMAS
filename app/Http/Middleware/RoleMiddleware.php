<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// Middleware ini mengecek apakah user yang sudah login memiliki role yang diizinkan oleh route.
// Di Laravel, middleware berjalan sebelum controller; cocok untuk menahan akses halaman berdasarkan auth/role.
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    // $roles berasal dari parameter route seperti role:ADMIN_TU atau role:SUPER_ADMIN.
    public function handle(Request $request, Closure $next, string ...$roles): Response|RedirectResponse
    {
        $user = Auth::user();

        // Middleware ini menjadi pagar utama agar route per role tidak bisa diakses user yang belum login.
        if (! $user) {
            return redirect()->route('login');
        }

        // Akun nonaktif dipaksa logout agar user yang statusnya dicabut tidak bisa tetap memakai sesi lama.
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda tidak aktif.',
            ]);
        }

        // Jika role tidak sesuai, user dikembalikan ke dashboard miliknya, bukan dibiarkan masuk ke area lain.
        if (! in_array($user->role, $roles, true)) {
            return redirect()->route($this->dashboardRoute($user));
        }

        // Jika semua pengecekan lolos, request diteruskan ke controller/closure route berikutnya.
        return $next($request);
    }

    // Helper ini menentukan dashboard tujuan ketika user mencoba membuka area role lain.
    private function dashboardRoute(object $user): string
    {
        // Redirect fallback selalu dikembalikan ke dashboard role masing-masing supaya user tidak nyasar ke area lain.
        return match ($user->role) {
            'SUPER_ADMIN' => 'super-admin.dashboard',
            'ADMIN_TU' => 'admin.dashboard',
            'PEMOHON' => 'pemohon.dashboard',
            'VERIFIKATOR' => 'verifikator.dashboard',
            default => 'home',
        };
    }
}
