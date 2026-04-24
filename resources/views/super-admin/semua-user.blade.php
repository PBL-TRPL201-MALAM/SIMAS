@include('template.header', ['pageTitle' => 'Semua User'])
@include('template.super-admin-sidebar')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Semua User</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar akun, status, dan role pengguna</p>
        </div>
        <a href="{{ route('super-admin.tambah-user') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">Tambah User</a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white border border-slate-100 p-4">
              <p class="text-xs text-slate-400 font-light">User Aktif</p>
              <p class="text-2xl font-extrabold text-slate-900 mt-2">21</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-4">
              <p class="text-xs text-slate-400 font-light">User Nonaktif</p>
              <p class="text-2xl font-extrabold text-slate-900 mt-2">3</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-4">
              <p class="text-xs text-slate-400 font-light">Role Terbanyak</p>
              <p class="text-2xl font-extrabold text-slate-900 mt-2">PEMOHON</p>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 px-5 py-4 border-b border-slate-100">
              <div>
                <h3 class="text-sm font-semibold text-slate-800">Manajemen User</h3>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Super Admin bisa melihat detail, edit, ubah role, dan aktif/nonaktif user.</p>
              </div>
              <div class="flex flex-wrap gap-2">
                <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-500">Semua</button>
                <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-500">Aktif</button>
                <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-500">Nonaktif</button>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Nama</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Username</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Role</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Unit Kerja</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-semibold text-slate-800">Nabila Salsabila</p><p class="text-[11px] text-slate-400">nabila@kampus.ac.id</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">nabila.sa</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">ADMIN_TU</span></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500">Akademik</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Aktif</span></td>
                    <td class="px-5 py-3.5">
                      <div class="flex flex-wrap gap-2 text-[11px] font-medium">
                        <a href="{{ route('super-admin.tambah-user') }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                        <a href="{{ route('super-admin.role-akses') }}" class="text-blue-600 hover:text-blue-700">Ubah Role</a>
                        <button type="button" class="text-red-500">Nonaktifkan</button>
                      </div>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-semibold text-slate-800">Rian Kurniawan</p><p class="text-[11px] text-slate-400">rian@kampus.ac.id</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">rian.krn</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">VERIFIKATOR</span></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500">Direktorat</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Aktif</span></td>
                    <td class="px-5 py-3.5">
                      <div class="flex flex-wrap gap-2 text-[11px] font-medium">
                        <a href="{{ route('super-admin.tambah-user') }}" class="text-blue-600 hover:text-blue-700">Detail</a>
                        <a href="{{ route('super-admin.role-akses') }}" class="text-blue-600 hover:text-blue-700">Ubah Role</a>
                        <button type="button" class="text-red-500">Nonaktifkan</button>
                      </div>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-semibold text-slate-800">Super Admin SIMAS</p><p class="text-[11px] text-slate-400">root@simas.local</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">superadmin</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-full">SUPER_ADMIN</span></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500">Sistem</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Aktif</span></td>
                    <td class="px-5 py-3.5">
                      <div class="flex flex-wrap gap-2 text-[11px] font-medium">
                        <a href="{{ route('super-admin.role-akses') }}" class="text-blue-600 hover:text-blue-700">Lihat Akses</a>
                      </div>
                    </td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs font-semibold text-slate-800">Dina Oktavia</p><p class="text-[11px] text-slate-400">dina@kampus.ac.id</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600">dina.okt</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">PEMOHON</span></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500">Mahasiswa</p></td>
                    <td class="px-5 py-3.5"><span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Nonaktif</span></td>
                    <td class="px-5 py-3.5">
                      <div class="flex flex-wrap gap-2 text-[11px] font-medium">
                        <a href="{{ route('super-admin.tambah-user') }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                        <button type="button" class="text-blue-600">Aktifkan</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')

