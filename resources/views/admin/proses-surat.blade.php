@include('template.header', ['pageTitle' => 'Proses Surat'])
@include('template.admin-sidebar')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      {{-- Topbar --}}
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
        <div class="max-w-2xl mx-auto">

          @php
            $perihalSurat = $surat->perihal ?? request('perihal') ?? '-';
            $pemohonSurat = $surat->pemohon ?? request('pemohon');
            $ringkasanSurat = $surat->ringkasan ?? request('ringkasan') ?? '';
          @endphp

          {{-- Info surat yang diproses --}}
          <div id="proses-info-bar" class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3 mb-5">
            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div class="min-w-0">
              <p class="text-[11px] font-semibold text-blue-700">Sedang memproses surat:</p>
              <p id="proses-perihal-info" class="text-[11px] text-blue-600 font-light truncate">{{ $perihalSurat }}{{ $pemohonSurat ? ' - ' . $pemohonSurat : '' }}</p>
            </div>
            <a href="{{ route('admin.pengajuan-masuk') }}" class="ml-auto text-[10px] font-medium text-blue-500 hover:text-blue-700 shrink-0 transition-colors duration-200">Kembali ke daftar</a>
          </div>

          {{-- Step Indicator --}}
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

          {{-- ============================================================
               STEP 1: Upload PDF + Metadata
          ============================================================ --}}
          <div id="proses-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 1 - Upload PDF & Data Surat</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Unggah PDF hasil pemeriksaan lalu lengkapi metadata surat.</p>
            </div>
            <div class="px-6 py-6 space-y-5">

              {{-- Upload PDF --}}
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 tracking-wide">Unggah Draf PDF <span class="text-blue-400">*</span></label>
                <div id="pdf-drop-zone" class="relative flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-6 py-8 hover:border-blue-300 hover:bg-blue-50/30 transition-all duration-200 cursor-pointer">
                  <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                  </div>
                  <div class="text-center">
                    <p class="text-xs font-semibold text-slate-700">Klik atau seret file PDF ke sini</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Format: PDF · Maks. 10 MB</p>
                  </div>
                  <input id="pdf-file-input" type="file" accept=".pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </div>
                <div id="pdf-file-preview" class="hidden flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-2.5">
                  <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <p id="pdf-file-name" class="text-[11px] font-medium text-blue-700 truncate"></p>
                  <button id="pdf-file-remove" type="button" class="ml-auto text-slate-400 hover:text-slate-600 shrink-0 transition-colors duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                  </button>
                </div>
              </div>

              <div class="border-t border-slate-100 pt-5">
                <p class="text-xs font-semibold text-slate-700 mb-4">Metadata Surat</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Unit Kerja / Unit Pengolah <span class="text-blue-400">*</span></label>
                    <input type="text" name="unit_kerja" placeholder="Contoh: Tata Usaha Jurusan TRPL"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Penanda Tangan <span class="text-blue-400">*</span></label>
                    <select name="penanda_tangan" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                      <option value="" disabled selected>Pilih penanda tangan</option>
                      <option>Direktur Polibatam</option>
                      <option>Wakil Direktur I</option>
                      <option>Wakil Direktur II</option>
                      <option>Wakil Direktur III</option>
                      <option>Kepala Jurusan</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Templat Surat</label>
                    <select name="templat" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                      <option value="" disabled selected>Pilih templat</option>
                      <option>Templat Surat Resmi Polibatam</option>
                      <option>Templat Surat Keterangan</option>
                      <option>Templat Surat Permohonan</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Versi Kop Surat</label>
                    <select name="versi_kop" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                      <option value="" disabled selected>Pilih versi kop</option>
                      <option>Kop Polibatam Standar</option>
                      <option>Kop Jurusan TRPL</option>
                      <option>Kop Tanpa Logo</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Jenis <span class="text-blue-400">*</span></label>
                    <select name="jenis" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                      <option value="" disabled selected>Pilih jenis surat</option>
                      <option>Surat Keterangan</option>
                      <option>Surat Permohonan</option>
                      <option>Surat Izin</option>
                      <option>Surat Pengantar</option>
                      <option>Surat Tugas</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Sifat <span class="text-blue-400">*</span></label>
                    <select name="sifat" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                      <option value="" disabled selected>Pilih sifat</option>
                      <option>Biasa</option>
                      <option>Penting</option>
                      <option>Segera</option>
                      <option>Sangat Segera</option>
                      <option>Rahasia</option>
                    </select>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Kode Hal</label>
                    <input type="text" name="kode_hal" placeholder="Contoh: ADM/001"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Hal <span class="text-blue-400">*</span></label>
                    <input type="text" name="hal" placeholder="Contoh: Permohonan Izin Penelitian"
                      value="{{ $perihalSurat }}"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  </div>

                </div>

                <div class="space-y-4 mt-4">

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Kepada / Tujuan <span class="text-blue-400">*</span></label>
                    <input type="text" name="tujuan" placeholder="Contoh: Yth. Kepala Dinas Pendidikan Kota Batam"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Isi / Ringkasan <span class="text-blue-400">*</span></label>
                    <textarea name="isi_ringkasan" rows="3" placeholder="Tuliskan ringkasan isi surat secara singkat..."
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none">{{ $ringkasanSurat }}</textarea>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Tembusan</label>
                    <textarea name="tembusan" rows="2" placeholder="Contoh:&#10;1. Direktur Polibatam&#10;2. Wakil Direktur II"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Keterangan Tambahan</label>
                    <textarea name="keterangan_tambahan" rows="2" placeholder="Catatan atau informasi tambahan jika diperlukan..."
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Pengonsep Surat</label>
                      <input type="text" name="pengonsep" placeholder="Nama pengonsep surat"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                    </div>
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Pemberkasan (Nama Arsip)</label>
                      <input type="text" name="pemberkasan" placeholder="Nama arsip / judul berkas"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                    </div>
                  </div>

                  <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3">
                    <div>
                      <p class="text-xs font-semibold text-slate-700">Dilihat Publik</p>
                      <p class="text-[10px] text-slate-400 font-light mt-0.5">Aktifkan jika surat ini dapat diakses oleh umum</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" name="dilihat_publik" class="sr-only peer" />
                      <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 transition-colors duration-200"></div>
                      <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                    </label>
                  </div>

                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Lampiran</label>
                    <input type="file" name="lampiran" multiple
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-500 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100" />
                    <p class="text-[10px] text-slate-400 font-light">Bisa memilih lebih dari satu file lampiran.</p>
                  </div>

                </div>
              </div>

              <div class="flex justify-end pt-2">
                <button id="proses-next-1" type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                  Lanjut - Atur Posisi Elemen
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </button>
              </div>
            </div>
          </div>

          {{-- ============================================================
               STEP 2: Atur Posisi Elemen
          ============================================================ --}}
          <div id="proses-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 2 - Atur Posisi Elemen</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan posisi penempatan nomor surat, tanggal, dan TTE pada dokumen PDF.</p>
            </div>
            <div class="px-6 py-6 space-y-5">

              <div class="rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                <p class="text-[11px] font-semibold text-blue-700 mb-1">Catatan penting:</p>
                <p class="text-[11px] text-blue-600 font-light">Posisi yang diatur di sini hanya disimpan sebagai data koordinat. Elemen belum digenerate ke dokumen final - generate dilakukan setelah semua verifikator menyetujui.</p>
              </div>

              <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                <p class="text-xs font-semibold text-slate-700">Nomor Surat</p>
                <div class="grid grid-cols-2 gap-3">
                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Posisi X (dari kiri, px)</label>
                    <input type="number" name="nomor_x" placeholder="Contoh: 120" min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                  </div>
                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Posisi Y (dari atas, px)</label>
                    <input type="number" name="nomor_y" placeholder="Contoh: 85" min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                  </div>
                </div>
              </div>

              <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                <p class="text-xs font-semibold text-slate-700">Tanggal Surat</p>
                <div class="grid grid-cols-2 gap-3">
                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Posisi X (dari kiri, px)</label>
                    <input type="number" name="tanggal_x" placeholder="Contoh: 120" min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                  </div>
                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Posisi Y (dari atas, px)</label>
                    <input type="number" name="tanggal_y" placeholder="Contoh: 110" min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                  </div>
                </div>
              </div>

              <div class="rounded-xl border border-slate-100 bg-slate-50/30 p-4 space-y-3">
                <p class="text-xs font-semibold text-slate-700">TTE (Tanda Tangan Elektronik)</p>
                <div class="grid grid-cols-2 gap-3">
                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Posisi X (dari kiri, px)</label>
                    <input type="number" name="tte_x" placeholder="Contoh: 380" min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                  </div>
                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Posisi Y (dari atas, px)</label>
                    <input type="number" name="tte_y" placeholder="Contoh: 680" min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Lebar TTE (px)</label>
                    <input type="number" name="tte_w" placeholder="Contoh: 120" min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                  </div>
                  <div class="space-y-1.5">
                    <label class="block text-[11px] font-medium text-slate-500">Tinggi TTE (px)</label>
                    <input type="number" name="tte_h" placeholder="Contoh: 60" min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200" />
                  </div>
                </div>
              </div>

              <p class="text-[10px] text-slate-400 font-light">* Koordinat dalam satuan pixel dari pojok kiri atas halaman dokumen PDF.</p>

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

          {{-- ============================================================
               STEP 3: Tentukan Tingkat Verifikasi
          ============================================================ --}}
          <div id="proses-step-3" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 3 - Tingkat Verifikasi</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan jalur verifikasi dokumen sebelum dikirim ke verifikator.</p>
            </div>
            <div class="px-6 py-6 space-y-5">

              <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-700 tracking-wide">Jalur Verifikasi <span class="text-blue-400">*</span></label>

                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                  <input type="radio" name="jalur_verifikasi" value="1" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                  <div>
                    <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 saja</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Dokumen hanya perlu disetujui oleh Verifikator Level 1</p>
                  </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                  <input type="radio" name="jalur_verifikasi" value="2" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                  <div>
                    <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 -> Level 2</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Dokumen harus disetujui oleh Level 1 terlebih dahulu, lalu diteruskan ke Level 2</p>
                  </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                  <input type="radio" name="jalur_verifikasi" value="3" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                  <div>
                    <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 -> Level 2 -> Level 3</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Dokumen harus melewati tiga tingkat persetujuan secara berurutan</p>
                  </div>
                </label>
              </div>

              <div class="space-y-3">
                <p class="text-xs font-semibold text-slate-700">Pilih Verifikator</p>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 1 <span class="text-blue-400">*</span></label>
                  <select name="verifikator_1" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="" disabled selected>Pilih verifikator level 1</option>
                    <option>Kepala Jurusan TRPL</option>
                    <option>Kepala Jurusan TI</option>
                    <option>Wakil Direktur I</option>
                    <option>Wakil Direktur II</option>
                  </select>
                </div>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 2</label>
                  <select name="verifikator_2" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="">- Tidak ada (jika hanya 1 level) -</option>
                    <option>Wakil Direktur I</option>
                    <option>Wakil Direktur II</option>
                    <option>Wakil Direktur III</option>
                  </select>
                </div>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 3</label>
                  <select name="verifikator_3" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="">- Tidak ada (jika hanya 1-2 level) -</option>
                    <option>Direktur Polibatam</option>
                  </select>
                </div>
              </div>

              <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-start gap-3">
                <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-[11px] text-blue-600 font-light leading-relaxed">Setelah dikirim, sistem akan meneruskan dokumen ke verifikator pertama. Jika ditolak oleh salah satu verifikator, proses berhenti dan dokumen dikembalikan untuk diperbaiki.</p>
              </div>

              <div class="flex items-center justify-between pt-2">
                <button id="proses-back-2" type="button"
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                  Kembali
                </button>
                <button id="proses-submit" type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                  Kirim ke Verifikator
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
              </div>

            </div>
          </div>

        </div>
      </main>
    </div>

@include('template.footer')
