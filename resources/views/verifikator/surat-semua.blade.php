@include('template.header', ['pageTitle' => 'Semua Surat', 'modalVariant' => 'verifikator'])
@include('template.verifikator-sidebar', ['activePage' => 'surat-semua'])
<div class="flex flex-col flex-1 min-w-0 overflow-hidden"><header class="flex items-center h-16 px-6 bg-white border-b border-slate-100/80 shrink-0"><div><h1 class="text-sm font-bold text-slate-900">Semua Surat Biasa</h1></div></header><main class="flex-1 overflow-y-auto p-6"><div class="rounded-2xl bg-white border border-slate-100 p-6 text-sm text-slate-600">Rekap semua surat biasa ditempatkan pada halaman terpisah ini.</div></main></div>
@include('template.footer')
