@include('template.header', ['pageTitle' => 'SK Menunggu Verifikasi', 'modalVariant' => 'verifikator'])
@include('template.verifikator-sidebar', ['activePage' => 'sk-menunggu'])
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="lg:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">SK Menunggu Verifikasi</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar SK yang menunggu verifikasi.</p>
        </div>
        <a href="{{ route('verifikator.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-sk-menunggu" class="page-content space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">SK Menunggu Verifikasi</h2>
            <span class="text-[11px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">2 dokumen</span>
          </div>
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level Saya</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="SK" data-perihal="SK Kegiatan KKN 2025" data-pemohon="Budi Santoso"
                    data-tanggal="08 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="SK untuk kegiatan KKN mahasiswa semester genap 2025."
                    data-sk-tentang="Pembentukan Panitia KKN 2025"
                    data-sk-menimbang="a. bahwa KKN merupakan bagian kurikulum;&#10;b. bahwa perlu dibentuk panitia;"
                    data-sk-memutuskan="PERTAMA : Membentuk Panitia KKN 2025;&#10;KEDUA   : Panitia bertanggung jawab kepada Direktur;">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">SK Kegiatan KKN 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Budi Santoso</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">08 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="SK" data-perihal="SK Seminar Nasional 2025" data-pemohon="Dewi Lestari"
                    data-tanggal="06 Apr 2025" data-level="Level 1" data-status="menunggu"
                    data-ringkasan="SK pembentukan panitia seminar nasional bidang teknologi informasi."
                    data-sk-tentang="Pembentukan Panitia Seminar Nasional 2025"
                    data-sk-menimbang="a. bahwa seminar nasional perlu diselenggarakan;&#10;b. bahwa perlu dibentuk panitia pelaksana;"
                    data-sk-memutuskan="PERTAMA : Membentuk Panitia Seminar Nasional 2025;&#10;KEDUA   : Kegiatan dilaksanakan bulan Juli 2025;">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[180px]">SK Seminar Nasional 2025</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">Dewi Lestari</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level 1</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">06 Apr 2025</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-verifikasi text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Verifikasi</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        {{-- ============================================================
             PAGE: SK DISETUJUI
        ============================================================ --}}
      </main>
    </div>
@include('template.footer')
