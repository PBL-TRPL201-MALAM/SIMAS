@include('template.header', ['pageTitle' => 'Dashboard Verifikator', 'modalVariant' => 'verifikator'])
@include('template.verifikator-sidebar', ['activePage' => 'dashboard'])

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <div><h1 class="text-sm font-bold text-slate-900">Dashboard</h1><p class="text-[11px] text-slate-400 font-light">Selamat datang, Verifikator</p></div>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-6">
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white border border-slate-100 p-5"><p class="text-2xl font-extrabold text-slate-900">5</p><p class="text-xs text-slate-400 font-light mt-0.5">Menunggu Verifikasi</p></div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5"><p class="text-2xl font-extrabold text-slate-900">18</p><p class="text-xs text-slate-400 font-light mt-0.5">Disetujui</p></div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5"><p class="text-2xl font-extrabold text-slate-900">2</p><p class="text-xs text-slate-400 font-light mt-0.5">Ditolak</p></div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5"><p class="text-2xl font-extrabold text-slate-900">25</p><p class="text-xs text-slate-400 font-light mt-0.5">Total Diverifikasi</p></div>
          </div>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-800">Menunggu Verifikasi Saya</h3></div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead><tr class="bg-slate-50/60"><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal / Judul</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Jenis</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level</th></tr></thead>
                <tbody class="divide-y divide-slate-50">
                  <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">Permohonan Izin Penelitian</td><td class="px-5 py-3.5 text-xs text-slate-600">Ahmad Fauzi</td><td class="px-5 py-3.5 text-[10px] text-blue-600">Surat Biasa</td><td class="px-5 py-3.5 text-[10px] text-slate-600">Level 1</td></tr>
                  <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">SK Kegiatan KKN 2025</td><td class="px-5 py-3.5 text-xs text-slate-600">Budi Santoso</td><td class="px-5 py-3.5 text-[10px] text-blue-600">SK</td><td class="px-5 py-3.5 text-[10px] text-slate-600">Level 1</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')
