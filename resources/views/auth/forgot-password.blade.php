<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('partials.favicon')
    <title>Lupa Kata Sandi — SIMAS</title>

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
           PANEL KIRI — FORM LUPA KATA SANDI
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
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Lupa kata sandi?</h1>
            <p class="mt-1.5 text-sm text-slate-400 font-light">Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi.</p>
          </div>

          {{-- Alert Sukses --}}
          @if (session('status'))
            <div class="flex items-start gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 mb-6 animate-fade-in">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <p class="text-xs text-emerald-600 font-medium">{{ session('status') }}</p>
            </div>
          @endif

          {{-- Alert Error --}}
          @if ($errors->any())
            <div class="flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3 mb-6 animate-fade-in">
              <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <p class="text-xs text-red-600 font-medium">{{ $errors->first() }}</p>
            </div>
          @endif

          {{-- Form --}}
          <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf

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
                  value="{{ old('email') }}" placeholder="Masukkan alamat email terdaftar"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 hover:border-slate-300" />
              </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
              class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3.5 text-sm font-semibold text-white shadow-md shadow-blue-200/60 hover:shadow-blue-300/70 hover:from-blue-700 hover:to-blue-600 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none transition-all duration-300 mt-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Kirim Tautan Reset
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
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
          </div>

          <h2 class="text-3xl font-extrabold text-white leading-tight tracking-tight">
            Pulihkan<br />
            <span class="text-sky-200">Akses Anda</span>
          </h2>
          <p class="mt-4 text-sm text-white/60 font-light leading-relaxed max-w-xs mx-auto">
            Kami akan mengirimkan tautan reset ke email yang terdaftar di sistem SIMAS. Periksa kotak masuk dan folder spam Anda.
          </p>

          {{-- Steps --}}
          <div class="mt-10 flex flex-col gap-4 text-left max-w-xs mx-auto">
            <div class="flex items-start gap-3">
              <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/15 text-xs font-bold text-white ring-1 ring-white/20">1</div>
              <div>
                <p class="text-sm font-semibold text-white">Masukkan Email</p>
                <p class="text-xs text-white/50 font-light">Email yang terdaftar di akun SIMAS Anda.</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/15 text-xs font-bold text-white ring-1 ring-white/20">2</div>
              <div>
                <p class="text-sm font-semibold text-white">Cek Kotak Masuk</p>
                <p class="text-xs text-white/50 font-light">Klik tautan reset yang kami kirimkan.</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/15 text-xs font-bold text-white ring-1 ring-white/20">3</div>
              <div>
                <p class="text-sm font-semibold text-white">Buat Kata Sandi Baru</p>
                <p class="text-xs text-white/50 font-light">Masukkan dan konfirmasi kata sandi baru Anda.</p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </body>
</html>
