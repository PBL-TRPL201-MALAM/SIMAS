@include('template.header', ['pageTitle' => 'Tambah User'])
@include('template.super-admin-sidebar')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Tambah User</h1>
          <p class="text-[11px] text-slate-400 font-light">Form untuk membuat atau memperbarui akun</p>
        </div>
        <a href="{{ route('super-admin.semua-user') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Kembali</a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-100 p-6">
            <div class="mb-5">
              <h2 class="text-sm font-semibold text-slate-800">Data Akun</h2>
              <p class="text-[11px] text-slate-400 font-light mt-1">Tampilan ini sudah menyiapkan field inti untuk tambah user, edit user, dan ubah status.</p>
            </div>

            <form class="space-y-5">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Nama Lengkap</label>
                  <input type="text" value="Nur Aisyah" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Username</label>
                  <input type="text" value="nur.aisyah" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Email</label>
                  <input type="email" value="nur.aisyah@kampus.ac.id" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Nomor HP</label>
                  <input type="text" value="0812-3456-7890" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Role</label>
                  <select class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option>ADMIN_TU</option>
                    <option>PEMOHON</option>
                    <option selected>VERIFIKATOR</option>
                    <option>SUPER_ADMIN</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Status Akun</label>
                  <select class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option selected>Aktif</option>
                    <option>Nonaktif</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Unit Kerja</label>
                  <select class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option selected>Direktorat</option>
                    <option>Akademik</option>
                    <option>Keuangan</option>
                    <option>Mahasiswa</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Jabatan</label>
                  <select class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option selected>Koordinator Verifikator</option>
                    <option>Staf Administrasi</option>
                    <option>Operator</option>
                    <option>Admin Sistem</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Catatan</label>
                <textarea rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">Akun disiapkan untuk verifikasi dokumen tingkat 1 dan tingkat 2.</textarea>
              </div>

              <div class="flex flex-wrap gap-3">
                <button type="button" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">Simpan User</button>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Reset Password</button>
              </div>
            </form>
          </div>

          <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-100 p-5">
              <h3 class="text-sm font-semibold text-slate-800">Role yang tersedia</h3>
              <ul class="mt-4 space-y-2 text-xs text-slate-500 font-light">
                <li><strong class="text-slate-700">ADMIN_TU</strong> untuk proses dan publikasi dokumen.</li>
                <li><strong class="text-slate-700">PEMOHON</strong> untuk membuat dan memantau pengajuan.</li>
                <li><strong class="text-slate-700">VERIFIKATOR</strong> untuk validasi dan keputusan dokumen.</li>
                <li><strong class="text-slate-700">SUPER_ADMIN</strong> untuk kontrol penuh user dan monitoring.</li>
              </ul>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5">
              <h3 class="text-sm font-semibold text-slate-800">Preview Detail User</h3>
              <div class="mt-4 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Nama</span>
                  <span class="font-medium text-slate-700">Nur Aisyah</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Role</span>
                  <span class="font-medium text-slate-700">VERIFIKATOR</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Status</span>
                  <span class="font-medium text-blue-600">Aktif</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Unit Kerja</span>
                  <span class="font-medium text-slate-700">Direktorat</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')

