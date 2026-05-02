@include('template.header', ['pageTitle' => 'Surat Saya', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'surat-saya'])
    <!-- View ini menerima $suratSaya dari PemohonSuratControllerindex, berisi surat biasa milik user login. -->
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
          <!-- Flash message ini berasal dari redirect setelah pengajuan/unduh berhasil atau gagal. -->
          @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
              {{ session('status') }}
            </div>
          @endif
          @if (session('error'))
            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
              {{ session('error') }}
            </div>
          @endif

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">Surat Saya</h2>
            <div class="flex items-center gap-2 flex-wrap">
              <!-- Tombol filter bekerja di sisi frontend melalui data-status pada setiap baris surat. -->
              <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
                <button data-filter="semua" data-target="surat" class="filter-btn active-filter rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
                <button data-filter="diproses" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
                <button data-filter="published" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Published</button>
                <button data-filter="ditolak" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Ditolak</button>
              </div>
              <a href="{{ route('pemohon.buat-surat') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Buat Baru
              </a>
            </div>
          </div>

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
                  <!-- forelse memastikan tabel tetap punya empty state saat pemohon belum pernah mengajukan surat. -->
                  @forelse ($suratSaya as $dokumen)
                    <!-- Blok php menyiapkan status filter dan label tampilan dari status_dokumen. -->
                    @php
                      $statusFilter = $dokumen->status_dokumen === 'PUBLISHED'
                          ? 'published'
                          : (in_array($dokumen->status_dokumen, ['DITOLAK', 'PERLU_REVISI'], true) ? 'ditolak' : 'diproses');
                      $statusLabel = str_replace('_', ' ', $dokumen->status_dokumen);
                    @endphp
                    <!-- Atribut data-* ini dibaca JavaScript untuk mengisi modal detail tanpa query tambahan. -->
                    <!-- URL download hanya diisi jika status sudah PUBLISHED, sehingga tombol modal bisa disembunyikan untuk status lain. -->
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                        data-status="{{ $statusFilter }}"
                        data-jenis="Surat Biasa"
                        data-perihal="{{ $dokumen->suratBiasa?->hal ?? '-' }}"
                        data-tanggal="{{ optional($dokumen->created_at)->format('d M Y') }}"
                        data-nomor="{{ $dokumen->suratBiasa?->nomor_surat ?? '-' }}"
                        data-keterangan="{{ $dokumen->suratBiasa?->ringkasan_isi ?? '-' }}"
                        data-download-url="{{ $dokumen->status_dokumen === 'PUBLISHED' ? route('pemohon.surat.download', $dokumen->dokumen_id) : '' }}">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">{{ $dokumen->suratBiasa?->hal ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ optional($dokumen->created_at)->format('d M Y') }}</p></td>
                      <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $statusFilter === 'published' ? 'text-slate-600 bg-slate-100' : ($statusFilter === 'ditolak' ? 'text-red-600 bg-red-50' : 'text-blue-600 bg-blue-50') }}">
                          <span class="w-1 h-1 rounded-full {{ $statusFilter === 'published' ? 'bg-slate-400' : ($statusFilter === 'ditolak' ? 'bg-red-500' : 'bg-blue-500') }}"></span>{{ ucwords(strtolower($statusLabel)) }}
                        </span>
                      </td>
                      <td class="px-5 py-3.5">
                        <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                      </td>
                    </tr>
                  @empty
                  @endforelse
                </tbody>
              </table>
            </div>
            <!-- Empty state ini juga dipakai saat filter frontend menyembunyikan semua baris yang tidak cocok. -->
            <div id="surat-empty" class="{{ $suratSaya->isEmpty() ? 'flex' : 'hidden' }} flex-col items-center justify-center py-16 text-center">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-sm font-semibold text-slate-600">Tidak ada surat</p>
              <p class="text-xs text-slate-400 font-light mt-1">Belum ada pengajuan surat dari akun ini.</p>
            </div>
          </div>
        </div>
      </main>
    </div>
@include('template.footer')
