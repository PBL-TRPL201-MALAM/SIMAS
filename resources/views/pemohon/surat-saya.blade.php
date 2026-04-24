@include('template.header', ['pageTitle' => 'Surat Saya', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'surat-saya'])
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Surat Saya</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar semua pengajuan surat kamu.</p>
        </div>
        <a href="{{ route('pemohon.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-surat-saya" class="page-content space-y-4">

          {{-- Header + Filter --}}
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">Surat Saya</h2>
            <div class="flex items-center gap-2 flex-wrap">
              {{-- Filter status --}}
              <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
                <button data-filter="semua" data-target="surat" class="filter-btn active-filter rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
                <button data-filter="diproses" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
                <button data-filter="published" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Published</button>
                <button data-filter="ditolak" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Ditolak</button>
              </div>
              {{-- Buat baru --}}
              <a href="{{ route('pemohon.buat-surat') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Buat Baru
              </a>
            </div>
          </div>

          {{-- Tabel --}}
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full" id="tabel-surat">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="tbody-surat">

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="diproses" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Penelitian" data-tanggal="10 Apr 2025" data-nomor="—" data-keterangan="Dokumen sedang diperiksa oleh Admin/TU. Menunggu upload PDF dan pengaturan data surat.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">Permohonan Izin Penelitian</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">10 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diproses</span></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                    </td>
                  </tr>

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="diproses" data-jenis="Surat Biasa" data-perihal="Permohonan Izin Magang" data-tanggal="02 Apr 2025" data-nomor="—" data-keterangan="Dokumen sedang dalam proses verifikasi Level 1.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">Permohonan Izin Magang</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">02 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diproses</span></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                    </td>
                  </tr>

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="published" data-jenis="Surat Biasa" data-perihal="Surat Keterangan Mahasiswa Aktif" data-tanggal="20 Mar 2025" data-nomor="001/SIMAS/TU/2025" data-keterangan="Surat telah diterbitkan dan dapat diunduh.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">Surat Keterangan Mahasiswa Aktif</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">20 Mar 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Published</span></td>
                    <td class="px-5 py-3.5 flex items-center gap-3">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                      <button type="button" class="btn-download inline-flex items-center gap-1 text-[11px] font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Unduh
                      </button>
                    </td>
                  </tr>

                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row" data-status="ditolak" data-jenis="Surat Biasa" data-perihal="Permohonan Surat Keterangan" data-tanggal="05 Apr 2025" data-nomor="—" data-keterangan="Dokumen ditolak oleh Admin/TU. Alasan: Format surat tidak sesuai standar Polibatam. Harap perbaiki dan ajukan kembali.">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">Permohonan Surat Keterangan</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">05 Apr 2025</p></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Ditolak</span></td>
                    <td class="px-5 py-3.5">
                      <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
            {{-- Empty state --}}
            <div id="surat-empty" class="hidden flex flex-col items-center justify-center py-16 text-center">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-sm font-semibold text-slate-600">Tidak ada surat</p>
              <p class="text-xs text-slate-400 font-light mt-1">Belum ada surat dengan status ini.</p>
            </div>
          </div>
        </div>


        {{-- ============================================================
             PAGE: SK SAYA — Tabel + Filter + Aksi
        ============================================================ --}}
      </main>
    </div>
@include('template.footer')

