@include('template.header', ['pageTitle' => 'Buat Pengajuan SK', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'buat-sk'])

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <div><h1 class="text-sm font-bold text-slate-900">Buat Pengajuan SK</h1><p class="text-[11px] text-slate-400 font-light">Isi data SK dan pilih dasar hukum.</p></div>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-2xl mx-auto space-y-6">
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30"><h2 class="text-sm font-bold text-slate-900">Data Surat Keputusan</h2></div>
            <div class="px-6 py-6 space-y-4">
              <input type="text" placeholder="Judul SK" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm" />
              <input type="text" placeholder="Tentang" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm" />
              <textarea rows="4" placeholder="Menimbang..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm resize-none"></textarea>
              <textarea rows="5" placeholder="Memutuskan..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm resize-none"></textarea>
              <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3 text-xs text-slate-500">Dasar hukum dipilih dari master data yang tersedia.</div>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')
