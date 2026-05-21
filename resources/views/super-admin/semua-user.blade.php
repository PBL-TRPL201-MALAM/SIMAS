@include('template.layouts.header', ['pageTitle' => 'Semua User'])
@include('template.sidebar.super-admin')

    <!-- View ini menerima $users, $activeCount, $inactiveCount, $topRole, dan $currentStatus dari UserController::index. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Semua User</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar akun, status, dan role pengguna</p>
        </div>
        <a href="{{ route('super-admin.users.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">Tambah User</a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-6">
          <!-- Flash status muncul setelah tambah/update/toggle status user berhasil. -->
          @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
              {{ session('status') }}
            </div>
          @endif

          <!-- Error status biasanya berasal dari aturan bisnis seperti tidak boleh menonaktifkan Super Admin terakhir. -->
          @if ($errors->has('status'))
            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
              {{ $errors->first('status') }}
            </div>
          @endif

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Kartu ringkasan ini memakai hasil count dari controller, bukan menghitung ulang di Blade. -->
            <div class="rounded-2xl bg-white border border-slate-100 p-4">
              <p class="text-xs text-slate-400 font-light">User Aktif</p>
              <p class="text-2xl font-extrabold text-slate-900 mt-2">{{ $activeCount }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-4">
              <p class="text-xs text-slate-400 font-light">User Nonaktif</p>
              <p class="text-2xl font-extrabold text-slate-900 mt-2">{{ $inactiveCount }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-4">
              <p class="text-xs text-slate-400 font-light">Role Terbanyak</p>
              <p class="text-2xl font-extrabold text-slate-900 mt-2">{{ $topRole ?? '-' }}</p>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 px-5 py-4 border-b border-slate-100">
              <div>
                <h3 class="text-sm font-semibold text-slate-800">Manajemen User</h3>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Super Admin bisa melihat detail, edit, ubah role, dan aktif/nonaktif user.</p>
              </div>
              <div class="flex flex-wrap gap-2">
                <!-- Link filter mengirim query string status ke UserControllerindex untuk memuat ulang daftar user. -->
                <a href="{{ route('super-admin.users.index') }}" class="rounded-xl border px-3 py-2 text-xs font-medium {{ $currentStatus === '' ? 'border-blue-200 bg-blue-50 text-blue-600' : 'border-slate-200 text-slate-500' }}">Semua</a>
                <a href="{{ route('super-admin.users.index', ['status' => 'active']) }}" class="rounded-xl border px-3 py-2 text-xs font-medium {{ $currentStatus === 'active' ? 'border-blue-200 bg-blue-50 text-blue-600' : 'border-slate-200 text-slate-500' }}">Aktif</a>
                <a href="{{ route('super-admin.users.index', ['status' => 'inactive']) }}" class="rounded-xl border px-3 py-2 text-xs font-medium {{ $currentStatus === 'inactive' ? 'border-blue-200 bg-blue-50 text-blue-600' : 'border-slate-200 text-slate-500' }}">Nonaktif</a>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Nama</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Username</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Role</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Email</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <!-- forelse menampilkan daftar user atau empty state jika filter tidak menemukan data. -->
                  @forelse ($users as $user)
                    <!-- $isCurrentUser dipakai agar akun yang sedang login tidak bisa dinonaktifkan dari daftar ini. -->
                    @php($isCurrentUser = auth()->id() === $user->user_id)
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                      <td class="px-5 py-3.5">
                        <p class="text-xs font-semibold text-slate-800">{{ $user->nama }}</p>
                        <p class="text-[11px] text-slate-400">{{ $user->unit_kerja ?: '-' }}</p>
                      </td>
                      <td class="px-5 py-3.5"><p class="text-xs text-slate-600">{{ $user->username }}</p></td>
                      <td class="px-5 py-3.5">
                        <!-- Badge role berubah warna berdasarkan role user agar mudah dipindai oleh Super Admin. -->
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $user->role === 'SUPER_ADMIN' ? 'text-slate-700 bg-slate-100' : ($user->role === 'ADMIN_SURAT' ? 'text-blue-600 bg-blue-50' : ($user->role === 'VERIFIKATOR' ? 'text-amber-600 bg-amber-50' : 'text-indigo-600 bg-indigo-50')) }}">
                          {{ $user->role }}
                        </span>
                      </td>
                      <td class="px-5 py-3.5"><p class="text-xs text-slate-500">{{ $user->email }}</p></td>
                      <td class="px-5 py-3.5">
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $user->is_active ? 'text-blue-600 bg-blue-50' : 'text-slate-500 bg-slate-100' }}">
                          {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                      </td>
                      <td class="px-5 py-3.5">
                        <div class="flex flex-wrap items-center gap-2 text-[11px] font-medium">
                          <a href="{{ route('super-admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                          @if ($isCurrentUser && $user->is_active)
                            <!-- Aksi toggle disembunyikan untuk akun yang sedang dipakai agar user tidak mengunci dirinya sendiri. -->
                            <span class="text-slate-300 cursor-not-allowed">Sedang Dipakai</span>
                          @else
                            <!-- Form ini menembak route toggle-status untuk aktif/nonaktif user tanpa membuka halaman edit. -->
                            <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST">
                              <!-- csrf dan method('PATCH') diperlukan untuk aksi update sebagian pada status akun. -->
                              @csrf
                              @method('PATCH')
                              <button type="submit" class="{{ $user->is_active ? 'text-red-500' : 'text-blue-600' }}">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                              </button>
                            </form>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="px-5 py-8 text-center text-xs text-slate-400">Belum ada user yang sesuai filter.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.layouts.footer')

