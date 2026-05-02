@include('template.header', ['pageTitle' => 'Buat Surat Baru', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'buat-surat'])
    <!-- View ini menampilkan wizard Pemohon untuk mengunggah draft DOCX dan mengisi data awal surat biasa. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Buat Surat Baru</h1>
          <p class="text-[11px] text-slate-400 font-light">Upload draft dan lengkapi data surat.</p>
        </div>
        <a href="{{ route('pemohon.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-buat-surat" class="page-content">
          <div class="max-w-2xl mx-auto">

            <div class="flex items-center mb-6">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="surat-step2-circle-wrap">
                  <span class="text-[11px] font-bold text-white">1</span>
                </div>
                <span class="text-xs font-semibold text-blue-600">Upload Draft</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="surat-step2-circle">
                  <span class="text-[11px] font-bold text-slate-400">2</span>
                </div>
                <span class="text-xs font-medium text-slate-400" id="surat-step2-label">Data Surat</span>
              </div>
            </div>

            <div id="surat-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <!-- Step 1 hanya memilih file draft; file ini tetap dikirim bersama form di step 2 melalui atribut form="surat-biasa-form". -->
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 1 - Upload Draft Surat</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Siapkan draft surat dalam format DOCX lalu unggah ke sistem.</p>
              </div>
              <div class="px-6 py-6 space-y-5">
                <!-- Error validasi dari PemohonSuratControllerstore muncul di sini setelah redirect back. -->
                @if ($errors->any())
                  <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
                    {{ $errors->first() }}
                  </div>
                @endif

                <div class="rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                  <p class="text-[11px] font-semibold text-blue-700 mb-1.5">Yang perlu disiapkan:</p>
                  <ul class="space-y-1">
                    <li class="text-[11px] text-blue-600 font-light flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-blue-400 shrink-0"></span>Buat draft surat menggunakan Microsoft Word</li>
                    <li class="text-[11px] text-blue-600 font-light flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-blue-400 shrink-0"></span>Simpan file dalam format <strong>.DOCX</strong></li>
                    <li class="text-[11px] text-blue-600 font-light flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-blue-400 shrink-0"></span>Ukuran file maksimal 10 MB</li>
                  </ul>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">File Draft Surat (DOCX) <span class="text-blue-400">*</span></label>
                  <div id="surat-drop-zone" class="relative flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-6 py-10 hover:border-blue-300 hover:bg-blue-50/30 transition-all duration-200 cursor-pointer">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                      <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    </div>
                    <div class="text-center">
                      <p class="text-xs font-semibold text-slate-700">Klik atau seret file ke sini</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Format: DOCX · Maks. 10 MB</p>
                    </div>
                    <input id="surat-file-input" name="draft_surat" form="surat-biasa-form" type="file" accept=".docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                  </div>
                  <!-- Preview nama file diisi oleh JavaScript agar user tahu file DOCX mana yang akan dikirim. -->
                  <div id="surat-file-preview" class="hidden flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-2.5 mt-2">
                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p id="surat-file-name" class="text-[11px] font-medium text-blue-700 truncate"></p>
                    <button id="surat-file-remove" type="button" class="ml-auto text-slate-400 hover:text-slate-600 transition-colors duration-200 shrink-0">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                  </div>
                </div>
                <div class="flex justify-end pt-2">
                  <button id="surat-next-btn" type="button" disabled
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-40 disabled:cursor-not-allowed disabled:transform-none transition-all duration-200">
                    Lanjut
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>

            <div id="surat-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <!-- Step 2 berisi form utama; action route pemohon.surat.store diproses oleh PemohonSuratControllerstore. -->
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 2 - Data Surat</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Lengkapi informasi awal surat yang akan diajukan.</p>
              </div>
              <form id="surat-biasa-form" action="{{ route('pemohon.surat.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
                <!-- csrf wajib untuk form POST Laravel dan enctype diperlukan karena form mengirim file DOCX. -->
                @csrf
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-2.5">
                  <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                  <p id="surat-step2-filename" class="text-[11px] font-medium text-slate-600 truncate"></p>
                  <button id="surat-back-btn" type="button" class="ml-auto text-[10px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200 shrink-0">Ganti file</button>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Perihal <span class="text-blue-400">*</span></label>
                  <!-- old('perihal') menjaga input tetap terisi jika validasi gagal. -->
                  <input type="text" name="perihal" value="{{ old('perihal') }}" placeholder="Contoh: Permohonan Izin Penelitian Lapangan"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  <p class="text-[10px] text-slate-400 font-light">Tuliskan pokok/inti dari surat yang diajukan.</p>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Ringkasan Isi Surat <span class="text-blue-400">*</span></label>
                  <!-- Ringkasan dikirim sebagai data awal surat_biasa agar Admin/TU memahami isi pengajuan sebelum memeriksa DOCX. -->
                  <textarea name="ringkasan" rows="5" placeholder="Tuliskan ringkasan isi surat secara singkat dan jelas..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none">{{ old('ringkasan') }}</textarea>
                  <p class="text-[10px] text-slate-400 font-light">Ringkasan ini membantu Admin/TU memahami isi surat sebelum memeriksa file DOCX.</p>
                </div>
                <div class="flex items-center justify-between pt-2">
                  <button id="surat-back-btn-2" type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    Kembali
                  </button>
                  <button id="surat-submit-btn" type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Ajukan Surat
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </main>
    </div>
@include('template.footer')
