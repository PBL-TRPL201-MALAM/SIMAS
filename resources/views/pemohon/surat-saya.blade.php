@include('template.header', ['pageTitle' => 'Surat Saya', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'surat-saya'])

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <div><h1 class="text-sm font-bold text-slate-900">Surat Saya</h1><p class="text-[11px] text-slate-400 font-light">Daftar semua pengajuan surat biasa.</p></div>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead><tr class="bg-slate-50/60 border-b border-slate-100"><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th><th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th></tr></thead>
              <tbody class="divide-y divide-slate-50">
                <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">Permohonan Izin Penelitian</td><td class="px-5 py-3.5 text-[11px] text-slate-400">10 Apr 2025</td><td class="px-5 py-3.5 text-[10px] text-blue-600">Diproses</td></tr>
                <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">Surat Keterangan Mahasiswa Aktif</td><td class="px-5 py-3.5 text-[11px] text-slate-400">20 Mar 2025</td><td class="px-5 py-3.5 text-[10px] text-slate-600">Published</td></tr>
                <tr><td class="px-5 py-3.5 text-xs font-medium text-slate-800">Permohonan Surat Keterangan</td><td class="px-5 py-3.5 text-[11px] text-slate-400">05 Apr 2025</td><td class="px-5 py-3.5 text-[10px] text-slate-500">Ditolak</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')
