@include('template.header', ['pageTitle' => 'Unit Kerja'])
@include('template.super-admin-sidebar')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Unit Kerja</h1>
          <p class="text-[11px] text-slate-400 font-light">Data unit kerja dan jabatan pendukung akun</p>
        </div>
        <button type="button" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">Tambah Unit</button>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
              <h2 class="text-sm font-semibold text-slate-800">Daftar Unit Kerja</h2>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Unit</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">PIC</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs">
                  <tr><td class="px-5 py-4 font-medium text-slate-800">Akademik</td><td class="px-5 py-4 text-slate-500">Admin Akademik</td><td class="px-5 py-4"><span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Aktif</span></td></tr>
                  <tr><td class="px-5 py-4 font-medium text-slate-800">Direktorat</td><td class="px-5 py-4 text-slate-500">Sekretariat</td><td class="px-5 py-4"><span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Aktif</span></td></tr>
                  <tr><td class="px-5 py-4 font-medium text-slate-800">Kemahasiswaan</td><td class="px-5 py-4 text-slate-500">Staf Mahasiswa</td><td class="px-5 py-4"><span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Nonaktif</span></td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
              <h2 class="text-sm font-semibold text-slate-800">Daftar Jabatan</h2>
            </div>
            <div class="divide-y divide-slate-100">
              <div class="px-5 py-4">
                <p class="text-xs font-semibold text-slate-800">Koordinator Verifikator</p>
                <p class="text-xs text-slate-500 font-light mt-1">Dipakai untuk user verifikator level utama.</p>
              </div>
              <div class="px-5 py-4">
                <p class="text-xs font-semibold text-slate-800">Staf Administrasi</p>
                <p class="text-xs text-slate-500 font-light mt-1">Digunakan oleh role ADMIN_TU.</p>
              </div>
              <div class="px-5 py-4">
                <p class="text-xs font-semibold text-slate-800">Operator Sistem</p>
                <p class="text-xs text-slate-500 font-light mt-1">Khusus akun internal untuk dukungan aplikasi.</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')

