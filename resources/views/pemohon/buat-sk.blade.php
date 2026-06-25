@include('template.layouts.header', ['pageTitle' => 'Buat Pengajuan SK'])
@include('template.sidebar.pemohon', ['activePage' => 'buat-sk'])

@php
  $dasarHukumOptions = collect($dasarHukumAktif ?? [])->map(function ($dasarHukum) {
      return [
          'value' => $dasarHukum->dasar_hukum_id,
          // Label Mengingat mengikuti format sederhana dari master: judul_hukum + keterangan.
          'label' => $dasarHukum->labelMengingat(),
      ];
  })->values();
@endphp

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Buat Pengajuan SK</h1>
          <p class="text-[11px] text-slate-400 font-light">Susun dan kirim pengajuan Surat Keputusan.</p>
        </div>
        <a href="{{ route('pemohon.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>

      <main class="flex-1 overflow-y-auto p-6 bg-slate-100">
        <div id="page-buat-sk" class="page-content">
          <div class="max-w-5xl mx-auto">
            @if ($errors->any())
              <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3">
                <p class="text-xs font-semibold text-red-700">Pengajuan SK belum bisa dikirim:</p>
                <ul class="mt-1 space-y-1 text-[11px] text-red-600 font-light">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form id="sk-form" method="POST" action="{{ route('pemohon.sk.store') }}">
              @csrf
              {{-- Form ini tersambung ke database; JavaScript hanya membantu penyusunan baris dan review sebelum submit. --}}

              {{-- Stepper: 2 langkah (Isi Data SK → Review & Kirim) --}}
              <div class="flex items-center mb-6">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="sk-circle-1"><span class="text-[11px] font-bold text-white">1</span></div>
                  <span class="text-xs font-semibold text-blue-600" id="sk-label-1">Isi Data SK</span>
                </div>
                <div class="flex-1 h-px bg-slate-200 mx-3"></div>
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-circle-2"><span class="text-[11px] font-bold text-slate-400">2</span></div>
                  <span class="text-xs font-medium text-slate-400" id="sk-label-2">Review & Kirim</span>
                </div>
              </div>

              {{-- ============================================================ --}}
              {{-- STEP 1: Isi Data SK (gabungan Data SK + Dasar Hukum) --}}
              {{-- ============================================================ --}}
              <div id="sk-step-1" class="rounded-2xl bg-white border border-slate-200/80 shadow-lg shadow-slate-200/50 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                  <h2 class="text-sm font-bold text-slate-900">Langkah 1 — Isi Data Surat Keputusan</h2>
                  <p class="text-xs text-slate-400 font-light mt-0.5">Lengkapi informasi utama, dasar hukum, dan butir keputusan.</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                  {{-- Judul SK --}}
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Judul SK <span class="text-blue-400">*</span></label>
                    <input id="sk-judul" name="judul_sk" type="text" value="{{ old('judul_sk') }}" placeholder="Contoh: SK Pembentukan Panitia Seminar Nasional 2025"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  </div>

                  {{-- Tentang --}}
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Tentang <span class="text-blue-400">*</span></label>
                    <input id="sk-tentang" name="tentang" type="text" value="{{ old('tentang') }}" placeholder="Contoh: Pembentukan Panitia Seminar Nasional Tahun 2025"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                    <p class="text-[10px] text-slate-400 font-light">Deskripsi singkat isi SK yang akan diterbitkan.</p>
                  </div>

                  {{-- Menimbang --}}
                  <div class="space-y-2">
                    <div class="flex items-center justify-between gap-3">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Menimbang <span class="text-blue-400">*</span></label>
                      <button id="sk-add-menimbang" type="button" class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-[11px] font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">+ Tambah Menimbang</button>
                    </div>
                    <div id="sk-menimbang-list" class="space-y-2">
                      <div class="sk-menimbang-row flex items-start gap-3">
                        <span class="sk-menimbang-label w-7 shrink-0 pt-3 text-xs font-semibold text-blue-600">a.</span>
                        <textarea name="menimbang[]" rows="2" placeholder="Contoh: bahwa dalam rangka pelaksanaan Seminar Nasional perlu dibentuk panitia"
                          class="sk-menimbang-input min-h-[46px] flex-1 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                        <button type="button" class="sk-remove-menimbang mt-2.5 hidden rounded-lg border border-red-100 bg-red-50 px-2.5 py-1.5 text-[10px] font-semibold text-red-600 hover:bg-red-100 transition-all duration-200">Hapus</button>
                      </div>
                    </div>
                  </div>

                  {{-- ========================================== --}}
                  {{-- Dasar Hukum / Mengingat (pindahan dari Step 2 lama) --}}
                  {{-- ========================================== --}}
                  <div class="space-y-2 rounded-2xl border border-blue-100/80 bg-blue-50/20 p-4">
                    <div class="flex items-center justify-between gap-3">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Mengingat (Dasar Hukum) <span class="text-blue-400">*</span></label>
                      <button id="sk-add-mengingat" type="button" class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-[11px] font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">+ Tambah Dasar Hukum</button>
                    </div>
                    <div id="sk-mengingat-list" class="space-y-2">
                      <div id="sk-mengingat-empty" class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-4 text-center">
                        <p class="text-xs font-medium text-slate-500">Belum ada dasar hukum dipilih.</p>
                        <p class="mt-1 text-[10px] text-slate-400 font-light">Klik tombol Tambah Dasar Hukum untuk memilih dari master aktif.</p>
                      </div>
                    </div>
                    {{-- Hidden input ini disusun ulang oleh JavaScript agar urutan dasar_hukum_id[] mengikuti list Mengingat. --}}
                    <div id="sk-mengingat-hidden-inputs"></div>
                    <p class="text-[10px] text-slate-400 font-light">Nomor Mengingat dibuat otomatis mengikuti urutan baris.</p>
                  </div>

                  {{-- Menetapkan & Diktum --}}
                  <div class="space-y-4 rounded-2xl border border-slate-100 bg-slate-50/40 p-4">
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 tracking-wide">Menetapkan <span class="text-blue-400">*</span></label>
                      <input id="sk-menetapkan" name="menetapkan" type="text" value="{{ old('menetapkan') }}" placeholder="Contoh: Keputusan Direktur tentang Pembentukan Panitia Seminar Nasional"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100" />
                    </div>

                    <div class="space-y-2">
                      <div class="flex items-center justify-between gap-3">
                        <label class="block text-xs font-semibold text-slate-700 tracking-wide">Diktum Keputusan <span class="text-blue-400">*</span></label>
                        <button id="sk-add-diktum" type="button" class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-[11px] font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">+ Tambah Diktum</button>
                      </div>
                      <div id="sk-diktum-list" class="space-y-2">
                        <div class="sk-diktum-row flex items-start gap-3">
                          <span class="sk-diktum-label w-20 shrink-0 pt-3 text-xs font-semibold text-blue-600">KESATU</span>
                          <textarea name="memutuskan[]" rows="2" placeholder="Contoh: Membentuk Panitia Seminar Nasional Tahun 2025"
                            class="sk-diktum-input min-h-[46px] flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                          <button type="button" class="sk-remove-diktum mt-2.5 hidden rounded-lg border border-red-100 bg-red-50 px-2.5 py-1.5 text-[10px] font-semibold text-red-600 hover:bg-red-100 transition-all duration-200">Hapus</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  {{-- Pesan validasi Dasar Hukum (ditampilkan oleh JS jika kosong) --}}
                  <div id="sk-mengingat-error" class="hidden rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                    Pilih minimal 1 dasar hukum (Mengingat) sebelum melanjutkan ke Review.
                  </div>

                  {{-- Tombol Lanjut ke Review --}}
                  <div class="flex justify-end pt-2">
                    <button id="sk-proto-next-1" type="button"
                      class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                      Lanjut Review
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </button>
                  </div>
                </div>
              </div>

              {{-- ============================================================ --}}
              {{-- STEP 2: Review & Kirim (sebelumnya Step 3) --}}
              {{-- ============================================================ --}}
              <div id="sk-step-2" class="hidden rounded-2xl bg-white border border-slate-200/80 shadow-lg shadow-slate-200/50 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                  <h2 class="text-sm font-bold text-slate-900">Langkah 2 — Review & Kirim</h2>
                  <p class="text-xs text-slate-400 font-light mt-0.5">Periksa susunan SK sebelum dikirim ke Admin Surat.</p>
                </div>
                <div class="px-6 py-6 space-y-4">
                  <div class="space-y-3">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Judul SK</p><p id="review-judul" class="text-sm font-medium text-slate-800"></p></div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tentang</p><p id="review-tentang" class="text-sm font-medium text-slate-800"></p></div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Menimbang</p><ul id="review-menimbang" class="space-y-1"></ul></div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Mengingat</p><ul id="review-mengingat" class="space-y-1"></ul></div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                      <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Memutuskan</p>
                      <div class="flex items-start gap-2 text-xs text-slate-600 font-light mb-1">
                        <span class="w-24 shrink-0 font-semibold text-slate-500">Menetapkan</span>
                        <span id="review-menetapkan" class="leading-relaxed"></span>
                      </div>
                      <ul id="review-memutuskan" class="space-y-1"></ul>
                    </div>
                  </div>
                  <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-center gap-3">
                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-[11px] text-blue-600 font-light">Setelah dikirim, status SK menjadi Diajukan dan menunggu review Admin Surat.</p>
                  </div>
                  <div class="flex items-center justify-between pt-2">
                    <button id="sk-proto-back-1" type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>Kembali
                    </button>
                    <button id="sk-proto-submit-btn" type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                      Kirim Pengajuan SK<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </button>
                  </div>
                </div>
              </div>
            </form>

            {{-- Modal pencarian dasar hukum aktif dari tabel dasar_hukum untuk menghindari dropdown panjang di Step 2. --}}
            <div id="sk-dasar-hukum-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 px-4 py-6">
              <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                  <div>
                    <h3 class="text-sm font-bold text-slate-900">Pilih Dasar Hukum</h3>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">Cari dan pilih dasar hukum aktif untuk bagian Mengingat.</p>
                  </div>
                  <button id="sk-dasar-modal-close" type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">Tutup</button>
                </div>
                <div class="px-5 py-4 space-y-3">
                  <div class="space-y-1.5">
                    <label for="sk-dasar-search" class="block text-xs font-semibold text-slate-700 tracking-wide">Cari Dasar Hukum</label>
                    <input id="sk-dasar-search" type="search" placeholder="Ketik judul atau keterangan dasar hukum..."
                      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  </div>

                  <div id="sk-dasar-modal-list" class="max-h-[360px] space-y-2 overflow-y-auto pr-1">
                    @forelse ($dasarHukumOptions as $option)
                      <div class="sk-dasar-option flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-4 py-3 transition-all duration-200 hover:border-blue-100 hover:bg-blue-50/30"
                        data-dasar-id="{{ $option['value'] }}"
                        data-dasar-label="{{ $option['label'] }}">
                        <p class="min-w-0 flex-1 text-xs font-medium leading-relaxed text-slate-700">{{ $option['label'] }}</p>
                        <button type="button" class="sk-pilih-dasar shrink-0 rounded-lg bg-blue-600 px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-500 transition-all duration-200">Pilih</button>
                      </div>
                    @empty
                      <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-8 text-center">
                        <p class="text-xs font-semibold text-slate-600">Belum ada dasar hukum aktif.</p>
                        <p class="mt-1 text-[11px] text-slate-400 font-light">Tambahkan data aktif dari Master Dasar Hukum terlebih dahulu.</p>
                      </div>
                    @endforelse
                  </div>

                  <div id="sk-dasar-empty-search" class="hidden rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-8 text-center">
                    <p class="text-xs font-semibold text-slate-600">Dasar hukum tidak ditemukan.</p>
                    <p class="mt-1 text-[11px] text-slate-400 font-light">Coba gunakan kata kunci lain.</p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
@include('template.layouts.footer')
