@include('template.header', ['pageTitle' => 'Buat Surat', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'buat-surat'])

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <div><h1 class="text-sm font-bold text-slate-900">Buat Surat Baru</h1><p class="text-[11px] text-slate-400 font-light">Upload draft dan lengkapi data surat.</p></div>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-2xl mx-auto space-y-6">
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30"><h2 class="text-sm font-bold text-slate-900">Langkah 1 - Upload Draft Surat</h2></div>
            <div class="px-6 py-6 space-y-4">
              <div class="rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3 text-[11px] text-blue-600">Format file DOCX, maksimal 10 MB.</div>
              <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-6 py-10 text-center">
                <p class="text-xs font-semibold text-slate-700">Klik atau seret file ke sini</p>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Format: DOCX</p>
              </div>
              <input type="text" placeholder="Perihal surat" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm" />
              <textarea rows="5" placeholder="Ringkasan isi surat..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm resize-none"></textarea>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.footer')
