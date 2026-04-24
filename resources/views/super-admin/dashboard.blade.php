@include('template.header', ['pageTitle' => 'Dashboard Super Admin'])
@include('template.super-admin-sidebar')

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
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/70 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-11a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">24</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total User</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/70 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">186</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Total Dokumen</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/70 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">132</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Dokumen Published</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-5 hover:shadow-md hover:shadow-blue-50/70 transition-all duration-300">
              <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="text-2xl font-extrabold text-slate-900">21</p>
              <p class="text-xs text-slate-400 font-light mt-0.5">Dokumen Pending</p>
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
                <div class="px-5 py-4 flex items-start gap-3">
                  <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v6m3-3h-6M5 20h6a2 2 0 002-2v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1a2 2 0 002 2zm7-13a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">User baru ditambahkan</p>
                    <p class="text-xs text-slate-500 font-light mt-1">Akun verifikator atas nama Nur Aisyah dibuat oleh Super Admin.</p>
                    <p class="text-[11px] text-slate-400 mt-1.5">24 April 2026, 09:10</p>
                  </div>
                </div>
                <div class="px-5 py-4 flex items-start gap-3">
                  <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4 6 4v14M9 9h.01M9 12h.01M9 15h.01M15 9h.01M15 12h.01M15 15h.01" /></svg>
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">Unit kerja diperbarui</p>
                    <p class="text-xs text-slate-500 font-light mt-1">Data unit kerja "Akademik" ditandai aktif kembali untuk proses dokumen.</p>
                    <p class="text-[11px] text-slate-400 mt-1.5">24 April 2026, 08:42</p>
                  </div>
                </div>
                <div class="px-5 py-4 flex items-start gap-3">
                  <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">Dokumen pending meningkat</p>
                    <p class="text-xs text-slate-500 font-light mt-1">Ada 5 dokumen baru yang belum selesai diverifikasi pada shift pagi.</p>
                    <p class="text-[11px] text-slate-400 mt-1.5">24 April 2026, 08:15</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5 space-y-4">
              <div>
                <h3 class="text-sm font-semibold text-slate-800">Distribusi Role</h3>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Komposisi user aktif dalam sistem</p>
              </div>
              <div class="space-y-3">
                <div>
                  <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-medium text-slate-700">Pemohon</span>
                    <span class="text-slate-400">12 user</span>
                  </div>
                  <div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full w-[50%] bg-blue-500 rounded-full"></div></div>
                </div>
                <div>
                  <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-medium text-slate-700">Admin / TU</span>
                    <span class="text-slate-400">5 user</span>
                  </div>
                  <div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full w-[22%] bg-blue-500 rounded-full"></div></div>
                </div>
                <div>
                  <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-medium text-slate-700">Verifikator</span>
                    <span class="text-slate-400">6 user</span>
                  </div>
                  <div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full w-[28%] bg-amber-500 rounded-full"></div></div>
                </div>
                <div>
                  <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-medium text-slate-700">Super Admin</span>
                    <span class="text-slate-400">1 user</span>
                  </div>
                  <div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full w-[10%] bg-slate-700 rounded-full"></div></div>
                </div>
              </div>

              <div class="rounded-2xl bg-slate-50 p-4">
                <h4 class="text-xs font-semibold text-slate-700">Fokus hari ini</h4>
                <ul class="mt-3 space-y-2 text-xs text-slate-500 font-light">
                  <li>Periksa akun yang belum aktif dalam 7 hari terakhir.</li>
                  <li>Tinjau dokumen pending lebih dari 2 hari.</li>
                  <li>Validasi unit kerja dan jabatan yang baru ditambahkan.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')

