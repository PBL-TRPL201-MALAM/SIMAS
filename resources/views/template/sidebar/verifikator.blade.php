<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col overflow-y-auto border-r border-slate-100 bg-white shadow-xl transition-all duration-300 xl:relative xl:z-30 xl:h-screen xl:translate-x-0 xl:shadow-none">
  <!-- Sidebar Verifikator memisahkan pekerjaan aktif dan monitoring seluruh dokumen per jenis surat. -->
  <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-blue-50/80 blur-2xl pointer-events-none"></div>

  <div class="relative flex items-center gap-2.5 px-5 h-16 border-b border-slate-100/80 shrink-0">
    <img src="{{ asset('images/logo.png') }}" alt="Logo SIMAS" class="h-7 w-auto object-contain" />
    <div>
      <span class="text-sm font-bold tracking-tight text-slate-900 block">SIMAS</span>
      <span class="text-[10px] font-medium text-blue-500">{{ auth()->user()->role === 'PENANDATANGAN' ? 'Penandatangan' : 'Verifikator' }}</span>
    </div>
  </div>

  <!-- $activePage menentukan class aktif, sedangkan $sidebarStats menampilkan jumlah dokumen yang menunggu. -->
  @php
    $activePage = $activePage ?? '';
    $sidebarStats = $sidebarStats ?? ['surat_menunggu_count' => 0, 'sk_menunggu_count' => 0];
  @endphp
  <nav class="flex-1 px-3 py-4 space-y-0.5">
    <p class="px-2 pb-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Menu Utama</p>

    <a href="{{ route('verifikator.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'dashboard' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
      <span>Dashboard</span>
    </a>

    <div class="pt-3 pb-1.5"><p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Biasa</p></div>
    <!-- Badge pada menu Perlu Verifikasi menghitung antrean aktif yang sudah boleh diproses oleh verifikator login. -->
    <a href="{{ route('verifikator.surat-menunggu') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'surat-menunggu' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <div class="flex items-center gap-3">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span>Perlu Verifikasi</span>
      </div>
      <span class="text-[10px] font-semibold {{ $activePage === 'surat-menunggu' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-600' }} px-1.5 py-0.5 rounded-full">{{ $sidebarStats['surat_menunggu_count'] }}</span>
    </a>
    <a href="{{ route('verifikator.surat-semua') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'surat-semua' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
      <span>Semua Surat</span>
    </a>

    <div class="pt-3 pb-1.5"><p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Keputusan</p></div>
    <a href="{{ route('verifikator.sk-menunggu') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'sk-menunggu' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <div class="flex items-center gap-3">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span>Perlu Verifikasi SK</span>
      </div>
      <span class="text-[10px] font-semibold {{ $activePage === 'sk-menunggu' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-600' }} px-1.5 py-0.5 rounded-full">{{ $sidebarStats['sk_menunggu_count'] }}</span>
    </a>
    <a href="{{ route('verifikator.sk-semua') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'sk-semua' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
      <span>Semua SK</span>
    </a>
  </nav>

  <div class="px-3 py-4 border-t border-slate-100/80 shrink-0 space-y-0.5">
    <!-- Area bawah sidebar berisi profil verifikator dan form logout. -->
    <a href="{{ route('verifikator.profil') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'profil' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
      <span>Profil Saya</span>
    </a>
    <form action="{{ route('logout') }}" method="POST" class="w-full">
      <!-- csrf wajib karena logout adalah request POST yang mengubah session user. -->
      @csrf

      <button type="submit"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-500 transition-all duration-200">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Keluar</span>
      </button>
    </form>
  </div>
</aside>
