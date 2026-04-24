@include('template.header', ['pageTitle' => 'Pengajuan Masuk'])
@include('template.admin-sidebar')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      {{-- Topbar --}}
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Pengajuan Masuk</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar pengajuan surat yang masuk</p>
        </div>
        <button type="button"
          class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </button>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-4">

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">Pengajuan Surat Masuk</h2>
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
              <button data-filter="semua" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
              <button data-filter="diajukan" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diajukan</button>
              <button data-filter="diproses" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="tbody-pengajuan">

                  @foreach($pengajuan ?? [] as $item)
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-filter-status="{{ strtolower($item->status) }}"
                    data-jenis="{{ $item->jenis }}"
                    data-perihal="{{ $item->perihal }}"
                    data-pemohon="{{ $item->pemohon }}"
                    data-tanggal="{{ $item->tanggal }}"
                    data-status="{{ $item->status }}"
                    data-ringkasan="{{ $item->ringkasan }}">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">{{ $item->pemohon }}</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">{{ $item->perihal }}</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ $item->tanggal }}</p></td>
                    <td class="px-5 py-3.5">
                      @if(strtolower($item->status) === 'diajukan')
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span>
                      @else
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>{{ $item->status }}</span>
                      @endif
                    </td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      @if(strtolower($item->status) === 'diajukan')
                      <a href="{{ route('admin.proses-surat', ['perihal' => $item->perihal, 'pemohon' => $item->pemohon]) }}" class="inline-flex items-center text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                      @endif
                    </td>
                  </tr>
                  @endforeach

                  {{-- Fallback data statis --}}
                  @if(empty($pengajuan) || count($pengajuan) === 0)
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-filter-status="diajukan" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-pemohon="Ahmad Fauzi" data-tanggal="10 Apr 2025" data-status="Diajukan" data-ringkasan="Pemohon mengajukan izin penelitian untuk keperluan tugas akhir di wilayah Batam.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Ahmad Fauzi</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <a href="{{ route('admin.proses-surat', ['perihal' => 'Permohonan Izin Penelitian', 'pemohon' => 'Ahmad Fauzi']) }}" class="inline-flex items-center text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-filter-status="diajukan" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Magang" data-pemohon="Siti Rahma" data-tanggal="09 Apr 2025" data-status="Diajukan" data-ringkasan="Pemohon mengajukan surat izin magang di perusahaan teknologi selama 3 bulan.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Siti Rahma</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">Permohonan Izin Magang</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">09 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                      <a href="{{ route('admin.proses-surat', ['perihal' => 'Permohonan Izin Magang', 'pemohon' => 'Siti Rahma']) }}" class="inline-flex items-center text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-filter-status="diproses" data-jenis="Surat Biasa" data-perihal="Surat Keterangan Aktif Kuliah" data-pemohon="Rina Dewi" data-tanggal="07 Apr 2025" data-status="Diproses" data-ringkasan="Surat keterangan mahasiswa aktif untuk keperluan beasiswa.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Rina Dewi</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">Surat Keterangan Aktif Kuliah</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">07 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Diproses</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
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

