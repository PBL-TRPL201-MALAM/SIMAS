@include('template.layouts.header', ['pageTitle' => 'Dashboard'])
@include('template.sidebar.pemohon', ['activePage' => 'dashboard'])
    <!-- Dashboard Pemohon menerima $stats, $latestDocuments, dan $recentActivities dari DashboardController::pemohon. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Dashboard</h1>
          <p class="text-[11px] text-slate-400 font-light">Selamat datang di SIMAS</p>
        </div>
        <a href="{{ route('pemohon.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-dashboard" class="page-content space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">Halo!</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Berikut ringkasan dokumen kamu hari ini.</p>
            </div>
            <div class="flex items-center gap-2">
              <a href="{{ route('pemohon.buat-surat') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-blue-200 hover:text-blue-600 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Surat Baru
              </a>
              <a href="{{ route('pemohon.buat-sk') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Pengajuan SK
              </a>
            </div>
          </div>

          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- $stats berisi hasil count dokumen pemohon berdasarkan status, disiapkan oleh controller. -->
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['total_dokumen'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total Dokumen</p>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['sedang_diproses'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Sedang Diproses</p>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['disetujui'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Disetujui</p>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['ditolak_revisi'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Ditolak / Revisi</p>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Dokumen Terbaru</h3>
                <a href="{{ route('pemohon.surat-saya') }}" class="text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat semua</a>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="bg-slate-50/60">
                      <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                      <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Jenis</th>
                      <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                      <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-50">
                    <!-- forelse menampilkan daftar dokumen jika ada, dan empty state jika koleksi kosong. -->
                    @forelse ($latestDocuments as $dokumen)
                      <!-- Blok php ini hanya menyiapkan label tampilan dari data relasi suratBiasa/suratKeputusan. -->
                      @php
                        $judulDokumen = $dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN'
                            ? ($dokumen->suratKeputusan?->judul_sk ?: $dokumen->suratKeputusan?->tentang ?: '-')
                            : ($dokumen->suratBiasa?->hal ?: '-');
                        $jenisLabel = $dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN' ? 'SK' : 'Surat Biasa';
                        $statusLabel = ucwords(strtolower(str_replace('_', ' ', $dokumen->status_dokumen)));
                        $statusClasses = match ($dokumen->status_dokumen) {
                            'PUBLISHED' => 'text-slate-600 bg-slate-100',
                            'DITOLAK', 'PERLU_REVISI' => 'text-red-600 bg-red-50',
                            default => 'text-blue-600 bg-blue-50',
                        };
                        $statusDot = match ($dokumen->status_dokumen) {
                            'PUBLISHED' => 'bg-slate-400',
                            'DITOLAK', 'PERLU_REVISI' => 'bg-red-500',
                            default => 'bg-blue-500',
                        };
                      @endphp
                      <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                        <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 truncate max-w-[180px]">{{ $judulDokumen }}</p></td>
                        <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $jenisLabel }}</span></td>
                        <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ optional($dokumen->created_at)->format('d M Y') }}</p></td>
                        <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold {{ $statusClasses }} px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full {{ $statusDot }}"></span>{{ $statusLabel }}</span></td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-xs text-slate-400">Belum ada dokumen yang diajukan.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Aktivitas Terbaru</h3>
              </div>
              <div class="px-5 py-4 space-y-4">
                <!-- $recentActivities berisi riwayat dokumen terbaru milik pemohon yang dikirim dari controller. -->
                @forelse ($recentActivities as $activity)
                  @php
                    $judulAktivitas = $activity->dokumen?->jenis_dokumen === 'SURAT_KEPUTUSAN'
                        ? ($activity->dokumen?->suratKeputusan?->judul_sk ?: $activity->dokumen?->suratKeputusan?->tentang ?: 'Dokumen')
                        : ($activity->dokumen?->suratBiasa?->hal ?: 'Dokumen');
                  @endphp
                  <div class="flex gap-3">
                    <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center shrink-0 mt-0.5">
                      <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div>
                      <p class="text-xs font-medium text-slate-700">{{ $activity->aksi }}</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">{{ $judulAktivitas }}</p>
                      <p class="text-[10px] text-slate-300 mt-1">{{ optional($activity->created_at)->format('d M Y, H:i') }}</p>
                    </div>
                  </div>
                @empty
                  <div class="py-8 text-center">
                    <p class="text-sm font-semibold text-slate-600">Belum ada aktivitas.</p>
                    <p class="text-[11px] text-slate-400 font-light mt-1">Riwayat dokumen akan muncul di sini setelah ada proses berjalan.</p>
                  </div>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
@include('template.layouts.footer')
