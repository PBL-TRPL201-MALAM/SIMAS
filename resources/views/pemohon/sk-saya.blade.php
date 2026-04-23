@include('template.header', ['pageTitle' => 'SK Saya', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'sk-saya'])

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <div><h1 class="text-sm font-bold text-slate-900">SK Saya</h1><p class="text-[11px] text-slate-400 font-light">Daftar semua pengajuan surat keputusan.</p></div>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead><tr class="bg-slate-50/60 border-b border-slate-100"><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th></tr></thead>
              <tbody class="divide-y divide-slate-50">
                <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">SK Pembentukan Panitia Seminar</td><td class="px-5 py-3.5 text-[11px] text-slate-400">08 Apr 2025</td><td class="px-5 py-3.5 text-[10px] text-slate-600">Published</td></tr>
                <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">SK Kegiatan KKN 2025</td><td class="px-5 py-3.5 text-[11px] text-slate-400">10 Apr 2025</td><td class="px-5 py-3.5 text-[10px] text-blue-600">Diproses</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')
