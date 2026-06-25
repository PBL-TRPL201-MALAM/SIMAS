@include('template.layouts.header', ['pageTitle' => 'Dashboard Super Admin'])
@include('template.sidebar.super-admin')

    <!-- Dashboard Super Admin menerima $stats, $roleDistribution, dan $recentActivities dari DashboardController::superAdmin. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Dashboard</h1>
          <p class="text-[11px] text-slate-400 font-light">Ringkasan kendali sistem dan pengguna</p>
        </div>
        <a href="{{ route('super-admin.semua-user') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v6m3-3h-6M5 20h6a2 2 0 002-2v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1a2 2 0 002 2zm7-13a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          Kelola User
        </a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-6">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
              <h2 class="text-base font-bold text-slate-900">Halo, Super Admin!</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Pusat kontrol untuk user, akses, data master, dan monitoring dokumen.</p>
            </div>
            <div class="flex flex-wrap gap-2">
              <a href="{{ route('super-admin.tambah-user') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3.5 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-all duration-200">Tambah User</a>
              <a href="{{ route('super-admin.semua-dokumen') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Lihat Dokumen</a>
            </div>
          </div>

          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- $stats adalah ringkasan global sistem: total user, total dokumen, published, dan pending. -->
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/70 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-11a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['total_user'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total User</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/70 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['total_dokumen'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total Dokumen</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/70 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['published'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Dokumen Published</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/70 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">{{ $stats['pending'] }}</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Dokumen Pending</p>
            </div>
          </div>

          <!-- Charts: Distribusi Role + Tren Dokumen Bulanan -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-semibold text-slate-800">Distribusi User per Role</h3>
                  <p class="text-[11px] text-slate-400 font-light mt-0.5">Komposisi pengguna aktif dalam sistem</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                </div>
              </div>
              <div class="relative w-full flex items-center justify-center" style="min-height: 260px;">
                <canvas id="chartRoleDistribusi"></canvas>
              </div>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/60 transition-all duration-300">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-semibold text-slate-800">Tren Dokumen Bulanan</h3>
                  <p class="text-[11px] text-slate-400 font-light mt-0.5">Jumlah dokumen per bulan ({{ date('Y') }})</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
              </div>
              <div class="relative w-full" style="min-height: 260px;">
                <canvas id="chartTrenDokumen"></canvas>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div>
                  <h3 class="text-sm font-semibold text-slate-800">Aktivitas Terbaru</h3>
                  <p class="text-[11px] text-slate-400 font-light mt-0.5">Pemantauan perubahan sistem dan tindakan user</p>
                </div>
                <a href="{{ route('super-admin.log-aktivitas') }}" class="text-[11px] font-medium text-blue-600 hover:text-blue-700 transition-colors duration-200">Buka log</a>
              </div>
              <div class="divide-y divide-slate-100">
                <!-- forelse menampilkan aktivitas terbaru atau empty state jika log belum ada. -->
                @forelse ($recentActivities as $activity)
                  <!-- Blok php menentukan judul dokumen dari relasi surat biasa/SK sebelum ditampilkan. -->
                  @php
                    $judul = $activity->dokumen?->jenis_dokumen === 'SURAT_KEPUTUSAN'
                        ? ($activity->dokumen?->suratKeputusan?->judul_sk ?: $activity->dokumen?->suratKeputusan?->tentang ?: 'Dokumen')
                        : ($activity->dokumen?->suratBiasa?->hal ?: 'Dokumen');
                  @endphp
                  <div class="px-5 py-4 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" /></svg>
                    </div>
                    <div class="min-w-0">
                      <p class="text-sm font-semibold text-slate-800">{{ $activity->aksi }}</p>
                      <p class="text-xs text-slate-500 font-light mt-1">{{ $judul }}</p>
                      <p class="text-[11px] text-slate-400 mt-1.5">{{ optional($activity->created_at)->format('d M Y, H:i') }}</p>
                    </div>
                  </div>
                @empty
                  <div class="px-5 py-10 text-center">
                    <p class="text-sm font-semibold text-slate-600">Belum ada aktivitas sistem.</p>
                    <p class="text-[11px] text-slate-400 font-light mt-1">Riwayat perubahan akan tampil di sini setelah ada proses dokumen atau perubahan data.</p>
                  </div>
                @endforelse
              </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5 space-y-4">
              <div>
                <h3 class="text-sm font-semibold text-slate-800">Distribusi Role</h3>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Komposisi user aktif dalam sistem</p>
              </div>
              <div class="space-y-3">
                <!-- $roleDistribution berisi label, total, dan persentase user per role untuk progress bar. -->
                @foreach ($roleDistribution as $item)
                  <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                      <span class="font-medium text-slate-700">{{ $item['label'] }}</span>
                      <span class="text-slate-400">{{ $item['total'] }} user</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full {{ $loop->last ? 'bg-slate-700' : ($loop->iteration === 3 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ max(4, $item['percentage']) }}%"></div></div>
                  </div>
                @endforeach
              </div>

              <div class="rounded-2xl bg-slate-50 p-4">
                <h4 class="text-xs font-semibold text-slate-700">Fokus hari ini</h4>
                <ul class="mt-3 space-y-2 text-xs text-slate-500 font-light">
                  <li>Total user aktif: {{ $stats['total_user'] }} akun.</li>
                  <li>Dokumen pending saat ini: {{ $stats['pending'] }} dokumen.</li>
                  <li>Dokumen yang sudah published: {{ $stats['published'] }} dokumen.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Doughnut Chart: Distribusi Role
    const roleData = @json($roleDistribution);
    const ctxRole = document.getElementById('chartRoleDistribusi');
    if (ctxRole) {
        new Chart(ctxRole, {
            type: 'doughnut',
            data: {
                labels: roleData.map(item => item.label),
                datasets: [{
                    data: roleData.map(item => item.total),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',   // Blue 500 — Pemohon
                        'rgba(14, 165, 233, 0.8)',   // Sky 500 — Admin Surat
                        'rgba(245, 158, 11, 0.8)',   // Amber 500 — Verifikator
                        'rgba(139, 92, 246, 0.8)',   // Violet 500 — Penandatangan
                        'rgba(100, 116, 139, 0.8)',  // Slate 500 — Super Admin
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(14, 165, 233, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(100, 116, 139, 1)',
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
                            padding: 14,
                            usePointStyle: true,
                            pointStyleWidth: 8,
                            font: { family: 'Inter', size: 10, weight: '500' },
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
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                return context.label + ': ' + context.parsed + ' user (' + pct + '%)';
                            }
                        }
                    },
                },
            },
        });
    }

    // Bar Chart: Tren Dokumen Bulanan
    const trenData = @json($chartTrenDokumen);
    const ctxTren = document.getElementById('chartTrenDokumen');
    if (ctxTren) {
        new Chart(ctxTren, {
            type: 'bar',
            data: {
                labels: trenData.labels,
                datasets: [{
                    label: 'Jumlah Dokumen',
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
});
</script>

@include('template.layouts.footer')
