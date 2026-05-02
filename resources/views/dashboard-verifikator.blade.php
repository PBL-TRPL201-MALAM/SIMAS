<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Verifikator — SIMAS</title>

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
       MODAL VERIFIKASI DOKUMEN
       Dipakai untuk Surat Biasa & SK — berisi preview metadata + aksi
  ================================================================ -->
  <div id="modal-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden">

      <!-- Modal header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Verifikasi Dokumen</h3>
          <p id="modal-jenis-badge" class="text-[11px] text-slate-400 font-light mt-0.5"></p>
        </div>
        <button id="modal-close" type="button"
          class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>

      <!-- Modal body -->
      <div class="px-6 py-5 space-y-3 max-h-[60vh] overflow-y-auto">

        <!-- Info grid -->
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Perihal / Judul</p>
            <p id="modal-perihal" class="text-xs font-semibold text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
            <p id="modal-pemohon" class="text-xs font-medium text-slate-700"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Pengajuan</p>
            <p id="modal-tanggal" class="text-xs font-medium text-slate-700"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Level Verifikasi Saya</p>
            <p id="modal-level" class="text-xs font-medium text-blue-600"></p>
          </div>
        </div>

        <!-- Ringkasan / isi -->
        <div class="rounded-xl bg-slate-50 px-4 py-3">
          <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Ringkasan / Isi</p>
          <p id="modal-ringkasan" class="text-xs text-slate-600 font-light leading-relaxed"></p>
        </div>

        <!-- Khusus SK: tampil bagian-bagian SK -->
        <div id="modal-sk-section" class="hidden space-y-2">
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tentang</p>
            <p id="modal-sk-tentang" class="text-xs text-slate-600 font-light"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Menimbang</p>
            <p id="modal-sk-menimbang" class="text-xs text-slate-600 font-light whitespace-pre-line"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Memutuskan</p>
            <p id="modal-sk-memutuskan" class="text-xs text-slate-600 font-light whitespace-pre-line"></p>
          </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-slate-100 pt-3">
          <p class="text-xs font-semibold text-slate-700 mb-3">Keputusan Verifikasi</p>

          <!-- Radio pilihan -->
          <div class="space-y-2 mb-3">
            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
              <input type="radio" name="keputusan_verifikasi" value="setuju" id="radio-setuju"
                class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" checked />
              <div>
                <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">✅ Setujui Dokumen</p>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Dokumen disetujui dan diteruskan ke level berikutnya (jika ada)</p>
              </div>
            </label>
            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
              <input type="radio" name="keputusan_verifikasi" value="tolak" id="radio-tolak"
                class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
              <div>
                <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">❌ Tolak Dokumen</p>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Proses berhenti, dokumen dikembalikan untuk diperbaiki</p>
              </div>
            </label>
          </div>

          <!-- Alasan penolakan — muncul saat pilih Tolak -->
          <div id="alasan-wrap" class="hidden space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 tracking-wide">
              Alasan Penolakan <span class="text-blue-400">*</span>
            </label>
            <textarea id="alasan-penolakan" rows="3"
              placeholder="Tuliskan alasan penolakan secara jelas agar pemohon dapat memperbaikinya..."
              class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
            <p class="text-[10px] text-slate-400 font-light">Alasan ini akan dicatat dan dikirimkan kepada pemohon.</p>
          </div>
        </div>

      </div>

      <!-- Modal footer -->
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <button id="modal-close-btn" type="button"
          class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
          Batal
        </button>
        <div class="flex items-center gap-2">
          <!-- Tombol Unduh PDF -->
          <button id="modal-unduh-btn" type="button"
            class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            Unduh PDF
          </button>
          <!-- Tombol submit keputusan -->
          <button id="modal-submit-btn" type="button"
            class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            Kirim Keputusan
          </button>
        </div>
      </div>
    </div>
  </div>


  <div class="flex h-screen w-full overflow-hidden">

    <!-- ================================================================
         SIDEBAR VERIFIKATOR
    ================================================================ -->
    <aside id="sidebar" class="relative flex flex-col w-64 shrink-0 bg-white border-r border-slate-100 h-screen overflow-y-auto z-30 transition-all duration-300">

      <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-blue-50/80 blur-2xl pointer-events-none"></div>

      <!-- Logo -->
      
<div class="relative flex items-center gap-2.5 px-5 h-16 border-b border-slate-100/80 shrink-0">
  <img src="{{ asset('images/logo.png') }}"
       alt="Logo SIMAS"
       class="h-7 w-auto object-contain" />
  <div>
    <!-- Hapus span pertama ini kalau logo sudah include teks -->
    <span class="text-sm font-bold tracking-tight text-slate-900 block">SIMAS</span>
    <span class="text-[10px] font-medium text-blue-500">Verifikator</span>
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

        <a href="#" data-page="surat-menunggu"
           class="nav-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <div class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Menunggu</span>
          </div>
          <span class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">3</span>
        </a>

        <a href="#" data-page="surat-disetujui"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Disetujui</span>
        </a>

        <a href="#" data-page="surat-ditolak"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Ditolak</span>
        </a>

        <a href="#" data-page="surat-semua"
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

        <a href="#" data-page="sk-menunggu"
           class="nav-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <div class="flex items-center gap-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Menunggu</span>
          </div>
          <span class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">2</span>
        </a>

        <a href="#" data-page="sk-disetujui"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Disetujui</span>
        </a>

        <a href="#" data-page="sk-ditolak"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Ditolak</span>
        </a>

        <a href="#" data-page="sk-semua"
           class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
          </svg>
          <span>Semua SK</span>
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
          <p id="page-subtitle" class="text-[11px] text-slate-400 font-light">Selamat datang, Verifikator</p>
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
              <h2 class="text-base font-bold text-slate-900">Halo, Verifikator! 👋</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Ada <strong class="text-blue-600">5 dokumen</strong> yang menunggu verifikasi dari kamu.</p>
            </div>
            <a href="#" data-page="surat-menunggu" class="quick-nav inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Lihat yang Menunggu
            </a>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">5</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Menunggu Verifikasi</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">18</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Disetujui</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">2</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Ditolak</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">25</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total Diverifikasi</p>
            </div>
          </div>

          <!-- Tabel dokumen menunggu -->
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 class="text-sm font-semibold text-slate-800">Menunggu Verifikasi Saya</h3>
              <a href="#" data-page="surat-menunggu" class="quick-nav text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat semua →</a>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal / Judul</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Jenis</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="Surat Biasa"
                    data-perihal="Permohonan Izin Penelitian"
                    data-pemohon="Ahmad Fauzi"
                    data-tanggal="10 Apr 2025"
                    data-level="Level 1"
                    data-ringkasan="Permohonan izin penelitian untuk keperluan tugas akhir di wilayah Batam selama 2 bulan."
                    data-status="menunggu">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Surat Biasa</span></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">
                        Verifikasi
                      </button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="SK"
                    data-perihal="SK Kegiatan KKN 2025"
                    data-pemohon="Budi Santoso"
                    data-tanggal="08 Apr 2025"
                    data-level="Level 1"
                    data-ringkasan="SK untuk kegiatan KKN mahasiswa semester genap 2025."
                    data-sk-tentang="Pembentukan Panitia KKN 2025"
                    data-sk-menimbang="a. bahwa KKN merupakan bagian dari kurikulum;&#10;b. bahwa perlu dibentuk panitia pelaksana;"
                    data-sk-memutuskan="PERTAMA : Membentuk Panitia KKN 2025;&#10;KEDUA   : Panitia bertanggung jawab kepada Direktur;"
                    data-status="menunggu">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">SK Kegiatan KKN 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Budi Santoso</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">SK</span></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">08 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">
                        Verifikasi
                      </button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="Surat Biasa"
                    data-perihal="Surat Keterangan Aktif Kuliah"
                    data-pemohon="Rina Dewi"
                    data-tanggal="07 Apr 2025"
                    data-level="Level 2"
                    data-ringkasan="Surat keterangan mahasiswa aktif untuk keperluan beasiswa tahun akademik 2025."
                    data-status="menunggu">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Surat Keterangan Aktif Kuliah</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Rina Dewi</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Surat Biasa</span></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 2</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">07 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">
                        Verifikasi
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SURAT MENUNGGU
        ============================================================ -->
        <div id="page-surat-menunggu" class="page-content hidden space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Surat Menunggu Verifikasi</h2>
            <span class="text-[11px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">3 dokumen</span>
          </div>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level Saya</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-pemohon="Ahmad Fauzi"
                    data-tanggal="10 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="Permohonan izin penelitian untuk keperluan tugas akhir di wilayah Batam selama 2 bulan.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="Surat Biasa" data-perihal="Permohonan Izin Magang" data-pemohon="Siti Rahma"
                    data-tanggal="09 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="Permohonan izin magang di perusahaan teknologi selama 3 bulan.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">Permohonan Izin Magang</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Siti Rahma</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">09 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="Surat Biasa" data-perihal="Surat Keterangan Aktif Kuliah" data-pemohon="Rina Dewi"
                    data-tanggal="07 Apr 2025" data-level="Level 2" data-status="menunggu"
                    data-ringkasan="Surat keterangan mahasiswa aktif untuk keperluan beasiswa.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">Surat Keterangan Aktif Kuliah</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Rina Dewi</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 2</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">07 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SURAT DISETUJUI
        ============================================================ -->
        <div id="page-surat-disetujui" class="page-content hidden space-y-4">
          <h2 class="text-sm font-bold text-slate-900">Surat yang Sudah Disetujui</h2>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tgl Disetujui</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status Akhir</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">Surat Keterangan Mahasiswa Aktif</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Dewi Lestari</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">05 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">Permohonan Surat Pengantar</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Rizki Pratama</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 2</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">02 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diproses</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SURAT DITOLAK
        ============================================================ -->
        <div id="page-surat-ditolak" class="page-content hidden space-y-4">
          <h2 class="text-sm font-bold text-slate-900">Surat yang Ditolak</h2>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tgl Ditolak</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Alasan</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Permohonan Surat Keterangan</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Hendra</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">03 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light max-w-[200px]">Format surat tidak sesuai standar Polibatam.</p></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SEMUA SURAT
        ============================================================ -->
        <div id="page-surat-semua" class="page-content hidden space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Semua Surat Biasa</h2>
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
              <button data-filter="semua" class="filter-surat rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
              <button data-filter="menunggu" class="filter-surat rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Menunggu</button>
              <button data-filter="disetujui" class="filter-surat rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Disetujui</button>
              <button data-filter="ditolak" class="filter-surat rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Ditolak</button>
            </div>
          </div>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbody-surat-semua" class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 surat-row" data-row-status="menunggu"
                    data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-pemohon="Ahmad Fauzi"
                    data-tanggal="10 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="Permohonan izin penelitian untuk keperluan tugas akhir.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Menunggu</span></td>
                    <td class="px-5 py-3.5"><button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 surat-row" data-row-status="disetujui">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Surat Keterangan Mahasiswa</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Dewi Lestari</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Disetujui</span></td>
                    <td class="px-5 py-3.5"><span class="text-[11px] text-slate-400 font-light">—</span></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 surat-row" data-row-status="ditolak">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Permohonan Surat Keterangan</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Hendra</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Ditolak</span></td>
                    <td class="px-5 py-3.5"><span class="text-[11px] text-slate-400 font-light">—</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SK MENUNGGU
        ============================================================ -->
        <div id="page-sk-menunggu" class="page-content hidden space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">SK Menunggu Verifikasi</h2>
            <span class="text-[11px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">2 dokumen</span>
          </div>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level Saya</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="SK" data-perihal="SK Kegiatan KKN 2025" data-pemohon="Budi Santoso"
                    data-tanggal="08 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="SK untuk kegiatan KKN mahasiswa semester genap 2025."
                    data-sk-tentang="Pembentukan Panitia KKN 2025"
                    data-sk-menimbang="a. bahwa KKN merupakan bagian kurikulum;&#10;b. bahwa perlu dibentuk panitia;"
                    data-sk-memutuskan="PERTAMA : Membentuk Panitia KKN 2025;&#10;KEDUA   : Panitia bertanggung jawab kepada Direktur;">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">SK Kegiatan KKN 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Budi Santoso</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">08 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="SK" data-perihal="SK Seminar Nasional 2025" data-pemohon="Dewi Lestari"
                    data-tanggal="06 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="SK pembentukan panitia seminar nasional bidang teknologi informasi."
                    data-sk-tentang="Pembentukan Panitia Seminar Nasional 2025"
                    data-sk-menimbang="a. bahwa seminar nasional perlu diselenggarakan;&#10;b. bahwa perlu dibentuk panitia pelaksana;"
                    data-sk-memutuskan="PERTAMA : Membentuk Panitia Seminar Nasional 2025;&#10;KEDUA   : Kegiatan dilaksanakan bulan Juli 2025;">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">SK Seminar Nasional 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Dewi Lestari</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">06 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SK DISETUJUI
        ============================================================ -->
        <div id="page-sk-disetujui" class="page-content hidden space-y-4">
          <h2 class="text-sm font-bold text-slate-900">SK yang Sudah Disetujui</h2>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tgl Disetujui</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status Akhir</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">SK Pembentukan Panitia Seminar</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Rizki Pratama</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">01 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SK DITOLAK
        ============================================================ -->
        <div id="page-sk-ditolak" class="page-content hidden space-y-4">
          <h2 class="text-sm font-bold text-slate-900">SK yang Ditolak</h2>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 text-center">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-sm font-semibold text-slate-600">Belum ada SK yang ditolak</p>
              <p class="text-xs text-slate-400 font-light mt-1">SK yang kamu tolak akan muncul di sini.</p>
            </div>
          </div>
        </div>


        <!-- ============================================================
             PAGE: SEMUA SK
        ============================================================ -->
        <div id="page-sk-semua" class="page-content hidden space-y-4">
          <h2 class="text-sm font-bold text-slate-900">Semua Surat Keputusan</h2>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="SK" data-perihal="SK Kegiatan KKN 2025" data-pemohon="Budi Santoso"
                    data-tanggal="08 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="SK untuk kegiatan KKN mahasiswa semester genap 2025."
                    data-sk-tentang="Pembentukan Panitia KKN 2025"
                    data-sk-menimbang="a. bahwa KKN merupakan bagian kurikulum;"
                    data-sk-memutuskan="PERTAMA : Membentuk Panitia KKN 2025;">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">SK Kegiatan KKN 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Budi Santoso</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Menunggu</span></td>
                    <td class="px-5 py-3.5"><button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">SK Pembentukan Panitia Seminar</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Rizki Pratama</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                    <td class="px-5 py-3.5"><span class="text-[11px] text-slate-400 font-light">—</span></td>
                  </tr>
                </tbody>
              </table>
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
