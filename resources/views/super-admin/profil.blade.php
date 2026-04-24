@include('template.header', ['pageTitle' => 'Profil Super Admin'])
@include('template.super-admin-sidebar')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Profil Saya</h1>
          <p class="text-[11px] text-slate-400 font-light">Informasi akun Super Admin</p>
        </div>
        <div class="w-9 h-9"></div>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-3xl rounded-2xl bg-white border border-slate-100 p-6">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center">
              <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-900">Super Admin</h2>
              <p class="text-sm text-slate-500">Halaman profil dipisah agar struktur sidebar dan tampilan konsisten dengan role lain.</p>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')

