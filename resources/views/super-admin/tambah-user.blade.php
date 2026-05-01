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
        <a href="{{ route('super-admin.users.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Kembali</a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-100 p-6">
            <div class="mb-5">
              <h2 class="text-sm font-semibold text-slate-800">Data Akun</h2>
              <p class="text-[11px] text-slate-400 font-light mt-1">Tampilan ini sudah menyiapkan field inti untuk tambah user, edit user, dan ubah status.</p>
            </div>

            @if ($errors->any())
              <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                {{ $errors->first() }}
              </div>
            @endif

            <form action="{{ route('super-admin.users.store') }}" method="POST" class="space-y-5">
              @csrf
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Nama Lengkap</label>
                  <input type="text" name="nama" value="{{ old('nama') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Username</label>
                  <input type="text" name="username" value="{{ old('username') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Email</label>
                  <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Password</label>
                  <input type="password" name="password" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Role</label>
                  <select name="role" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    @foreach ($roles as $role)
                      <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Status Akun</label>
                  <select name="is_active" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Unit Kerja</label>
                  <select name="unit_kerja" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option value="">Pilih unit kerja</option>
                    @foreach ($unitKerjas as $unitKerja)
                      <option value="{{ $unitKerja }}" {{ old('unit_kerja') === $unitKerja ? 'selected' : '' }}>{{ $unitKerja }}</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Jabatan</label>
                  <select name="jabatan" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option value="">Pilih jabatan</option>
                    @foreach ($jabatans as $jabatan)
                      <option value="{{ $jabatan }}" {{ old('jabatan') === $jabatan ? 'selected' : '' }}>{{ $jabatan }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">NIP / NIK</label>
                <input type="text" name="nip_nik" value="{{ old('nip_nik') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
              </div>

              <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">Simpan User</button>
                <a href="{{ route('super-admin.users.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Batal</a>
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
                  <span class="font-medium text-slate-700">{{ old('nama') ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Role</span>
                  <span class="font-medium text-slate-700">{{ old('role') ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Status</span>
                  <span class="font-medium text-blue-600">{{ old('is_active', '1') === '1' ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Unit Kerja</span>
                  <span class="font-medium text-slate-700">{{ old('unit_kerja') ?: '-' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')

