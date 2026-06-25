<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Controller ini menangani alur Lupa Kata Sandi (Forgot Password) lengkap:
// menampilkan form email, mengirim link reset, menampilkan form reset, dan memproses pembaruan password.
class PasswordResetController extends Controller
{
    // Menampilkan form input email untuk meminta link reset password.
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    // Memproses pengiriman email berisi link token reset password menggunakan Password Broker bawaan Laravel.
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        // Password::sendResetLink akan membuat token, menyimpannya di tabel password_reset_tokens,
        // lalu mengirim notifikasi ResetPassword ke email user yang ditemukan.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Jika berhasil, redirect kembali ke halaman forgot-password dengan pesan sukses.
        // Jika gagal (email tidak ditemukan, throttle, dll), kembalikan pesan error ke input email.
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)])->withInput();
    }

    // Menampilkan form reset password dengan token dari URL dan email dari query string.
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    // Memproses pembaruan password baru menggunakan Password Broker Laravel.
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Password::reset memvalidasi token + email, lalu memanggil callback untuk meng-update password user.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        // Jika sukses, redirect ke halaman login dengan pesan bahwa password sudah direset.
        // Jika gagal (token expired, email salah, dll), kembalikan error ke form reset.
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Kata sandi berhasil direset! Silakan masuk dengan kata sandi baru Anda.')
            : back()->withErrors(['email' => __($status)])->withInput();
    }
}
