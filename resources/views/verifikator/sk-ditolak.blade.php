@include('template.header', ['pageTitle' => 'SK Ditolak', 'modalVariant' => 'verifikator'])
@include('template.verifikator-sidebar', ['activePage' => 'sk-ditolak'])
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">SK Ditolak</h1>
          <p class="text-[11px] text-slate-400 font-light">Riwayat SK yang ditolak.</p>
        </div>
        <a href="{{ route('verifikator.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-sk-ditolak" class="page-content space-y-4">
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


        {{-- ============================================================
             PAGE: SEMUA SK
        ============================================================ --}}
      </main>
    </div>
@include('template.footer')

