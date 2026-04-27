<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

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

        return $next($request);
    }

    private function dashboardRoute(object $user): string
    {
        return match ($user->role) {
            'SUPER_ADMIN' => 'super-admin.dashboard',
            'ADMIN_TU' => 'admin.dashboard',
            'PEMOHON' => 'pemohon.dashboard',
            'VERIFIKATOR' => 'verifikator.dashboard',
            default => 'home',
        };
    }
}
