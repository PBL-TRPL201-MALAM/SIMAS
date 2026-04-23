<aside id="sidebar" class="relative flex flex-col w-64 shrink-0 bg-white border-r border-slate-100 h-screen overflow-y-auto z-30 transition-all duration-300">

  <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-blue-50/80 blur-2xl pointer-events-none"></div>

  <div class="relative flex items-center gap-2.5 px-5 h-16 border-b border-slate-100/80 shrink-0">
    <img src="{{ asset('images/logo.png') }}"
         alt="Logo SIMAS"
         class="h-7 w-auto object-contain" />
    <div>
      <span class="text-sm font-bold tracking-tight text-slate-900 block">SIMAS</span>
      <span class="text-[10px] font-medium text-blue-500">Admin / TU</span>
    </div>
  </div>

  <nav class="flex-1 px-3 py-4 space-y-0.5">
    <p class="px-2 pb-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Menu Utama</p>

    <a href="{{ route('admin.dashboard') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
      </svg>
      <span>Dashboard</span>
    </a>

    <div class="pt-3 pb-1.5">
      <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Biasa</p>
    </div>

    <a href="{{ route('admin.pengajuan-masuk') }}"
       class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.pengajuan-masuk') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <div class="flex items-center gap-3">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <span>Pengajuan Masuk</span>
      </div>
      <span class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">4</span>
    </a>

    <a href="{{ route('admin.semua-surat') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.semua-surat') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <span>Semua Surat</span>
    </a>

    <div class="pt-3 pb-1.5">
      <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Keputusan</p>
    </div>

    <a href="{{ route('admin.pengajuan-sk') }}"
       class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.pengajuan-sk') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <div class="flex items-center gap-3">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <span>Pengajuan SK Masuk</span>
      </div>
      <span class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">2</span>
    </a>

    <a href="{{ route('admin.semua-sk') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.semua-sk') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
      </svg>
      <span>Semua SK</span>
    </a>

    <div class="pt-3 pb-1.5">
      <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Master Data</p>
    </div>

    <a href="{{ route('admin.master-dasar-hukum') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.master-dasar-hukum') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>
      <span>Master Dasar Hukum</span>
    </a>
  </nav>

  <div class="px-3 py-4 border-t border-slate-100/80 shrink-0 space-y-0.5">
    <a href="{{ route('admin.profil') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.profil') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
      <span>Profil Saya</span>
    </a>

    <a href="{{ route('login') }}"
       class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-500 transition-all duration-200">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a2 2 0 013 3v1" />
      </svg>
      <span>Keluar</span>
    </a>
  </div>
</aside>
