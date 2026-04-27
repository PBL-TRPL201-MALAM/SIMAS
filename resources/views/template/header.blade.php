<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $pageTitle ?? 'Dashboard' }} - SIMAS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      * { font-family: 'Poppins', sans-serif; }
      ::-webkit-scrollbar { width: 4px; height: 4px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
      ::-webkit-scrollbar-thumb:hover { background: #bfdbfe; }
    </style>
  </head>

  <body class="bg-slate-50 antialiased overflow-hidden h-screen">
  @php($modalVariant = $modalVariant ?? 'admin')

  <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
    <div class="flex items-center gap-3 rounded-2xl bg-slate-900 px-4 py-3 shadow-xl min-w-[220px]">
      <div id="toast-icon" class="w-5 h-5 shrink-0"></div>
      <p id="toast-msg" class="text-xs font-medium text-white"></p>
    </div>
  </div>

  @if($modalVariant === 'admin')
  <div id="modal-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-5xl rounded-2xl bg-white shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Detail Pengajuan</h3>
          <p id="modal-jenis" class="text-[11px] text-slate-400 font-light mt-0.5"></p>
        </div>
        <button id="modal-close" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
      <div class="px-6 py-5 space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Perihal / Judul</p>
            <p id="modal-perihal" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
            <p id="modal-pemohon" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Diajukan</p>
            <p id="modal-tanggal" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</p>
            <p id="modal-status" class="text-xs font-medium text-slate-800"></p>
          </div>
        </div>
        <div class="rounded-xl bg-slate-50 px-4 py-3">
          <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Ringkasan</p>
          <p id="modal-ringkasan" class="text-xs text-slate-600 font-light leading-relaxed"></p>
        </div>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <button id="modal-close-btn" type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
          Tutup
        </button>
        <div class="flex items-center gap-2">
          <button id="modal-download-docx" type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            Unduh DOCX
          </button>
          <button id="modal-proses-btn" type="button" data-route-surat="{{ route('admin.proses-surat') }}" data-route-sk="{{ route('admin.proses-sk') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            <!-- Proses Surat -->
          </button>
        </div>
      </div>
    </div>
  </div>
  @elseif($modalVariant === 'pemohon')
  <div id="modal-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Detail Dokumen</h3>
          <p id="modal-jenis" class="text-[11px] text-slate-400 font-light mt-0.5"></p>
        </div>
        <button id="modal-close" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
      <div class="px-6 py-5 space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Perihal / Judul</p>
            <p id="modal-perihal" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Diajukan</p>
            <p id="modal-tanggal" class="text-xs font-medium text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</p>
            <p id="modal-status" class="text-xs font-medium"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Nomor Surat</p>
            <p id="modal-nomor" class="text-xs font-medium text-slate-800"></p>
          </div>
        </div>
        <div class="rounded-xl bg-slate-50 px-4 py-3">
          <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Keterangan</p>
          <p id="modal-keterangan" class="text-xs text-slate-600 font-light leading-relaxed"></p>
        </div>
      </div>
      <div id="modal-footer" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <button id="modal-close-btn" type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
          Tutup
        </button>
        <button id="modal-download-btn" type="button" class="hidden inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
          Unduh Dokumen
        </button>
      </div>
    </div>
  </div>
  @elseif($modalVariant === 'verifikator')
  <div id="modal-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Verifikasi Dokumen</h3>
          <p id="modal-jenis-badge" class="text-[11px] text-slate-400 font-light mt-0.5"></p>
        </div>
        <button id="modal-close" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
      <div class="px-6 py-5 space-y-3 max-h-[78vh] overflow-y-auto">
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Perihal / Judul</p>
            <p id="modal-perihal" class="text-xs font-semibold text-slate-800"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
            <p id="modal-pemohon" class="text-xs font-medium text-slate-700"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Pengajuan</p>
            <p id="modal-tanggal" class="text-xs font-medium text-slate-700"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Level Verifikasi Saya</p>
            <p id="modal-level" class="text-xs font-medium text-blue-600"></p>
          </div>
        </div>
        <div class="rounded-xl bg-slate-50 px-4 py-3">
          <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Ringkasan / Isi</p>
          <p id="modal-ringkasan" class="text-xs text-slate-600 font-light leading-relaxed"></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden">
          <div class="flex items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3">
            <div>
              <p class="text-xs font-semibold text-slate-700">Preview PDF Hasil Pemeriksaan</p>
              <p class="text-[11px] text-slate-400 font-light">Dokumen dapat dibaca langsung di modal ini.</p>
            </div>
            <span id="modal-pdf-status" class="text-[10px] font-medium text-slate-400">Belum dimuat</span>
          </div>
          <div class="h-[55vh] min-h-[420px] bg-slate-100">
            <iframe id="modal-pdf-frame" title="Preview PDF Verifikasi" class="hidden h-full w-full" src="about:blank"></iframe>
            <div id="modal-pdf-placeholder" class="flex h-full items-center justify-center px-6 text-center">
              <div>
                <p class="text-sm font-semibold text-slate-600">Preview PDF belum tersedia.</p>
                <p class="mt-1 text-[11px] font-light text-slate-400">File hasil pemeriksaan akan tampil di sini jika tersedia.</p>
              </div>
            </div>
          </div>
        </div>
        <div id="modal-sk-section" class="hidden space-y-2">
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tentang</p>
            <p id="modal-sk-tentang" class="text-xs text-slate-600 font-light"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Menimbang</p>
            <p id="modal-sk-menimbang" class="text-xs text-slate-600 font-light whitespace-pre-line"></p>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Memutuskan</p>
            <p id="modal-sk-memutuskan" class="text-xs text-slate-600 font-light whitespace-pre-line"></p>
          </div>
        </div>

        <div class="border-t border-slate-100 pt-3">
          <p class="text-xs font-semibold text-slate-700 mb-3">Keputusan Verifikasi</p>

          <div class="space-y-2 mb-3">
            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
              <input type="radio" name="keputusan_verifikasi" value="setuju" id="radio-setuju" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" checked />
              <div>
                <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">✅ Setujui Dokumen</p>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Dokumen disetujui dan diteruskan ke level berikutnya (jika ada)</p>
              </div>
            </label>
            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50/30 cursor-pointer transition-all duration-200 group">
              <input type="radio" name="keputusan_verifikasi" value="tolak" id="radio-tolak" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-100 shrink-0" />
              <div>
                <p class="text-xs font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">❌ Tolak Dokumen</p>
                <p class="text-[11px] text-slate-400 font-light mt-0.5">Proses berhenti, dokumen dikembalikan untuk diperbaiki</p>
              </div>
            </label>
          </div>

          <div id="alasan-wrap" class="hidden space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 tracking-wide">
              Alasan Penolakan <span class="text-blue-400">*</span>
            </label>
            <textarea id="alasan-penolakan" rows="3" placeholder="Tuliskan alasan penolakan secara jelas agar pemohon dapat memperbaikinya..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
            <p class="text-[10px] text-slate-400 font-light">Alasan ini akan dicatat dan dikirimkan kepada pemohon.</p>
          </div>
        </div>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <button id="modal-close-btn" type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
          Tutup
        </button>
        <div class="flex items-center gap-2">
          <button id="modal-unduh-btn" type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            Unduh PDF
          </button>
          <button id="modal-submit-btn" type="button" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            Kirim Keputusan
          </button>
        </div>
      </div>
    </div>
  </div>
  @endif

  <div class="flex h-screen w-full overflow-hidden">
