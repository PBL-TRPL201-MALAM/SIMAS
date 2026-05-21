@include('template.layouts.header', ['pageTitle' => 'Pengajuan SK Masuk'])
@include('admin.partials.detail-modal')
@include('template.sidebar.admin')

    {{-- View ini menerima $skList dari AdminSkController dan tidak memakai fallback data contoh statis. --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Pengajuan SK Masuk</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar pengajuan surat keputusan yang masuk</p>
        </div>
        <a href="{{ route('admin.profil') }}"
          class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-4">
          @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
              {{ session('status') }}
            </div>
          @endif

          @if (session('error'))
            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
              {{ session('error') }}
            </div>
          @endif

          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Pengajuan SK Masuk</h2>
            <span class="text-[11px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">{{ $skList->count() }} dokumen</span>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tentang</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  @forelse($skList as $dokumen)
                    @php
                      $sk = $dokumen->suratKeputusan;
                      $menimbangText = $sk?->skMenimbang?->sortBy('urutan')->pluck('isi_menimbang')->implode("\n") ?: '-';
                      $mengingatText = $sk?->dasarHukum?->map(function ($dasarHukum) {
                          return $dasarHukum->labelMengingat();
                      })->implode("\n") ?: '-';
                      $memutuskanText = $sk?->skMemutuskan?->sortBy('urutan')->pluck('isi_memutuskan')->implode("\n") ?: '-';
                    @endphp
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                      data-dokumen-id="{{ $dokumen->dokumen_id }}"
                      data-jenis="SK"
                      data-perihal="{{ $sk?->judul_sk ?? '-' }}"
                      data-pemohon="{{ $dokumen->pemohon?->nama ?? '-' }}"
                      data-tanggal="{{ optional($dokumen->created_at)->format('d M Y') }}"
                      data-status="{{ $dokumen->status_dokumen }}"
                      data-ringkasan="{{ $sk?->tentang ?? '-' }}"
                      data-sk-tentang="{{ $sk?->tentang ?? '-' }}"
                      data-sk-menimbang="{{ $menimbangText }}"
                      data-sk-memutuskan="{{ $memutuskanText }}">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">{{ $dokumen->pemohon?->nama ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[220px]">{{ $sk?->judul_sk ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-500 max-w-[260px]">{{ \Illuminate\Support\Str::limit($sk?->tentang ?? '-', 90) }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ optional($dokumen->created_at)->format('d M Y') }}</p></td>
                      <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span></td>
                      <td class="px-5 py-3.5">
                        <div class="flex flex-wrap items-center gap-2">
                          <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                          <a href="{{ route('admin.proses-sk', ['dokumen' => $dokumen->dokumen_id]) }}" class="inline-flex items-center text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Review & Proses</a>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-600">Belum ada pengajuan SK masuk.</p>
                        <p class="mt-1 text-[11px] font-light text-slate-400">Pengajuan dari Pemohon akan muncul di sini setelah dikirim.</p>
                      </td>
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
