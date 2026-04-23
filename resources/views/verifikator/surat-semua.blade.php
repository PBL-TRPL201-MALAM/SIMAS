@include('template.header', ['pageTitle' => 'Semua Surat', 'modalVariant' => 'verifikator'])
@include('template.verifikator-sidebar', ['activePage' => 'surat-semua'])
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="lg:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Semua Surat</h1>
          <p class="text-[11px] text-slate-400 font-light">Rekap semua surat biasa.</p>
        </div>
        <a href="{{ route('verifikator.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-surat-semua" class="page-content space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Semua Surat Biasa</h2>
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
              <button data-filter="semua" class="filter-surat rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
              <button data-filter="menunggu" class="filter-surat rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Menunggu</button>
              <button data-filter="disetujui" class="filter-surat rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Disetujui</button>
              <button data-filter="ditolak" class="filter-surat rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Ditolak</button>
            </div>
          </div>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbody-surat-semua" class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 surat-row" data-row-status="menunggu"
                    data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-pemohon="Ahmad Fauzi"
                    data-tanggal="10 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="Permohonan izin penelitian untuk keperluan tugas akhir.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Menunggu</span></td>
                    <td class="px-5 py-3.5"><button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 surat-row" data-row-status="disetujui">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Surat Keterangan Mahasiswa</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Dewi Lestari</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Disetujui</span></td>
                    <td class="px-5 py-3.5"><span class="text-[11px] text-slate-400 font-light">â€”</span></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 surat-row" data-row-status="ditolak">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[160px]">Permohonan Surat Keterangan</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Hendra</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Ditolak</span></td>
                    <td class="px-5 py-3.5"><span class="text-[11px] text-slate-400 font-light">â€”</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        {{-- ============================================================
             PAGE: SK MENUNGGU
        ============================================================ --}}
      </main>
    </div>
@include('template.footer')
