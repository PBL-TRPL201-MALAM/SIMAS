@include('template.header', ['pageTitle' => 'Buat Pengajuan SK', 'modalVariant' => 'pemohon'])
@include('template.pemohon-sidebar', ['activePage' => 'buat-sk'])
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Buat Pengajuan SK</h1>
          <p class="text-[11px] text-slate-400 font-light">Lengkapi data dan review pengajuan SK.</p>
        </div>
        <a href="{{ route('pemohon.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-buat-sk" class="page-content">
          <div class="max-w-2xl mx-auto">

            <!-- Step indicator -->
            <div class="flex items-center mb-6">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0" id="sk-circle-1"><span class="text-[11px] font-bold text-white">1</span></div>
                <span class="text-xs font-semibold text-blue-600" id="sk-label-1">Data SK</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-circle-2"><span class="text-[11px] font-bold text-slate-400">2</span></div>
                <span class="text-xs font-medium text-slate-400" id="sk-label-2">Dasar Hukum</span>
              </div>
              <div class="flex-1 h-px bg-slate-200 mx-3"></div>
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0" id="sk-circle-3"><span class="text-[11px] font-bold text-slate-400">3</span></div>
                <span class="text-xs font-medium text-slate-400" id="sk-label-3">Review</span>
              </div>
            </div>

            <!-- Step 1 -->
            <div id="sk-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 1 Data Surat Keputusan</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Isi informasi utama Surat Keputusan yang akan diajukan.</p>
              </div>
              <div class="px-6 py-6 space-y-5">
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Judul SK <span class="text-blue-400">*</span></label>
                  <input id="sk-judul" type="text" placeholder="Contoh: SK Pembentukan Panitia Seminar Nasional 2025"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Tentang <span class="text-blue-400">*</span></label>
                  <input id="sk-tentang" type="text" placeholder="Contoh: Pembentukan Panitia Seminar Nasional Tahun 2025"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                  <p class="text-[10px] text-slate-400 font-light">Deskripsi singkat isi SK yang akan diterbitkan.</p>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Menimbang <span class="text-blue-400">*</span></label>
                  <textarea id="sk-menimbang" rows="4" placeholder="Contoh:&#10;a. bahwa dalam rangka pelaksanaan Seminar Nasional perlu dibentuk panitia;&#10;b. bahwa untuk kelancaran pelaksanaan kegiatan tersebut perlu ditetapkan Surat Keputusan;"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                  <p class="text-[10px] text-slate-400 font-light">Tuliskan butir-butir pertimbangan (a, b, c, ...) secara manual.</p>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Memutuskan <span class="text-blue-400">*</span></label>
                  <textarea id="sk-memutuskan" rows="5" placeholder="Contoh:&#10;PERTAMA : Membentuk Panitia Seminar Nasional Tahun 2025;&#10;KEDUA   : Panitia bertugas merencanakan dan melaksanakan kegiatan;&#10;KETIGA  : SK ini berlaku sejak tanggal ditetapkan;"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                  <p class="text-[10px] text-slate-400 font-light">Tuliskan diktum keputusan (PERTAMA, KEDUA, ...) secara manual.</p>
                </div>
                <div class="flex justify-end pt-2">
                  <button id="sk-next-1" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut Pilih Dasar Hukum
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Step 2 -->
            <div id="sk-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 2 Dasar Hukum (Mengingat)</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Pilih dasar hukum yang relevan dari master yang tersedia.</p>
              </div>
              <div class="px-6 py-6 space-y-4">
                <div class="relative">
                  <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                  <input id="sk-search-dasar" type="text" placeholder="Cari dasar hukum..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="space-y-2 max-h-72 overflow-y-auto pr-1" id="sk-dasar-list">
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="1" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">UU No. 20 Tahun 2003</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Sistem Pendidikan Nasional</p></div>
                  </label>
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="2" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">PP No. 4 Tahun 2014</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Penyelenggaraan Pendidikan Tinggi dan Pengelolaan Perguruan Tinggi</p></div>
                  </label>
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="3" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Permenristekdikti No. 44 Tahun 2015</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Standar Nasional Pendidikan Tinggi</p></div>
                  </label>
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="4" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Statuta Polibatam Tahun 2020</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Statuta Politeknik Negeri Batam</p></div>
                  </label>
                  <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="checkbox" value="5" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-100 shrink-0" />
                    <div><p class="text-xs font-medium text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Peraturan Direktur Polibatam No. 01 Tahun 2023</p><p class="text-[11px] text-slate-400 font-light mt-0.5">tentang Tata Kelola Administrasi Polibatam</p></div>
                  </label>
                </div>
                <p class="text-[10px] text-slate-400 font-light">Pilih satu atau lebih dasar hukum yang relevan.</p>
                <div class="flex items-center justify-between pt-2">
                  <button id="sk-back-1" type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>Kembali
                  </button>
                  <button id="sk-next-2" type="button" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Lanjut Review<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Step 3 -->
            <div id="sk-step-3" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
                <h2 class="text-sm font-bold text-slate-900">Langkah 3 Review & Submit</h2>
                <p class="text-xs text-slate-400 font-light mt-0.5">Periksa kembali data sebelum mengajukan SK.</p>
              </div>
              <div class="px-6 py-6 space-y-4">
                <div class="space-y-3">
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Judul SK</p><p id="review-judul" class="text-sm font-medium text-slate-800"</p></div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tentang</p><p id="review-tentang" class="text-sm font-medium text-slate-800"</p></div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Menimbang</p><p id="review-menimbang" class="text-xs text-slate-600 font-light whitespace-pre-line"</p></div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Mengingat</p><ul id="review-mengingat" class="space-y-1"></ul></div>
                  <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3"><p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Memutuskan</p><p id="review-memutuskan" class="text-xs text-slate-600 font-light whitespace-pre-line"</p></div>
                </div>
                <div class="rounded-xl border border-blue-100 bg-blue-50/40 px-4 py-3 flex items-center gap-3">
                  <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <p class="text-[11px] text-blue-600 font-light">Setelah disubmit, status SK menjadi <strong>Diajukan</strong> dan menunggu review Admin/TU.</p>
                </div>
                <div class="flex items-center justify-between pt-2">
                  <button id="sk-back-2" type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>Kembali
                  </button>
                  <button id="sk-submit-btn" type="button" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    Submit Pengajuan SK<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>


        <!-- ============================================================
             PAGE: SURAT SAYA Tabel + Filter + Aksi
        ============================================================ -->
      </main>
    </div>
@include('template.footer')

