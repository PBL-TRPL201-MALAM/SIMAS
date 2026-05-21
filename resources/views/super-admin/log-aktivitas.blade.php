@include('template.layouts.header', ['pageTitle' => 'Log Aktivitas'])
@include('template.sidebar.super-admin')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Log Aktivitas</h1>
          <p class="text-[11px] text-slate-400 font-light">Audit trail untuk tindakan user dan sistem</p>
        </div>
        <button type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Export Log</button>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-slate-50/60">
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Waktu</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pengguna</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aktivitas</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Objek</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Keterangan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 text-xs">
                <tr><td class="px-5 py-4 text-slate-500">24 Apr 2026 09:10</td><td class="px-5 py-4 font-medium text-slate-800">superadmin</td><td class="px-5 py-4 text-slate-500">CREATE_USER</td><td class="px-5 py-4 text-slate-500">nur.aisyah</td><td class="px-5 py-4 text-slate-500">Akun verifikator baru dibuat</td></tr>
                <tr><td class="px-5 py-4 text-slate-500">24 Apr 2026 08:42</td><td class="px-5 py-4 font-medium text-slate-800">superadmin</td><td class="px-5 py-4 text-slate-500">UPDATE_UNIT</td><td class="px-5 py-4 text-slate-500">Akademik</td><td class="px-5 py-4 text-slate-500">Status unit diubah menjadi aktif</td></tr>
                <tr><td class="px-5 py-4 text-slate-500">24 Apr 2026 08:15</td><td class="px-5 py-4 font-medium text-slate-800">system</td><td class="px-5 py-4 text-slate-500">DOC_PENDING_ALERT</td><td class="px-5 py-4 text-slate-500">5 dokumen</td><td class="px-5 py-4 text-slate-500">Notifikasi pending lebih dari 24 jam</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

@include('template.layouts.footer')

