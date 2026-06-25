{{-- File profil tunggal yang dipakai oleh semua role (kecuali Super Admin yang memakai halaman edit user terpisah).
     Variabel $sidebarView, $roleLabel, $routePrefix, dan $pageSubtitle dikirim dari ProfilController::edit(). --}}

@include('template.layouts.header', ['pageTitle' => 'Profil Saya'])
@include($sidebarView, ['activePage' => 'profil'])

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Profil Saya</h1>
          <p class="text-[11px] text-slate-400 font-light">{{ $pageSubtitle }}</p>
        </div>
        <div class="w-9 h-9"></div>
      </header>

      <main class="flex-1 overflow-y-auto p-6 bg-slate-100">
        <div id="page-profil" class="page-content max-w-5xl mx-auto space-y-6">

          @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
              {{ session('status') }}
            </div>
          @endif

          <div class="rounded-2xl bg-white border border-slate-200/80 shadow-lg shadow-slate-200/50 p-6">
            <div class="flex items-center gap-4 mb-6">
              <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div>
                <h2 class="text-base font-bold text-slate-900">{{ $user->nama }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }} &middot; {{ $roleLabel }}</p>
              </div>
            </div>

            @if ($errors->any() && ! $errors->has('current_password') && ! $errors->has('password'))
              <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                {{ $errors->first() }}
              </div>
            @endif

            <form action="{{ route($routePrefix . '.profil.update') }}" method="POST" class="space-y-5">
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

              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Email <span class="text-[10px] font-normal text-slate-400">(tidak dapat diubah)</span></label>
                <input type="email" value="{{ $user->email }}" readonly class="w-full rounded-xl border border-slate-200 bg-gray-100 text-gray-500 cursor-not-allowed px-4 py-3 text-sm focus:outline-none" />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Unit Kerja <span class="text-[10px] font-normal text-slate-400">(tidak dapat diubah)</span></label>
                  <input type="text" value="{{ $user->unit_kerja ?? '-' }}" readonly class="w-full rounded-xl border border-slate-200 bg-gray-100 text-gray-500 cursor-not-allowed px-4 py-3 text-sm focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">NIP / NIK <span class="text-[10px] font-normal text-slate-400">(tidak dapat diubah)</span></label>
                  <input type="text" value="{{ $user->nip_nik ?? '-' }}" readonly class="w-full rounded-xl border border-slate-200 bg-gray-100 text-gray-500 cursor-not-allowed px-4 py-3 text-sm focus:outline-none" />
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Jabatan <span class="text-[10px] font-normal text-slate-400">(tidak dapat diubah)</span></label>
                <input type="text" value="{{ $user->jabatan ?? '-' }}" readonly class="w-full rounded-xl border border-slate-200 bg-gray-100 text-gray-500 cursor-not-allowed px-4 py-3 text-sm focus:outline-none" />
              </div>

              <p class="text-[10px] text-slate-400 font-light">Data Email, Unit Kerja, NIP/NIK, dan Jabatan hanya dapat diubah oleh Super Admin.</p>

              <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
                Simpan Perubahan
              </button>
            </form>
          </div>

          <div class="rounded-2xl bg-white border border-slate-200/80 shadow-lg shadow-slate-200/50 p-6">
            <h2 class="text-sm font-semibold text-slate-800 mb-1">Ganti Password</h2>
            <p class="text-[11px] text-slate-400 font-light mb-5">Pastikan password baru minimal 8 karakter.</p>

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

            <form action="{{ route($routePrefix . '.profil.password') }}" method="POST" class="space-y-4">
              @csrf
              @method('PUT')
              <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Password Saat Ini</label>
                <input type="password" name="current_password" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Password Baru</label>
                  <input type="password" name="password" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-600 mb-2">Konfirmasi Password Baru</label>
                  <input type="password" name="password_confirmation" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-400 focus:outline-none" />
                </div>
              </div>
              <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">
                Ganti Password
              </button>
            </form>
          </div>

        </div>
      </main>
    </div>

@include('template.layouts.footer')
