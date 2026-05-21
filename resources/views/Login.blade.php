<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Masuk — SIMAS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      * { font-family: 'Inter', sans-serif; }

      /* ── Carousel Login (panel kanan) ── */
      .login-carousel-track {
        display: flex;
        width: 100%;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: transform;
      }
      .login-carousel-slide {
        min-width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
        overflow: hidden;
      }

      /* Dot aktif */
      .login-dot.active {
        background-color: rgba(255, 255, 255, 0.95);
        width: 20px;
        border-radius: 4px;
      }

      /* Caption fade */
      .login-caption {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.4s ease 0.25s, transform 0.4s ease 0.25s;
      }
      .login-caption.visible {
        opacity: 1;
        transform: translateY(0);
      }
    </style>
  </head>

  <body class="bg-white antialiased overflow-hidden h-screen">

    <div class="flex h-screen w-full">
      <!-- View Auth ini berdiri sendiri, tidak memakai layout dashboard karena user belum login dan belum punya role. -->

      <!-- ================================================================
           PANEL KIRI — FORM LOGIN
      ================================================================ -->
      <div class="relative flex flex-col justify-center w-full lg:w-1/2 px-8 sm:px-16 xl:px-24 bg-white overflow-hidden">

        <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-blue-50/60 blur-3xl pointer-events-none"></div>
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-sky-50/40 blur-2xl pointer-events-none"></div>

        <div class="relative w-full max-w-sm mx-auto">

          <!-- Logo -->
          <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-10 group">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SIMAS" class="h-8 w-auto object-contain" />
            <span class="text-base font-bold tracking-tight text-slate-900">SIMAS</span>
          </a>

          <!-- Heading -->
          <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat datang kembali!</h1>
            <p class="mt-1.5 text-sm text-slate-400 font-light">Masuk untuk mengakses sistem administrasi Polibatam.</p>
          </div>

          <!-- Alert Error -->
          <!-- session('status') dikirim lewat redirect, misalnya setelah logout berhasil. -->
          @if (session('status'))
            <div class="flex items-start gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 mb-6">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <p class="text-xs text-emerald-600 font-medium">{{ session('status') }}</p>
            </div>
          @endif

          <!-- $errors otomatis tersedia setelah validasi login gagal di AuthController. -->
          @if ($errors->any())
            <div class="flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3 mb-6">
              <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <p class="text-xs text-red-600 font-medium">{{ $errors->first() }}</p>
            </div>
          @endif

          <!-- Form Login -->
          <!-- Form ini mengirim data login ke route login.attempt menggunakan method POST. -->
          <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
            <!-- csrf wajib di Laravel untuk melindungi form dari serangan CSRF. -->
            @csrf

            <!-- Email / Username -->
            <!-- Input login menerima email atau username; old('login') mengisi ulang nilai jika validasi gagal. -->
            <div class="space-y-1.5">
              <label for="login" class="block text-xs font-semibold text-slate-700 tracking-wide">Email atau Username</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input id="login" name="login" type="text" autocomplete="username"
                  value="{{ old('login') }}" placeholder="Masukkan email atau username"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 hover:border-slate-300" />
              </div>
              <p id="login-error" class="hidden text-[11px] text-red-500 font-medium mt-1"></p>
            </div>

            <!-- Password -->
            <!-- Input password dikirim ke controller dan dibandingkan dengan hash password user. -->
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-semibold text-slate-700 tracking-wide">Kata Sandi</label>
                <a href="#" class="text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lupa kata sandi?</a>
              </div>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input id="password" name="password" type="password" autocomplete="current-password"
                  placeholder="Masukkan kata sandi"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-11 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 hover:border-slate-300" />
                <button id="toggle-password" type="button"
                  class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-blue-500 transition-colors duration-200"
                  aria-label="Tampilkan kata sandi">
                  <svg id="icon-eye" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg id="icon-eye-off" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>
              <p id="password-error" class="hidden text-[11px] text-red-500 font-medium mt-1"></p>
            </div>

            <div class="flex items-center justify-between">
              <!-- Remember me mengirim nilai remember agar session login bisa dibuat lebih lama jika dipilih. -->
              <label for="remember" class="inline-flex items-center gap-2 text-xs text-slate-500 font-medium">
                <input id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}
                  class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                <span>Ingat saya</span>
              </label>
            </div>

            <!-- Submit -->
            <button id="submit-btn" type="submit"
              class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3.5 text-sm font-semibold text-white shadow-md shadow-blue-200/60 hover:shadow-blue-300/70 hover:from-blue-700 hover:to-blue-600 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none transition-all duration-300 mt-2">
              <span id="btn-text">Masuk ke SIMAS</span>
              <svg id="btn-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
            </button>

          </form>

          <!-- Akun Demo -->
          <!-- Akun demo membantu proses belajar/testing manual; login tetap diproses melalui form normal di atas. -->
          <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-[11px] text-slate-500 leading-relaxed">
              <p class="mb-2 font-semibold text-slate-700">Akun Demo</p>

              <div class="space-y-1">                  
                  <p><span class="font-semibold text-slate-600">Pemohon</span> : pemohon / 12345678</p>
                  <p><span class="font-semibold text-slate-600">Admin Surat</span> : adminsurat / 12345678</p>
                  <p><span class="font-semibold text-slate-600">Verifikator</span> : verifikator / 12345678</p>
                  <p><span class="font-semibold text-slate-600">Verifikator</span> : direktur / 12345678</p>
                  <p><span class="font-semibold text-slate-600">Super Admin</span> : superadmin / 12345678</p>
              </div>
          </div>

          <p class="mt-8 text-center text-[11px] text-slate-300 font-light">&copy; 2026 SIMAS · PBL-TRPL201-MALAM </p>

        </div>
      </div>


      <!-- ================================================================
           PANEL KANAN — BRANDING + CAROUSEL
      ================================================================ -->
      <!-- Panel kanan hanya untuk tampilan branding/carousel dan tidak mengirim data ke controller. -->
      <div class="hidden lg:flex relative w-1/2 flex-col justify-between overflow-hidden p-14">

        <!-- Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-500 to-sky-400"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff07_1px,transparent_1px),linear-gradient(to_bottom,#ffffff07_1px,transparent_1px)] bg-[size:40px_40px]"></div>
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-blue-800/20 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col justify-between h-full">

          <!-- Tagline atas -->
          <div>
            <h2 class="text-4xl font-extrabold text-white leading-[1.15] tracking-tight">
              Administrasi<br />
              <span class="text-sky-200">Lebih Cerdas,</span><br />
              Lebih Terkelola.
            </h2>
            <p class="mt-4 text-sm text-white/60 font-light leading-relaxed max-w-xs">
              Platform pengelolaan dokumen resmi Polibatam yang terintegrasi, aman, dan mudah digunakan.
            </p>
          </div>

          <!-- ── CAROUSEL ── -->
          <div class="flex flex-col gap-4">

            <!-- Slide area -->
            <div class="overflow-hidden rounded-2xl shadow-xl shadow-blue-900/30 ring-1 ring-white/20"
              id="login-carousel-container">
              <div class="login-carousel-track" id="login-carousel-track">

                <!-- SLIDE 1 -->
                <div class="login-carousel-slide">
                  <div class="relative w-full aspect-video overflow-hidden">
                    <!--
                      Ganti dengan:
                      <img src="{ { asset('images/carousel/r1.png') } }"
                           alt="Dashboard SIMAS"
                           class="w-full h-full object-cover object-center" />
                    -->
                    <img src="{{ asset('images/carousel/c1.png') }}"
                         alt="Dashboard SIMAS"
                         class="w-full h-full object-cover object-center" />
                    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                  </div>
                </div>

                <!-- SLIDE 2 -->
                <div class="login-carousel-slide">
                  <div class="relative w-full aspect-video overflow-hidden">
                    <img src="{{ asset('images/carousel/c2.png') }}"
                         alt="Form Pengajuan SIMAS"
                         class="w-full h-full object-cover object-center" />
                    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                  </div>
                </div>

                <!-- SLIDE 3 -->
                <div class="login-carousel-slide">
                  <div class="relative w-full aspect-video overflow-hidden">
                    <img src="{{ asset('images/carousel/c3.png') }}"
                         alt="Alur Verifikasi SIMAS"
                         class="w-full h-full object-cover object-center" />
                    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                  </div>
                </div>

              </div>
            </div>

            <!-- Caption + Dots -->
            <div class="flex flex-col items-center gap-3">

              <!-- Caption -->
              <div class="relative h-12 w-full text-center overflow-hidden">

                <div class="login-caption absolute inset-0 flex flex-col items-center justify-center" data-login-slide="0">
                  <p class="text-sm font-semibold text-white">Dashboard Terpusat</p>
                  <p class="text-xs text-white/60 font-light mt-0.5">Pantau semua dokumen dalam satu tampilan ringkas.</p>
                </div>

                <div class="login-caption absolute inset-0 flex flex-col items-center justify-center" data-login-slide="1">
                  <p class="text-sm font-semibold text-white">Pengajuan Mudah & Cepat</p>
                  <p class="text-xs text-white/60 font-light mt-0.5">Ajukan surat kapan saja, dari mana saja.</p>
                </div>

                <div class="login-caption absolute inset-0 flex flex-col items-center justify-center" data-login-slide="2">
                  <p class="text-sm font-semibold text-white">Verifikasi Bertingkat & Transparan</p>
                  <p class="text-xs text-white/60 font-light mt-0.5">Setiap tahap persetujuan tercatat real-time.</p>
                </div>

              </div>

              <!-- Dots -->
              <div class="flex items-center gap-2" id="login-carousel-dots">
                <button type="button" data-login-dot="0"
                  class="login-dot active h-1.5 w-5 rounded-full bg-white/90 transition-all duration-300"></button>
                <button type="button" data-login-dot="1"
                  class="login-dot h-1.5 w-1.5 rounded-full bg-white/30 hover:bg-white/50 transition-all duration-300"></button>
                <button type="button" data-login-dot="2"
                  class="login-dot h-1.5 w-1.5 rounded-full bg-white/30 hover:bg-white/50 transition-all duration-300"></button>
              </div>

            </div>
          </div>

          <!-- Quote bawah -->
          <div class="border-t border-white/10 pt-6">
            <p class="text-xs text-white/40 font-light leading-relaxed">
              Sistem ini dikembangkan sebagai bagian dari program Project-Based Learning Politeknik Negeri Batam untuk meningkatkan tata kelola administrasi institusi.
            </p>
          </div>

        </div>
      </div>

    </div>

  </body>
</html>
