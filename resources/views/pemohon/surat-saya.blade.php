@include('template.layouts.header', ['pageTitle' => 'Surat Saya'])
@include('template.sidebar.pemohon', ['activePage' => 'surat-saya'])
    <!-- View ini menerima $suratSaya dari PemohonSuratControllerindex, berisi surat biasa milik user login. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Surat Saya</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar semua pengajuan surat kamu.</p>
        </div>
        <a href="{{ route('pemohon.profil') }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>
      <main class="flex-1 overflow-y-auto p-6">
        <div id="page-surat-saya" class="page-content space-y-4">
          <!-- Flash message ini berasal dari redirect setelah pengajuan/unduh berhasil atau gagal. -->
          @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
              {{ session('status') }}
            </div>
          @endif
          @if (session('error'))
            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-medium text-red-700">
              {{ session('error') }}
            </div>
          @endif

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">Surat Saya</h2>
            <div class="flex items-center gap-2 flex-wrap">
              <!-- Tombol filter bekerja di sisi frontend melalui data-status pada setiap baris surat. -->
              <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
                <button data-filter="semua" data-target="surat" class="filter-btn active-filter rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
                <button data-filter="diproses" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
                <button data-filter="published" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Published</button>
                <button data-filter="ditolak" data-target="surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Ditolak</button>
              </div>
              <a href="{{ route('pemohon.buat-surat') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Buat Baru
              </a>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full" id="tabel-surat">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="tbody-surat">
                  <!-- forelse memastikan tabel tetap punya empty state saat pemohon belum pernah mengajukan surat. -->
                  @forelse ($suratSaya as $dokumen)
                    <!-- Blok php menyiapkan status filter dan label tampilan dari status_dokumen. -->
                    @php
                      $statusFilter = $dokumen->status_dokumen === 'PUBLISHED'
                          ? 'published'
                          : (in_array($dokumen->status_dokumen, ['DITOLAK', 'PERLU_REVISI'], true) ? 'ditolak' : 'diproses');
                      $statusLabel = str_replace('_', ' ', $dokumen->status_dokumen);
                      $statusDisplay = ucwords(strtolower($statusLabel));
                      $statusButuhCatatan = in_array($dokumen->status_dokumen, ['PERLU_REVISI', 'DITOLAK'], true);
                      // Tombol Perbaiki hanya boleh aktif untuk dokumen yang benar-benar kembali ke Pemohon.
                      $canRevise = in_array($dokumen->status_dokumen, ['PERLU_REVISI', 'DITOLAK'], true);
                      $finalPdf = $dokumen->dokumenFiles->firstWhere('file_type', 'FINAL_PDF');
                      $canViewFinal = $dokumen->status_dokumen === 'PUBLISHED' && $finalPdf;
                      $canDownload = $canViewFinal;
                      $lampiranPayload = $dokumen->dokumenFiles
                          ->where('file_type', 'LAMPIRAN')
                          ->values()
                          ->map(fn ($lampiran) => [
                              'name' => $lampiran->file_name,
                              'previewUrl' => $lampiran->isPreviewableLampiran() ? route('pemohon.lampiran.preview', $lampiran) : '',
                              'downloadUrl' => route('pemohon.lampiran.download', $lampiran),
                              'canPreview' => $lampiran->isPreviewableLampiran(),
                          ]);
                      $modalActionData = [
                          // Tombol lihat/unduh hanya boleh muncul saat dokumen sudah PUBLISHED dan record FINAL_PDF tersedia.
                          'view_url' => $canViewFinal ? route('pemohon.surat.preview', $dokumen) : '',
                          'revision_url' => $canRevise ? route('pemohon.surat.revisi', $dokumen) : '',
                          'download_url' => $canDownload ? route('pemohon.surat.download', $dokumen) : '',
                          'can_view' => $canViewFinal,
                          'can_revise' => $canRevise,
                          'can_download' => $canDownload,
                      ];
                      $catatanRevisiPayload = $statusButuhCatatan ? app(\App\Http\Controllers\PemohonSuratController::class)->resolveRevisionNotePayload($dokumen) : ['source' => '', 'text' => ''];
                    @endphp
                    <!-- Atribut data-* ini dibaca JavaScript untuk mengisi modal detail tanpa query tambahan. -->
                    <!-- Flag can_* menjadi sumber utama tombol modal agar URL aksi tidak pernah muncul pada status yang salah. -->
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                        data-status="{{ $statusFilter }}"
                        data-status-code="{{ $dokumen->status_dokumen }}"
                        data-jenis="Surat Biasa"
                        data-perihal="{{ $dokumen->suratBiasa?->hal ?? '-' }}"
                        data-tanggal="{{ optional($dokumen->created_at)->format('d M Y') }}"
                        data-nomor="{{ $dokumen->suratBiasa?->nomor_surat ?? '-' }}"
                        data-keterangan="{{ $dokumen->suratBiasa?->ringkasan_isi ?? '-' }}"
                        data-status-label="{{ $statusDisplay }}"
                        data-catatan-revisi="{{ $catatanRevisiPayload['text'] }}"
                        data-catatan-revisi-source="{{ $catatanRevisiPayload['source'] }}"
                        data-view-url="{{ $modalActionData['view_url'] }}"
                        data-revision-url="{{ $modalActionData['revision_url'] }}"
                        data-download-url="{{ $modalActionData['download_url'] }}"
                        data-lampiran="{{ $lampiranPayload->toJson() }}"
                        data-can-view="{{ $modalActionData['can_view'] ? 'true' : 'false' }}"
                        data-can-revise="{{ $modalActionData['can_revise'] ? 'true' : 'false' }}"
                        data-can-download="{{ $modalActionData['can_download'] ? 'true' : 'false' }}">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[200px]">{{ $dokumen->suratBiasa?->hal ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ optional($dokumen->created_at)->format('d M Y') }}</p></td>
                      <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $statusFilter === 'published' ? 'text-slate-600 bg-slate-100' : ($statusFilter === 'ditolak' ? 'text-red-600 bg-red-50' : 'text-blue-600 bg-blue-50') }}">
                          <span class="w-1 h-1 rounded-full {{ $statusFilter === 'published' ? 'bg-slate-400' : ($statusFilter === 'ditolak' ? 'bg-red-500' : 'bg-blue-500') }}"></span>{{ $statusDisplay }}
                        </span>
                      </td>
                      <td class="px-5 py-3.5">
                        <button type="button" class="btn-detail text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Lihat Detail</button>
                      </td>
                    </tr>
                  @empty
                  @endforelse
                </tbody>
              </table>
            </div>
            <!-- Empty state ini juga dipakai saat filter frontend menyembunyikan semua baris yang tidak cocok. -->
            <div id="surat-empty" class="{{ $suratSaya->isEmpty() ? 'flex' : 'hidden' }} flex-col items-center justify-center py-16 text-center">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <p class="text-sm font-semibold text-slate-600">Tidak ada surat</p>
              <p class="text-xs text-slate-400 font-light mt-1">Belum ada pengajuan surat dari akun ini.</p>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Modal detail diletakkan di halaman Surat Saya karena aksi tombolnya khusus mengikuti status dokumen milik Pemohon. -->
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
          <div id="modal-lampiran-wrap" class="hidden rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Lampiran Pendukung</p>
            <div id="modal-lampiran-list" class="space-y-2"></div>
          </div>
          <div id="modal-catatan-revisi-wrap" class="hidden rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider mb-1">Catatan Revisi</p>
            <p id="modal-catatan-revisi-source" class="text-[11px] font-semibold text-amber-700 mb-1"></p>
            <p id="modal-catatan-revisi" class="text-xs text-amber-700 font-light leading-relaxed whitespace-pre-line"></p>
          </div>
        </div>
        <div id="modal-footer" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
          <button id="modal-close-btn" type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
            Tutup
          </button>
          <div class="flex items-center gap-2">
            <a id="modal-view-btn" target="_blank" rel="noopener" style="display: none;" class="hidden inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Lihat Dokumen
            </a>
            <a id="modal-revisi-btn" style="display: none;" class="hidden inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z" /></svg>
              Perbaiki Pengajuan
            </a>
            <a id="modal-download-btn" style="display: none;" class="hidden inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
              Unduh Dokumen
            </a>
          </div>
        </div>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modal-overlay');
        if (!modal) return;

        const closeButtons = [
          document.getElementById('modal-close'),
          document.getElementById('modal-close-btn'),
        ];
        const viewButton = document.getElementById('modal-view-btn');
        const downloadButton = document.getElementById('modal-download-btn');
        const reviseButton = document.getElementById('modal-revisi-btn');
        const lampiranWrap = document.getElementById('modal-lampiran-wrap');
        const lampiranList = document.getElementById('modal-lampiran-list');

        const isTrue = (value) => value === true || value === 'true' || value === '1';
        const revisionStatuses = ['PERLU_REVISI', 'DITOLAK'];
        const publishedStatus = 'PUBLISHED';
        const statusMap = {
          diproses: { text: 'Diproses', className: 'text-blue-600' },
          published: { text: 'Published', className: 'text-slate-600' },
          ditolak: { text: 'Ditolak', className: 'text-slate-500' },
        };

        const parseJsonArray = (payload) => {
          if (!payload) return [];

          try {
            const parsed = JSON.parse(payload);
            return Array.isArray(parsed) ? parsed : [];
          } catch (error) {
            return [];
          }
        };

        const setText = (id, value) => {
          const element = document.getElementById(id);
          if (element) element.textContent = value || '-';
        };

        const hideActionButton = (button) => {
          if (!button) return;

          button.removeAttribute('href');
          button.classList.add('hidden');
          button.style.display = 'none';
        };

        const showActionButton = (button, url) => {
          if (!button || !url) return;

          button.href = url;
          button.classList.remove('hidden');
          button.style.display = 'inline-flex';
        };

        const resetActionButtons = () => {
          // Semua tombol aksi wajib di-reset setiap modal dibuka agar status PUBLISHED tidak mewarisi tombol revisi.
          [viewButton, downloadButton, reviseButton].forEach((button) => {
            hideActionButton(button);
          });
        };

        const renderLampiran = (payload) => {
          if (!lampiranWrap || !lampiranList) return;

          const lampiranItems = parseJsonArray(payload);
          lampiranList.innerHTML = '';

          if (lampiranItems.length === 0) {
            lampiranWrap.classList.add('hidden');
            return;
          }

          lampiranItems.forEach((lampiran) => {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-2 rounded-lg border border-slate-100 bg-white px-3 py-2';

            const name = document.createElement('span');
            name.className = 'min-w-0 flex-1 truncate text-[11px] font-medium text-slate-700';
            name.textContent = lampiran.name || 'Lampiran';
            item.appendChild(name);

            const actionWrap = document.createElement('span');
            actionWrap.className = 'inline-flex shrink-0 items-center gap-1';

            if (isTrue(lampiran.canPreview) && lampiran.previewUrl) {
              const previewLink = document.createElement('a');
              previewLink.href = lampiran.previewUrl;
              previewLink.target = '_blank';
              previewLink.rel = 'noopener';
              previewLink.className = 'rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200';
              previewLink.textContent = 'Lihat';
              actionWrap.appendChild(previewLink);
            }

            if (lampiran.downloadUrl) {
              const downloadLink = document.createElement('a');
              downloadLink.href = lampiran.downloadUrl;
              downloadLink.className = 'rounded-md bg-slate-50 px-2 py-0.5 text-[10px] font-semibold text-slate-600 hover:bg-slate-100 transition-all duration-200';
              downloadLink.textContent = 'Unduh';
              actionWrap.appendChild(downloadLink);
            }

            item.appendChild(actionWrap);
            lampiranList.appendChild(item);
          });

          lampiranWrap.classList.remove('hidden');
        };

        const closeModal = () => {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        };

        const openModal = (row) => {
          const statusCode = (row.dataset.statusCode || '').trim().toUpperCase();
          const status = statusMap[row.dataset.status] || { text: row.dataset.status || '-', className: 'text-slate-600' };
          const statusElement = document.getElementById('modal-status');
          const isRevisionStatus = revisionStatuses.includes(statusCode);

          resetActionButtons();

          setText('modal-jenis', row.dataset.jenis);
          setText('modal-perihal', row.dataset.perihal);
          setText('modal-tanggal', row.dataset.tanggal);
          setText('modal-nomor', row.dataset.nomor);
          setText('modal-keterangan', row.dataset.keterangan);

          if (statusElement) {
            statusElement.textContent = row.dataset.statusLabel || status.text;
            statusElement.className = `text-xs font-semibold ${status.className}`;
          }

          const catatanWrap = document.getElementById('modal-catatan-revisi-wrap');
          const catatanSource = document.getElementById('modal-catatan-revisi-source');
          const catatanText = document.getElementById('modal-catatan-revisi');
          if (isRevisionStatus && catatanWrap && catatanText) {
            catatanText.textContent = (row.dataset.catatanRevisi || '').trim() || 'Catatan revisi belum tersedia.';
            if (catatanSource) catatanSource.textContent = row.dataset.catatanRevisiSource || 'Catatan Revisi';
            catatanWrap.classList.remove('hidden');
          } else {
            catatanWrap?.classList.add('hidden');
            if (catatanSource) catatanSource.textContent = '';
            if (catatanText) catatanText.textContent = '';
          }

          renderLampiran(row.dataset.lampiran);

          // Enum status menjadi gerbang utama: jangan menampilkan tombol hanya karena URL tersedia.
          const data = {
            status_dokumen: statusCode,
            can_view: isTrue(row.dataset.canView),
            can_download: isTrue(row.dataset.canDownload),
            can_revise: isTrue(row.dataset.canRevise),
            view_url: row.dataset.viewUrl || '',
            download_url: row.dataset.downloadUrl || '',
            revision_url: row.dataset.revisionUrl || '',
          };
          const statusDokumen = String(data.status_dokumen || '').toUpperCase();

          if (statusDokumen === 'PUBLISHED' && data.can_download === true) {
            showActionButton(viewButton, data.can_view === true ? data.view_url : '');
            showActionButton(downloadButton, data.download_url);
            hideActionButton(reviseButton);
          } else if ((statusDokumen === 'PERLU_REVISI' || statusDokumen === 'DITOLAK') && data.can_revise === true) {
            showActionButton(reviseButton, data.revision_url);
            hideActionButton(viewButton);
            hideActionButton(downloadButton);
          }

          modal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        };

        closeButtons.forEach((button) => button?.addEventListener('click', closeModal));
        modal.addEventListener('click', (event) => {
          if (event.target === modal) closeModal();
        });

        document.getElementById('tbody-surat')?.addEventListener('click', (event) => {
          const detailButton = event.target.closest('.btn-detail');
          if (!detailButton) return;

          const row = detailButton.closest('.doc-row');
          if (row) openModal(row);
        });
      });
    </script>
@include('template.layouts.footer')
