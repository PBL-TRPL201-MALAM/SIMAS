@include('template.layouts.header', ['pageTitle' => 'Proses SK'])
@include('template.sidebar.admin')

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
  $initialStep = min(3, max(1, (int) ($initialStep ?? 1)));
  $selectedForLevel = fn (int $level) => (string) old('sk_verifikator_' . $level, $selectedVerifikators[$level] ?? '');
  $selectedPenandatanganLabel = $selectedPenandatangan
      ? $selectedPenandatangan->nama . ($selectedPenandatangan->jabatan ? ' - ' . $selectedPenandatangan->jabatan : '')
      : '-';
@endphp

    {{-- View ini mereview data SK dari tabel surat_keputusan, sk_dasar_hukum, sk_menimbang, dan sk_memutuskan. --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Review & Proses SK</h1>
          <p class="text-[11px] text-slate-400 font-light">Tinjau isi SK dan tentukan jalur verifikasi</p>
        </div>
        <a href="{{ route('admin.profil') }}"
          class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-2xl mx-auto">
          @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3">
              <p class="text-xs font-semibold text-red-700">Proses SK belum bisa disimpan:</p>
              <ul class="mt-1 space-y-1 text-[11px] text-red-600 font-light">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if (session('error'))
            <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs text-red-700 font-light">
              {{ session('error') }}
            </div>
          @endif

          @if (session('status'))
            <div class="mb-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs text-emerald-700 font-light">
              {{ session('status') }}
            </div>
          @endif

          <div class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3 mb-5">
            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div class="min-w-0">
              <p class="text-[11px] font-semibold text-blue-700">Sedang mereview SK:</p>
              <p id="sk-proses-judul-info" class="text-[11px] text-blue-600 font-light truncate">{{ $sk?->judul_sk ?? '-' }} - {{ $dokumen->pemohon?->nama ?? '-' }}</p>
            </div>
            <a href="{{ route('admin.pengajuan-sk') }}" class="ml-auto text-[10px] font-medium text-blue-500 hover:text-blue-700 shrink-0 transition-colors duration-200">Kembali ke daftar</a>
          </div>

          <form id="sk-proses-form" method="POST" action="{{ route('admin.proses-sk.kirim-verifikasi', $dokumen) }}" data-initial-step="{{ $initialStep }}">
            @csrf
            {{-- Form ini hanya memproses SK; route Surat Biasa tetap menggunakan controller proses surat sendiri. --}}
            <div class="flex items-center mb-6">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="sk-proses-circle-1">
                  <span class="text-[11px] font-bold text-white">1</span>
                </div>
                <span class="text-xs font-semibold text-blue-600" id="sk-proses-label-1">Review SK</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-proses-circle-2">
                  <span class="text-[11px] font-bold text-slate-400">2</span>
                </div>
                <span class="text-xs font-medium text-slate-400" id="sk-proses-label-2">Verifikasi</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-proses-circle-3">
                  <span class="text-[11px] font-bold text-slate-400">3</span>
                </div>
                <span class="text-xs font-medium text-slate-400" id="sk-proses-label-3">Konfirmasi</span>
              </div>
            </div>

            <div id="sk-proses-step-1" class="{{ $initialStep === 1 ? '' : 'hidden ' }}rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 1 - Review Isi SK</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Periksa kelengkapan dan kesesuaian isi SK dari pemohon.</p>
              </div>
              <div class="px-6 py-6 space-y-4">
                <div class="space-y-3">
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Judul SK</p>
                    <p id="sk-review-judul" class="text-sm font-semibold text-slate-800">{{ $sk?->judul_sk ?? '-' }}</p>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Pemohon</p>
                    <p class="text-sm font-medium text-slate-700">{{ $dokumen->pemohon?->nama ?? '-' }}</p>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Tentang</p>
                    <p id="sk-review-tentang" class="text-sm font-medium text-slate-700">{{ $sk?->tentang ?? '-' }}</p>
                  </div>

                  <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-4">
                    <div class="mb-3">
                      <p class="text-xs font-semibold text-blue-700">Metadata SK</p>
                      <p class="mt-0.5 text-[11px] text-blue-600 font-light">Lengkapi metadata resmi sebelum menentukan verifikator.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                      <div class="space-y-1.5">
                        <label for="sk-nomor-sk" class="block text-xs font-semibold text-slate-700 tracking-wide">Nomor SK <span class="text-blue-400">*</span></label>
                        <input id="sk-nomor-sk" type="text" name="nomor_sk" value="{{ old('nomor_sk', $sk?->nomor_sk) }}" placeholder="Contoh: 123/PL29/SK/2026"
                          class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                      </div>
                      <div class="space-y-1.5">
                        <label for="sk-tanggal-sk" class="block text-xs font-semibold text-slate-700 tracking-wide">Tanggal SK <span class="text-blue-400">*</span></label>
                        <input id="sk-tanggal-sk" type="date" name="tanggal_sk" value="{{ old('tanggal_sk', optional($sk?->tanggal_sk)->format('Y-m-d')) }}"
                          class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                      </div>
                      <div class="space-y-1.5">
                        <label for="sk-penandatangan-id" class="block text-xs font-semibold text-slate-700 tracking-wide">Penandatangan <span class="text-blue-400">*</span></label>
                        <select id="sk-penandatangan-id" name="penandatangan_id"
                          class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                          <option value="" disabled {{ $selectedPenandatanganId ? '' : 'selected' }}>Pilih penandatangan</option>
                          @foreach ($penandatangans as $penandatangan)
                            <option value="{{ $penandatangan->user_id }}" @selected((string) $selectedPenandatanganId === (string) $penandatangan->user_id)>
                              {{ $penandatangan->nama }} - {{ $penandatangan->jabatan }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Menimbang</p>
                    <ul id="sk-review-menimbang" class="space-y-1.5">
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
                    <ul id="sk-review-mengingat" class="space-y-1.5">
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
                    {{-- Format review dibuat sejajar dengan preview Pemohon: label di kiri, isi di kanan. --}}
                    <div class="mb-1 flex items-start gap-2 text-xs text-slate-600 font-light">
                      <span class="w-24 shrink-0 font-semibold text-slate-500">Menetapkan</span>
                      <span class="leading-relaxed">{{ $sk?->judul_sk ?? '-' }}</span>
                    </div>
                    <ul id="sk-review-memutuskan" class="space-y-1.5">
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

                <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-4">
                  <div class="mb-3">
                    <label for="sk-catatan-admin" class="block text-xs font-semibold text-amber-700 tracking-wide">Catatan untuk Pemohon</label>
                    <p class="mt-0.5 text-[11px] text-amber-600 font-light">Wajib diisi jika SK dikembalikan untuk revisi.</p>
                  </div>
                  {{-- Catatan ini dipakai oleh tombol Kembalikan Revisi, tetapi tetap boleh diisi sebagai catatan admin saat lanjut verifikasi. --}}
                  <textarea id="sk-catatan-admin" name="catatan_admin" rows="4"
                    placeholder="Tuliskan catatan revisi untuk pemohon, misalnya bagian Menimbang perlu diperjelas..."
                    class="w-full rounded-xl border border-amber-200 bg-white/80 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-amber-400 focus:bg-white focus:ring-2 focus:ring-amber-100 resize-none">{{ old('catatan_admin', $sk?->catatan_admin) }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-2">
                  <button id="sk-proses-tolak-btn" type="submit" formaction="{{ route('admin.proses-sk.revisi', $dokumen) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    Kembalikan Revisi
                  </button>
                  <button id="sk-proses-next-1" type="submit" formaction="{{ route('admin.proses-sk.metadata', $dokumen) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut - Tentukan Verifikasi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>

            <div id="sk-proses-step-2" class="{{ $initialStep === 2 ? '' : 'hidden ' }}rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 2 - Tingkat Verifikasi</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan jalur verifikasi SK sebelum dikirim ke verifikator.</p>
              </div>
              <div class="px-6 py-6 space-y-5">
                {{-- Hidden ini dipakai ringkasan UI; controller tetap mengambil penandatangan final dari dokumen.penandatangan_id. --}}
                <input type="hidden" name="penandatangan_final" value="{{ $selectedPenandatanganId }}" />

                <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3">
                  <p class="text-[10px] font-semibold text-blue-700 uppercase tracking-wider mb-1">Penandatangan Final</p>
                  {{-- Penandatangan final dibaca dari metadata SK dan otomatis menjadi level terakhir. --}}
                  <p id="sk-penandatangan-final-label" data-label="{{ $selectedPenandatanganLabel }}" class="text-sm font-semibold text-slate-800">{{ $selectedPenandatanganLabel }}</p>
                  <p class="mt-1 text-[11px] text-blue-600 font-light">Penandatangan final readonly dari metadata SK dan tidak muncul lagi di dropdown verifikator.</p>
                </div>

                <div class="space-y-3">
                  <div>
                    <p class="text-xs font-semibold text-slate-700">Pilih Verifikator</p>
                    <p class="mt-0.5 text-[11px] text-slate-400 font-light">Level 1 wajib diisi, sedangkan Level 2 dan Level 3 boleh dikosongkan.</p>
                  </div>

                  @for ($level = 1; $level <= 3; $level++)
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Verifikator Level {{ $level }} {{ $level === 1 ? '*' : '(opsional)' }}</label>
                      <select name="sk_verifikator_{{ $level }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                        <option value="">{{ $level === 1 ? 'Pilih verifikator level 1' : '- Opsional -' }}</option>
                        @foreach ($verifikators as $verifikator)
                          {{-- Opsi ini sudah dikecualikan dari penandatangan final oleh controller. --}}
                          <option value="{{ $verifikator->user_id }}" @selected($selectedForLevel($level) === (string) $verifikator->user_id)>
                            {{ $verifikator->nama }}{{ $verifikator->jabatan ? ' - ' . $verifikator->jabatan : '' }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  @endfor
                </div>

                <div class="flex items-center justify-between pt-2">
                  <button id="sk-proses-back-1" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    Kembali
                  </button>
                  <button id="sk-proses-next-2" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut - Konfirmasi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>

            <div id="sk-proses-step-3" class="{{ $initialStep === 3 ? '' : 'hidden ' }}rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 3 - Konfirmasi & Kirim</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Periksa ringkasan sebelum SK dikirim ke verifikator pertama.</p>
              </div>
              <div class="px-6 py-6 space-y-4">
                <div class="space-y-3">
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">SK yang Diproses</p>
                    <p id="sk-konfirmasi-judul" class="text-sm font-semibold text-slate-800">{{ $sk?->judul_sk ?? '-' }}</p>
                  </div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
                    <p id="sk-konfirmasi-pemohon" class="text-sm font-medium text-slate-700">{{ $dokumen->pemohon?->nama ?? '-' }}</p>
                  </div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Metadata SK</p>
                    <div class="space-y-1 text-xs text-slate-600 font-light">
                      <p>Nomor SK: <span id="sk-konfirmasi-nomor" class="font-medium text-slate-700">-</span></p>
                      <p>Tanggal SK: <span id="sk-konfirmasi-tanggal" class="font-medium text-slate-700">-</span></p>
                      <p>Penandatangan: <span id="sk-konfirmasi-penandatangan" class="font-medium text-slate-700">-</span></p>
                    </div>
                  </div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Urutan Verifikasi</p>
                    <p id="sk-konfirmasi-jalur" class="text-sm font-medium text-slate-700">-</p>
                  </div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Verifikator</p>
                    <div id="sk-konfirmasi-verifikator" class="space-y-1"></div>
                  </div>
                  <div id="sk-konfirmasi-catatan-wrap" class="hidden rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Catatan Admin</p>
                    <p id="sk-konfirmasi-catatan" class="text-xs text-slate-600 font-light leading-relaxed">-</p>
                  </div>
                </div>

                <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-start gap-3">
                  <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <p class="text-[11px] text-blue-600 font-light leading-relaxed">Setelah dikirim, sistem meneruskan SK ke verifikator pertama. Level berikutnya baru aktif setelah level sebelumnya menyetujui.</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                  <button id="sk-proses-back-2" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    Kembali
                  </button>
                  <button id="sk-proses-submit" type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Kirim SK ke Verifikator
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                  </button>
                </div>
              </div>
            </div>
          </form>

        </div>
      </main>
    </div>

@include('template.layouts.footer')
