<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin/TU — SIMAS</title>

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

  <!-- ================================================================
       TOAST NOTIFICATION
  ================================================================ -->
  <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
    <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-4 py-3 shadow-xl min-w-[220px]">
      <div id="toast-icon" class="w-5 h-5 shrink-0"></div>
      <p id="toast-msg" class="text-xs font-medium text-white"></p>
    </div>
  </div>

  <!-- ================================================================
       MODAL DETAIL DOKUMEN
  ================================================================ -->
  <div id="modal-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Detail Pengajuan</h3>
          <p id="modal-jenis" class="text-[11px] text-slate-400 font-light mt-0.5"></p>
        </div>
        <button id="modal-close" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
      <div class="px-6 py-5 space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Perihal / Judul</p>
            <p id="modal-perihal" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
            <p id="modal-pemohon" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Diajukan</p>
            <p id="modal-tanggal" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</p>
            <p id="modal-status" class="text-xs font-medium text-slate-800"></p>
          </div>
        </div>
        <div class="rounded-xl bg-slate-50 px-4 py-3">
          <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Ringkasan</p>
          <p id="modal-ringkasan" class="text-xs text-slate-600 font-light leading-relaxed"></p>
        </div>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <button id="modal-close-btn" type="button"
          class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
          Tutup
        </button>
        <div class="flex items-center gap-2">
          <button id="modal-download-docx" type="button"
            class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            Unduh DOCX
          </button>
          <button id="modal-proses-btn" type="button"
            class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            Proses Surat
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="flex h-screen w-full overflow-hidden">

    <!-- ================================================================
         SIDEBAR ADMIN/TU
    ================================================================ -->
    <aside id="sidebar" class="relative flex flex-col w-64 shrink-0 bg-white border-r border-slate-100 h-screen overflow-y-auto z-30 transition-all duration-300">

      <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-blue-50/80 blur-2xl pointer-events-none"></div>

      <!-- Logo -->
      <!-- Logo -->
<div class="relative flex items-center gap-2.5 px-5 h-16 border-b border-slate-100/80 shrink-0">
  <img src="{{ asset('images/logo.png') }}"
       alt="Logo SIMAS"
       class="h-7 w-auto object-contain" />
  <div>
    <!-- Hapus span pertama ini kalau logo sudah include teks -->
    <span class="text-sm font-bold tracking-tight text-slate-900 block">SIMAS</span>
    <span class="text-[10px] font-medium text-blue-500">Admin / TU</span>
  </div>
</div>

      <!-- Nav -->
      <nav class="flex-1 px-3 py-4 space-y-0.5">

        <p class="px-2 pb-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Menu Utama</p>

        <a href="#" data-page="dashboard"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 bg-blue-600 text-white shadow-sm shadow-blue-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span>Dashboard</span>
        </a>

        <!-- Surat Biasa -->
        <div class="pt-3 pb-1.5">
          <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Biasa</p>
        </div>

        <a href="#" data-page="pengajuan-masuk"
           class="nav-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <div class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <span>Pengajuan Masuk</span>
          </div>
          <span class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">4</span>
        </a>

        <a href="#" data-page="semua-surat"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Semua Surat</span>
        </a>

        <!-- SK -->
        <div class="pt-3 pb-1.5">
          <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Keputusan</p>
        </div>

        <a href="#" data-page="pengajuan-sk"
           class="nav-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <div class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <span>Pengajuan SK Masuk</span>
          </div>
          <span class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">2</span>
        </a>

        <a href="#" data-page="semua-sk"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
          </svg>
          <span>Semua SK</span>
        </a>

        <!-- Master Data -->
        <div class="pt-3 pb-1.5">
          <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Master Data</p>
        </div>

        <a href="#" data-page="master-dasar-hukum"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          <span>Master Dasar Hukum</span>
        </a>

      </nav>

      <!-- Profil & Logout -->
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


    <!-- ================================================================
         AREA KONTEN UTAMA
    ================================================================ -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <!-- Topbar -->
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="lg:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 id="page-title" class="text-sm font-bold text-slate-900">Dashboard</h1>
          <p id="page-subtitle" class="text-[11px] text-slate-400 font-light">Selamat datang, Admin/TU</p>
        </div>
        <button type="button" data-page="profil"
          class="nav-link w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </button>
      </header>

      <main class="flex-1 overflow-y-auto p-6">


        <!-- ============================================================
             PAGE: DASHBOARD
        ============================================================ -->
        <div id="page-dashboard" class="page-content space-y-6">

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">Halo, Admin/TU! 👋</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Ada <strong class="text-blue-600">4 pengajuan</strong> yang menunggu diproses hari ini.</p>
            </div>
            <a href="#" data-page="pengajuan-masuk" class="quick-nav inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
              Lihat Pengajuan
            </a>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">4</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Pengajuan Masuk</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">7</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Sedang Diproses</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">12</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Published</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">23</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total Dokumen</p>
            </div>
          </div>

          <!-- Tabel pengajuan terbaru -->
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 class="text-sm font-semibold text-slate-800">Pengajuan Masuk Terbaru</h3>
              <a href="#" data-page="pengajuan-masuk" class="quick-nav text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat semua →</a>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Jenis</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-pemohon="Ahmad Fauzi" data-tanggal="10 Apr 2025" data-status="Diajukan" data-ringkasan="Pemohon mengajukan izin penelitian untuk keperluan tugas akhir di wilayah Batam.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 truncate max-w-[150px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Surat Biasa</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <button type="button" class="btn-proses inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">
                        Proses
                      </button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Magang" data-pemohon="Siti Rahma" data-tanggal="09 Apr 2025" data-status="Diajukan" data-ringkasan="Pemohon mengajukan surat izin magang di perusahaan teknologi selama 3 bulan.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Siti Rahma</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 truncate max-w-[150px]">Permohonan Izin Magang</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Surat Biasa</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">09 Apr 2025</p></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <button type="button" class="btn-proses inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">
                        Proses
                      </button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-jenis="SK" data-perihal="SK Kegiatan KKN 2025" data-pemohon="Budi Santoso" data-tanggal="08 Apr 2025" data-status="Diajukan" data-ringkasan="Pengajuan SK untuk kegiatan KKN mahasiswa semester genap 2025.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Budi Santoso</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 truncate max-w-[150px]">SK Kegiatan KKN 2025</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">SK</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">08 Apr 2025</p></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <button type="button" class="btn-proses inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">
                        Proses
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: PENGAJUAN MASUK (Surat Biasa)
        ============================================================ -->
        <div id="page-pengajuan-masuk" class="page-content hidden space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">Pengajuan Surat Masuk</h2>
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
              <button data-filter="semua" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
              <button data-filter="diajukan" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diajukan</button>
              <button data-filter="diproses" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="tbody-pengajuan">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-filter-status="diajukan" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-pemohon="Ahmad Fauzi" data-tanggal="10 Apr 2025" data-status="Diajukan" data-ringkasan="Pemohon mengajukan izin penelitian untuk keperluan tugas akhir di wilayah Batam.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <button type="button" class="btn-proses text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-filter-status="diajukan" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Magang" data-pemohon="Siti Rahma" data-tanggal="09 Apr 2025" data-status="Diajukan" data-ringkasan="Pemohon mengajukan surat izin magang di perusahaan teknologi selama 3 bulan.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Siti Rahma</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">Permohonan Izin Magang</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">09 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <button type="button" class="btn-proses text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-filter-status="diproses" data-jenis="Surat Biasa" data-perihal="Surat Keterangan Aktif Kuliah" data-pemohon="Rina Dewi" data-tanggal="07 Apr 2025" data-status="Diproses" data-ringkasan="Surat keterangan mahasiswa aktif untuk keperluan beasiswa.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Rina Dewi</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">Surat Keterangan Aktif Kuliah</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">07 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Diproses</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: PROSES SURAT — MULTI-STEP WIZARD 3 STEP
             Step 1: Upload PDF + Isi Metadata
             Step 2: Atur Posisi Elemen (nomor, tanggal, TTE)
             Step 3: Tentukan Tingkat Verifikasi
        ============================================================ -->
        <div id="page-proses-surat" class="page-content hidden">
          <div class="max-w-2xl mx-auto">

            <!-- Info surat yang diproses -->
            <div id="proses-info-bar" class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3 mb-5">
              <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              <div class="min-w-0">
                <p class="text-[11px] font-semibold text-blue-700">Sedang memproses surat:</p>
                <p id="proses-perihal-info" class="text-[11px] text-blue-600 font-light truncate">—</p>
              </div>
              <button id="proses-back-to-list" type="button" class="ml-auto text-[10px] font-medium text-blue-500 hover:text-blue-700 shrink-0 transition-colors duration-200">← Kembali ke daftar</button>
            </div>

            <!-- Step Indicator -->
            <div class="flex items-center mb-6">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="proses-circle-1"><span class="text-[11px] font-bold text-white">1</span></div>
                <span class="text-xs font-semibold text-blue-600" id="proses-label-1">Upload & Metadata</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="proses-circle-2"><span class="text-[11px] font-bold text-slate-400">2</span></div>
                <span class="text-xs font-medium text-slate-400" id="proses-label-2">Posisi Elemen</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="proses-circle-3"><span class="text-[11px] font-bold text-slate-400">3</span></div>
                <span class="text-xs font-medium text-slate-400" id="proses-label-3">Verifikasi</span>
              </div>
            </div>


            <!-- STEP 1: Upload PDF + Metadata -->
            <div id="proses-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 1 — Upload PDF & Data Surat</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Unggah PDF hasil pemeriksaan lalu lengkapi metadata surat.</p>
              </div>
              <div class="px-6 py-6 space-y-5">

                <!-- Upload PDF -->
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Unggah Draf PDF <span class="text-blue-400">*</span></label>
                  <div id="pdf-drop-zone" class="relative flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-6 py-8 hover:border-blue-300 hover:bg-blue-50/30 transition-all duration-200 cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                      <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    </div>
                    <div class="text-center">
                      <p class="text-xs font-semibold text-slate-700">Klik atau seret file PDF ke sini</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Format: PDF · Maks. 10 MB</p>
                    </div>
                    <input id="pdf-file-input" type="file" accept=".pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                  </div>
                  <div id="pdf-file-preview" class="hidden flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-2.5">
                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p id="pdf-file-name" class="text-[11px] font-medium text-blue-700 truncate"></p>
                    <button id="pdf-file-remove" type="button" class="ml-auto text-slate-400 hover:text-slate-600 shrink-0 transition-colors duration-200">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                  </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                  <p class="text-xs font-semibold text-slate-700 mb-4">Metadata Surat</p>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- 1. Unit Kerja -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Unit Kerja / Unit Pengolah <span class="text-blue-400">*</span></label>
                      <input type="text" name="unit_kerja" placeholder="Contoh: Tata Usaha Jurusan TRPL"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                    </div>

                    <!-- 2. Penanda Tangan -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Penanda Tangan <span class="text-blue-400">*</span></label>
                      <select name="penanda_tangan" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="" disabled selected>Pilih penanda tangan</option>
                        <option>Direktur Polibatam</option>
                        <option>Wakil Direktur I</option>
                        <option>Wakil Direktur II</option>
                        <option>Wakil Direktur III</option>
                        <option>Kepala Jurusan</option>
                      </select>
                    </div>

                    <!-- 3. Templat Surat -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Templat Surat</label>
                      <select name="templat" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="" disabled selected>Pilih templat</option>
                        <option>Templat Surat Resmi Polibatam</option>
                        <option>Templat Surat Keterangan</option>
                        <option>Templat Surat Permohonan</option>
                      </select>
                    </div>

                    <!-- 4. Versi Kop Surat -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Versi Kop Surat</label>
                      <select name="versi_kop" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="" disabled selected>Pilih versi kop</option>
                        <option>Kop Polibatam Standar</option>
                        <option>Kop Jurusan TRPL</option>
                        <option>Kop Tanpa Logo</option>
                      </select>
                    </div>

                    <!-- 5. Jenis -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Jenis <span class="text-blue-400">*</span></label>
                      <select name="jenis" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="" disabled selected>Pilih jenis surat</option>
                        <option>Surat Keterangan</option>
                        <option>Surat Permohonan</option>
                        <option>Surat Izin</option>
                        <option>Surat Pengantar</option>
                        <option>Surat Tugas</option>
                      </select>
                    </div>

                    <!-- 6. Sifat -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Sifat <span class="text-blue-400">*</span></label>
                      <select name="sifat" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="" disabled selected>Pilih sifat</option>
                        <option>Biasa</option>
                        <option>Penting</option>
                        <option>Segera</option>
                        <option>Sangat Segera</option>
                        <option>Rahasia</option>
                      </select>
                    </div>

                    <!-- 7. Kode Hal -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Kode Hal</label>
                      <input type="text" name="kode_hal" placeholder="Contoh: ADM/001"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                    </div>

                    <!-- 8. Hal -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Hal <span class="text-blue-400">*</span></label>
                      <input type="text" name="hal" placeholder="Contoh: Permohonan Izin Penelitian"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                    </div>

                  </div>

                  <!-- Full width fields -->
                  <div class="space-y-4 mt-4">

                    <!-- 9. Kepada/Tujuan -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Kepada / Tujuan <span class="text-blue-400">*</span></label>
                      <input type="text" name="tujuan" placeholder="Contoh: Yth. Kepala Dinas Pendidikan Kota Batam"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                    </div>

                    <!-- 10. Isi/Ringkasan -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Isi / Ringkasan <span class="text-blue-400">*</span></label>
                      <textarea name="isi_ringkasan" rows="3" placeholder="Tuliskan ringkasan isi surat secara singkat..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                    </div>

                    <!-- 11. Tembusan -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Tembusan</label>
                      <textarea name="tembusan" rows="2" placeholder="Contoh:&#10;1. Direktur Polibatam&#10;2. Wakil Direktur II"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                    </div>

                    <!-- 12. Keterangan Tambahan -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Keterangan Tambahan</label>
                      <textarea name="keterangan_tambahan" rows="2" placeholder="Catatan atau informasi tambahan jika diperlukan..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                    </div>

                    <!-- Grid 2 kolom lagi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                      <!-- 13. Pengonsep Surat -->
                      <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-wide">Pengonsep Surat</label>
                        <input type="text" name="pengonsep" placeholder="Nama pengonsep surat"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                      </div>

                      <!-- 14. Pemberkasan -->
                      <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-wide">Pemberkasan (Nama Arsip)</label>
                        <input type="text" name="pemberkasan" placeholder="Nama arsip / judul berkas"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                      </div>

                    </div>

                    <!-- 15. Dilihat Publik -->
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3">
                      <div>
                        <p class="text-xs font-semibold text-slate-700">Dilihat Publik</p>
                        <p class="text-[10px] text-slate-400 font-light mt-0.5">Aktifkan jika surat ini dapat diakses oleh umum</p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dilihat_publik" class="sr-only peer" />
                        <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 transition-colors duration-200"></div>
                        <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                      </label>
                    </div>

                    <!-- 16. Lampiran -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Lampiran</label>
                      <input type="file" name="lampiran" multiple
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-500 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100" />
                      <p class="text-[10px] text-slate-400 font-light">Bisa memilih lebih dari satu file lampiran.</p>
                    </div>

                  </div>
                </div>

                <div class="flex justify-end pt-2">
                  <button id="proses-next-1" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut — Atur Posisi Elemen
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>


            <!-- STEP 2: Atur Posisi Elemen -->
            <div id="proses-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 2 — Atur Posisi Elemen</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan posisi penempatan nomor surat, tanggal, dan TTE pada dokumen PDF.</p>
              </div>
              <div class="px-6 py-6 space-y-5">

                <!-- Info -->
                <div class="rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                  <p class="text-[11px] font-semibold text-blue-700 mb-1">Catatan penting:</p>
                  <p class="text-[11px] text-blue-600 font-light">Posisi yang diatur di sini hanya disimpan sebagai data koordinat. Elemen belum digenerate ke dokumen final — generate dilakukan setelah semua verifikator menyetujui.</p>
                </div>

                <!-- Nomor Surat -->
                <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                  <p class="text-xs font-semibold text-slate-700">📄 Nomor Surat</p>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi X (dari kiri, px)</label>
                      <input type="number" name="nomor_x" placeholder="Contoh: 120" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi Y (dari atas, px)</label>
                      <input type="number" name="nomor_y" placeholder="Contoh: 85" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                    </div>
                  </div>
                </div>

                <!-- Tanggal Surat -->
                <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                  <p class="text-xs font-semibold text-slate-700">📅 Tanggal Surat</p>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi X (dari kiri, px)</label>
                      <input type="number" name="tanggal_x" placeholder="Contoh: 120" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi Y (dari atas, px)</label>
                      <input type="number" name="tanggal_y" placeholder="Contoh: 110" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                    </div>
                  </div>
                </div>

                <!-- TTE -->
                <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                  <p class="text-xs font-semibold text-slate-700">✍️ TTE (Tanda Tangan Elektronik)</p>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi X (dari kiri, px)</label>
                      <input type="number" name="tte_x" placeholder="Contoh: 380" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi Y (dari atas, px)</label>
                      <input type="number" name="tte_y" placeholder="Contoh: 680" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Lebar TTE (px)</label>
                      <input type="number" name="tte_w" placeholder="Contoh: 120" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Tinggi TTE (px)</label>
                      <input type="number" name="tte_h" placeholder="Contoh: 60" min="0"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                    </div>
                  </div>
                </div>

                <p class="text-[10px] text-slate-400 font-light">* Koordinat dalam satuan pixel dari pojok kiri atas halaman dokumen PDF.</p>

                <div class="flex items-center justify-between pt-2">
                  <button id="proses-back-1" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    Kembali
                  </button>
                  <button id="proses-next-2" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut — Tentukan Verifikasi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>


            <!-- STEP 3: Tentukan Tingkat Verifikasi -->
            <div id="proses-step-3" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 3 — Tingkat Verifikasi</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan jalur verifikasi dokumen sebelum dikirim ke verifikator.</p>
              </div>
              <div class="px-6 py-6 space-y-5">

                <!-- Pilih jalur -->
                <div class="space-y-2">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Jalur Verifikasi <span class="text-blue-400">*</span></label>

                  <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="radio" name="jalur_verifikasi" value="1" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 saja</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Dokumen hanya perlu disetujui oleh Verifikator Level 1</p>
                    </div>
                  </label>

                  <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="radio" name="jalur_verifikasi" value="2" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 → Level 2</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Dokumen harus disetujui oleh Level 1 terlebih dahulu, lalu diteruskan ke Level 2</p>
                    </div>
                  </label>

                  <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="radio" name="jalur_verifikasi" value="3" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 → Level 2 → Level 3</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Dokumen harus melewati tiga tingkat persetujuan secara berurutan</p>
                    </div>
                  </label>
                </div>

                <!-- Pilih verifikator per level -->
                <div class="space-y-3">
                  <p class="text-xs font-semibold text-slate-700">Pilih Verifikator</p>

                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 1 <span class="text-blue-400">*</span></label>
                    <select name="verifikator_1" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                      <option value="" disabled selected>Pilih verifikator level 1</option>
                      <option>Kepala Jurusan TRPL</option>
                      <option>Kepala Jurusan TI</option>
                      <option>Wakil Direktur I</option>
                      <option>Wakil Direktur II</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 2</label>
                    <select name="verifikator_2" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                      <option value="">— Tidak ada (jika hanya 1 level) —</option>
                      <option>Wakil Direktur I</option>
                      <option>Wakil Direktur II</option>
                      <option>Wakil Direktur III</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 3</label>
                    <select name="verifikator_3" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                      <option value="">— Tidak ada (jika hanya 1-2 level) —</option>
                      <option>Direktur Polibatam</option>
                    </select>
                  </div>
                </div>

                <!-- Info -->
                <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-start gap-3">
                  <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <p class="text-[11px] text-blue-600 font-light leading-relaxed">Setelah dikirim, sistem akan meneruskan dokumen ke verifikator pertama. Jika ditolak oleh salah satu verifikator, proses berhenti dan dokumen dikembalikan untuk diperbaiki.</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                  <button id="proses-back-2" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    Kembali
                  </button>
                  <button id="proses-submit" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Kirim ke Verifikator
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                  </button>
                </div>

              </div>
            </div>

          </div>
        </div>


        <!-- ============================================================
             PAGE: SEMUA SURAT
        ============================================================ -->
        <div id="page-semua-surat" class="page-content hidden space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Semua Surat Biasa</h2>
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
              <button data-filter="semua" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
              <button data-filter="diajukan" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diajukan</button>
              <button data-filter="diproses" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
              <button data-filter="verifikasi" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Verifikasi</button>
              <button data-filter="published" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Published</button>
            </div>
          </div>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Nomor Surat</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[160px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">—</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span></td>
                    <td class="px-5 py-3.5"><button type="button" class="btn-proses text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</button></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Rina Dewi</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[160px]">Surat Keterangan Aktif Kuliah</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">001/SIMAS/TU/2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                    <td class="px-5 py-3.5"><button type="button" class="text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: PENGAJUAN SK MASUK
        ============================================================ -->
        <div id="page-pengajuan-sk" class="page-content hidden space-y-4">
          <h2 class="text-sm font-bold text-slate-900">Pengajuan SK Masuk</h2>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-jenis="SK" data-perihal="SK Kegiatan KKN 2025" data-pemohon="Budi Santoso" data-tanggal="08 Apr 2025" data-status="Diajukan" data-ringkasan="Pengajuan SK untuk kegiatan KKN mahasiswa semester genap 2025.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Budi Santoso</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">SK Kegiatan KKN 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">08 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <button type="button" class="btn-proses-sk text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Review & Proses</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-jenis="SK" data-perihal="SK Seminar Nasional 2025" data-pemohon="Dewi Lestari" data-tanggal="06 Apr 2025" data-status="Diajukan" data-ringkasan="SK pembentukan panitia seminar nasional bidang teknologi informasi.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Dewi Lestari</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">SK Seminar Nasional 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">06 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <button type="button" class="btn-proses-sk text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Review & Proses</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SEMUA SK
        ============================================================ -->
        <div id="page-semua-sk" class="page-content hidden space-y-4">
          <h2 class="text-sm font-bold text-slate-900">Semua Surat Keputusan</h2>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Nomor SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Rizki Pratama</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">SK Pembentukan Panitia Seminar</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">SK/002/SIMAS/2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: MASTER DASAR HUKUM
        ============================================================ -->
        <div id="page-master-dasar-hukum" class="page-content hidden space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Master Dasar Hukum</h2>
            <button id="btn-tambah-dasar" type="button"
              class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
              Tambah Dasar Hukum
            </button>
          </div>

          <!-- Form tambah (toggle via JS) -->
          <div id="form-tambah-dasar" class="hidden rounded-2xl bg-white border border-blue-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h3 class="text-sm font-bold text-slate-900">Tambah Dasar Hukum Baru</h3>
            </div>
            <div class="px-6 py-5 space-y-4">
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 tracking-wide">Nama Peraturan <span class="text-blue-400">*</span></label>
                <input type="text" id="input-nama-dasar" placeholder="Contoh: UU No. 20 Tahun 2003"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
              </div>
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 tracking-wide">Tentang <span class="text-blue-400">*</span></label>
                <input type="text" id="input-tentang-dasar" placeholder="Contoh: tentang Sistem Pendidikan Nasional"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
              </div>
              <div class="flex items-center gap-3">
                <button id="btn-simpan-dasar" type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                  Simpan
                </button>
                <button id="btn-batal-dasar" type="button"
                  class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                  Batal
                </button>
              </div>
            </div>
          </div>

          <!-- Tabel dasar hukum -->
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">No</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Nama Peraturan</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tentang</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbody-dasar-hukum" class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">1</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">UU No. 20 Tahun 2003</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">tentang Sistem Pendidikan Nasional</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200">Hapus</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">2</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">PP No. 4 Tahun 2014</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">tentang Penyelenggaraan Pendidikan Tinggi</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200">Hapus</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">3</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Peraturan Direktur Polibatam No. 01 Tahun 2023</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">tentang Tata Kelola Administrasi Polibatam</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200">Hapus</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: PROSES SK — MULTI-STEP WIZARD 3 STEP
             Step 1: Review isi SK + catatan/komentar
             Step 2: Tentukan tingkat verifikasi
             Step 3: Konfirmasi & kirim ke verifikator
        ============================================================ -->
        <div id="page-proses-sk" class="page-content hidden">
          <div class="max-w-2xl mx-auto">

            <!-- Info bar SK yang diproses -->
            <div class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3 mb-5">
              <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              <div class="min-w-0">
                <p class="text-[11px] font-semibold text-blue-700">Sedang mereview SK:</p>
                <p id="sk-proses-judul-info" class="text-[11px] text-blue-600 font-light truncate">—</p>
              </div>
              <button id="sk-proses-back-to-list" type="button" class="ml-auto text-[10px] font-medium text-blue-500 hover:text-blue-700 shrink-0 transition-colors duration-200">← Kembali ke daftar</button>
            </div>

            <!-- Step Indicator -->
            <div class="flex items-center mb-6">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="sk-proses-circle-1">
                  <span class="text-[11px] font-bold text-white">1</span>
                </div>
                <span class="text-xs font-semibold text-blue-600" id="sk-proses-label-1">Review SK</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-proses-circle-2">
                  <span class="text-[11px] font-bold text-slate-400">2</span>
                </div>
                <span class="text-xs font-medium text-slate-400" id="sk-proses-label-2">Verifikasi</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-proses-circle-3">
                  <span class="text-[11px] font-bold text-slate-400">3</span>
                </div>
                <span class="text-xs font-medium text-slate-400" id="sk-proses-label-3">Konfirmasi</span>
              </div>
            </div>


            <!-- STEP 1: Review Isi SK + Catatan -->
            <div id="sk-proses-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 1 — Review Isi SK</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Periksa kelengkapan dan kesesuaian isi SK dari pemohon.</p>
              </div>
              <div class="px-6 py-6 space-y-4">

                <!-- Isi SK dari pemohon (readonly) -->
                <div class="space-y-3">

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Judul SK</p>
                    <p id="sk-review-judul" class="text-sm font-semibold text-slate-800">—</p>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Tentang</p>
                    <p id="sk-review-tentang" class="text-sm font-medium text-slate-700">—</p>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Menimbang</p>
                    <p id="sk-review-menimbang" class="text-xs text-slate-600 font-light leading-relaxed whitespace-pre-line">—</p>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Mengingat</p>
                    <ul id="sk-review-mengingat" class="space-y-1.5">
                      <li class="text-xs text-slate-400 font-light">—</li>
                    </ul>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Memutuskan</p>
                    <p id="sk-review-memutuskan" class="text-xs text-slate-600 font-light leading-relaxed whitespace-pre-line">—</p>
                  </div>

                </div>

                <!-- Divider -->
                <div class="border-t border-slate-100 pt-4">
                  <p class="text-xs font-semibold text-slate-700 mb-3">Catatan / Komentar Admin</p>

                  <!-- Status review -->
                  <div class="space-y-2 mb-4">
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                      <input type="radio" name="status_review_sk" value="lanjut" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" checked />
                      <div>
                        <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">✅ Lanjutkan ke verifikasi</p>
                        <p class="text-[11px] text-slate-400 font-light mt-0.5">Isi SK sudah sesuai, siap dikirim ke verifikator</p>
                      </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                      <input type="radio" name="status_review_sk" value="revisi" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                      <div>
                        <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">🔄 Kembalikan untuk revisi</p>
                        <p class="text-[11px] text-slate-400 font-light mt-0.5">Ada bagian yang perlu diperbaiki oleh pemohon</p>
                      </div>
                    </label>
                  </div>

                  <!-- Kolom catatan -->
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">
                      Catatan untuk Pemohon
                      <span class="text-[10px] font-normal text-slate-400 ml-1">(opsional jika lanjut, wajib jika revisi)</span>
                    </label>
                    <textarea id="sk-catatan-admin" name="catatan_admin" rows="4"
                      placeholder="Contoh: Bagian Menimbang perlu diperjelas, dasar hukum poin 3 kurang relevan..."
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                    <p class="text-[10px] text-slate-400 font-light">Catatan ini akan dikirim ke pemohon melalui email jika SK dikembalikan untuk revisi.</p>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-2">
                  <button id="sk-proses-tolak-btn" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    Kembalikan Revisi
                  </button>
                  <button id="sk-proses-next-1" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut — Tentukan Verifikasi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>

              </div>
            </div>


            <!-- STEP 2: Tentukan Tingkat Verifikasi -->
            <div id="sk-proses-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 2 — Tingkat Verifikasi</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan jalur verifikasi SK sebelum dikirim ke verifikator.</p>
              </div>
              <div class="px-6 py-6 space-y-5">

                <!-- Pilih jalur -->
                <div class="space-y-2">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Jalur Verifikasi <span class="text-blue-400">*</span></label>

                  <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="radio" name="jalur_verifikasi_sk" value="1" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 saja</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">SK hanya perlu disetujui oleh Verifikator Level 1</p>
                    </div>
                  </label>

                  <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="radio" name="jalur_verifikasi_sk" value="2" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 → Level 2</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">SK harus disetujui Level 1 terlebih dahulu, lalu diteruskan ke Level 2</p>
                    </div>
                  </label>

                  <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="radio" name="jalur_verifikasi_sk" value="3" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 → Level 2 → Level 3</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">SK harus melewati tiga tingkat persetujuan secara berurutan</p>
                    </div>
                  </label>
                </div>

                <!-- Pilih verifikator per level -->
                <div class="space-y-3">
                  <p class="text-xs font-semibold text-slate-700">Pilih Verifikator</p>

                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 1 <span class="text-blue-400">*</span></label>
                    <select name="sk_verifikator_1" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                      <option value="" disabled selected>Pilih verifikator level 1</option>
                      <option>Kepala Jurusan TRPL</option>
                      <option>Kepala Jurusan TI</option>
                      <option>Wakil Direktur I</option>
                      <option>Wakil Direktur II</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 2</label>
                    <select name="sk_verifikator_2" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                      <option value="">— Tidak ada (jika hanya 1 level) —</option>
                      <option>Wakil Direktur I</option>
                      <option>Wakil Direktur II</option>
                      <option>Wakil Direktur III</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 3</label>
                    <select name="sk_verifikator_3" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                      <option value="">— Tidak ada (jika hanya 1-2 level) —</option>
                      <option>Direktur Polibatam</option>
                    </select>
                  </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                  <button id="sk-proses-back-1" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    Kembali
                  </button>
                  <button id="sk-proses-next-2" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut — Konfirmasi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>


            <!-- STEP 3: Konfirmasi & Kirim -->
            <div id="sk-proses-step-3" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 3 — Konfirmasi & Kirim</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Periksa ringkasan sebelum SK dikirim ke verifikator pertama.</p>
              </div>
              <div class="px-6 py-6 space-y-4">

                <!-- Ringkasan konfirmasi -->
                <div class="space-y-3">

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">SK yang Diproses</p>
                    <p id="sk-konfirmasi-judul" class="text-sm font-semibold text-slate-800">—</p>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
                    <p id="sk-konfirmasi-pemohon" class="text-sm font-medium text-slate-700">—</p>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Jalur Verifikasi</p>
                    <p id="sk-konfirmasi-jalur" class="text-sm font-medium text-slate-700">—</p>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Verifikator</p>
                    <div id="sk-konfirmasi-verifikator" class="space-y-1"></div>
                  </div>

                  <!-- Catatan jika ada -->
                  <div id="sk-konfirmasi-catatan-wrap" class="hidden rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Catatan Admin</p>
                    <p id="sk-konfirmasi-catatan" class="text-xs text-slate-600 font-light leading-relaxed">—</p>
                  </div>

                </div>

                <!-- Info -->
                <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-start gap-3">
                  <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <p class="text-[11px] text-blue-600 font-light leading-relaxed">Setelah dikirim, sistem akan meneruskan SK ke verifikator pertama. Jika ditolak oleh salah satu verifikator, proses berhenti dan SK dikembalikan untuk diperbaiki.</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                  <button id="sk-proses-back-2" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    Kembali
                  </button>
                  <button id="sk-proses-submit" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Kirim SK ke Verifikator
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                  </button>
                </div>

              </div>
            </div>

          </div>
        </div>


        <!-- ============================================================
             PAGE: PROFIL — Placeholder
        ============================================================ -->
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
