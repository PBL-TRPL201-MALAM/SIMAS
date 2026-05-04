@include('template.header', ['pageTitle' => 'Proses Surat'])
@include('template.admin-sidebar')

    <!-- View ini menerima $dokumen dan data pendukung dari AdminProsesSuratController::show untuk wizard proses surat. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Proses Surat</h1>
          <p class="text-[11px] text-slate-400 font-light">Upload PDF, metadata & verifikasi</p>
        </div>
        <button type="button"
          class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </button>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-6xl mx-auto">

          <!-- Variabel lokal ini merapikan data dari relasi dokumen sebelum dipakai di tampilan. -->
          @php
            $perihalSurat = $dokumen->suratBiasa?->hal ?? request('perihal') ?? '-';
            $pemohonSurat = $dokumen->pemohon?->nama ?? request('pemohon');
            $ringkasanSurat = $dokumen->suratBiasa?->ringkasan_isi ?? request('ringkasan') ?? '';
            $unitKerjaAdmin = auth()->user()?->unit_kerja ?: '-';
            $initialStep = $initialStep ?? 1;
            $selectedVerifikators = $selectedVerifikators ?? collect();
          @endphp

          <!-- Error validasi dari step metadata atau step verifikasi ditampilkan di bagian atas wizard. -->
          @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3">
              <p class="text-[11px] font-semibold text-red-700 mb-1">Data proses surat belum bisa disimpan:</p>
              <ul class="space-y-1 text-[11px] text-red-600 font-light">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Flash status berasal dari redirect setelah metadata/PDF berhasil disimpan. -->
          @if (session('status'))
            <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3">
              <p class="text-[11px] font-semibold text-emerald-700">{{ session('status') }}</p>
            </div>
          @endif

          <div id="proses-info-bar" class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3 mb-5">
            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div class="min-w-0">
              <p class="text-[11px] font-semibold text-blue-700">Sedang memproses surat:</p>
              <p id="proses-perihal-info" class="text-[11px] text-blue-600 font-light truncate">{{ $perihalSurat }}{{ $pemohonSurat ? ' - ' . $pemohonSurat : '' }}</p>
            </div>
            <a href="{{ route('admin.pengajuan-masuk') }}" class="ml-auto text-[10px] font-medium text-blue-500 hover:text-blue-700 shrink-0 transition-colors duration-200">Kembali ke daftar</a>
          </div>

          <!-- Stepper ini hanya penanda UI; perpindahan step dikontrol oleh JavaScript dan nilai $initialStep dari controller. -->
          <div class="flex items-center mb-6">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="proses-circle-1"><span class="text-[11px] font-bold text-white">1</span></div>
              <span class="text-xs font-semibold text-blue-600" id="proses-label-1">Upload & Metadata</span>
            </div>
            <div class="flex-1 h-px bg-slate-200 mx-3"></div>
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="proses-circle-2"><span class="text-[11px] font-bold text-slate-400">2</span></div>
              <span class="text-xs font-medium text-slate-400" id="proses-label-2">Posisi Elemen</span>
            </div>
            <div class="flex-1 h-px bg-slate-200 mx-3"></div>
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="proses-circle-3"><span class="text-[11px] font-bold text-slate-400">3</span></div>
              <span class="text-xs font-medium text-slate-400" id="proses-label-3">Verifikasi</span>
            </div>
          </div>

          <!-- Form step 1 mengirim PDF hasil pemeriksaan dan metadata surat ke route admin.proses-surat.store. -->
          <form id="proses-metadata-form" action="{{ route('admin.proses-surat.store', $dokumen) }}" method="POST" enctype="multipart/form-data" class="contents">
            <!-- csrf wajib untuk request POST dan enctype dibutuhkan karena form mengunggah file PDF. -->
            @csrf
            <input type="hidden" id="proses-start-step" value="{{ $initialStep }}">
          <div id="proses-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <!-- Step 1 melengkapi metadata resmi surat sebelum dokumen masuk ke tahap posisi elemen dan verifikasi. -->
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 1 - Upload PDF & Data Surat</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Unggah PDF hasil pemeriksaan lalu lengkapi metadata inti surat.</p>
            </div>
            <div class="px-6 py-6 space-y-5">

              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 tracking-wide">Unggah Draf PDF <span class="text-blue-400">*</span></label>
                <div id="pdf-drop-zone" class="relative flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-6 py-8 hover:border-blue-300 hover:bg-blue-50/30 transition-all duration-200 cursor-pointer">
                  <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                  </div>
                  <div class="text-center">
                    <p class="text-xs font-semibold text-slate-700">Klik atau seret file PDF ke sini</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Format: PDF - Maks. 10 MB</p>
                  </div>
                  <input id="pdf-file-input" name="hasil_pemeriksaan_pdf" type="file" accept=".pdf,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </div>
                <div id="pdf-file-preview" class="hidden flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-2.5">
                  <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <p id="pdf-file-name" class="text-[11px] font-medium text-blue-700 truncate"></p>
                  <p id="pdf-file-source" class="text-[10px] text-blue-500 font-light shrink-0"></p>
                  <button id="pdf-file-remove" type="button" class="ml-auto text-slate-400 hover:text-slate-600 shrink-0 transition-colors duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                  </button>
                </div>
              </div>

              <div class="border-t border-slate-100 pt-5">
                <p class="text-xs font-semibold text-slate-700 mb-4">Metadata Surat</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Unit Kerja / Unit Pengolah</label>
                    <input type="text" name="unit_kerja" value="{{ $unitKerjaAdmin }}" readonly
                      class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700 font-light outline-none cursor-not-allowed" />
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Penandatangan <span class="text-blue-400">*</span></label>
                    <!-- $penandatangans berisi user role VERIFIKATOR yang jabatannya boleh menjadi penandatangan final. -->
                    <select name="penanda_tangan" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                      <option value="" disabled {{ $selectedPenandatanganId ? '' : 'selected' }}>Pilih penanda tangan</option>
                      @foreach ($penandatangans as $penandatangan)
                        <option value="{{ $penandatangan->user_id }}" @selected((string) $selectedPenandatanganId === (string) $penandatangan->user_id)>
                          {{ $penandatangan->nama }} - {{ $penandatangan->jabatan }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">
                        Jenis Surat <span class="text-blue-400">*</span>
                    </label>

                    <select name="jenis_surat"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">

                        <!-- $jenisSuratOptions berasal dari helper UserReferenceOptions agar pilihan UI sama dengan validasi controller. -->
                        <option value="" disabled {{ old('jenis_surat', $dokumen->suratBiasa?->jenis_surat) ? '' : 'selected' }}>
                            Pilih jenis surat
                        </option>

                        @foreach ($jenisSuratOptions as $jenisSurat)
                            <option value="{{ $jenisSurat }}" @selected(old('jenis_surat', $dokumen->suratBiasa?->jenis_surat) === $jenisSurat)>
                                {{ $jenisSurat }}
                            </option>
                        @endforeach

                    </select>
                </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Sifat Surat <span class="text-blue-400">*</span></label>
                    <select name="sifat_surat" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                      <option value="" disabled {{ old('sifat_surat', $dokumen->suratBiasa?->sifat_surat) ? '' : 'selected' }}>Pilih sifat surat</option>
                      @foreach (['Biasa', 'Penting', 'Segera', 'Sangat Segera', 'Rahasia'] as $sifatSurat)
                        <option value="{{ $sifatSurat }}" @selected(old('sifat_surat', $dokumen->suratBiasa?->sifat_surat) === $sifatSurat)>{{ $sifatSurat }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Nomor Surat <span class="text-blue-400">*</span></label>
                    <input type="text" name="nomor_surat" placeholder="Contoh: B/123/TU/2026" value="{{ old('nomor_surat', $dokumen->suratBiasa?->nomor_surat) }}"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Tanggal Surat <span class="text-blue-400">*</span></label>
                    <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', optional($dokumen->suratBiasa?->tanggal_surat)->format('Y-m-d')) }}"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  </div>
                </div>

                <div class="space-y-4 mt-4">
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Isi / Ringkasan</label>
                    <!-- Ringkasan dapat direvisi Admin/TU sebagai hasil pemeriksaan sebelum metadata disimpan. -->
                    <textarea name="isi_ringkasan" rows="4"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none">{{ old('isi_ringkasan', $ringkasanSurat) }}</textarea>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Catatan Tambahan</label>
                    <textarea name="catatan_tambahan" rows="3" placeholder="Catatan atau informasi tambahan jika diperlukan..."
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none">{{ old('catatan_tambahan', $dokumen->suratBiasa?->catatan ?? $dokumen->suratBiasa?->keterangan_tambahan) }}</textarea>
                  </div>
                </div>
              </div>

              <div class="flex justify-end pt-2">
                <button id="proses-save-step-1" type="submit"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                  Simpan PDF & Metadata
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </button>
              </div>
            </div>
          </div>
          </form>

          <!-- Step 2 menyimpan koordinat nomor surat, tanggal, dan QR/TTE melalui endpoint JSON storePosisiElemen. -->
          <div id="proses-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 2 - Atur Posisi Elemen</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan posisi penempatan nomor surat, tanggal, dan TTE pada dokumen PDF.</p>
            </div>
            <!-- data-* ini menjadi jembatan data dari Blade ke JavaScript pengatur preview PDF dan posisi elemen. -->
            <div
              id="posisi-elemen-config"
              class="px-6 py-6 space-y-5"
              data-dokumen-id="{{ $dokumen->dokumen_id }}"
              data-start-step="{{ $initialStep }}"
              data-save-url="{{ route('admin.proses-surat.posisi-elemen', $dokumen) }}"
              data-preview-url="{{ $previewPdfUrl }}"
              data-preview-name="{{ $previewPdfName ?? '' }}"
              data-page-count="{{ $previewPdfPageCount ?? 1 }}"
              data-existing-positions='@json($existingPositions)'
            >
              <div class="rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                <p class="text-[11px] font-semibold text-blue-700 mb-1">Catatan penting:</p>
                <p class="text-[11px] text-blue-600 font-light">Pilih salah satu elemen, lalu klik area preview PDF untuk menyimpan koordinat. Posisi disimpan dalam pixel relatif terhadap preview dasar dan akan otomatis menyesuaikan saat zoom berubah.</p>
              </div>

              <div class="grid grid-cols-1 xl:grid-cols-[280px,1fr] gap-5">
                <div class="space-y-4">
                  <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                    <div>
                      <p class="text-xs font-semibold text-slate-700">Pilih Elemen</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Setelah tombol dipilih, klik area preview di kanan.</p>
                    </div>

                    <!-- Tombol berikut memilih elemen aktif; klik di preview akan menyimpan koordinat elemen yang dipilih. -->
                    <button type="button" class="btn-set-elemen w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-left transition-all duration-200 hover:border-blue-300 hover:bg-blue-50/40" data-elemen="nomor_surat">
                      <p class="text-xs font-semibold text-slate-700">Set Lokasi Nomor Surat</p>
                      <p id="status-elemen-nomor_surat" class="text-[10px] text-slate-400 font-light mt-1">Belum ditentukan</p>
                    </button>

                    <button type="button" class="btn-set-elemen w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-left transition-all duration-200 hover:border-blue-300 hover:bg-blue-50/40" data-elemen="tanggal_surat">
                      <p class="text-xs font-semibold text-slate-700">Set Lokasi Tanggal Surat</p>
                      <p id="status-elemen-tanggal_surat" class="text-[10px] text-slate-400 font-light mt-1">Belum ditentukan</p>
                    </button>

                    <button type="button" class="btn-set-elemen w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-left transition-all duration-200 hover:border-blue-300 hover:bg-blue-50/40" data-elemen="tte">
                      <p class="text-xs font-semibold text-slate-700">Set Lokasi QRCode / TTE</p>
                      <p id="status-elemen-tte" class="text-[10px] text-slate-400 font-light mt-1">Belum ditentukan</p>
                    </button>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                    <div>
                      <p class="text-xs font-semibold text-slate-700">Halaman Preview</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Jika elemen ada di halaman lain, ubah halaman lalu klik Tampilkan.</p>
                    </div>
                    <!-- Jumlah halaman berasal dari controller; JavaScript memakai nilai ini untuk membatasi navigasi PDF multi-page. -->
                    <div class="flex items-center gap-3">
                      <button id="pdf-page-prev" type="button" class="shrink-0 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-medium text-slate-600 hover:border-blue-300 hover:text-blue-600 transition-all duration-200">
                        Prev
                      </button>
                      <input id="pdf-page-input" type="number" min="1" max="{{ $previewPdfPageCount ?? 1 }}" value="1"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100" />
                      <button id="pdf-page-next" type="button" class="shrink-0 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-medium text-slate-600 hover:border-blue-300 hover:text-blue-600 transition-all duration-200">
                        Next
                      </button>
                      <button id="pdf-page-apply" type="button" class="shrink-0 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-600 hover:border-blue-300 hover:text-blue-600 transition-all duration-200">
                        Tampilkan
                      </button>
                    </div>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                      <p class="text-xs font-semibold text-slate-700">Zoom Preview</p>
                      <span id="pdf-zoom-label" class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">100%</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <button id="pdf-zoom-out" type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:border-blue-300 hover:text-blue-600 transition-all duration-200">Zoom Out</button>
                      <button id="pdf-zoom-reset" type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:border-blue-300 hover:text-blue-600 transition-all duration-200">Reset</button>
                      <button id="pdf-zoom-in" type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:border-blue-300 hover:text-blue-600 transition-all duration-200">Zoom In</button>
                    </div>
                  </div>

                  <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4">
                    <p class="text-xs font-semibold text-slate-700">Elemen Aktif</p>
                    <p id="aktif-elemen-label" class="text-[11px] text-slate-400 font-light mt-1">Belum ada elemen yang dipilih.</p>
                  </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                      <p class="text-xs font-semibold text-slate-700">Preview PDF</p>
                      <p id="pdf-preview-caption" class="text-[11px] text-slate-400 font-light mt-0.5">Unggah PDF di langkah 1 untuk mulai mengatur posisi elemen.</p>
                    </div>
                    <span id="pdf-preview-page-badge" class="inline-flex items-center justify-center rounded-full bg-blue-50 px-3 py-1 text-[10px] font-semibold text-blue-600">Halaman 1</span>
                  </div>

                  <div id="pdf-preview-empty" class="rounded-xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-600">Preview PDF belum tersedia</p>
                    <p class="text-[11px] text-slate-400 font-light mt-1">Pilih file PDF pada langkah pertama, lalu lanjutkan ke langkah ini.</p>
                  </div>

                  <div id="pdf-preview-stage" class="hidden rounded-xl border border-slate-200 bg-white overflow-hidden">
                    <!-- Preview PDF dipakai sebagai kanvas klik; posisi disimpan relatif agar bisa dipetakan kembali saat generate PDF. -->
                    <div id="pdf-scroll-container" class="overflow-auto bg-slate-100/60" style="height: max(900px, calc(100vh - 250px));">
                      <div id="pdf-preview-canvas" class="relative mx-auto my-4 rounded-lg shadow-sm overflow-hidden bg-white">
                        <iframe id="pdf-preview-frame" title="Preview PDF proses surat" class="absolute inset-0 border-0 bg-white"></iframe>
                        <button
                          id="pdf-click-overlay"
                          type="button"
                          class="absolute inset-0 z-10 cursor-crosshair bg-transparent"
                          aria-label="Klik untuk menentukan posisi elemen pada preview PDF"
                        ></button>
                        <div id="pdf-marker-layer" class="pointer-events-none absolute inset-0 z-20"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                  <p class="text-xs font-semibold text-slate-700">Nomor Surat</p>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi X</label>
                      <input id="input-nomor-surat-x" type="number" name="nomor_x" min="0" readonly
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi Y</label>
                      <input id="input-nomor-surat-y" type="number" name="nomor_y" min="0" readonly
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none" />
                    </div>
                  </div>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                  <p class="text-xs font-semibold text-slate-700">Tanggal Surat</p>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi X</label>
                      <input id="input-tanggal-surat-x" type="number" name="tanggal_x" min="0" readonly
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi Y</label>
                      <input id="input-tanggal-surat-y" type="number" name="tanggal_y" min="0" readonly
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none" />
                    </div>
                  </div>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                  <p class="text-xs font-semibold text-slate-700">QRCode / TTE</p>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi X</label>
                      <input id="input-tte-x" type="number" name="tte_x" min="0" readonly
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Posisi Y</label>
                      <input id="input-tte-y" type="number" name="tte_y" min="0" readonly
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Lebar</label>
                      <input id="input-tte-w" type="number" name="tte_w" min="0" readonly
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-[11px] font-medium text-slate-500">Tinggi</label>
                      <input id="input-tte-h" type="number" name="tte_h" min="0" readonly
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 font-light outline-none" />
                    </div>
                  </div>
                </div>
              </div>

              <p class="text-[10px] text-slate-400 font-light">Koordinat disimpan terhadap preview dasar. Saat zoom diubah, marker dan klik posisi akan tetap mengikuti skala preview yang sedang ditampilkan.</p>

              <div class="flex items-center justify-between pt-2">
                <button id="proses-back-1" type="button"
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                  Kembali
                </button>
                <button id="proses-next-2" type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                  Lanjut - Tentukan Verifikasi
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Form step 3 mengirim susunan verifikator dan penandatangan final ke route kirim-verifikasi. -->
          <form id="proses-verifikasi-form" action="{{ route('admin.proses-surat.kirim-verifikasi', $dokumen) }}" method="POST" class="contents">
            <!-- csrf melindungi request POST yang membuat ulang flow verifikasi dokumen. -->
            @csrf
          <div id="proses-step-3" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <!-- Step 3 menentukan alur verifikasi bertingkat; penandatangan final selalu ditempatkan sebagai level terakhir. -->
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 3 - Tingkat Verifikasi</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan jalur verifikasi dokumen sebelum dikirim ke verifikator.</p>
            </div>
            <div class="px-6 py-6 space-y-5">

              <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-start gap-3">
                <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-[11px] text-blue-600 font-light leading-relaxed">Pilih verifikator jika memang diperlukan. Penandatangan final dari metadata surat akan otomatis ditempatkan sebagai level terakhir setelah seluruh verifikator yang dipilih.</p>
              </div>

              <div class="space-y-3">
                <p class="text-xs font-semibold text-slate-700">Pilih Verifikator</p>
                <!-- penandatangan_final dikirim hidden karena nilainya dipilih pada metadata step 1. -->
                <input type="hidden" name="penandatangan_final" value="{{ $selectedPenandatanganId }}">

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 1</label>
                  <!-- $verifikators berisi user aktif role VERIFIKATOR selain penandatangan final. -->
                  <select name="verifikator_1" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="">- Tidak ada (opsional) -</option>
                    @foreach ($verifikators as $verifikator)
                      <option value="{{ $verifikator->user_id }}" @selected((string) old('verifikator_1', $selectedVerifikators->get(1)) === (string) $verifikator->user_id)>
                        {{ $verifikator->nama }}{{ $verifikator->jabatan ? ' - ' . $verifikator->jabatan : '' }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 2</label>
                  <select name="verifikator_2" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="">- Tidak ada (opsional) -</option>
                    @foreach ($verifikators as $verifikator)
                      <option value="{{ $verifikator->user_id }}" @selected((string) old('verifikator_2', $selectedVerifikators->get(2)) === (string) $verifikator->user_id)>
                        {{ $verifikator->nama }}{{ $verifikator->jabatan ? ' - ' . $verifikator->jabatan : '' }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 3</label>
                  <select name="verifikator_3" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="">- Tidak ada (opsional) -</option>
                    @foreach ($verifikators as $verifikator)
                      <option value="{{ $verifikator->user_id }}" @selected((string) old('verifikator_3', $selectedVerifikators->get(3)) === (string) $verifikator->user_id)>
                        {{ $verifikator->nama }}{{ $verifikator->jabatan ? ' - ' . $verifikator->jabatan : '' }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Penandatangan Final</label>
                  <div class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700 font-light">
                    {{ $selectedPenandatangan?->nama ? $selectedPenandatangan->nama . ' - ' . $selectedPenandatangan->jabatan : 'Pilih penandatangan di metadata surat terlebih dulu' }}
                  </div>
                </div>
              </div>

              <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-start gap-3">
                <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-[11px] text-blue-600 font-light leading-relaxed">Jika semua dropdown verifikator dikosongkan, penandatangan final otomatis menjadi Level 1. Jika ada verifikator yang dipilih, penandatangan akan diproses setelah level terakhir tersebut selesai menyetujui dokumen.</p>
              </div>

              <div class="flex items-center justify-between pt-2">
                <button id="proses-back-2" type="button"
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                  Kembali
                </button>
                <button id="proses-submit" type="submit"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                  Kirim ke Verifikator
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
              </div>

            </div>
          </div>
          </form>

        </div>
      </main>
    </div>

@include('template.footer')
