@include('template.header', ['pageTitle' => 'Proses SK'])
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
          <h1 class="text-sm font-bold text-slate-900">Review & Proses SK</h1>
          <p class="text-[11px] text-slate-400 font-light">Tinjau isi SK dan tentukan jalur verifikasi</p>
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
            $judulSk = $sk->judul ?? request('judul') ?? '-';
            $pemohonSk = $sk->pemohon ?? request('pemohon') ?? '-';
            $tentangSk = $sk->tentang ?? request('ringkasan') ?? '-';
            $menimbangSk = $sk->menimbang ?? request('ringkasan') ?? '-';
            $mengingatSk = $sk->mengingat ?? '';
            $memutuskanSk = $sk->memutuskan ?? request('ringkasan') ?? '-';
          @endphp

          {{-- Info bar SK yang diproses --}}
          <div class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3 mb-5">
            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div class="min-w-0">
              <p class="text-[11px] font-semibold text-blue-700">Sedang mereview SK:</p>
              <p id="sk-proses-judul-info" class="text-[11px] text-blue-600 font-light truncate">{{ $judulSk }}</p>
            </div>
            <a href="{{ route('admin.pengajuan-sk') }}" class="ml-auto text-[10px] font-medium text-blue-500 hover:text-blue-700 shrink-0 transition-colors duration-200">Kembali ke daftar</a>
          </div>

          {{-- Step Indicator --}}
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

          <div id="sk-proses-step-1" class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 1 - Review Isi SK</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Periksa kelengkapan dan kesesuaian isi SK dari pemohon.</p>
            </div>
            <div class="px-6 py-6 space-y-4">

              <div class="space-y-3">
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Judul SK</p>
                  <p id="sk-review-judul" class="text-sm font-semibold text-slate-800">{{ $judulSk }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Pemohon</p>
                  <p class="text-sm font-medium text-slate-700">{{ $pemohonSk }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Tentang</p>
                  <p id="sk-review-tentang" class="text-sm font-medium text-slate-700">{{ $tentangSk }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Menimbang</p>
                  <p id="sk-review-menimbang" class="text-xs text-slate-600 font-light leading-relaxed whitespace-pre-line">{{ $menimbangSk }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Mengingat</p>
                  <ul id="sk-review-mengingat" class="space-y-1.5">
                    @if(!empty($mengingatSk))
                      @foreach(explode("\n", $mengingatSk) as $mengingat)
                        <li class="text-xs text-slate-600 font-light">{{ $mengingat }}</li>
                      @endforeach
                    @else
                      <li class="text-xs text-slate-400 font-light">-</li>
                    @endif
                  </ul>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Memutuskan</p>
                  <p id="sk-review-memutuskan" class="text-xs text-slate-600 font-light leading-relaxed whitespace-pre-line">{{ $memutuskanSk }}</p>
                </div>
              </div>

              <div class="border-t border-slate-100 pt-4">
                <p class="text-xs font-semibold text-slate-700 mb-3">Catatan / Komentar Admin</p>

                <div class="space-y-2 mb-4">
                  <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="radio" name="status_review_sk" value="lanjut" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" checked />
                    <div>
                      <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Lanjutkan ke verifikasi</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Isi SK sudah sesuai, siap dikirim ke verifikator</p>
                    </div>
                  </label>
                  <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                    <input type="radio" name="status_review_sk" value="revisi" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Kembalikan untuk revisi</p>
                      <p class="text-[11px] text-slate-400 font-light mt-0.5">Ada bagian yang perlu diperbaiki oleh pemohon</p>
                    </div>
                  </label>
                </div>

                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 tracking-wide">
                    Catatan untuk Pemohon
                    <span class="text-[10px] font-normal text-slate-400 ml-1">(opsional jika lanjut, wajib jika revisi)</span>
                  </label>
                  <textarea id="sk-catatan-admin" name="catatan_admin" rows="4"
                    placeholder="Contoh: Bagian Menimbang perlu diperjelas, dasar hukum poin 3 kurang relevan..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                  <p class="text-[10px] text-slate-400 font-light">Catatan ini akan dikirim ke pemohon melalui email jika SK dikembalikan untuk revisi.</p>
                </div>
              </div>

              <div class="flex items-center justify-between pt-2">
                <button id="sk-proses-tolak-btn" type="button"
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                  Kembalikan Revisi
                </button>
                <button id="sk-proses-next-1" type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                  Lanjut - Tentukan Verifikasi
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </button>
              </div>

            </div>
          </div>

          <div id="sk-proses-step-2" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 2 - Tingkat Verifikasi</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Tentukan jalur verifikasi SK sebelum dikirim ke verifikator.</p>
            </div>
            <div class="px-6 py-6 space-y-5">

              <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-700 tracking-wide">Jalur Verifikasi <span class="text-blue-400">*</span></label>

                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                  <input type="radio" name="jalur_verifikasi_sk" value="1" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                  <div>
                    <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 saja</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">SK hanya perlu disetujui oleh Verifikator Level 1</p>
                  </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                  <input type="radio" name="jalur_verifikasi_sk" value="2" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                  <div>
                    <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 -> Level 2</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">SK harus disetujui Level 1 terlebih dahulu, lalu diteruskan ke Level 2</p>
                  </div>
                </label>

                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
                  <input type="radio" name="jalur_verifikasi_sk" value="3" class="mt-0.5 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
                  <div>
                    <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">Level 1 -> Level 2 -> Level 3</p>
                    <p class="text-[11px] text-slate-400 font-light mt-0.5">SK harus melewati tiga tingkat persetujuan secara berurutan</p>
                  </div>
                </label>
              </div>

              <div class="space-y-3">
                <p class="text-xs font-semibold text-slate-700">Pilih Verifikator</p>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 1 <span class="text-blue-400">*</span></label>
                  <select name="sk_verifikator_1" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="" disabled selected>Pilih verifikator level 1</option>
                    <option>Kepala Jurusan TRPL</option>
                    <option>Kepala Jurusan TI</option>
                    <option>Wakil Direktur I</option>
                    <option>Wakil Direktur II</option>
                  </select>
                </div>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 2</label>
                  <select name="sk_verifikator_2" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="">- Tidak ada (jika hanya 1 level) -</option>
                    <option>Wakil Direktur I</option>
                    <option>Wakil Direktur II</option>
                    <option>Wakil Direktur III</option>
                  </select>
                </div>

                <div class="space-y-1.5">
                  <label class="block text-[11px] font-medium text-slate-500">Verifikator Level 3</label>
                  <select name="sk_verifikator_3" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-light outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200">
                    <option value="">- Tidak ada (jika hanya 1-2 level) -</option>
                    <option>Direktur Polibatam</option>
                  </select>
                </div>
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

          <div id="sk-proses-step-3" class="hidden rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h2 class="text-sm font-bold text-slate-900">Langkah 3 - Konfirmasi & Kirim</h2>
              <p class="text-xs text-slate-400 font-light mt-0.5">Periksa ringkasan sebelum SK dikirim ke verifikator pertama.</p>
            </div>
            <div class="px-6 py-6 space-y-4">

              <div class="space-y-3">
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">SK yang Diproses</p>
                  <p id="sk-konfirmasi-judul" class="text-sm font-semibold text-slate-800">{{ $judulSk }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
                  <p id="sk-konfirmasi-pemohon" class="text-sm font-medium text-slate-700">{{ $pemohonSk }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                  <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Jalur Verifikasi</p>
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
                <p class="text-[11px] text-blue-600 font-light leading-relaxed">Setelah dikirim, sistem akan meneruskan SK ke verifikator pertama. Jika ditolak oleh salah satu verifikator, proses berhenti dan SK dikembalikan untuk diperbaiki.</p>
              </div>

              <div class="flex items-center justify-between pt-2">
                <button id="sk-proses-back-2" type="button"
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                  Kembali
                </button>
                <button id="sk-proses-submit" type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                  Kirim SK ke Verifikator
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
              </div>

            </div>
          </div>

        </div>
      </main>
    </div>

@include('template.footer')
