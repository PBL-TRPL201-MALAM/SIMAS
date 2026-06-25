@include('template.layouts.header', ['pageTitle' => 'Dashboard'])
@include('admin.partials.detail-modal')
@include('template.sidebar.admin')

    <!-- Dashboard Admin Surat menerima $stats dan $latestIncoming dari DashboardController::admin. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Dashboard</h1>
          <p class="text-[11px] text-slate-400 font-light">Selamat datang, Admin Surat</p>
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
              <h2 class="text-base font-bold text-slate-900">Halo, Admin Surat!</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Ada <strong class="text-blue-600">{{ $stats['pengajuan_masuk'] }} pengajuan</strong> yang menunggu diproses hari ini.</p>
            </div>
            <a href="{{ route('admin.pengajuan-masuk') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
              Lihat Pengajuan
            </a>
          </div>

          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- $stats berisi jumlah pengajuan masuk, dokumen diproses, published, dan total dokumen. -->
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['pengajuan_masuk'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Pengajuan Masuk</p>
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
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['published'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Published</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['total_dokumen'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total Dokumen</p>
            </div>
          </div>

          <!-- Charts: Tren Bulanan + Jenis Surat -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-semibold text-slate-800">Tren Pengajuan Bulanan</h3>
                  <p class="text-[11px] text-slate-400 font-light mt-0.5">Jumlah pengajuan per bulan ({{ date('Y') }})</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
              </div>
              <div class="relative w-full" style="min-height: 240px;">
                <canvas id="chartTrenBulanan"></canvas>
              </div>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-semibold text-slate-800">Perbandingan Jenis Surat</h3>
                  <p class="text-[11px] text-slate-400 font-light mt-0.5">Surat Biasa vs Surat Keputusan</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                </div>
              </div>
              <div class="relative w-full flex items-center justify-center" style="min-height: 240px;">
                <canvas id="chartJenisSurat"></canvas>
              </div>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
              <h3 class="text-sm font-semibold text-slate-800">Pengajuan Masuk Terbaru</h3>
              <a href="{{ route('admin.pengajuan-masuk') }}" class="text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat semua</a>
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
                  <!-- forelse menampilkan pengajuan terbaru; jika kosong, baris empty state tetap muncul. -->
                  @forelse($latestIncoming as $item)
                    <!-- Blok php membedakan tampilan surat biasa dan SK dari relasi yang sudah diload controller. -->
                    @php
                      $isSk = $item->jenis_dokumen === 'SURAT_KEPUTUSAN';
                      $perihal = $isSk
                          ? ($item->suratKeputusan?->judul_sk ?: $item->suratKeputusan?->tentang ?: '-')
                          : ($item->suratBiasa?->hal ?: '-');
                      $ringkasan = $isSk
                          ? ($item->suratKeputusan?->tentang ?: '-')
                          : ($item->suratBiasa?->ringkasan_isi ?: '-');
                    @endphp
                    <!-- Data detail ini dibaca modal admin lokal saat tombol Detail ditekan. -->
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                      data-dokumen-id="{{ $item->dokumen_id }}"
                      data-jenis="{{ $isSk ? 'SK' : 'Surat Biasa' }}"
                      data-perihal="{{ $perihal }}"
                      data-pemohon="{{ $item->pemohon?->nama ?? '-' }}"
                      data-tanggal="{{ optional($item->created_at)->format('d M Y') }}"
                      data-status="{{ ucwords(strtolower(str_replace('_', ' ', $item->status_dokumen))) }}"
                      data-ringkasan="{{ $ringkasan }}">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">{{ $item->pemohon?->nama ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-xs text-slate-600 truncate max-w-[170px]">{{ $perihal }}</p></td>
                      <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $isSk ? 'SK' : 'Surat Biasa' }}</span></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ optional($item->created_at)->format('d M Y') }}</p></td>
                      <td class="px-5 py-3.5 flex items-center gap-2">
                        <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                        <!-- Tombol Proses mengarahkan Admin Surat ke wizard proses surat atau SK sesuai jenis dokumen. -->
                        <a href="{{ $isSk ? route('admin.proses-sk', ['dokumen' => $item->dokumen_id]) : route('admin.proses-surat', ['dokumen' => $item->dokumen_id, 'step' => 1]) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="px-5 py-10 text-center text-xs text-slate-400">Belum ada pengajuan masuk.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bar Chart: Tren Pengajuan Bulanan
    const trenData = @json($chartTrenBulanan);
    const ctxTren = document.getElementById('chartTrenBulanan');
    if (ctxTren) {
        new Chart(ctxTren, {
            type: 'bar',
            data: {
                labels: trenData.labels,
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: trenData.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 32,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 11 },
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { family: 'Inter', size: 10 },
                            color: '#94a3b8',
                            stepSize: 1,
                        },
                        grid: { color: 'rgba(241, 245, 249, 0.8)' },
                        border: { display: false },
                    },
                    x: {
                        ticks: {
                            font: { family: 'Inter', size: 10 },
                            color: '#94a3b8',
                        },
                        grid: { display: false },
                        border: { display: false },
                    },
                },
            },
        });
    }

    // Doughnut Chart: Jenis Surat
    const jenisData = @json($chartJenisSurat);
    const ctxJenis = document.getElementById('chartJenisSurat');
    if (ctxJenis) {
        new Chart(ctxJenis, {
            type: 'doughnut',
            data: {
                labels: jenisData.labels,
                datasets: [{
                    data: jenisData.data,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',   // Blue 500 — Surat Biasa
                        'rgba(139, 92, 246, 0.8)',   // Violet 500 — SK
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(139, 92, 246, 1)',
                    ],
                    borderWidth: 2,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            pointStyleWidth: 8,
                            font: { family: 'Inter', size: 11, weight: '500' },
                            color: '#64748b',
                        },
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 11 },
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: true,
                        boxPadding: 4,
                    },
                },
            },
        });
    }
});
</script>

@include('template.layouts.footer')
