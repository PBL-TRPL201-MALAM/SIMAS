<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('partials.favicon')
    <title>SIMAS — Sistem Manajemen Administrasi Surat Polibatam</title>
    <meta name="description" content="Platform pengelolaan surat, SK, dan dokumen resmi Politeknik Negeri Batam. Terintegrasi, multi-level verifikasi, dan mudah digunakan." />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      * { font-family: 'Inter', sans-serif; }

      /* ── Navbar scroll effect ── */
      .navbar-scrolled {
        background: rgba(255, 255, 255, 0.92) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06);
      }

      /* ── Carousel ── */
      .carousel-track {
        display: flex;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: transform;
        width: 100%;
      }
      .carousel-slide {
        min-width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
        overflow: hidden;
      }
      .carousel-dot.active {
        background-color: #2563eb;
        width: 24px;
        border-radius: 4px;
      }
      .slide-caption {
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.4s ease 0.3s, transform 0.4s ease 0.3s;
      }
      .slide-caption.visible {
        opacity: 1;
        transform: translateY(0);
      }

      /* ── FAQ Accordion ── */
      .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s ease, padding 0.35s ease;
      }
      .faq-answer.open {
        max-height: 400px;
      }
      .faq-icon {
        transition: transform 0.3s ease;
      }
      .faq-item.open .faq-icon {
        transform: rotate(45deg);
      }

      /* ── Subtle gradient text ── */
      .gradient-text {
        background: linear-gradient(135deg, #1d4ed8 0%, #0ea5e9 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }

      /* ── Feature card hover ── */
      .feature-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
      }
      .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08);
        border-color: #bfdbfe;
      }

      /* ── Stat counter ── */
      .stat-card {
        transition: transform 0.2s ease;
      }
      .stat-card:hover {
        transform: translateY(-2px);
      }

      .hero-float {
        animation: heroFloat 5s ease-in-out infinite;
      }

      @keyframes heroFloat {
        0%, 100% {
          transform: translateY(0);
        }
        50% {
          transform: translateY(-14px);
        }
      }
    </style>
  </head>

  <body class="bg-white text-slate-900 antialiased overflow-x-hidden">

    <!-- ============================================================
         SYSTEM STATUS BAR (poin 5)
    ============================================================ -->
    <div class="bg-slate-900 text-white py-2 px-4 text-center text-[11px] font-medium flex items-center justify-center gap-6 flex-wrap">
      <span class="flex items-center gap-1.5">
        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
        Semua sistem beroperasi normal
      </span>
      <span class="text-slate-400 hidden sm:inline">·</span>
      <span class="text-slate-300 hidden sm:inline">
        SIMAS v2.0 · Pemeliharaan terjadwal: Minggu, 06 Jul 2026 pukul 01.00–03.00 WIB
      </span>
      <a href="#pengumuman" class="text-blue-400 hover:text-blue-300 transition-colors duration-200 hidden sm:inline">Selengkapnya →</a>
    </div>

    <!-- ============================================================
         NAVBAR (poin 3 — sticky glassmorphic)
    ============================================================ -->
    <header id="navbar" class="fixed inset-x-0 top-0 z-50 transition-all duration-300 bg-white/60 backdrop-blur-xl border-b border-white/20"
            style="top: 32px;">
      <nav class="mx-auto max-w-7xl px-6 lg:px-10 flex items-center justify-between h-16">

        <a href="#" class="flex items-center gap-2.5 group">
          <img src="{{ asset('images/logo.png') }}" alt="Logo SIMAS — Polibatam" class="h-8 w-auto object-contain" />
          <div class="leading-none">
            <span class="text-base font-bold tracking-tight text-slate-900 block">SIMAS</span>
            <span class="text-[9px] font-medium text-blue-500 tracking-widest uppercase">Polibatam</span>
          </div>
        </a>

        <div class="hidden lg:flex items-center gap-7">
          <a href="#fitur" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors duration-200">Fitur</a>
          <a href="#cara-kerja" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors duration-200">Cara Kerja</a>
          <a href="#faq" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors duration-200">FAQ</a>
          <a href="#tentang" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors duration-200">Tentang</a>
        </div>

        <div class="flex items-center gap-3">

          <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
            Masuk
          </a>
          <button id="menu-toggle" type="button" aria-label="Buka menu navigasi" class="lg:hidden -m-1 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>
        </div>
      </nav>

      <!-- Mobile menu -->
      <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-100/80 bg-white/95 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-6 py-5 flex flex-col gap-1">
          <a href="#fitur" class="text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all duration-200">Fitur</a>
          <a href="#cara-kerja" class="text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all duration-200">Cara Kerja</a>
          <a href="#faq" class="text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all duration-200">FAQ</a>
          <a href="#tentang" class="text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all duration-200">Tentang</a>
          <hr class="border-slate-100 my-1" />
          <a href="{{ route('login') }}" class="mt-1 inline-flex justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-all duration-200">
            Masuk
          </a>
        </div>
      </div>
    </header>

    <main>

      <!-- ============================================================
           SECTION 1: HERO (poin 1)
      ============================================================ -->
      <section id="hero" class="relative min-h-screen flex items-center justify-center overflow-hidden" style="padding-top: 96px;">

        <!-- Background -->
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-50 via-white to-blue-50/40"></div>
        <div class="absolute -z-10 -top-40 -left-40 w-[600px] h-[600px] rounded-full bg-gradient-to-br from-blue-100/60 to-sky-100/40 blur-3xl"></div>
        <div class="absolute -z-10 bottom-0 right-0 w-[500px] h-[500px] rounded-full bg-gradient-to-tl from-blue-50/70 to-indigo-100/30 blur-3xl"></div>

        <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-6 text-center lg:grid-cols-[minmax(0,1fr)_minmax(380px,0.85fr)] lg:px-10 lg:text-left">
          <div>

          <h1 class="text-5xl sm:text-6xl lg:text-[76px] font-extrabold tracking-tight text-slate-900 leading-[1.06] text-balance">
            Administrasi Surat<br />
            <span class="gradient-text">Tanpa Kerumitan.</span>
          </h1>

          <p class="mt-6 max-w-2xl mx-auto text-base sm:text-lg text-slate-500 font-light leading-relaxed lg:mx-0">
            Platform tunggal untuk mengelola surat, SK, dan dokumen resmi Polibatam — dari pengajuan hingga tanda tangan digital, semua tercatat dan bisa dipantau kapan saja.
          </p>

          <!-- CTA Group (poin 1) -->
          <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3 lg:justify-start">
            <a href="{{ route('login') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 rounded-2xl bg-blue-600 px-8 py-4 text-sm font-bold text-white shadow-xl shadow-blue-200/60 hover:bg-blue-700 hover:shadow-blue-300/70 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
              </svg>
              Masuk
            </a>
            <a href="#fitur"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 bg-white px-8 py-4 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50/30 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
              Pelajari Fitur
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </a>
          </div>

          <!-- Trust badges (poin 7) -->
          <div class="mt-10 flex flex-wrap items-center justify-center gap-4 text-[11px] text-slate-400 font-medium lg:justify-start">
            <span class="w-px h-3 bg-slate-200 hidden sm:block"></span>
            <span class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Tidak ada registrasi mandiri
            </span>
            <span class="w-px h-3 bg-slate-200 hidden sm:block"></span>
            <span class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              TTE terintegrasi QR Code resmi
            </span>
          </div>

          <!-- Scroll indicator -->
          <div class="mt-16 flex justify-center lg:justify-start">
            <a href="#showcase" class="flex flex-col items-center gap-1 text-slate-300 hover:text-blue-400 transition-colors duration-200" aria-label="Scroll ke bawah untuk lihat tampilan produk">
              <span class="text-[10px] font-medium tracking-widest uppercase">Lihat Tampilan</span>
              <svg class="w-4 h-4 animate-bounce mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </a>
          </div>
          </div>

          <div class="pointer-events-none relative mx-auto w-full max-w-[520px] lg:mx-0">
            <img src="{{ asset('images/hand2.png') }}"
                 alt="Ilustrasi layanan digital SIMAS"
                 class="hero-float h-auto w-full object-contain"
                 loading="eager" />
          </div>
        </div>
      </section>


      <!-- ============================================================
           SECTION 2: STAT NUMBERS
      ============================================================ -->
      <section class="py-14 border-y border-slate-100 bg-slate-50/50">
        <div class="mx-auto max-w-5xl px-6 lg:px-10">
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @foreach ([
              ['value' => '6', 'suffix' => 'Role', 'label' => 'Pengguna terintegrasi', 'color' => 'text-blue-600'],
              ['value' => '13', 'suffix' => 'Jenis', 'label' => 'Template surat tersedia', 'color' => 'text-blue-600'],
              ['value' => '3', 'suffix' => 'Level', 'label' => 'Verifikasi bertingkat', 'color' => 'text-blue-600'],
              ['value' => '100%', 'suffix' => '', 'label' => 'Terdigitalisasi, tanpa kertas', 'color' => 'text-blue-600'],
            ] as $stat)
            <div class="stat-card">
              <p class="text-3xl sm:text-4xl font-extrabold {{ $stat['color'] }}">{{ $stat['value'] }}<span class="text-xl ml-0.5">{{ $stat['suffix'] }}</span></p>
              <p class="text-xs text-slate-500 font-light mt-1">{{ $stat['label'] }}</p>
            </div>
            @endforeach
          </div>
        </div>
      </section>


      <!-- ============================================================
           SECTION 3: MOCKUP PRODUK (poin 2)
      ============================================================ -->
      <section id="showcase" class="py-20 sm:py-28 relative overflow-hidden bg-gradient-to-b from-slate-50/60 to-white">

        <div class="absolute -z-10 top-0 left-1/2 -translate-x-1/2 w-[900px] h-[500px] rounded-full bg-blue-50/60 blur-3xl pointer-events-none"></div>

        <div class="mx-auto max-w-6xl px-6 lg:px-10">

          <div class="text-center mb-14">
            <span class="text-[11px] font-semibold tracking-widest text-blue-600 uppercase">Tampilan Produk</span>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">Lihat SIMAS dari Dekat</h2>
            <p class="mt-3 text-sm text-slate-400 font-light leading-relaxed max-w-md mx-auto">
              Antarmuka bersih, intuitif, dan dirancang untuk kecepatan kerja administrasi sehari-hari.
            </p>
          </div>

          <!-- Laptop mockup wrapper (poin 2) -->
          <div class="relative mx-auto max-w-5xl">
            <!-- Monitor frame -->
            <div class="relative rounded-2xl bg-slate-800 p-2 sm:p-3 shadow-2xl shadow-slate-900/30 ring-1 ring-white/10">
              <!-- Screen bar -->
              <div class="flex items-center gap-1.5 px-3 py-2 bg-slate-700 rounded-t-xl">
                <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="ml-2 flex-1 bg-slate-600 rounded-md h-4 max-w-[220px] mx-auto flex items-center justify-center px-3">
                  <span class="text-[9px] text-slate-400 truncate">simas.polibatam.ac.id/dashboard</span>
                </span>
              </div>
              <!-- Screen content / carousel -->
              <div class="overflow-hidden rounded-b-xl" id="carousel-container">
                <div class="carousel-track" id="carousel-track">

                  <!-- SLIDE 1 — dengan skeleton loading (poin 2) -->
                  <div class="carousel-slide">
                    <div class="relative w-full aspect-[1497/704] overflow-hidden bg-slate-100 group">
                      {{-- Skeleton loader tampil sambil gambar dimuat --}}
                      <div class="absolute inset-0 animate-pulse bg-gradient-to-r from-slate-200 via-slate-100 to-slate-200 image-skeleton" id="skeleton-1"></div>
                      <img src="{{ asset('images/carousel/N1.png') }}"
                           alt="Tampilan Dashboard SIMAS — halaman utama dengan statistik dokumen"
                           class="w-full h-full object-contain object-center relative z-10"
                           loading="lazy"
                           onload="document.getElementById('skeleton-1').style.display='none'" />
                      <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-slate-900/20 to-transparent pointer-events-none z-20"></div>
                    </div>
                  </div>

                  <!-- SLIDE 2 -->
                  <div class="carousel-slide">
                    <div class="relative w-full aspect-[1497/704] overflow-hidden bg-slate-100">
                      <div class="absolute inset-0 animate-pulse bg-gradient-to-r from-slate-200 via-slate-100 to-slate-200" id="skeleton-2"></div>
                      <img src="{{ asset('images/carousel/N2.png') }}"
                           alt="Tampilan form pengajuan surat di SIMAS"
                           class="w-full h-full object-contain object-center relative z-10"
                           loading="lazy"
                           onload="document.getElementById('skeleton-2').style.display='none'" />
                      <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-slate-900/20 to-transparent pointer-events-none z-20"></div>
                    </div>
                  </div>

                  <!-- SLIDE 3 -->
                  <div class="carousel-slide">
                    <div class="relative w-full aspect-[1497/704] overflow-hidden bg-slate-100">
                      <div class="absolute inset-0 animate-pulse bg-gradient-to-r from-slate-200 via-slate-100 to-slate-200" id="skeleton-3"></div>
                      <img src="{{ asset('images/carousel/N4.png') }}"
                           alt="Tampilan alur verifikasi bertingkat di SIMAS"
                           class="w-full h-full object-contain object-center relative z-10"
                           loading="lazy"
                           onload="document.getElementById('skeleton-3').style.display='none'" />
                      <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-slate-900/20 to-transparent pointer-events-none z-20"></div>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- Laptop base stand decoration -->
            <div class="mx-auto mt-0 w-1/3 h-4 bg-slate-700 rounded-b-full shadow-lg"></div>
            <div class="mx-auto w-1/2 h-1.5 bg-slate-600 rounded-full"></div>

            <!-- Prev / Next buttons -->
            <button id="carousel-prev" type="button" aria-label="Slide sebelumnya"
              class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-5 w-10 h-10 rounded-full bg-white shadow-lg border border-slate-100 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 active:scale-95 transition-all duration-200 z-10">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button id="carousel-next" type="button" aria-label="Slide berikutnya"
              class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-5 w-10 h-10 rounded-full bg-white shadow-lg border border-slate-100 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 active:scale-95 transition-all duration-200 z-10">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>

          <!-- Caption + Dots -->
          <div class="mt-8 flex flex-col items-center gap-4">
            <div class="relative h-16 w-full max-w-lg text-center overflow-hidden">
              <div class="slide-caption absolute inset-0 flex flex-col items-center justify-center" data-slide="0">
                <p class="text-base font-semibold text-slate-800">Dashboard Terpusat</p>
                <p class="text-sm text-slate-400 font-light mt-0.5">Pantau semua dokumen dan statusnya dalam satu tampilan yang ringkas.</p>
              </div>
              <div class="slide-caption absolute inset-0 flex flex-col items-center justify-center" data-slide="1">
                <p class="text-base font-semibold text-slate-800">Pengajuan Mudah & Cepat</p>
                <p class="text-sm text-slate-400 font-light mt-0.5">Ajukan surat kapan saja, dari mana saja — tanpa perlu datang ke kantor.</p>
              </div>
              <div class="slide-caption absolute inset-0 flex flex-col items-center justify-center" data-slide="2">
                <p class="text-base font-semibold text-slate-800">Verifikasi Bertingkat & Transparan</p>
                <p class="text-sm text-slate-400 font-light mt-0.5">Setiap tahap persetujuan tercatat — pemohon bisa memantau progres real-time.</p>
              </div>
            </div>
            <div class="flex items-center gap-2" id="carousel-dots">
              <button type="button" data-dot="0" aria-label="Slide 1" class="carousel-dot active h-2 w-6 rounded-full bg-blue-600 transition-all duration-300"></button>
              <button type="button" data-dot="1" aria-label="Slide 2" class="carousel-dot h-2 w-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all duration-300"></button>
              <button type="button" data-dot="2" aria-label="Slide 3" class="carousel-dot h-2 w-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all duration-300"></button>
            </div>
          </div>

        </div>
      </section>


      <!-- ============================================================
           SECTION 4: FEATURES (poin 3 & 4 — hover + progressive disclosure)
      ============================================================ -->
      <section id="fitur" class="py-24 sm:py-32 relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-white via-slate-50/50 to-white"></div>

        <div class="mx-auto max-w-7xl px-6 lg:px-10">
          <div class="max-w-xl mx-auto text-center mb-14">
            <span class="text-[11px] font-semibold tracking-widest text-blue-600 uppercase">Fitur Unggulan</span>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">Semua yang Dibutuhkan Administrasi Modern</h2>
            <p class="mt-3 text-sm text-slate-400 font-light leading-relaxed">Dirancang untuk mempercepat alur kerja dari pengajuan hingga penerbitan dokumen resmi.</p>
          </div>

          {{-- 6 fitur utama selalu tampil (poin 4) --}}
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="features-primary">
            @foreach([
              ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Pengelolaan Surat & SK', 'desc' => 'Kelola surat masuk, surat keluar, SK, dan disposisi secara digital dalam satu platform terpusat.', 'color' => 'bg-blue-50 text-blue-600'],
              ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Verifikasi Multi-Level', 'desc' => 'Alur persetujuan bertingkat: Kepala Unit → Wakil Direktur → Direktur, dengan notifikasi real-time.', 'color' => 'bg-emerald-50 text-emerald-600'],
              ['icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', 'title' => 'Tanda Tangan Elektronik', 'desc' => 'Pengesahan dokumen dengan TTE terintegrasi QR Code & barcode yang dapat divalidasi publik.', 'color' => 'bg-violet-50 text-violet-600'],
              ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Multi-Role Pengguna', 'desc' => 'Dukungan 6 peran: Admin Surat, Pemohon, Verifikator, Penandatangan, Super Admin, Mahasiswa.', 'color' => 'bg-amber-50 text-amber-600'],
              ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'title' => 'Pencarian & Arsip Digital', 'desc' => 'Temukan dokumen lama dalam hitungan detik — filter berdasarkan jenis, tanggal, dan status.', 'color' => 'bg-sky-50 text-sky-600'],
              ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Dashboard & Laporan', 'desc' => 'Pantau statistik administrasi secara real-time — jumlah dokumen, status, dan rekap periodik.', 'color' => 'bg-rose-50 text-rose-600'],
            ] as $card)
            <div class="feature-card group rounded-2xl border border-slate-100/80 bg-white p-6 cursor-default">
              <div class="w-10 h-10 rounded-xl {{ $card['color'] }} flex items-center justify-center mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                </svg>
              </div>
              <h3 class="text-sm font-semibold text-slate-800 mb-1.5">{{ $card['title'] }}</h3>
              <p class="text-xs text-slate-400 font-light leading-relaxed">{{ $card['desc'] }}</p>
            </div>
            @endforeach
          </div>

          {{-- Fitur sekunder tersembunyi (poin 4 — progressive disclosure) --}}
          <div id="features-secondary" class="hidden mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
              ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Notifikasi Real-Time', 'desc' => 'Pemberitahuan otomatis setiap kali status dokumen berubah — tidak perlu cek manual lagi.', 'color' => 'bg-indigo-50 text-indigo-600'],
              ['icon' => 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 10-5.657-5.657l-6.586 6.586a6 6 0 108.485 8.485L20.5 13', 'title' => 'Lampiran Multi-Format', 'desc' => 'Dukung lampiran PDF, DOC, JPG, PNG hingga 10MB per file dalam satu pengajuan.', 'color' => 'bg-teal-50 text-teal-600'],
              ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'Audit Trail Lengkap', 'desc' => 'Setiap perubahan status, aksi user, dan keputusan tercatat dalam log yang transparan.', 'color' => 'bg-orange-50 text-orange-600'],
            ] as $card)
            <div class="feature-card group rounded-2xl border border-slate-100/80 bg-white p-6 cursor-default">
              <div class="w-10 h-10 rounded-xl {{ $card['color'] }} flex items-center justify-center mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                </svg>
              </div>
              <h3 class="text-sm font-semibold text-slate-800 mb-1.5">{{ $card['title'] }}</h3>
              <p class="text-xs text-slate-400 font-light leading-relaxed">{{ $card['desc'] }}</p>
            </div>
            @endforeach
          </div>

          {{-- Toggle button (poin 4) --}}
          <div class="mt-8 flex justify-center">
            <button id="toggle-features" type="button"
              class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-xs font-semibold text-slate-600 hover:border-blue-300 hover:text-blue-600 transition-all duration-200">
              <span id="toggle-features-text">Lihat Semua Fitur</span>
              <svg id="toggle-features-icon" class="w-3.5 h-3.5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>

        </div>
      </section>


      <!-- ============================================================
           SECTION 5: HOW IT WORKS (poin 3)
      ============================================================ -->
      <section id="cara-kerja" class="py-24 sm:py-32 relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-50/80 via-blue-50/20 to-white"></div>

        <div class="mx-auto max-w-7xl px-6 lg:px-10">
          <div class="max-w-xl mx-auto text-center mb-14">
            <span class="text-[11px] font-semibold tracking-widest text-blue-600 uppercase">Cara Kerja</span>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">Dari Pengajuan ke Dokumen Resmi</h2>
            <p class="mt-3 text-sm text-slate-400 font-light leading-relaxed">Empat langkah sederhana — semua terekam dan bisa dipantau kapan saja.</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
              ['no'=>'01','title'=>'Ajukan Dokumen', 'desc'=>'Upload PDF atau isi form SK digital, lengkapi data, lalu kirim dalam hitungan menit.', 'color' => 'from-blue-600 to-blue-500'],
              ['no'=>'02','title'=>'Verifikasi Bertingkat', 'desc'=>'Dokumen diperiksa dan disetujui secara berurutan oleh pejabat yang berwenang.', 'color' => 'from-violet-600 to-violet-500'],
              ['no'=>'03','title'=>'Tanda Tangan Elektronik', 'desc'=>'Dokumen final disahkan dengan TTE resmi disertai QR Code validasi publik.', 'color' => 'from-emerald-600 to-emerald-500'],
              ['no'=>'04','title'=>'Terbit & Diarsipkan', 'desc'=>'Pemohon menerima dokumen resmi yang bisa diunduh dan tersimpan permanen di sistem.', 'color' => 'from-amber-500 to-amber-400'],
            ] as $step)
            <div class="feature-card flex flex-col items-center text-center p-6 rounded-2xl bg-white border border-slate-100/80">
              <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $step['color'] }} flex items-center justify-center mb-4 shadow-md">
                <span class="text-sm font-bold text-white">{{ $step['no'] }}</span>
              </div>
              <h3 class="text-sm font-semibold text-slate-800 mb-2">{{ $step['title'] }}</h3>
              <p class="text-xs text-slate-400 font-light leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
          </div>
        </div>
      </section>


      <!-- ============================================================
           SECTION 6: PENGUMUMAN & STATUS (poin 5)
      ============================================================ -->
      <section id="pengumuman" class="py-16 sm:py-20 bg-slate-50 border-y border-slate-100">
        <div class="mx-auto max-w-5xl px-6 lg:px-10">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
              <span class="text-[11px] font-semibold tracking-widest text-blue-600 uppercase">Info & Pengumuman</span>
              <h2 class="mt-1 text-2xl font-bold text-slate-900">Informasi Terkini</h2>
            </div>
            <!-- System status badge -->
            <div class="inline-flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5">
              <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse" aria-hidden="true"></span>
              <div>
                <p class="text-xs font-semibold text-emerald-700">Semua sistem beroperasi normal</p>
                <p class="text-[10px] text-emerald-600 font-light">Terakhir diperbarui: hari ini</p>
              </div>
            </div>
          </div>

          <div class="space-y-3">
            @foreach([
              ['type' => 'info', 'date' => '27 Jun 2026', 'title' => 'Pembaruan Sistem v2.0 — Fitur Lampiran Multi-File Tersedia', 'body' => 'Pemohon kini dapat melampirkan lebih dari satu file pendukung (PDF, DOC, JPG, PNG, maks. 10 MB per file) dalam satu pengajuan surat biasa.', 'badge' => 'Pembaruan', 'badge_color' => 'bg-blue-100 text-blue-700'],
              ['type' => 'warning', 'date' => '06 Jul 2026', 'title' => 'Pemeliharaan Terjadwal — Server akan offline sementara', 'body' => 'Sistem SIMAS akan mengalami downtime terjadwal pada Minggu, 06 Juli 2026 pukul 01.00–03.00 WIB. Harap selesaikan pengajuan sebelum waktu tersebut.', 'badge' => 'Maintenance', 'badge_color' => 'bg-amber-100 text-amber-700'],
              ['type' => 'info', 'date' => '15 Jun 2026', 'title' => 'Panduan Pengajuan SK Baru Telah Diterbitkan', 'body' => 'Panduan lengkap alur pengajuan Surat Keputusan oleh Pemohon sudah tersedia. Unduh di halaman Panduan Penggunaan atau hubungi Admin Surat.', 'badge' => 'Panduan', 'badge_color' => 'bg-emerald-100 text-emerald-700'],
            ] as $item)
            <div class="rounded-2xl bg-white border border-slate-100 px-5 py-4 flex gap-4 items-start hover:border-slate-200 transition-all duration-200">
              <div class="shrink-0 w-1 self-stretch rounded-full {{ $item['type'] === 'warning' ? 'bg-amber-400' : 'bg-blue-400' }}"></div>
              <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                  <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $item['badge_color'] }}">{{ $item['badge'] }}</span>
                  <span class="text-[10px] text-slate-400 font-light">{{ $item['date'] }}</span>
                </div>
                <p class="text-sm font-semibold text-slate-800">{{ $item['title'] }}</p>
                <p class="text-[11px] text-slate-500 font-light mt-1 leading-relaxed">{{ $item['body'] }}</p>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </section>


      <!-- ============================================================
           SECTION 7: MINI FAQ / ACCORDION (poin 6)
      ============================================================ -->
      <section id="faq" class="py-24 sm:py-32">
        <div class="mx-auto max-w-3xl px-6 lg:px-10">
          <div class="text-center mb-12">
            <span class="text-[11px] font-semibold tracking-widest text-blue-600 uppercase">Bantuan</span>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">Pertanyaan Umum</h2>
            <p class="mt-3 text-sm text-slate-400 font-light">Tidak menemukan jawaban? Hubungi tim IT Helpdesk Polibatam.</p>
          </div>

          <div class="space-y-2" id="faq-container">
            @foreach([
              [
                'q' => 'Mengapa saya tidak bisa login ke SIMAS?',
                'a' => 'Pastikan Anda menggunakan akun resmi Polibatam (username dan password SSO yang sama dengan portal kampus). Jika gagal, coba reset password di portal SSO Polibatam terlebih dahulu. Jika masih bermasalah setelah reset, hubungi IT Helpdesk Polibatam karena akun Anda mungkin belum diaktivasi di sistem SIMAS.'
              ],
              [
                'q' => 'Berapa lama proses verifikasi dokumen berlangsung?',
                'a' => 'Durasi verifikasi tergantung pada jumlah level yang ditetapkan Admin Surat dan ketersediaan Verifikator/Penandatangan. Biasanya 1–3 hari kerja untuk dokumen standar. Anda bisa memantau status dokumen secara real-time di menu "Surat Saya" atau "SK Saya" — setiap perubahan status langsung terbarui.'
              ],
              [
                'q' => 'Bagaimana alur Tanda Tangan Elektronik (TTE) di SIMAS?',
                'a' => 'TTE di SIMAS diterapkan secara digital saat dokumen dinyatakan "Siap Publish" oleh Admin Surat. Setelah semua level verifikasi menyetujui, Admin Surat mem-publish dokumen dan sistem otomatis membuat PDF final dengan overlay QR Code validasi. QR Code tersebut dapat dipindai oleh siapa pun untuk memverifikasi keaslian dokumen melalui halaman publik SIMAS.'
              ],
              [
                'q' => 'Apakah dokumen yang sudah dipublish bisa diubah?',
                'a' => 'Tidak. Dokumen yang sudah berstatus PUBLISHED tidak dapat diubah untuk menjaga integritas rekam jejak digital. Jika ada kesalahan, Pemohon perlu mengajukan dokumen baru dengan keterangan revisi. Riwayat seluruh pengajuan tetap tersimpan dan dapat dilihat di sistem.'
              ],
              [
                'q' => 'Siapa yang bisa menggunakan SIMAS?',
                'a' => 'SIMAS hanya bisa diakses oleh civitas akademika Polibatam yang sudah memiliki akun SSO aktif. Tidak ada registrasi mandiri — akun dikelola oleh Super Admin SIMAS. Jika Anda dosen, staf, atau mahasiswa aktif tetapi belum bisa login, hubungi Admin IT untuk aktivasi akun SIMAS.'
              ],
              [
                'q' => 'Apakah saya bisa mengajukan revisi setelah dokumen dikembalikan?',
                'a' => 'Ya. Jika status dokumen Anda berubah menjadi "Perlu Revisi", Anda dapat membuka menu "Surat Saya" atau "SK Saya", lalu klik tombol "Perbaiki Pengajuan". Upload ulang PDF atau perbaiki data sesuai catatan dari Admin Surat atau Verifikator, lalu kirim kembali.'
              ],
            ] as $index => $item)
            <div class="faq-item rounded-2xl border border-slate-100 bg-white overflow-hidden" data-faq="{{ $index }}">
              <button type="button"
                class="faq-trigger w-full flex items-center justify-between gap-4 px-5 py-4 text-left"
                aria-expanded="false"
                aria-controls="faq-answer-{{ $index }}">
                <span class="text-sm font-semibold text-slate-800">{{ $item['q'] }}</span>
                <span class="faq-icon shrink-0 w-5 h-5 flex items-center justify-center rounded-full bg-slate-100 text-slate-500" aria-hidden="true">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                </span>
              </button>
              <div class="faq-answer" id="faq-answer-{{ $index }}" role="region">
                <p class="px-5 pb-4 text-xs text-slate-500 font-light leading-relaxed">{{ $item['a'] }}</p>
              </div>
            </div>
            @endforeach
          </div>

          <div class="mt-8 text-center">
            <p class="text-xs text-slate-400">Masih ada pertanyaan?
              <a href="https://wa.me/0778469856" target="_blank" rel="noopener noreferrer" class="text-blue-600 font-medium hover:underline">Chat IT Helpdesk via WhatsApp</a>
              atau
              <a href="mailto:it@polibatam.ac.id" class="text-blue-600 font-medium hover:underline">kirim email</a>.
            </p>
          </div>
        </div>
      </section>


      <!-- ============================================================
           SECTION 8: CTA FINAL
      ============================================================ -->
      <section id="cta" class="py-20 sm:py-28">
        <div class="mx-auto max-w-5xl px-6 lg:px-8">
          <div class="relative rounded-3xl overflow-hidden px-8 py-16 sm:px-16 sm:py-20 text-center">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-700 via-blue-600 to-sky-500"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff06_1px,transparent_1px),linear-gradient(to_bottom,#ffffff06_1px,transparent_1px)] bg-[size:40px_40px]"></div>
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-blue-900/20 blur-2xl pointer-events-none"></div>
            <div class="relative">
              <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" aria-hidden="true"></span>
                <span class="text-xs font-semibold text-white/90">Secured via Polibatam SSO</span>
              </div>
              <h2 class="text-3xl sm:text-4xl font-bold text-white tracking-tight text-balance leading-tight">
                Siap Digitalisasi Administrasi<br />Polibatam?
              </h2>
              <p class="mt-4 max-w-md mx-auto text-sm text-white/70 font-light leading-relaxed">
                Login menggunakan akun kampus Polibatam yang sudah Anda miliki. Tidak perlu daftar, tidak perlu password baru.
              </p>
              <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-7 py-3.5 text-sm font-bold text-blue-700 shadow-lg hover:bg-blue-50 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                  </svg>
                  Masuk dengan Akun Polibatam
                </a>
                <a href="#faq" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-7 py-3.5 text-sm font-medium text-white hover:bg-white/20 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                  Lihat Pertanyaan Umum
                </a>
              </div>
              <p class="mt-5 text-[11px] text-white/40 font-light">
                Hanya untuk civitas akademika Polibatam · Tidak ada registrasi mandiri
              </p>
            </div>
          </div>
        </div>
      </section>

    </main>


    <!-- ============================================================
         FOOTER (poin 8 — refined links)
    ============================================================ -->
    <footer id="footer" class="border-t border-slate-100/80 bg-gradient-to-b from-white to-slate-50/50">
      <div class="mx-auto max-w-7xl px-6 lg:px-10 py-12 lg:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
          <div class="lg:col-span-2">
            <a href="#" class="flex items-center gap-2.5">
              <img src="{{ asset('images/logo.png') }}" alt="Logo SIMAS Polibatam" class="h-8 w-auto object-contain" />
              <div class="leading-none">
                <span class="text-base font-bold text-slate-900 block">SIMAS</span>
                <span class="text-[9px] font-medium text-blue-500 tracking-widest uppercase">Polibatam</span>
              </div>
            </a>
            <p class="mt-4 max-w-xs text-xs text-slate-400 font-light leading-relaxed">
              Sistem Manajemen Administrasi Surat — platform digital pengelolaan surat, SK, dan dokumen resmi Politeknik Negeri Batam.
            </p>
            <!-- System status mini (poin 5) -->
            <div class="mt-4 inline-flex items-center gap-2 text-[10px] text-emerald-600 font-medium">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" aria-hidden="true"></span>
              Semua sistem beroperasi normal
            </div>
          </div>

          <div>
            <h4 class="text-[11px] font-semibold tracking-widest text-slate-700 uppercase mb-4">Navigasi</h4>
            <ul class="space-y-2.5">
              <li><a href="#" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Beranda</a></li>
              <li><a href="#fitur" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Fitur</a></li>
              <li><a href="#cara-kerja" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Cara Kerja</a></li>
              <li><a href="#faq" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">FAQ</a></li>
              <li><a href="#pengumuman" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Pengumuman</a></li>
              <li><a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Masuk</a></li>
            </ul>
          </div>

          <div>
            <h4 class="text-[11px] font-semibold tracking-widest text-slate-700 uppercase mb-4">Dukungan</h4>
            <ul class="space-y-2.5">
              {{-- Poin 8 — link dukungan profesional --}}
              <li>
                <a href="/panduan-simas.pdf" target="_blank" rel="noopener noreferrer"
                   class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200 flex items-center gap-1.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Unduh Panduan Penggunaan (PDF)
                </a>
              </li>
              <li>
                <a href="https://wa.me/0778469856" target="_blank" rel="noopener noreferrer"
                   class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200 flex items-center gap-1.5">
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                  </svg>
                  IT Helpdesk via WhatsApp
                </a>
              </li>
              <li>
                <a href="mailto:it@polibatam.ac.id"
                   class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200 flex items-center gap-1.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  it@polibatam.ac.id
                </a>
              </li>
              <li>
                <a href="https://www.polibatam.ac.id" target="_blank" rel="noopener noreferrer"
                   class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200 flex items-center gap-1.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg>
                  Portal Polibatam
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div class="mt-10 pt-8 border-t border-slate-100/80">
          <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3 mb-4">
            <div>
              <h4 class="text-[11px] font-semibold tracking-widest text-slate-700 uppercase">Lokasi Kampus</h4>
              <p class="mt-2 text-xs text-slate-400 font-light">Politeknik Negeri Batam, Batam Centre, Kota Batam.</p>
            </div>
            <a href="https://www.google.com/maps/search/?api=1&query=Politeknik+Negeri+Batam"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors duration-200">
              Buka di Google Maps
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </a>
          </div>
          <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-sm">
            <iframe
              title="Lokasi Politeknik Negeri Batam di Google Maps"
              src="https://www.google.com/maps?q=Politeknik%20Negeri%20Batam&output=embed"
              class="h-64 w-full sm:h-72 lg:h-80"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>

        <div class="mt-12 pt-6 border-t border-slate-100/80 flex flex-col sm:flex-row items-center justify-between gap-3">
          <p class="text-[11px] text-slate-300 font-light">&copy; 2026 SIMAS · Politeknik Negeri Batam. Dikembangkan oleh Tim PBL-TRPL201-MALAM.</p>
          <div class="flex items-center gap-4 text-[11px] text-slate-300 font-light">
            <span>Laravel · Tailwind CSS · MySQL</span>
            <span class="w-px h-3 bg-slate-200"></span>
            <span class="flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" aria-hidden="true"></span>
              Sistem Normal
            </span>
          </div>
        </div>
      </div>
    </footer>


    <script>
    document.addEventListener('DOMContentLoaded', () => {

      // ── Navbar scroll effect (poin 3) ──
      const navbar = document.getElementById('navbar');
      const statusBar = document.querySelector('.bg-slate-900');
      const statusBarHeight = statusBar ? statusBar.offsetHeight : 32;

      window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
          navbar.classList.add('navbar-scrolled');
          navbar.style.top = '0';
        } else {
          navbar.classList.remove('navbar-scrolled');
          navbar.style.top = statusBarHeight + 'px';
        }
      }, { passive: true });

      // ── Mobile menu ──
      const menuToggle = document.getElementById('menu-toggle');
      const mobileMenu = document.getElementById('mobile-menu');
      menuToggle?.addEventListener('click', () => {
        mobileMenu?.classList.toggle('hidden');
      });
      // Close mobile menu on link click
      mobileMenu?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => mobileMenu.classList.add('hidden'));
      });

      // ── Carousel ──
      const track = document.getElementById('carousel-track');
      const dots = document.querySelectorAll('.carousel-dot');
      const captions = document.querySelectorAll('.slide-caption');
      let current = 0;
      let autoplay;

      const goTo = (index) => {
        current = (index + 3) % 3;
        if (track) track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((dot, i) => {
          dot.classList.toggle('active', i === current);
          dot.style.width = i === current ? '24px' : '8px';
          dot.style.backgroundColor = i === current ? '#2563eb' : '#cbd5e1';
        });
        captions.forEach((caption, i) => {
          caption.classList.toggle('visible', i === current);
        });
      };

      // Init
      captions[0]?.classList.add('visible');
      dots.forEach((dot, i) => dot.addEventListener('click', () => { clearInterval(autoplay); goTo(i); resetAutoplay(); }));
      document.getElementById('carousel-prev')?.addEventListener('click', () => { clearInterval(autoplay); goTo(current - 1); resetAutoplay(); });
      document.getElementById('carousel-next')?.addEventListener('click', () => { clearInterval(autoplay); goTo(current + 1); resetAutoplay(); });

      const resetAutoplay = () => { autoplay = setInterval(() => goTo(current + 1), 4500); };
      resetAutoplay();

      // ── FAQ Accordion (poin 6) ──
      document.querySelectorAll('.faq-trigger').forEach(trigger => {
        trigger.addEventListener('click', () => {
          const item = trigger.closest('.faq-item');
          const answer = item.querySelector('.faq-answer');
          const isOpen = item.classList.contains('open');

          // Close all
          document.querySelectorAll('.faq-item.open').forEach(openItem => {
            openItem.classList.remove('open');
            openItem.querySelector('.faq-answer').classList.remove('open');
            openItem.querySelector('.faq-trigger').setAttribute('aria-expanded', 'false');
          });

          // Open clicked if was closed
          if (!isOpen) {
            item.classList.add('open');
            answer.classList.add('open');
            trigger.setAttribute('aria-expanded', 'true');
          }
        });
      });

      // ── Progressive disclosure fitur (poin 4) ──
      const toggleBtn = document.getElementById('toggle-features');
      const secondaryFeatures = document.getElementById('features-secondary');
      const toggleText = document.getElementById('toggle-features-text');
      const toggleIcon = document.getElementById('toggle-features-icon');
      let featuresOpen = false;

      toggleBtn?.addEventListener('click', () => {
        featuresOpen = !featuresOpen;
        if (featuresOpen) {
          secondaryFeatures.classList.remove('hidden');
          secondaryFeatures.classList.add('grid');
          toggleText.textContent = 'Sembunyikan';
          toggleIcon.style.transform = 'rotate(180deg)';
        } else {
          secondaryFeatures.classList.add('hidden');
          secondaryFeatures.classList.remove('grid');
          toggleText.textContent = 'Lihat Semua Fitur';
          toggleIcon.style.transform = 'rotate(0deg)';
        }
      });

      // ── Smooth scroll for nav links ──
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
          const target = document.querySelector(anchor.getAttribute('href'));
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
      });

    });
    </script>

  </body>
</html>
