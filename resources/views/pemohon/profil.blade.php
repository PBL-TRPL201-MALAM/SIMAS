@include('template.header', ['pageTitle' => 'Profil Pemohon', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'profil'])

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <div><h1 class="text-sm font-bold text-slate-900">Profil Saya</h1><p class="text-[11px] text-slate-400 font-light">Informasi dasar akun pemohon.</p></div>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div class="rounded-2xl bg-white border border-slate-100 p-6">
          <p class="text-sm font-semibold text-slate-700">Profil Saya</p>
          <p class="text-xs text-slate-400 font-light mt-1">Halaman ini sudah dipisah sebagai view mandiri.</p>
        </div>
      </main>
    </div>

@include('template.footer')
