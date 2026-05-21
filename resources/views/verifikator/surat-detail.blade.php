@include('template.layouts.header', ['pageTitle' => 'Detail Surat Verifikasi'])
@include('template.sidebar.verifikator', ['activePage' => $activePage ?? 'surat-menunggu'])
    <!-- View detail menerima $verifikasi, $dokumen, $previewPdfUrl, $downloadPdfUrl, dan $canProcess dari controller. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <div>
          <h1 class="text-sm font-bold text-slate-900">Detail Verifikasi Surat</h1>
          <p class="text-[11px] text-slate-400 font-light">{{ ($isReadOnly ?? false) ? 'Baca dokumen dalam mode pemantauan read-only.' : 'Baca dokumen dan kirim keputusan verifikasi dari halaman ini.' }}</p>
        </div>
        <a href="{{ $backUrl ?? route('verifikator.surat-menunggu') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali
        </a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-5">
          @php($lampiranFiles = $dokumen->dokumenFiles->where('file_type', 'LAMPIRAN'))

          <!-- Error validasi dari form setuju/tolak ditampilkan di sini setelah redirect back. -->
          @if ($errors->any())
            <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3">
              <p class="text-[11px] font-semibold text-red-700 mb-1">Keputusan belum bisa diproses:</p>
              <ul class="space-y-1 text-[11px] text-red-600 font-light">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="grid gap-5 xl:grid-cols-[minmax(0,1.2fr)_380px]">
            <section class="rounded-2xl border border-slate-100 bg-white overflow-hidden">
              <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                <div>
                  <h2 class="text-sm font-bold text-slate-900">Preview Dokumen</h2>
                  <p class="text-[11px] text-slate-400 font-light">PDF verifikasi ditampilkan penuh agar mudah dibaca.</p>
                </div>
                <!-- Tombol Unduh PDF hanya muncul jika controller menemukan file yang boleh diakses verifikator. -->
                @if ($downloadPdfUrl)
                  <a href="{{ $downloadPdfUrl }}" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh PDF
                  </a>
                @endif
              </div>
              <div class="bg-slate-100 p-4">
                <!-- if ini memilih antara iframe preview PDF atau empty state jika file PDF belum tersedia. -->
                @if ($previewPdfUrl)
                  <iframe src="{{ $previewPdfUrl }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH" class="h-[78vh] min-h-[720px] w-full rounded-2xl border border-slate-200 bg-white" title="Preview PDF Surat"></iframe>
                @else
                  <div class="flex h-[78vh] min-h-[420px] items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 text-center">
                    <div>
                      <p class="text-sm font-semibold text-slate-600">PDF verifikasi belum tersedia.</p>
                      <p class="mt-1 text-[11px] font-light text-slate-400">Verifikator tetap bisa melihat metadata dokumen di panel kanan.</p>
                    </div>
                  </div>
                @endif
              </div>
            </section>

            <aside class="space-y-4">
              <section class="rounded-2xl border border-slate-100 bg-white p-5 space-y-3">
                <!-- Informasi dokumen di panel kanan berasal dari relasi dokumen->suratBiasa dan dokumen->pemohon. -->
                <div class="flex items-center justify-between">
                  <h2 class="text-sm font-bold text-slate-900">Informasi Dokumen</h2>
                  @if ($verifikasi)
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-[10px] font-semibold text-blue-600">Level {{ $verifikasi->level }}</span>
                  @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[10px] font-semibold text-slate-500">Read-only</span>
                  @endif
                </div>
                <div class="grid grid-cols-1 gap-3">
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Perihal</p>
                    <p class="text-xs font-medium text-slate-800">{{ $dokumen->suratBiasa?->hal ?? '-' }}</p>
                  </div>
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Pemohon</p>
                    <p class="text-xs font-medium text-slate-800">{{ $dokumen->pemohon?->nama ?? '-' }}</p>
                  </div>
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Jenis Dokumen</p>
                    <p class="text-xs font-medium text-slate-800">Surat Biasa</p>
                  </div>
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Tanggal Pengajuan</p>
                    <p class="text-xs font-medium text-slate-800">{{ optional($dokumen->created_at)->format('d M Y H:i') ?? '-' }}</p>
                  </div>
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Ringkasan / Isi</p>
                    <p class="text-xs font-light leading-relaxed text-slate-600">{{ $dokumen->suratBiasa?->ringkasan_isi ?? '-' }}</p>
                  </div>
                </div>
              </section>

              <section class="rounded-2xl border border-slate-100 bg-white p-5 space-y-3">
                <div>
                  <h2 class="text-sm font-bold text-slate-900">Lampiran Pendukung</h2>
                  <p class="text-[11px] text-slate-400 font-light mt-1">File tambahan dari Pemohon, terpisah dari PDF utama.</p>
                </div>
                @if ($lampiranFiles->isNotEmpty())
                  <div class="space-y-2">
                    @foreach ($lampiranFiles as $lampiran)
                      <!-- Lampiran dapat diunduh untuk bahan pertimbangan, tetapi tidak menjadi sumber preview PDF. -->
                      <div class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-xs font-medium text-blue-600">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 10-5.657-5.657l-6.586 6.586a6 6 0 108.485 8.485L20.5 13" /></svg>
                        <span class="min-w-0 flex-1 truncate">{{ $lampiran->file_name }}</span>
                        <span class="inline-flex shrink-0 items-center gap-1">
                          @if ($lampiran->isPreviewableLampiran())
                            @if ($canAccessAssignedFiles ?? false)
                              <a href="{{ route('verifikator.lampiran.preview', $lampiran) }}" target="_blank" rel="noopener" class="rounded-lg bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">Lihat</a>
                            @endif
                          @endif
                          @if ($canAccessAssignedFiles ?? false)
                            <a href="{{ route('verifikator.lampiran.download', $lampiran) }}" class="rounded-lg bg-white px-2.5 py-1 text-[10px] font-semibold text-slate-600 hover:bg-slate-100 transition-all duration-200">Unduh</a>
                          @endif
                        </span>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-xs font-light text-slate-400">Tidak ada lampiran pendukung.</p>
                  </div>
                @endif
              </section>

              <section class="rounded-2xl border border-slate-100 bg-white p-5 space-y-4">
                <div>
                  <h2 class="text-sm font-bold text-slate-900">Aksi Verifikasi</h2>
                  <p class="text-[11px] text-slate-400 font-light mt-1">Setujui untuk meneruskan proses, atau tolak dengan catatan yang jelas.</p>
                </div>

                <!-- $canProcess memastikan form keputusan hanya muncul jika level verifikasi ini memang sudah boleh diproses. -->
                @if ($canProcess && $verifikasi)
                  <!-- Form setuju mengirim keputusan=setuju ke route verifikator.verifikasi.proses. -->
                  <form action="{{ route('verifikator.verifikasi.proses', $verifikasi) }}" method="POST">
                    <!-- csrf wajib karena form ini mengubah status verifikasi dan status dokumen. -->
                    @csrf
                    <input type="hidden" name="keputusan" value="setuju" />
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      Setujui
                    </button>
                  </form>

                  <!-- Form tolak mengirim keputusan=tolak dan catatan penolakan; catatan ini wajib diisi oleh controller. -->
                  <form action="{{ route('verifikator.verifikasi.proses', $verifikasi) }}" method="POST" class="space-y-3 rounded-2xl border border-red-100 bg-red-50/60 p-4">
                    <!-- csrf melindungi request POST untuk penolakan dokumen. -->
                    @csrf
                    <input type="hidden" name="keputusan" value="tolak" />
                    <div>
                      <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Penolakan</label>
                      <!-- old('catatan') menjaga catatan tetap muncul jika validasi penolakan gagal. -->
                      <textarea id="catatan" name="catatan" rows="4" placeholder="Tuliskan alasan penolakan agar pemohon bisa melakukan revisi..." class="w-full rounded-xl border border-red-100 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-red-300 focus:ring-2 focus:ring-red-100 resize-none">{{ old('catatan') }}</textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-3 text-xs font-semibold text-white hover:bg-red-700 transition-all duration-200">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      Tolak
                    </button>
                  </form>
                @elseif ($isReadOnly ?? false)
                  <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-xs font-semibold text-slate-700">Mode pemantauan read-only.</p>
                    <p class="mt-1 text-[11px] font-light text-slate-400">Aksi verifikasi hanya tersedia pada menu Perlu Verifikasi.</p>
                  </div>
                @else
                  <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-xs font-semibold text-slate-700">Dokumen ini tidak bisa diproses dari akun Anda saat ini.</p>
                    <p class="mt-1 text-[11px] font-light text-slate-400">Status verifikasi Anda sekarang: {{ $verifikasi?->status_verifikasi ?? 'Tidak ditugaskan' }}.</p>
                  </div>
                @endif
              </section>
            </aside>
          </div>
        </div>
      </main>
    </div>
@include('template.layouts.footer')
