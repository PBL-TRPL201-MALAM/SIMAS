<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Pemohon — SIMAS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      * { font-family: 'Poppins', sans-serif; }
      ::-webkit-scrollbar { width: 4px; height: 4px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
      ::-webkit-scrollbar-thumb:hover { background: #bfdbfe; }
    </style>
  </head>

  <body class="bg-slate-50 antialiased overflow-hidden h-screen">

  {{-- ================================================================
       TOAST NOTIFICATION — muncul di pojok kanan bawah
  ================================================================ --}}
  <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
    <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-4 py-3 shadow-xl shadow-slate-900/20 min-w-[220px]">
      <div id="toast-icon" class="w-5 h-5 shrink-0"></div>
      <p id="toast-msg" class="text-xs font-medium text-white"></p>
    </div>
  </div>

  {{-- ================================================================
       MODAL DETAIL DOKUMEN
  ================================================================ --}}
  <div id="modal-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden">
      {{-- Modal header --}}
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Detail Dokumen</h3>
          <p id="modal-jenis" class="text-[11px] text-slate-400 font-light mt-0.5"></p>
        </div>
        <button id="modal-close" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
      {{-- Modal body --}}
      <div class="px-6 py-5 space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Perihal / Judul</p>
            <p id="modal-perihal" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Diajukan</p>
            <p id="modal-tanggal" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</p>
            <p id="modal-status" class="text-xs font-medium"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Nomor Surat</p>
            <p id="modal-nomor" class="text-xs font-medium text-slate-800"></p>
          </div>
        </div>
        <div class="rounded-xl bg-slate-50 px-4 py-3">
          <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Keterangan</p>
          <p id="modal-keterangan" class="text-xs text-slate-600 font-light leading-relaxed"></p>
        </div>
      </div>
      {{-- Modal footer --}}
      <div id="modal-footer" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <button id="modal-close-btn" type="button"
          class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
          Tutup
        </button>
        <button id="modal-download-btn" type="button"
          class="hidden inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
          Unduh Dokumen
        </button>
      </div>
    </div>
  </div>

  <div class="flex h-screen w-full overflow-hidden">

    {{-- ================================================================
         SIDEBAR
    ================================================================ --}}
    <aside id="sidebar" class="relative flex flex-col w-64 shrink-0 bg-white border-r border-slate-100 h-screen overflow-y-auto z-30 transition-all duration-300">

      <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-blue-50/80 blur-2xl pointer-events-none"></div>

      {{-- Logo --}}
      {{-- Logo --}}
<div class="relative flex items-center gap-2.5 px-5 h-16 border-b border-slate-100/80 shrink-0">
  <img src="{{ asset('images/logo.png') }}"
       alt="Logo SIMAS"
       class="h-7 w-auto object-contain" />
  {{-- Hapus span ini kalau logo sudah include teks --}}
  <span class="text-sm font-bold tracking-tight text-slate-900">SIMAS</span>
</div>

      {{-- Navigasi --}}
      <nav class="flex-1 px-3 py-4 space-y-0.5">

        <p class="px-2 pb-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Menu Utama</p>

        <a href="#" data-page="dashboard"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 bg-blue-600 text-white shadow-sm shadow-blue-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span>Dashboard</span>
        </a>

        <div class="pt-3 pb-1.5">
          <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Biasa</p>
        </div>

        <a href="#" data-page="buat-surat"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          <span>Buat Surat Baru</span>
        </a>

        <a href="#" data-page="surat-saya"
           class="nav-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <div class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Surat Saya</span>
          </div>
          <span id="badge-surat" class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">3</span>
        </a>

        <div class="pt-3 pb-1.5">
          <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Keputusan</p>
        </div>

        <a href="#" data-page="buat-sk"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Buat Pengajuan SK</span>
        </a>

        <a href="#" data-page="sk-saya"
           class="nav-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <div class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
            </svg>
            <span>SK Saya</span>
          </div>
          <span id="badge-sk" class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">2</span>
        </a>

      </nav>

      {{-- Profil & Logout --}}
      <div class="px-3 py-4 border-t border-slate-100/80 shrink-0 space-y-0.5">
        <a href="#" data-page="profil"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span>Profil Saya</span>
        </a>
        <a href="{{ route('login') }}"
           class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-500 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Keluar</span>
        </a>
      </div>
    </aside>


    {{-- ================================================================
         AREA KONTEN UTAMA
    ================================================================ --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      {{-- Topbar --}}
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="lg:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 id="page-title" class="text-sm font-bold text-slate-900">Dashboard</h1>
          <p id="page-subtitle" class="text-[11px] text-slate-400 font-light">Selamat datang di SIMAS</p>
        </div>
        {{-- Icon user profil --}}
        <button type="button" data-page="profil"
          class="nav-link w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </button>
      </header>

      {{-- Konten --}}
      <main class="flex-1 overflow-y-auto p-6">


        {{-- ============================================================
             PAGE: DASHBOARD
        ============================================================ --}}
        <div id="page-dashboard" class="page-content space-y-6">

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">Halo! 👋</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Berikut ringkasan dokumen kamu hari ini.</p>
            </div>
            <div class="flex items-center gap-2">
              <a href="#" data-page="buat-surat" class="quick-nav inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-blue-200 hover:text-blue-600 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Surat Baru
              </a>
              <a href="#" data-page="buat-sk" class="quick-nav inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Pengajuan SK
              </a>
            </div>
          </div>

          {{-- Stats Cards — semua biru --}}
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">5</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total Dokumen</p>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">2</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Sedang Diproses</p>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">2</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Disetujui</p>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">1</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Ditolak / Revisi</p>
            </div>

          </div>

          {{-- Tabel + Aktivitas --}}
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Tabel dokumen terbaru --}}
            <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Dokumen Terbaru</h3>
                <a href="#" data-page="surat-saya" class="quick-nav text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat semua →</a>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="bg-slate-50/60">
                      <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                      <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Jenis</th>
                      <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                      <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-50">
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 truncate max-w-[150px]">Permohonan Izin Penelitian</p></td>
                      <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Surat Biasa</span></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                      <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diproses</span></td>
                    </tr>
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 truncate max-w-[150px]">SK Pembentukan Panitia</p></td>
                      <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">SK</span></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">08 Apr 2025</p></td>
                      <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                    </tr>
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 truncate max-w-[150px]">Permohonan Surat Keterangan</p></td>
                      <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Surat Biasa</span></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">05 Apr 2025</p></td>
                      <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Ditolak</span></td>
                    </tr>
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 truncate max-w-[150px]">SK Kegiatan Seminar</p></td>
                      <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">SK</span></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">01 Apr 2025</p></td>
                      <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            {{-- Aktivitas --}}
            <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Aktivitas Terbaru</h3>
              </div>
              <div class="px-5 py-4 space-y-4">
                <div class="flex gap-3">
                  <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                  </div>
                  <div>
                    <p class="text-xs font-medium text-slate-700">SK Panitia disetujui</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Verifikator Level 2 menyetujui</p>
                    <p class="text-[10px] text-slate-300 mt-1">08 Apr 2025, 14:32</p>
                  </div>
                </div>
                <div class="flex gap-3">
                  <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                  </div>
                  <div>
                    <p class="text-xs font-medium text-slate-700">Surat Keterangan ditolak</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Admin/TU meminta revisi</p>
                    <p class="text-[10px] text-slate-300 mt-1">05 Apr 2025, 09:15</p>
                  </div>
                </div>
                <div class="flex gap-3">
                  <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                  </div>
                  <div>
                    <p class="text-xs font-medium text-slate-700">Surat diajukan</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Permohonan izin penelitian</p>
                    <p class="text-[10px] text-slate-300 mt-1">10 Apr 2025, 10:00</p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>


        {{-- ============================================================
             PAGE: BUAT SURAT BARU — 2 STEP WIZARD
        ============================================================ --}}
        <div id="page-buat-surat" class="page-content hidden">
          <div class="max-w-2xl mx-auto">

            {{-- Step indicator --}}
            <div class="flex items-center mb-6">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="surat-step2-circle-wrap">
                  <span class="text-[11px] font-bold text-white">1</span>
                </div>
                <span class="text-xs font-semibold text-blue-600">Upload Draft</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="surat-step2-circle">
                  <span class="text-[11px] font-bold text-slate-400">2</span>
                </div>
                <span class="text-xs font-medium text-slate-400" id="surat-step2-label">Data Surat</span>
              </div>
            </div>

            {{-- Step 1 --}}
            <div id="surat-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 1 — Upload Draft Surat</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Siapkan draft surat dalam format DOCX lalu unggah ke sistem.</p>
              </div>
              <div class="px-6 py-6 space-y-5">
                <div class="rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                  <p class="text-[11px] font-semibold text-blue-700 mb-1.5">Yang perlu disiapkan:</p>
                  <ul class="space-y-1">
                    <li class="text-[11px] text-blue-600 font-light flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-blue-400 shrink-0"></span>Buat draft surat menggunakan Microsoft Word</li>
                    <li class="text-[11px] text-blue-600 font-light flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-blue-400 shrink-0"></span>Simpan file dalam format <strong>.DOCX</strong></li>
                    <li class="text-[11px] text-blue-600 font-light flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-blue-400 shrink-0"></span>Ukuran file maksimal 10 MB</li>
                  </ul>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">File Draft Surat (DOCX) <span class="text-blue-400">*</span></label>
                  <div id="surat-drop-zone" class="relative flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-6 py-10 hover:border-blue-300 hover:bg-blue-50/30 transition-all duration-200 cursor-pointer">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                      <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    </div>
                    <div class="text-center">
                      <p class="text-xs font-semibold text-slate-700">Klik atau seret file ke sini</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Format: DOCX · Maks. 10 MB</p>
                    </div>
                    <input id="surat-file-input" type="file" accept=".docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                  </div>
                  <div id="surat-file-preview" class="hidden flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-2.5 mt-2">
                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p id="surat-file-name" class="text-[11px] font-medium text-blue-700 truncate"></p>
                    <button id="surat-file-remove" type="button" class="ml-auto text-slate-400 hover:text-slate-600 transition-colors duration-200 shrink-0">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                  </div>
                </div>
                <div class="flex justify-end pt-2">
                  <button id="surat-next-btn" type="button" disabled
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-40 disabled:cursor-not-allowed disabled:transform-none transition-all duration-200">
                    Lanjut
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>

            {{-- Step 2 --}}
            <div id="surat-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 2 — Data Surat</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Lengkapi informasi awal surat yang akan diajukan.</p>
              </div>
              <form action="#" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
                @csrf
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-2.5">
                  <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                  <p id="surat-step2-filename" class="text-[11px] font-medium text-slate-600 truncate"></p>
                  <button id="surat-back-btn" type="button" class="ml-auto text-[10px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200 shrink-0">Ganti file</button>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Perihal <span class="text-blue-400">*</span></label>
                  <input type="text" name="perihal" placeholder="Contoh: Permohonan Izin Penelitian Lapangan"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  <p class="text-[10px] text-slate-400 font-light">Tuliskan pokok/inti dari surat yang diajukan.</p>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Ringkasan Isi Surat <span class="text-blue-400">*</span></label>
                  <textarea name="ringkasan" rows="5" placeholder="Tuliskan ringkasan isi surat secara singkat dan jelas..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                  <p class="text-[10px] text-slate-400 font-light">Ringkasan ini membantu Admin/TU memahami isi surat sebelum memeriksa file DOCX.</p>
                </div>
                <div class="flex items-center justify-between pt-2">
                  <button id="surat-back-btn-2" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    Kembali
                  </button>
                  <button id="surat-submit-btn" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Ajukan Surat
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </form>
            </div>

          </div>
        </div>


        {{-- ============================================================
             PAGE: BUAT PENGAJUAN SK — 3 STEP WIZARD
        ============================================================ --}}
        <div id="page-buat-sk" class="page-content hidden">
          <div class="max-w-2xl mx-auto">

            {{-- Step indicator --}}
            <div class="flex items-center mb-6">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="sk-circle-1"><span class="text-[11px] font-bold text-white">1</span></div>
                <span class="text-xs font-semibold text-blue-600" id="sk-label-1">Data SK</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-circle-2"><span class="text-[11px] font-bold text-slate-400">2</span></div>
                <span class="text-xs font-medium text-slate-400" id="sk-label-2">Dasar Hukum</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-circle-3"><span class="text-[11px] font-bold text-slate-400">3</span></div>
                <span class="text-xs font-medium text-slate-400" id="sk-label-3">Review</span>
              </div>
            </div>

            {{-- Step 1 --}}
            <div id="sk-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 1 — Data Surat Keputusan</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Isi informasi utama Surat Keputusan yang akan diajukan.</p>
              </div>
              <div class="px-6 py-6 space-y-5">
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Judul SK <span class="text-blue-400">*</span></label>
                  <input id="sk-judul" type="text" placeholder="Contoh: SK Pembentukan Panitia Seminar Nasional 2025"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Tentang <span class="text-blue-400">*</span></label>
                  <input id="sk-tentang" type="text" placeholder="Contoh: Pembentukan Panitia Seminar Nasional Tahun 2025"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  <p class="text-[10px] text-slate-400 font-light">Deskripsi singkat isi SK yang akan diterbitkan.</p>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Menimbang <span class="text-blue-400">*</span></label>
                  <textarea id="sk-menimbang" rows="4" placeholder="Contoh:&#10;a. bahwa dalam rangka pelaksanaan Seminar Nasional perlu dibentuk panitia;&#10;b. bahwa untuk kelancaran pelaksanaan kegiatan tersebut perlu ditetapkan Surat Keputusan;"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                  <p class="text-[10px] text-slate-400 font-light">Tuliskan butir-butir pertimbangan (a, b, c, ...) secara manual.</p>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Memutuskan <span class="text-blue-400">*</span></label>
                  <textarea id="sk-memutuskan" rows="5" placeholder="Contoh:&#10;PERTAMA : Membentuk Panitia Seminar Nasional Tahun 2025;&#10;KEDUA   : Panitia bertugas merencanakan dan melaksanakan kegiatan;&#10;KETIGA  : SK ini berlaku sejak tanggal ditetapkan;"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                  <p class="text-[10px] text-slate-400 font-light">Tuliskan diktum keputusan (PERTAMA, KEDUA, ...) secara manual.</p>
                </div>
                <div class="flex justify-end pt-2">
                  <button id="sk-next-1" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut — Pilih Dasar Hukum
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>

            {{-- Step 2 --}}
            <div id="sk-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 2 — Dasar Hukum (Mengingat)</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Pilih dasar hukum yang relevan dari master yang tersedia.</p>
              </div>
              <div class="px-6 py-6 space-y-4">
                <div class="relative">
                  <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                  <input id="sk-search-dasar" type="text" placeholder="Cari dasar hukum..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="space-y-2 max-h-72 overflow-y-auto pr-1" id="sk-dasar-list">
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="1" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">UU No. 20 Tahun 2003</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Sistem Pendidikan Nasional</p></div>
                  </label>
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="2" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">PP No. 4 Tahun 2014</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Penyelenggaraan Pendidikan Tinggi dan Pengelolaan Perguruan Tinggi</p></div>
                  </label>
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="3" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Permenristekdikti No. 44 Tahun 2015</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Standar Nasional Pendidikan Tinggi</p></div>
                  </label>
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="4" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Statuta Polibatam Tahun 2020</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Statuta Politeknik Negeri Batam</p></div>
                  </label>
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="5" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Peraturan Direktur Polibatam No. 01 Tahun 2023</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Tata Kelola Administrasi Polibatam</p></div>
                  </label>
                </div>
                <p class="text-[10px] text-slate-400 font-light">Pilih satu atau lebih dasar hukum yang relevan.</p>
                <div class="flex items-center justify-between pt-2">
                  <button id="sk-back-1" type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>Kembali
                  </button>
                  <button id="sk-next-2" type="button" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut — Review<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>

            {{-- Step 3 --}}
            <div id="sk-step-3" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 3 — Review & Submit</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Periksa kembali data sebelum mengajukan SK.</p>
              </div>
              <div class="px-6 py-6 space-y-4">
                <div class="space-y-3">
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Judul SK</p><p id="review-judul" class="text-sm font-medium text-slate-800">—</p></div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tentang</p><p id="review-tentang" class="text-sm font-medium text-slate-800">—</p></div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Menimbang</p><p id="review-menimbang" class="text-xs text-slate-600 font-light whitespace-pre-line">—</p></div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Mengingat</p><ul id="review-mengingat" class="space-y-1"></ul></div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Memutuskan</p><p id="review-memutuskan" class="text-xs text-slate-600 font-light whitespace-pre-line">—</p></div>
                </div>
                <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-center gap-3">
                  <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <p class="text-[11px] text-blue-600 font-light">Setelah disubmit, status SK menjadi <strong>Diajukan</strong> dan menunggu review Admin/TU.</p>
                </div>
                <div class="flex items-center justify-between pt-2">
                  <button id="sk-back-2" type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>Kembali
                  </button>
                  <button id="sk-submit-btn" type="button" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Submit Pengajuan SK<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>


        {{-- ============================================================
             PAGE: SURAT SAYA — Tabel + Filter + Aksi
        ============================================================ --}}
        <div id="page-surat-saya" class="page-content hidden space-y-4">

          {{-- Header + Filter --}}
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">Surat Saya</h2>
            <div class="flex items-center gap-2 flex-wrap">
              {{-- Filter status --}}
              <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
                <button data-filter="semua" data-target="surat" class="filter-btn active-filter rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
                <button data-filter="diproses" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
                <button data-filter="published" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Published</button>
                <button data-filter="ditolak" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Ditolak</button>
              </div>
              {{-- Buat baru --}}
              <a href="#" data-page="buat-surat" class="quick-nav inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Buat Baru
              </a>
            </div>
          </div>

          {{-- Tabel --}}
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full" id="tabel-surat">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="tbody-surat">

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="diproses" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-tanggal="10 Apr 2025" data-nomor="—" data-keterangan="Dokumen sedang diperiksa oleh Admin/TU. Menunggu upload PDF dan pengaturan data surat.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diproses</span></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                    </td>
                  </tr>

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="diproses" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Magang" data-tanggal="02 Apr 2025" data-nomor="—" data-keterangan="Dokumen sedang dalam proses verifikasi Level 1.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">Permohonan Izin Magang</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">02 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diproses</span></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                    </td>
                  </tr>

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="published" data-jenis="Surat Biasa" data-perihal="Surat Keterangan Mahasiswa Aktif" data-tanggal="20 Mar 2025" data-nomor="001/SIMAS/TU/2025" data-keterangan="Surat telah diterbitkan dan dapat diunduh.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">Surat Keterangan Mahasiswa Aktif</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">20 Mar 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-3">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                      <button type="button" class="btn-download inline-flex items-center gap-1 text-[11px] font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Unduh
                      </button>
                    </td>
                  </tr>

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="ditolak" data-jenis="Surat Biasa" data-perihal="Permohonan Surat Keterangan" data-tanggal="05 Apr 2025" data-nomor="—" data-keterangan="Dokumen ditolak oleh Admin/TU. Alasan: Format surat tidak sesuai standar Polibatam. Harap perbaiki dan ajukan kembali.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">Permohonan Surat Keterangan</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">05 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Ditolak</span></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
            {{-- Empty state --}}
            <div id="surat-empty" class="hidden flex flex-col items-center justify-center py-16 text-center">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-sm font-semibold text-slate-600">Tidak ada surat</p>
              <p class="text-xs text-slate-400 font-light mt-1">Belum ada surat dengan status ini.</p>
            </div>
          </div>
        </div>


        {{-- ============================================================
             PAGE: SK SAYA — Tabel + Filter + Aksi
        ============================================================ --}}
        <div id="page-sk-saya" class="page-content hidden space-y-4">

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">SK Saya</h2>
            <div class="flex items-center gap-2 flex-wrap">
              <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
                <button data-filter="semua" data-target="sk" class="filter-btn active-filter rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
                <button data-filter="diproses" data-target="sk" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
                <button data-filter="published" data-target="sk" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Published</button>
                <button data-filter="ditolak" data-target="sk" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Ditolak</button>
              </div>
              <a href="#" data-page="buat-sk" class="quick-nav inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Buat Baru
              </a>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full" id="tabel-sk">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="tbody-sk">

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="published" data-jenis="SK" data-perihal="SK Pembentukan Panitia Seminar" data-tanggal="08 Apr 2025" data-nomor="SK/002/SIMAS/2025" data-keterangan="SK telah diterbitkan dan dapat diunduh. Ditandatangani oleh Direktur Polibatam.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">SK Pembentukan Panitia Seminar</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">08 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-3">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                      <button type="button" class="btn-download inline-flex items-center gap-1 text-[11px] font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Unduh
                      </button>
                    </td>
                  </tr>

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="diproses" data-jenis="SK" data-perihal="SK Kegiatan KKN 2025" data-tanggal="10 Apr 2025" data-nomor="—" data-keterangan="SK sedang dalam proses review oleh Admin/TU.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">SK Kegiatan KKN 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diproses</span></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
            <div id="sk-empty" class="hidden flex flex-col items-center justify-center py-16 text-center">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2" /></svg>
              </div>
              <p class="text-sm font-semibold text-slate-600">Tidak ada SK</p>
              <p class="text-xs text-slate-400 font-light mt-1">Belum ada SK dengan status ini.</p>
            </div>
          </div>
        </div>


        {{-- ============================================================
             PAGE: PROFIL — Placeholder
        ============================================================ --}}
        <div id="page-profil" class="page-content hidden">
          <div class="flex flex-col items-center justify-center h-64 text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            </div>
            <p class="text-sm font-semibold text-slate-700">Profil Saya</p>
            <p class="text-xs text-slate-400 font-light mt-1">Halaman ini akan diisi di step berikutnya.</p>
          </div>
        </div>

      </main>
    </div>
  </div>
  </body>
</html>
