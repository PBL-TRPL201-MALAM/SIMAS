<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('partials.favicon')
    <title>Reset Kata Sandi — SIMAS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      * { font-family: 'Inter', sans-serif; }
    </style>
  </head>

  <body class="bg-white antialiased overflow-hidden h-screen">

    <div class="flex h-screen w-full">

      {{-- ================================================================
           PANEL KIRI — FORM RESET KATA SANDI
      ================================================================ --}}
      <div class="relative flex flex-col justify-center w-full lg:w-1/2 px-8 sm:px-16 xl:px-24 bg-white overflow-hidden">

        <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-blue-50/60 blur-3xl pointer-events-none"></div>
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-sky-50/40 blur-2xl pointer-events-none"></div>

        <div class="relative w-full max-w-sm mx-auto">

          {{-- Logo --}}
          <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-10 group">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SIMAS" class="h-8 w-auto object-contain" />
            <span class="text-base font-bold tracking-tight text-slate-900">SIMAS</span>
          </a>

          {{-- Heading --}}
          <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Reset kata sandi</h1>
            <p class="mt-1.5 text-sm text-slate-400 font-light">Masukkan kata sandi baru untuk akun Anda.</p>
          </div>

          {{-- Alert Error --}}
          @if ($errors->any())
            <div class="flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3 mb-6">
              <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div class="text-xs text-red-600 font-medium">
                @foreach ($errors->all() as $error)
                  <p>{{ $error }}</p>
                @endforeach
              </div>
            </div>
          @endif

          {{-- Form --}}
          <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Hidden token --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="space-y-1.5">
              <label for="email" class="block text-xs font-semibold text-slate-700 tracking-wide">Alamat Email</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <input id="email" name="email" type="email" autocomplete="email"
                  value="{{ old('email', $email) }}" readonly
                  class="w-full rounded-xl border border-slate-200 bg-slate-100/70 pl-10 pr-4 py-3 text-sm text-slate-600 font-light outline-none cursor-not-allowed" />
              </div>
            </div>

            {{-- Kata Sandi Baru --}}
            <div class="space-y-1.5">
              <label for="reset-password" class="block text-xs font-semibold text-slate-700 tracking-wide">Kata Sandi Baru</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input id="reset-password" name="password" type="password" autocomplete="new-password"
                  placeholder="Minimal 8 karakter"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-11 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 hover:border-slate-300" />
                <button id="reset-toggle-password" type="button"
                  class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-blue-500 transition-colors duration-200"
                  aria-label="Tampilkan kata sandi">
                  <svg id="reset-icon-eye" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg id="reset-icon-eye-off" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>
            </div>

            {{-- Konfirmasi Kata Sandi --}}
            <div class="space-y-1.5">
              <label for="reset-password-confirm" class="block text-xs font-semibold text-slate-700 tracking-wide">Konfirmasi Kata Sandi Baru</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <input id="reset-password-confirm" name="password_confirmation" type="password" autocomplete="new-password"
                  placeholder="Ulangi kata sandi baru"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-11 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 hover:border-slate-300" />
                <button id="reset-toggle-password-confirm" type="button"
                  class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-blue-500 transition-colors duration-200"
                  aria-label="Tampilkan konfirmasi kata sandi">
                  <svg id="reset-confirm-icon-eye" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg id="reset-confirm-icon-eye-off" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
              class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3.5 text-sm font-semibold text-white shadow-md shadow-blue-200/60 hover:shadow-blue-300/70 hover:from-blue-700 hover:to-blue-600 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none transition-all duration-300 mt-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              Reset Kata Sandi
            </button>

          </form>

          {{-- Link kembali ke Login --}}
          <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Kembali ke halaman masuk
            </a>
          </div>

          <p class="mt-8 text-center text-[11px] text-slate-300 font-light">&copy; 2026 SIMAS · PBL-TRPL201-MALAM</p>

        </div>
      </div>


      {{-- ================================================================
           PANEL KANAN — BRANDING
      ================================================================ --}}
      <div class="hidden lg:flex relative w-1/2 flex-col justify-center items-center overflow-hidden">

        {{-- Background --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-500 to-sky-400"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff07_1px,transparent_1px),linear-gradient(to_bottom,#ffffff07_1px,transparent_1px)] bg-[size:40px_40px]"></div>
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-blue-800/20 blur-3xl pointer-events-none"></div>

        <div class="relative text-center px-16">
          {{-- Icon --}}
          <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl bg-white/10 backdrop-blur-sm ring-1 ring-white/20 shadow-2xl shadow-blue-900/30">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
            </svg>
          </div>

          <h2 class="text-3xl font-extrabold text-white leading-tight tracking-tight">
            Kata Sandi Baru,<br />
            <span class="text-sky-200">Akses Baru</span>
          </h2>
          <p class="mt-4 text-sm text-white/60 font-light leading-relaxed max-w-xs mx-auto">
            Buat kata sandi baru yang kuat dan mudah Anda ingat. Disarankan menggunakan kombinasi huruf, angka, dan simbol.
          </p>

          {{-- Tips --}}
          <div class="mt-10 flex flex-col gap-3 text-left max-w-xs mx-auto">
            <div class="flex items-center gap-3 rounded-xl bg-white/10 backdrop-blur-sm px-4 py-3 ring-1 ring-white/10">
              <svg class="w-5 h-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <p class="text-xs text-white/80 font-light">Minimal 8 karakter</p>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-white/10 backdrop-blur-sm px-4 py-3 ring-1 ring-white/10">
              <svg class="w-5 h-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <p class="text-xs text-white/80 font-light">Kombinasi huruf besar & kecil</p>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-white/10 backdrop-blur-sm px-4 py-3 ring-1 ring-white/10">
              <svg class="w-5 h-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <p class="text-xs text-white/80 font-light">Sertakan angka dan simbol</p>
            </div>
          </div>
        </div>

      </div>

    </div>

    {{-- Toggle Password Visibility Script --}}
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Helper: setup toggle visibility untuk satu pasang tombol + input
        function setupToggle(btnId, inputId, eyeId, eyeOffId) {
          var btn = document.getElementById(btnId);
          var input = document.getElementById(inputId);
          var eye = document.getElementById(eyeId);
          var eyeOff = document.getElementById(eyeOffId);

          if (!btn || !input || !eye || !eyeOff) return;

          btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            // Saat berubah ke text (password terlihat): sembunyikan eye, tampilkan eye-off
            // Saat berubah ke password (tersembunyi): tampilkan eye, sembunyikan eye-off
            if (isPassword) {
              eye.classList.add('hidden');
              eyeOff.classList.remove('hidden');
            } else {
              eye.classList.remove('hidden');
              eyeOff.classList.add('hidden');
            }

            btn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
          });
        }

        // Kata Sandi Baru
        setupToggle('reset-toggle-password', 'reset-password', 'reset-icon-eye', 'reset-icon-eye-off');

        // Konfirmasi Kata Sandi Baru
        setupToggle('reset-toggle-password-confirm', 'reset-password-confirm', 'reset-confirm-icon-eye', 'reset-confirm-icon-eye-off');
      });
    </script>

  </body>
</html>
