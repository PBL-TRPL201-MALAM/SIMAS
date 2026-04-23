<aside id="sidebar" class="relative flex flex-col w-64 shrink-0 bg-white border-r border-slate-100 h-screen overflow-y-auto z-30 transition-all duration-300">
  <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-blue-50/80 blur-2xl pointer-events-none"></div>

  <div class="relative flex items-center gap-2.5 px-5 h-16 border-b border-slate-100/80 shrink-0">
    <img src="{{ asset('images/logo.png') }}" alt="Logo SIMAS" class="h-7 w-auto object-contain" />
    <div>
      <span class="text-sm font-bold tracking-tight text-slate-900 block">SIMAS</span>
      <span class="text-[10px] font-medium text-blue-500">Verifikator</span>
    </div>
  </div>

  @php($activePage = $activePage ?? '')
  <nav class="flex-1 px-3 py-4 space-y-0.5">
    <p class="px-2 pb-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Menu Utama</p>

    <a href="{{ route('verifikator.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'dashboard' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
      <span>Dashboard</span>
    </a>

    <div class="pt-3 pb-1.5"><p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Biasa</p></div>
    <a href="{{ route('verifikator.surat-menunggu') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'surat-menunggu' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Menunggu</span><span class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">3</span></a>
    <a href="{{ route('verifikator.surat-disetujui') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'surat-disetujui' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Disetujui</span></a>
    <a href="{{ route('verifikator.surat-ditolak') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'surat-ditolak' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Ditolak</span></a>
    <a href="{{ route('verifikator.surat-semua') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'surat-semua' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Semua Surat</span></a>

    <div class="pt-3 pb-1.5"><p class="px-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Surat Keputusan</p></div>
    <a href="{{ route('verifikator.sk-menunggu') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'sk-menunggu' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Menunggu</span><span class="text-[10px] font-semibold bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">2</span></a>
    <a href="{{ route('verifikator.sk-disetujui') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'sk-disetujui' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Disetujui</span></a>
    <a href="{{ route('verifikator.sk-ditolak') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'sk-ditolak' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Ditolak</span></a>
    <a href="{{ route('verifikator.sk-semua') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'sk-semua' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Semua SK</span></a>
  </nav>

  <div class="px-3 py-4 border-t border-slate-100/80 shrink-0 space-y-0.5">
    <a href="{{ route('verifikator.profil') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $activePage === 'profil' ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600' }}"><span>Profil Saya</span></a>
    <a href="{{ route('login') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-500 transition-all duration-200"><span>Keluar</span></a>
  </div>
</aside>
