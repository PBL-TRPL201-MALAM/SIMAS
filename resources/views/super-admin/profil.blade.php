@include('template.layouts.header', ['pageTitle' => 'Detail User'])
@include('template.sidebar.super-admin')

    <!-- View edit user menerima $user, $roles, $jabatans, dan $unitKerjas dari UserControlleredit. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Detail User</h1>
          <p class="text-[11px] text-slate-400 font-light">Lihat dan ubah data user oleh Super Admin</p>
        </div>
        <a href="{{ route('super-admin.users.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Kembali</a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-100 p-6">
            <div class="mb-5">
              <h2 class="text-sm font-semibold text-slate-800">Edit Data User</h2>
              <p class="text-[11px] text-slate-400 font-light mt-1">Perbarui data akun. Gunakan form Ganti Password di bawah untuk mengatur ulang password.</p>
            </div>

            <!-- Flash status muncul setelah data user berhasil diperbarui. -->
            @if (session('status'))
              <div class="mb-5 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
                {{ session('status') }}
              </div>
            @endif

            <!-- Error validasi atau error bisnis seperti self-deactivation ditampilkan di sini. Error password ditampilkan terpisah di card Ganti Password. -->
            @if ($errors->any() && ! $errors->has('current_password') && ! $errors->has('password'))
              <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                {{ $errors->first() }}
              </div>
            @endif

            <!-- $isCurrentUser dipakai untuk mencegah Super Admin menonaktifkan akun yang sedang dipakai. -->
            @php($isCurrentUser = auth()->id() === $user->user_id)

            <!-- Form update mengirim data ke route super-admin.users.update dengan method PUT. -->
            <form action="{{ route('super-admin.users.update', $user) }}" method="POST" class="space-y-5">
              <!-- csrf wajib, dan method('PUT') memberi tahu Laravel bahwa form POST ini harus diproses sebagai PUT. -->
              @csrf
              @method('PUT')

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Nama Lengkap</label>
                  <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Username</label>
                  <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Email</label>
                  <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Role</label>
                  <select name="role" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    @foreach ($roles as $role)
                      <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Status Akun</label>
                  <!-- Select status dinonaktifkan jika user yang diedit adalah akun Super Admin yang sedang login. -->
                  <select name="is_active" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" {{ $isCurrentUser && $user->is_active ? 'disabled' : '' }}>
                    <option value="1" {{ old('is_active', $user->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $user->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Nonaktif</option>
                  </select>
                  @if ($isCurrentUser && $user->is_active)
                    <!-- Hidden input menjaga nilai is_active tetap terkirim meskipun select status sedang disabled. -->
                    <input type="hidden" name="is_active" value="1" />
                    <p class="mt-1 text-[11px] text-slate-400">Akun SUPER_ADMIN yang sedang dipakai tidak bisa dinonaktifkan.</p>
                  @endif
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">NIP / NIK</label>
                  <input type="text" name="nip_nik" value="{{ old('nip_nik', $user->nip_nik) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Unit Kerja</label>
                  <select name="unit_kerja" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option value="">Pilih unit kerja</option>
                    @foreach ($unitKerjas as $unitKerja)
                      <option value="{{ $unitKerja }}" {{ old('unit_kerja', $user->unit_kerja) === $unitKerja ? 'selected' : '' }}>{{ $unitKerja }}</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Jabatan</label>
                  <select name="jabatan" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                    <option value="">Pilih jabatan</option>
                    @foreach ($jabatans as $jabatan)
                      <option value="{{ $jabatan }}" {{ old('jabatan', $user->jabatan) === $jabatan ? 'selected' : '' }}>{{ $jabatan }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">Simpan Perubahan</button>
              </div>
            </form>

            <!-- Form toggle status memakai PATCH untuk aksi cepat aktif/nonaktif tanpa mengubah field profil lain. -->
            <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="mt-3">
              <!-- csrf dan method('PATCH') diperlukan karena HTML form tidak punya method PATCH bawaan. -->
              @csrf
              @method('PATCH')
              @if ($isCurrentUser && $user->is_active)
                <!-- Tombol dibuat nonaktif jika targetnya adalah akun yang sedang dipakai. -->
                <span class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-300 cursor-not-allowed">
                  User Sedang Dipakai
                </span>
              @else
                <!-- Tombol ini mengaktifkan atau menonaktifkan user sesuai kondisi is_active saat ini. -->
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">
                  {{ $user->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}
                </button>
              @endif
            </form>

            <!-- ====== GANTI PASSWORD (BARU) ====== -->
            <!-- Jika Super Admin mengedit user LAIN, form langsung reset password tanpa perlu password lama dan konfirmasi. -->
            <!-- Jika Super Admin mengedit DIRI SENDIRI, form tetap memerlukan password saat ini + konfirmasi. -->
            <div class="mt-6 rounded-2xl border border-slate-100 bg-slate-50/40 p-6">
              <h3 class="text-sm font-semibold text-slate-800 mb-1">Ganti Password</h3>
              @if ($isCurrentUser)
                <p class="text-[11px] text-slate-400 font-light mb-5">Pastikan password baru minimal 8 karakter. Masukkan password saat ini sebagai konfirmasi keamanan.</p>
              @else
                <p class="text-[11px] text-slate-400 font-light mb-5">Atur password baru untuk user ini. Minimal 8 karakter.</p>
              @endif

              @if ($errors->has('current_password'))
                <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                  {{ $errors->first('current_password') }}
                </div>
              @endif
              @if ($errors->has('password'))
                <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                  {{ $errors->first('password') }}
                </div>
              @endif

              <!-- Route form berbeda tergantung apakah Super Admin mengedit diri sendiri atau user lain. -->
              <form action="{{ $isCurrentUser ? route('super-admin.profil.password') : route('super-admin.users.reset-password', $user) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                @if ($isCurrentUser)
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Password Saat Ini</label>
                    <div class="relative">
                      <input type="password" name="current_password" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-10 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                      <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        <svg class="w-4 h-4 eye-on hidden" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                      </button>
                    </div>
                  </div>
                @endif
                @if ($isCurrentUser)
                  {{-- Saat edit diri sendiri: dua kolom (password baru + konfirmasi) --}}
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs font-semibold text-slate-600 mb-2">Password Baru</label>
                      <div class="relative">
                        <input type="password" name="password" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-10 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition-colors">
                          <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                          <svg class="w-4 h-4 eye-on hidden" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>
                      </div>
                    </div>
                    <div>
                      <label class="block text-xs font-semibold text-slate-600 mb-2">Konfirmasi Password Baru</label>
                      <div class="relative">
                        <input type="password" name="password_confirmation" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-10 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition-colors">
                          <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                          <svg class="w-4 h-4 eye-on hidden" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>
                      </div>
                    </div>
                  </div>
                @else
                  {{-- Saat edit user lain: cukup satu kolom password baru saja --}}
                  <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Password Baru</label>
                    <div class="relative">
                      <input type="password" name="password" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-10 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                      <button type="button" onclick="togglePasswordVisibility(this)" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        <svg class="w-4 h-4 eye-on hidden" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                      </button>
                    </div>
                  </div>
                @endif
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">
                  Ganti Password
                </button>
              </form>
            </div>
            <!-- ====== END GANTI PASSWORD ====== -->

            <!-- Script toggle ikon mata untuk input password di halaman ini. -->
            <script>
              function togglePasswordVisibility(button) {
                const input = button.parentElement.querySelector('input');
                const eyeOff = button.querySelector('.eye-off');
                const eyeOn = button.querySelector('.eye-on');
                if (input.type === 'password') {
                  input.type = 'text';
                  eyeOff.classList.add('hidden');
                  eyeOn.classList.remove('hidden');
                } else {
                  input.type = 'password';
                  eyeOff.classList.remove('hidden');
                  eyeOn.classList.add('hidden');
                }
              }
            </script>

          </div>

          <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-100 p-6">
              <!-- Panel ringkasan membaca langsung properti $user dari model Eloquent yang dikirim controller. -->
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center">
                  <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-base font-bold text-slate-900">{{ $user->nama }}</h2>
                  <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
              </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5">
              <h3 class="text-sm font-semibold text-slate-800">Ringkasan User</h3>
              <div class="mt-4 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Role</span>
                  <span class="font-medium text-slate-700">{{ $user->role }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Status</span>
                  <span class="font-medium {{ $user->is_active ? 'text-blue-600' : 'text-slate-500' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Unit Kerja</span>
                  <span class="font-medium text-slate-700">{{ $user->unit_kerja ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Jabatan</span>
                  <span class="font-medium text-slate-700">{{ $user->jabatan ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Dibuat</span>
                  <span class="font-medium text-slate-700">{{ optional($user->created_at)->format('d M Y H:i') ?: '-' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    const jabatanSelect = document.querySelector('select[name="jabatan"]');
    const signerJabatans = @json($signerJabatans);

    function updateJabatanOptions() {
        const isSigner = roleSelect.value === 'PENANDATANGAN';
        const selectedValue = jabatanSelect.value;

        Array.from(jabatanSelect.options).forEach(option => {
            if (option.value === "") return;
            const isAllowed = !isSigner || signerJabatans.includes(option.value);
            option.disabled = !isAllowed;
            option.style.display = isAllowed ? 'block' : 'none';
        });

        // Reset selection if the current one becomes invalid
        if (selectedValue && isSigner && !signerJabatans.includes(selectedValue)) {
            jabatanSelect.value = "";
        }
    }

    if (roleSelect && jabatanSelect) {
        roleSelect.addEventListener('change', updateJabatanOptions);
        updateJabatanOptions();
    }
});
</script>

@include('template.layouts.footer')
