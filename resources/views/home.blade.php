<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SIMAS — Sistem Manajemen Administrasi Surat</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      * { font-family: 'Poppins', sans-serif; }
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
      /* Dot aktif */
      .carousel-dot.active {
        background-color: #2563eb;
        width: 24px;
        border-radius: 4px;
      }
      /* Caption fade-in saat slide aktif */
      .slide-caption {
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.4s ease 0.3s, transform 0.4s ease 0.3s;
      }
      .slide-caption.visible {
        opacity: 1;
        transform: translateY(0);
      }
      /* Navbar scroll effect */
      .navbar-scrolled {
        background: rgba(255,255,255,0.95) !important;
        box-shadow: 0 1px 12px rgba(0,0,0,0.06);
      }
    </style>
  </head>

  <body class="bg-white text-slate-900 antialiased overflow-x-hidden">

    {{-- ============================================================
         NAVBAR
    ============================================================ --}}
    <header id="navbar" class="fixed inset-x-0 top-0 z-50 transition-all duration-500 bg-white/70 backdrop-blur-xl border-b border-slate-100/60">
      <nav class="mx-auto max-w-7xl px-6 lg:px-10 flex items-center justify-between h-16">

        <a href="#" class="flex items-center gap-2.5 group">
  <img src="{{ asset('images/logo.png') }}"
       alt="Logo SIMAS"
       class="h-8 w-auto object-contain" />
  {{-- Hapus <span> ini kalau logo sudah include teks nama --}}
  <span class="text-base font-bold tracking-tight text-slate-900">SIMAS</span>
</a>

        <div class="hidden lg:flex items-center gap-7">
          <a href="#fitur"      class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors duration-200">Fitur</a>
          <a href="#cara-kerja" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors duration-200">Cara Kerja</a>
          <a href="#tentang"    class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors duration-200">Tentang</a>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:shadow-blue-300 hover:from-blue-700 hover:to-blue-600 active:scale-95 transition-all duration-200">
            Masuk
            <!-- <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg> -->
          </a>
          <button id="menu-toggle" type="button" class="lg:hidden -m-1 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>
        </div>
      </nav>

      <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-100/80 bg-white/95 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-6 py-5 flex flex-col gap-1">
          <a href="#fitur"      class="text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all duration-200">Fitur</a>
          <a href="#cara-kerja" class="text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all duration-200">Cara Kerja</a>
          <a href="#tentang"    class="text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all duration-200">Tentang</a>
          <hr class="border-slate-100 my-1" />
          <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg px-3 py-2.5 transition-all duration-200">Masuk</a>
          <a href="{{ route('login') }}" class="mt-1 inline-flex justify-center rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-blue-700 hover:to-blue-600 transition-all duration-200">
            Mulai Sekarang
          </a>
        </div>
      </div>
    </header>


    <main>

      {{-- ============================================================
           SECTION 1: HERO
      ============================================================ --}}
      <section id="hero" class="relative min-h-screen flex items-center justify-center pt-16 overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-50 via-white to-blue-50/40"></div>
        <div class="absolute -z-10 -top-40 -left-40 w-[600px] h-[600px] rounded-full bg-gradient-to-br from-blue-100/70 to-sky-100/50 blur-3xl"></div>
        <div class="absolute -z-10 bottom-0 right-0 w-[500px] h-[500px] rounded-full bg-gradient-to-tl from-blue-50/80 to-indigo-100/40 blur-3xl"></div>
        <div class="absolute -z-10 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[300px] rounded-full bg-gradient-to-r from-blue-50/30 via-sky-50/20 to-transparent blur-2xl"></div>

        <div class="mx-auto max-w-3xl px-6 lg:px-8 text-center">
          <h1 class="text-5xl sm:text-6xl lg:text-[72px] font-extrabold tracking-tight text-slate-900 leading-[1.08] text-balance">
            Administrasi Surat<br />
            <span class="bg-gradient-to-r from-blue-600 via-blue-500 to-sky-500 bg-clip-text text-transparent">
              Lebih Cerdas.
            </span>
          </h1>

          <p class="mt-6 max-w-xl mx-auto text-base sm:text-lg text-slate-400 font-light leading-relaxed">
            Platform pengelolaan surat, SK, dan dokumen resmi Polibatam — terintegrasi, multi-level, dan mudah diakses.
          </p>

          <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-200/60 hover:shadow-blue-300/70 hover:from-blue-700 hover:to-blue-600 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
              Akses Aplikasi
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </a>
            <a href="#fitur" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200/80 bg-white/80 px-7 py-3.5 text-sm font-medium text-slate-600 hover:border-blue-200 hover:text-blue-600 hover:bg-white hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
              Lihat Fitur
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </a>
          </div>

          <div class="mt-16 flex justify-center">
            <a href="#showcase" class="flex flex-col items-center gap-1 text-slate-300 hover:text-blue-400 transition-colors duration-200">
              <span class="text-[10px] font-medium tracking-widest uppercase">Scroll</span>
              <svg class="w-4 h-4 animate-bounce mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </a>
          </div>
        </div>
      </section>


      {{-- ============================================================
           SECTION 2: CAROUSEL PRODUK
           Gambar: simpan di public/images/carousel/
           - slide-1.png → screenshot dashboard utama
           - slide-2.png → screenshot form pengajuan
           - slide-3.png → screenshot alur verifikasi
      ============================================================ --}}
      <section id="showcase" class="py-20 sm:py-28 relative overflow-hidden bg-gradient-to-b from-slate-50/60 to-white">

        {{-- Dekorasi background --}}
        <div class="absolute -z-10 top-0 left-1/2 -translate-x-1/2 w-[900px] h-[500px] rounded-full bg-blue-50/60 blur-3xl pointer-events-none"></div>

        <div class="mx-auto max-w-6xl px-6 lg:px-10">

          {{-- Section header --}}
          <div class="text-center mb-12">
            <span class="text-[11px] font-semibold tracking-widest text-blue-600 uppercase">Tampilan Produk</span>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">
              Lihat SIMAS dari Dekat
            </h2>
            <p class="mt-3 text-sm text-slate-400 font-light leading-relaxed max-w-md mx-auto">
              Antarmuka yang bersih, intuitif, dan dirancang untuk produktivitas maksimal.
            </p>
          </div>

          {{-- Carousel wrapper --}}
          <div class="relative" id="carousel-wrapper">

            {{-- Slide area --}}
            <div class="overflow-hidden rounded-2xl shadow-2xl shadow-blue-100/60 ring-1 ring-slate-200/60"
              id="carousel-container"
              @mouseenter="pause"
              @mouseleave="resume">

              <div class="carousel-track" id="carousel-track">

                {{-- SLIDE 1: Dashboard Utama --}}
<div class="carousel-slide">
  <div class="relative w-full aspect-video overflow-hidden">
    <img src="{{ asset('images/carousel/r1.png') }}"
         alt="Dashboard SIMAS"
         class="w-full h-full object-cover object-center" />
    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
  </div>
</div>

                <div class="carousel-slide">
  <div class="relative w-full aspect-video overflow-hidden">
    <img src="{{ asset('images/carousel/r2.png') }}"
         alt="Form Pengajuan SIMAS"
         class="w-full h-full object-cover object-center" />
    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
  </div>
</div>

                <div class="carousel-slide">
  <div class="relative w-full aspect-video overflow-hidden">
    <img src="{{ asset('images/carousel/r3.png') }}"
         alt="Alur Verifikasi SIMAS"
         class="w-full h-full object-cover object-center" />
    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
  </div>
</div>

              </div>{{-- end track --}}
            </div>{{-- end container --}}

            {{-- Tombol Prev --}}
            <button id="carousel-prev" type="button"
              class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 lg:-translate-x-6 w-11 h-11 rounded-full bg-white shadow-lg shadow-slate-200/80 border border-slate-100 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:shadow-blue-100/60 active:scale-95 transition-all duration-200 z-10">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
            </button>

            {{-- Tombol Next --}}
            <button id="carousel-next" type="button"
              class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 lg:translate-x-6 w-11 h-11 rounded-full bg-white shadow-lg shadow-slate-200/80 border border-slate-100 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:shadow-blue-100/60 active:scale-95 transition-all duration-200 z-10">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
              </svg>
            </button>

          </div>{{-- end carousel-wrapper --}}

          {{-- Caption + Dots --}}
          <div class="mt-8 flex flex-col items-center gap-5">

            {{-- Caption teks per slide --}}
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

            {{-- Dots indicator --}}
            <div class="flex items-center gap-2" id="carousel-dots">
              <button type="button" data-dot="0"
                class="carousel-dot active h-2 w-6 rounded-full bg-blue-600 transition-all duration-300"></button>
              <button type="button" data-dot="1"
                class="carousel-dot h-2 w-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all duration-300"></button>
              <button type="button" data-dot="2"
                class="carousel-dot h-2 w-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all duration-300"></button>
            </div>

          </div>{{-- end caption + dots --}}

        </div>{{-- end max-w --}}
      </section>


      {{-- ============================================================
           SECTION 3: FEATURES
      ============================================================ --}}
      <section id="fitur" class="py-24 sm:py-32 relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-white via-slate-50/50 to-white"></div>
        <div class="absolute -z-10 top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] rounded-full bg-blue-50/50 blur-3xl"></div>

        <div class="mx-auto max-w-7xl px-6 lg:px-10">
          <div class="max-w-xl mx-auto text-center mb-14">
            <span class="text-[11px] font-semibold tracking-widest text-blue-600 uppercase">Fitur Unggulan</span>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">Semua yang Kamu Butuhkan</h2>
            <p class="mt-3 text-sm text-slate-400 font-light leading-relaxed">Dirancang untuk mempercepat alur administrasi — dari pengajuan hingga penerbitan dokumen resmi.</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
              ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Pengelolaan Surat', 'desc' => 'Kelola surat masuk, surat keluar, dan disposisi secara digital dalam satu platform terpusat.'],
              ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Verifikasi Multi-Level', 'desc' => 'Alur persetujuan bertingkat: Kepala Unit → Wakil Direktur → Direktur, dengan notifikasi real-time.'],
              ['icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', 'title' => 'Tanda Tangan Elektronik', 'desc' => 'Pengesahan dokumen dengan TTE terintegrasi QR Code & barcode resmi.'],
              ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Multi-Role Pengguna', 'desc' => 'Dukungan peran: Admin, Staf, Tata Usaha, Dosen, hingga Mahasiswa dengan akses terpisah.'],
              ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'title' => 'Pencarian & Arsip', 'desc' => 'Temukan dokumen lama dalam hitungan detik — filter berdasarkan jenis, tanggal, dan status.'],
              ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Dashboard & Laporan', 'desc' => 'Pantau statistik administrasi secara real-time — jumlah dokumen, status, dan rekap periodik.'],
            ] as $card)
            <div class="group rounded-2xl border border-slate-100/80 bg-white/80 backdrop-blur-sm p-6 hover:border-blue-100 hover:shadow-lg hover:shadow-blue-50/60 hover:-translate-y-1 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-sky-50 border border-blue-100/60 flex items-center justify-center mb-5 group-hover:from-blue-100 group-hover:to-sky-100 transition-all duration-200">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                </svg>
              </div>
              <h3 class="text-sm font-semibold text-slate-800 mb-1.5">{{ $card['title'] }}</h3>
              <p class="text-xs text-slate-400 font-light leading-relaxed">{{ $card['desc'] }}</p>
            </div>
            @endforeach
          </div>
        </div>
      </section>


      {{-- ============================================================
           SECTION 4: HOW IT WORKS
      ============================================================ --}}
      <section id="cara-kerja" class="py-24 sm:py-32 relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-50/80 via-blue-50/20 to-white"></div>
        <div class="absolute -z-10 bottom-0 right-0 w-[500px] h-[400px] rounded-full bg-gradient-to-tl from-sky-100/40 to-transparent blur-3xl"></div>

        <div class="mx-auto max-w-7xl px-6 lg:px-10">
          <div class="max-w-xl mx-auto text-center mb-14">
            <span class="text-[11px] font-semibold tracking-widest text-blue-600 uppercase">Cara Kerja</span>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">Proses yang Mudah & Transparan</h2>
            <p class="mt-3 text-sm text-slate-400 font-light leading-relaxed">Dari pengajuan hingga dokumen resmi diterbitkan — semua terekam dan bisa dipantau kapan saja.</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
              ['no'=>'01','title'=>'Buat Pengajuan',        'desc'=>'Pengguna mengisi form pengajuan dokumen sesuai jenis yang diperlukan.'],
              ['no'=>'02','title'=>'Verifikasi Bertingkat',  'desc'=>'Dokumen diperiksa dan disetujui secara berurutan oleh pejabat yang berwenang.'],
              ['no'=>'03','title'=>'Penandatanganan TTE',    'desc'=>'Dokumen final disahkan dengan tanda tangan elektronik resmi dan QR Code.'],
              ['no'=>'04','title'=>'Dokumen Diterbitkan',   'desc'=>'Pemohon menerima dokumen resmi yang bisa diunduh dan diarsipkan secara digital.'],
            ] as $step)
            <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-white/70 backdrop-blur-sm border border-slate-100/80 hover:border-blue-100 hover:shadow-md hover:shadow-blue-50/40 transition-all duration-300">
              <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-500 flex items-center justify-center mb-4 shadow-md shadow-blue-200/60">
                <span class="text-sm font-bold text-white">{{ $step['no'] }}</span>
              </div>
              <h3 class="text-sm font-semibold text-slate-800 mb-2">{{ $step['title'] }}</h3>
              <p class="text-xs text-slate-400 font-light leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
          </div>
        </div>
      </section>


      {{-- ============================================================
           SECTION 5: CTA
      ============================================================ --}}
      <section id="cta" class="py-20 sm:py-28">
        <div class="mx-auto max-w-5xl px-6 lg:px-8">
          <div class="relative rounded-3xl overflow-hidden px-8 py-16 sm:px-16 sm:py-20 text-center">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-500 to-sky-400"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff06_1px,transparent_1px),linear-gradient(to_bottom,#ffffff06_1px,transparent_1px)] bg-[size:40px_40px]"></div>
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-blue-800/20 blur-2xl pointer-events-none"></div>
            <div class="relative">
              <h2 class="text-3xl sm:text-4xl font-bold text-white tracking-tight text-balance leading-tight">
                Siap Transformasi Administrasi<br />di Polibatam?
              </h2>
              <p class="mt-4 max-w-md mx-auto text-sm text-white/70 font-light leading-relaxed">
                Bergabung dan rasakan kemudahan mengelola surat, SK, dan dokumen resmi — kapan saja, di mana saja.
              </p>
              <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-7 py-3.5 text-sm font-semibold text-blue-600 shadow-lg hover:bg-blue-50 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                  Masuk ke SIMAS
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>
                </a>
                <a href="#cara-kerja" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-7 py-3.5 text-sm font-medium text-white hover:bg-white/20 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                  Pelajari Lebih Lanjut
                </a>
              </div>
              <p class="mt-5 text-[11px] text-white/40 font-light">
                Khusus civitas akademika Polibatam · Tidak diperlukan registrasi mandiri
              </p>
            </div>
          </div>
        </div>
      </section>

    </main>


    {{-- ============================================================
         FOOTER
    ============================================================ --}}
    <footer id="footer" class="border-t border-slate-100/80 bg-gradient-to-b from-white to-slate-50/50">
      <div class="mx-auto max-w-7xl px-6 lg:px-10 py-12 lg:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
          <div class="lg:col-span-2">
            <a href="#" class="flex items-center gap-2.5">
  <img src="{{ asset('images/logo.png') }}"
       alt="Logo SIMAS"
       class="h-8 w-auto object-contain" />
  {{-- Hapus span di bawah ini kalau logo sudah include teks --}}
  <span class="text-base font-bold text-slate-900">SIMAS</span>
</a>
            <p class="mt-4 max-w-xs text-xs text-slate-400 font-light leading-relaxed">
              Sistem Manajemen Administrasi Surat — solusi digital tata kelola dokumen resmi Polibatam.
            </p>
          </div>
          <div>
            <h4 class="text-[11px] font-semibold tracking-widest text-slate-700 uppercase mb-4">Aplikasi</h4>
            <ul class="space-y-2.5">
              <li><a href="#"          class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Beranda</a></li>
              <li><a href="#fitur"     class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Fitur</a></li>
              <li><a href="#cara-kerja"class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Cara Kerja</a></li>
              <li><a href="#"          class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Login</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-[11px] font-semibold tracking-widest text-slate-700 uppercase mb-4">Polibatam</h4>
            <ul class="space-y-2.5">
              <li><a href="https://www.polibatam.ac.id" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">polibatam.ac.id</a></li>
              <li><a href="#" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Panduan Penggunaan</a></li>
              <li><a href="#" class="text-xs text-slate-400 hover:text-blue-600 font-light transition-colors duration-200">Hubungi Admin</a></li>
            </ul>
          </div>
        </div>
        <div class="mt-12 pt-6 border-t border-slate-100/80 flex flex-col sm:flex-row items-center justify-between gap-3">
          <p class="text-[11px] text-slate-300 font-light">&copy; 2026 SIMAS · Politeknik Negeri Batam. Dikembangkan oleh Tim PBL-TRPL201.</p>
          <p class="text-[11px] text-slate-300 font-light">Laravel · Tailwind CSS · MySQL</p>
        </div>
      </div>
    </footer>

  </body>
</html>
