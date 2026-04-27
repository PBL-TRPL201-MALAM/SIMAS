<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col overflow-y-auto border-r border-slate-100 bg-white shadow-xl transition-all duration-300 xl:relative xl:z-30 xl:h-screen xl:translate-x-0 xl:shadow-none">

  <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-blue-50/80 blur-2xl pointer-events-none"></div>

  <div class="relative flex items-center gap-3 px-5 h-16 border-b border-slate-100/80 shrink-0">
    <img src="{{ asset('images/logo.png') }}"
         alt="Logo SIMAS"
         class="h-7 w-auto object-contain" />
    <div>
      <span class="text-sm font-bold tracking-tight text-slate-900 block">SIMAS</span>
      <span class="text-[10px] font-medium text-blue-500">Super Admin</span>
    </div>
  </div>

  <nav class="flex-1 px-3 py-4 space-y-0.5">
    <p class="px-2 pb-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Menu Utama</p>

    <a href="{{ route('super-admin.dashboard') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
      </svg>
      <span>Dashboard</span>
    </a>

    <div class="pt-3 pb-1.5">
      <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Manajemen User</p>
    </div>

    <a href="{{ route('super-admin.users.index') }}"
       class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.users.index') || request()->routeIs('super-admin.semua-user') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <div class="flex items-center gap-3">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-11a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span>Semua User</span>
      </div>
      <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ request()->routeIs('super-admin.users.index') || request()->routeIs('super-admin.semua-user') ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-600' }}">{{ \App\Models\User::count() }}</span>
    </a>

    <a href="{{ route('super-admin.users.create') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.users.create') || request()->routeIs('super-admin.tambah-user') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v6m3-3h-6M5 20h6a2 2 0 002-2v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1a2 2 0 002 2zm7-13a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
      <span>Tambah User</span>
    </a>

    <a href="{{ route('super-admin.role-akses') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.role-akses') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 1.657-1.79 3-4 3s-4-1.343-4-3 1.79-3 4-3 4 1.343 4 3zm0 0V9a4 4 0 118 0v2m-8 0v6m8-6v6m-8 0h8" />
      </svg>
      <span>Role &amp; Akses</span>
    </a>

    <div class="pt-3 pb-1.5">
      <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Master Data</p>
    </div>

    <a href="{{ route('super-admin.dasar-hukum') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.dasar-hukum') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>
      <span>Dasar Hukum</span>
    </a>

    <a href="{{ route('super-admin.unit-kerja') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.unit-kerja') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4 6 4v14M9 9h.01M9 12h.01M9 15h.01M15 9h.01M15 12h.01M15 15h.01" />
      </svg>
      <span>Unit Kerja</span>
    </a>

    <div class="pt-3 pb-1.5">
      <p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Monitoring</p>
    </div>

    <a href="{{ route('super-admin.semua-dokumen') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.semua-dokumen') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <span>Semua Dokumen</span>
    </a>

    <a href="{{ route('super-admin.log-aktivitas') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.log-aktivitas') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>Log Aktivitas</span>
    </a>

  </nav>

  <div class="px-3 py-4 border-t border-slate-100/80 shrink-0 space-y-0.5">
    <a href="{{ route('super-admin.profil') }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('super-admin.profil') ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
      <span>Profil Saya</span>
    </a>

    <form action="{{ route('logout') }}" method="POST" class="w-full">
      @csrf

      <button type="submit"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-500 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Keluar</span>
      </button>
    </form>
    
  </div>
</aside>
