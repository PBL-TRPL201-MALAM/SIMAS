@include('template.header', ['pageTitle' => 'Dashboard Pemohon', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'dashboard'])

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <div>
          <h1 class="text-sm font-bold text-slate-900">Dashboard</h1>
          <p class="text-[11px] text-slate-400 font-light">Selamat datang di SIMAS</p>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">Halo!</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Berikut ringkasan dokumen kamu hari ini.</p>
            </div>
          </div>

          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white border border-slate-100 p-5"><p class="text-2xl font-extrabold text-slate-900">5</p><p class="text-xs text-slate-400 font-light mt-0.5">Total Dokumen</p></div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5"><p class="text-2xl font-extrabold text-slate-900">2</p><p class="text-xs text-slate-400 font-light mt-0.5">Sedang Diproses</p></div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5"><p class="text-2xl font-extrabold text-slate-900">2</p><p class="text-xs text-slate-400 font-light mt-0.5">Disetujui</p></div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5"><p class="text-2xl font-extrabold text-slate-900">1</p><p class="text-xs text-slate-400 font-light mt-0.5">Ditolak / Revisi</p></div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-800">Dokumen Terbaru</h3></div>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead><tr class="bg-slate-50/60"><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Jenis</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th></tr></thead>
                  <tbody class="divide-y divide-slate-50">
                    <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">Permohonan Izin Penelitian</td><td class="px-5 py-3.5 text-[10px] text-blue-600">Surat Biasa</td><td class="px-5 py-3.5 text-[11px] text-slate-400">10 Apr 2025</td><td class="px-5 py-3.5 text-[10px] text-blue-600">Diproses</td></tr>
                    <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">SK Pembentukan Panitia</td><td class="px-5 py-3.5 text-[10px] text-blue-600">SK</td><td class="px-5 py-3.5 text-[11px] text-slate-400">08 Apr 2025</td><td class="px-5 py-3.5 text-[10px] text-slate-600">Published</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-800">Aktivitas Terbaru</h3></div>
              <div class="px-5 py-4 space-y-4">
                <div><p class="text-xs font-medium text-slate-700">SK Panitia disetujui</p><p class="text-[11px] text-slate-400 font-light mt-0.5">Verifikator Level 2 menyetujui</p></div>
                <div><p class="text-xs font-medium text-slate-700">Surat Keterangan ditolak</p><p class="text-[11px] text-slate-400 font-light mt-0.5">Admin/TU meminta revisi</p></div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')
