@include('template.layouts.header', ['pageTitle' => 'Detail SK Verifikasi'])
@include('template.sidebar.verifikator', ['activePage' => $activePage ?? 'sk-menunggu'])

@php
  $sk = $suratKeputusan;
  $menimbangItems = $sk?->skMenimbang?->sortBy('urutan') ?? collect();
  $memutuskanItems = $sk?->skMemutuskan?->sortBy('urutan') ?? collect();
  $dasarHukumItems = $sk?->dasarHukum?->sortBy(fn ($item) => $item->pivot->urutan ?? 0) ?? collect();
  $diktumLabels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'];
  $diktumLabel = fn (int $index) => $diktumLabels[$index] ?? 'KE-' . ($index + 1);
  $alphaLabel = function (int $index): string {
      $label = '';
      $number = $index;

      do {
          $label = chr(97 + ($number % 26)) . $label;
          $number = intdiv($number, 26) - 1;
      } while ($number >= 0);

      return $label . '.';
  };
  $penandatanganLabel = $dokumen->penandatangan
      ? $dokumen->penandatangan->nama . ($dokumen->penandatangan->jabatan ? ' - ' . $dokumen->penandatangan->jabatan : '')
      : '-';
@endphp

    {{-- Halaman ini sengaja merender data SK dari database; PDF final SK belum dibuat pada tahap verifikasi ini. --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Detail Verifikasi SK</h1>
          <p class="text-[11px] text-slate-400 font-light">{{ ($isReadOnly ?? false) ? 'Tinjau isi SK dalam mode pemantauan read-only.' : 'Tinjau isi SK dan beri keputusan sesuai level verifikasi Anda.' }}</p>
        </div>
        <a href="{{ $backUrl ?? route('verifikator.sk-menunggu') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali
        </a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-6xl mx-auto space-y-5">
          @if ($errors->any())
            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3">
              <p class="text-xs font-semibold text-red-700">Keputusan SK belum bisa diproses:</p>
              <ul class="mt-1 space-y-1 text-[11px] text-red-600 font-light">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs text-emerald-700 font-light">
              {{ session('status') }}
            </div>
          @endif

          <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <section class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Review Isi SK</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Format review mengikuti halaman Admin Review SK agar verifikator membaca struktur yang sama.</p>
              </div>

              <div class="px-6 py-6 space-y-4">
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Judul SK</p>
                  <p class="text-sm font-semibold text-slate-800">{{ $sk?->judul_sk ?? '-' }}</p>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Pemohon</p>
                    <p class="text-sm font-medium text-slate-700">{{ $dokumen->pemohon?->nama ?? '-' }}</p>
                  </div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Pengajuan</p>
                    <p class="text-sm font-medium text-slate-700">{{ optional($dokumen->created_at)->format('d M Y H:i') ?? '-' }}</p>
                  </div>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Tentang</p>
                  <p class="text-sm font-medium leading-relaxed text-slate-700">{{ $sk?->tentang ?? '-' }}</p>
                </div>

                <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-4">
                  <div class="mb-3">
                    <p class="text-xs font-semibold text-blue-700">Metadata SK</p>
                    <p class="mt-0.5 text-[11px] text-blue-600 font-light">Penandatangan dibaca dari metadata utama dokumen dan menjadi level terakhir verifikasi.</p>
                  </div>
                  <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-xl bg-white/80 px-4 py-3">
                      <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-400 mb-1">Nomor SK</p>
                      <p class="text-xs font-medium text-slate-800">{{ $sk?->nomor_sk ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-white/80 px-4 py-3">
                      <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-400 mb-1">Tanggal SK</p>
                      <p class="text-xs font-medium text-slate-800">{{ optional($sk?->tanggal_sk)->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-white/80 px-4 py-3">
                      <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-400 mb-1">Penandatangan</p>
                      <p class="text-xs font-medium text-slate-800">{{ $penandatanganLabel }}</p>
                    </div>
                  </div>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Menimbang</p>
                  <ul class="space-y-1.5">
                    @forelse ($menimbangItems as $menimbang)
                      <li class="flex items-start gap-2 text-xs text-slate-600 font-light">
                        <span class="min-w-[54px] shrink-0 font-semibold text-slate-500">{{ $alphaLabel($loop->index) }}</span>
                        <span class="leading-relaxed">{{ $menimbang->isi_menimbang }}</span>
                      </li>
                    @empty
                      <li class="text-xs text-slate-400 font-light">-</li>
                    @endforelse
                  </ul>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Mengingat</p>
                  <ul class="space-y-1.5">
                    @forelse ($dasarHukumItems as $dasarHukum)
                      <li class="flex items-start gap-2 text-xs text-slate-600 font-light">
                        <span class="min-w-[54px] shrink-0 font-semibold text-slate-500">{{ $loop->iteration }}.</span>
                        <span class="leading-relaxed">{{ $dasarHukum->labelMengingat() }}</span>
                      </li>
                    @empty
                      <li class="text-xs text-slate-400 font-light">-</li>
                    @endforelse
                  </ul>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Memutuskan</p>
                  {{-- Struktur ini dibuat sama seperti review admin: Menetapkan lalu diktum berurutan. --}}
                  <div class="mb-1 flex items-start gap-2 text-xs text-slate-600 font-light">
                    <span class="w-24 shrink-0 font-semibold text-slate-500">Menetapkan</span>
                    <span class="leading-relaxed">{{ $sk?->judul_sk ?? '-' }}</span>
                  </div>
                  <ul class="space-y-1.5">
                    @forelse ($memutuskanItems as $memutuskan)
                      <li class="flex items-start gap-2 text-xs text-slate-600 font-light">
                        <span class="w-24 shrink-0 font-semibold text-slate-500">{{ $diktumLabel($loop->index) }}</span>
                        <span class="leading-relaxed">{{ $memutuskan->isi_memutuskan }}</span>
                      </li>
                    @empty
                      <li class="text-xs text-slate-400 font-light">-</li>
                    @endforelse
                  </ul>
                </div>
              </div>
            </section>

            <aside class="space-y-4">
              <section class="rounded-2xl border border-slate-100 bg-white p-5 space-y-3">
                <div class="flex items-center justify-between">
                  <h2 class="text-sm font-bold text-slate-900">Status Saya</h2>
                  @if ($verifikasi)
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-[10px] font-semibold text-blue-600">Level {{ $verifikasi->level }}</span>
                  @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[10px] font-semibold text-slate-500">Read-only</span>
                  @endif
                </div>

                <div class="grid grid-cols-1 gap-3">
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Status Verifikasi</p>
                    <p class="text-xs font-medium text-slate-800">{{ $verifikasi ? str_replace('_', ' ', $verifikasi->status_verifikasi) : 'Tidak ditugaskan' }}</p>
                  </div>
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Status Dokumen</p>
                    <p class="text-xs font-medium text-slate-800">{{ str_replace('_', ' ', $dokumen->status_dokumen) }}</p>
                  </div>
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Penandatangan Final</p>
                    <p class="text-xs font-medium text-slate-800">{{ $penandatanganLabel }}</p>
                  </div>
                </div>
              </section>

              <section class="rounded-2xl border border-slate-100 bg-white p-5 space-y-4">
                <div>
                  <h2 class="text-sm font-bold text-slate-900">Aksi Verifikasi</h2>
                  <p class="text-[11px] text-slate-400 font-light mt-1">Setujui jika isi SK sudah benar, atau kembalikan revisi dengan catatan untuk Pemohon.</p>
                </div>

                @if ($canProcess && $verifikasi)
                  <form action="{{ route('verifikator.verifikasi.proses', $verifikasi) }}" method="POST">
                    @csrf
                    <input type="hidden" name="keputusan" value="setuju" />
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      Setuju
                    </button>
                  </form>

                  <form action="{{ route('verifikator.verifikasi.proses', $verifikasi) }}" method="POST" class="space-y-3 rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
                    @csrf
                    <input type="hidden" name="keputusan" value="tolak" />
                    <div>
                      <label for="catatan" class="block text-xs font-semibold text-amber-700 mb-1.5">Catatan Revisi <span class="text-red-500">*</span></label>
                      {{-- Catatan wajib karena Pemohon membutuhkan alasan yang jelas saat SK dikembalikan. --}}
                      <textarea id="catatan" name="catatan" rows="4" required placeholder="Tuliskan bagian SK yang harus diperbaiki..."
                        class="w-full rounded-xl border border-amber-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 resize-none">{{ old('catatan') }}</textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-3 text-xs font-semibold text-white hover:bg-amber-700 transition-all duration-200">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h7M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      Kembalikan Revisi
                    </button>
                  </form>
                @elseif ($isReadOnly ?? false)
                  <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-xs font-semibold text-slate-700">Mode pemantauan read-only.</p>
                    <p class="mt-1 text-[11px] font-light text-slate-400">Aksi verifikasi hanya tersedia pada menu Perlu Verifikasi.</p>
                  </div>
                @else
                  <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-xs font-semibold text-slate-700">SK ini belum bisa diproses dari akun Anda saat ini.</p>
                    <p class="mt-1 text-[11px] font-light text-slate-400">Kemungkinan level sebelumnya belum menyetujui, atau keputusan Anda sudah tersimpan.</p>
                  </div>
                @endif
              </section>
            </aside>
          </div>
        </div>
      </main>
    </div>

@include('template.layouts.footer')
