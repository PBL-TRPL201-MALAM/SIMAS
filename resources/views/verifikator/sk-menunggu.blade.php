@include('template.header', ['pageTitle' => 'SK Menunggu Verifikasi', 'modalVariant' => 'none'])
@include('template.verifikator-sidebar', ['activePage' => 'sk-menunggu'])
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">SK Menunggu Verifikasi</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar surat keputusan yang menunggu verifikasi.</p>
        </div>
        <a href="{{ route('verifikator.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-sk-menunggu" class="page-content space-y-4">
          @if (session('status'))
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3">
              <p class="text-[11px] font-semibold text-emerald-700">{{ session('status') }}</p>
            </div>
          @endif

          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">SK Menunggu Verifikasi</h2>
            <span class="text-[11px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">{{ $skMenunggu->count() }} dokumen</span>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Level Saya</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  @forelse ($skMenunggu as $item)
                    @php($pdfFile = $item->dokumen->dokumenFiles->first())
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[220px]">{{ $item->dokumen->suratKeputusan?->judul_sk ?? $item->dokumen->suratKeputusan?->tentang ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-xs text-slate-600">{{ $item->dokumen->pemohon?->nama ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Level {{ $item->level }}</span></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ optional($item->dokumen->created_at)->format('d M Y') }}</p></td>
                      <td class="px-5 py-3.5">
                        <div class="flex flex-wrap items-center gap-2">
                          <a href="{{ route('verifikator.sk.detail', $item->dokumen) }}" class="text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</a>
                          @if ($pdfFile)
                            <a href="{{ route('verifikator.sk.unduh-pdf', $item->dokumen) }}" class="text-[11px] font-medium text-slate-500 hover:text-slate-700 transition-colors duration-200">Unduh PDF</a>
                          @endif
                          <a href="{{ route('verifikator.sk.detail', ['dokumen' => $item->dokumen, 'aksi' => 'setuju']) }}" class="inline-flex items-center text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Setujui</a>
                          <a href="{{ route('verifikator.sk.detail', ['dokumen' => $item->dokumen, 'aksi' => 'tolak']) }}" class="inline-flex items-center text-[11px] font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg transition-all duration-200">Tolak</a>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400">Belum ada SK yang menunggu verifikasi Anda.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
@include('template.footer')
