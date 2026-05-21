<!-- Modal detail admin lokal untuk halaman admin yang memakai tombol .btn-detail. -->
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
      <div id="modal-admin-revision-wrap" class="hidden rounded-xl border border-red-100 bg-red-50 px-4 py-3">
        <p class="text-[10px] font-semibold text-red-500 uppercase tracking-wider mb-1">Catatan Revisi</p>
        <p id="modal-admin-revision-source" class="text-[11px] font-semibold text-red-600 mb-1"></p>
        <p id="modal-admin-revision-note" class="text-xs text-red-700 font-light leading-relaxed whitespace-pre-line"></p>
      </div>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
      <button id="modal-close-btn" type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
        Tutup
      </button>
      <div class="flex items-center gap-2">
        <button id="modal-preview-pdf" type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Cek PDF
        </button>
        <button id="modal-proses-btn" type="button" data-route-surat="{{ route('admin.proses-surat') }}" data-route-sk="{{ route('admin.proses-sk') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
          <!-- Proses Surat -->
        </button>
      </div>
    </div>
  </div>
</div>
