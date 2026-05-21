@include('template.layouts.header', ['pageTitle' => 'Semua Dokumen'])
@include('template.sidebar.super-admin')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Semua Dokumen</h1>
          <p class="text-[11px] text-slate-400 font-light">Monitoring lintas role tanpa ikut memproses dokumen</p>
        </div>
        <a href="{{ route('super-admin.log-aktivitas') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Lihat Log</a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800">Monitoring Dokumen</h2>
            <p class="text-[11px] text-slate-400 font-light mt-0.5">Memuat status dokumen, pemohon, verifikator, dan tanggal publish.</p>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-slate-50/60">
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Dokumen</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Jenis</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Verifikator</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                  <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Published</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 text-xs">
                <tr>
                  <td class="px-5 py-4 font-medium text-slate-800">Permohonan Izin Penelitian</td>
                  <td class="px-5 py-4 text-slate-500">Surat</td>
                  <td class="px-5 py-4 text-slate-500">Ahmad Fauzi</td>
                  <td class="px-5 py-4 text-slate-500">Rian Kurniawan</td>
                  <td class="px-5 py-4"><span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Published</span></td>
                  <td class="px-5 py-4 text-slate-500">23 Apr 2026</td>
                </tr>
                <tr>
                  <td class="px-5 py-4 font-medium text-slate-800">SK Panitia Wisuda</td>
                  <td class="px-5 py-4 text-slate-500">SK</td>
                  <td class="px-5 py-4 text-slate-500">Siti Rahma</td>
                  <td class="px-5 py-4 text-slate-500">Nur Aisyah</td>
                  <td class="px-5 py-4"><span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Pending</span></td>
                  <td class="px-5 py-4 text-slate-500">-</td>
                </tr>
                <tr>
                  <td class="px-5 py-4 font-medium text-slate-800">Surat Keterangan Magang</td>
                  <td class="px-5 py-4 text-slate-500">Surat</td>
                  <td class="px-5 py-4 text-slate-500">Dina Oktavia</td>
                  <td class="px-5 py-4 text-slate-500">Budi Santoso</td>
                  <td class="px-5 py-4"><span class="text-[10px] font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Ditolak</span></td>
                  <td class="px-5 py-4 text-slate-500">-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

@include('template.layouts.footer')

