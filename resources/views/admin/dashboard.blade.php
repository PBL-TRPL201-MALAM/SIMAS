@include('template.header', ['pageTitle' => 'Dashboard'])
@include('template.admin-sidebar')

    {{-- ================================================================
         AREA KONTEN UTAMA
    ================================================================ --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">  

      {{-- Topbar --}}
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Dashboard</h1>
          <p class="text-[11px] text-slate-400 font-light">Selamat datang, Admin/TU</p>
        </div>
        <button type="button" data-page="profil"
          class="nav-link w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </button>
      </header>

      <main class="flex-1 overflow-y-auto p-6">

        <div class="space-y-6">

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">Halo, Admin/TU! 👋</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Ada <strong class="text-blue-600">4 pengajuan</strong> yang menunggu diproses hari ini.</p>
            </div>
            <a href="{{ route('admin.pengajuan-masuk') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
              Lihat Pengajuan
            </a>
          </div>

          {{-- Stats --}}
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">4</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Pengajuan Masuk</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">7</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Sedang Diproses</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">12</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Published</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">23</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total Dokumen</p>
            </div>
          </div>

          {{-- Tabel pengajuan terbaru --}}
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 class="text-sm font-semibold text-slate-800">Pengajuan Masuk Terbaru</h3>
              <a href="{{ route('admin.pengajuan-masuk') }}" class="text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat semua →</a>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Jenis</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  @foreach($pengajuanTerbaru ?? [] as $item)
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-jenis="{{ $item->jenis }}"
                    data-perihal="{{ $item->perihal }}"
                    data-pemohon="{{ $item->pemohon }}"
                    data-tanggal="{{ $item->tanggal }}"
                    data-status="{{ $item->status }}"
                    data-ringkasan="{{ $item->ringkasan }}">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">{{ $item->pemohon }}</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 truncate max-w-[150px]">{{ $item->perihal }}</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $item->jenis }}</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ $item->tanggal }}</p></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <a href="{{ route('admin.proses-surat', ['perihal' => $item->perihal, 'pemohon' => $item->pemohon]) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                    </td>
                  </tr>
                  @endforeach

                  {{-- Fallback data statis jika $pengajuanTerbaru kosong (untuk development) --}}
                  @if(empty($pengajuanTerbaru) || count($pengajuanTerbaru) === 0)
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-pemohon="Ahmad Fauzi" data-tanggal="10 Apr 2025" data-status="Diajukan" data-ringkasan="Pemohon mengajukan izin penelitian untuk keperluan tugas akhir di wilayah Batam.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 truncate max-w-[150px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Surat Biasa</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <a href="{{ route('admin.proses-surat', ['perihal' => 'Permohonan Izin Penelitian', 'pemohon' => 'Ahmad Fauzi']) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Magang" data-pemohon="Siti Rahma" data-tanggal="09 Apr 2025" data-status="Diajukan" data-ringkasan="Pemohon mengajukan surat izin magang di perusahaan teknologi selama 3 bulan.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Siti Rahma</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 truncate max-w-[150px]">Permohonan Izin Magang</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Surat Biasa</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">09 Apr 2025</p></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <a href="{{ route('admin.proses-surat', ['perihal' => 'Permohonan Izin Magang', 'pemohon' => 'Siti Rahma']) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-jenis="SK" data-perihal="SK Kegiatan KKN 2025" data-pemohon="Budi Santoso" data-tanggal="08 Apr 2025" data-status="Diajukan" data-ringkasan="Pengajuan SK untuk kegiatan KKN mahasiswa semester genap 2025.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Budi Santoso</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 truncate max-w-[150px]">SK Kegiatan KKN 2025</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">SK</span></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">08 Apr 2025</p></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <a href="{{ route('admin.proses-surat', ['perihal' => 'SK Kegiatan KKN 2025', 'pemohon' => 'Budi Santoso']) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                    </td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </main>
    </div>

@include('template.footer')

