@include('template.layouts.header', ['pageTitle' => 'Pengajuan Masuk'])
@include('template.sidebar.admin')

    <!-- View ini menerima $pengajuan dari AdminSuratMasukController dan menampilkan surat biasa yang baru masuk. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <!-- Topbar -->
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Pengajuan Masuk</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar pengajuan surat yang masuk</p>
        </div>
        <button type="button"
          class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </button>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-4">

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900">Pengajuan Surat Masuk</h2>
            <!-- Filter ini memakai data-filter-status pada baris tabel, sehingga tidak perlu request ulang ke controller. -->
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
              <button data-filter="semua" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
              <button data-filter="diajukan" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diajukan</button>
              <button data-filter="diproses" data-target="pengajuan" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Ringkasan</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Draft PDF</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Lampiran</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="tbody-pengajuan">
                  <!-- forelse membuat tabel tetap informatif jika belum ada pengajuan berstatus diajukan. -->
                  @forelse($pengajuan ?? [] as $item)
                  <!-- $draftFile tetap hanya DRAFT_PDF; lampiran pendukung tidak dipakai sebagai sumber preview. -->
                  @php
                    $draftFile = $item->dokumenFiles->firstWhere('file_type', 'DRAFT_PDF');
                    $lampiranFiles = $item->dokumenFiles->where('file_type', 'LAMPIRAN');
                  @endphp
                  <!-- Atribut data-* dipakai modal admin untuk menampilkan detail pengajuan tanpa membuka halaman baru. -->
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150 doc-row"
                    data-dokumen-id="{{ $item->dokumen_id }}"
                    data-filter-status="{{ strtolower($item->status_dokumen) }}"
                    data-jenis="Surat Biasa"
                    data-perihal="{{ $item->suratBiasa?->hal ?? '-' }}"
                    data-pemohon="{{ $item->pemohon?->nama ?? '-' }}"
                    data-tanggal="{{ optional($item->created_at)->format('d M Y') }}"
                    data-status="{{ $item->status_dokumen }}"
                    data-ringkasan="{{ $item->suratBiasa?->ringkasan_isi ?? '-' }}">
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">{{ $item->pemohon?->nama ?? '-' }}</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[180px]">{{ $item->suratBiasa?->hal ?? '-' }}</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-500 max-w-[220px]">{{ \Illuminate\Support\Str::limit($item->suratBiasa?->ringkasan_isi ?? '-', 80) }}</p></td>
                    <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ optional($item->created_at)->format('d M Y') }}</p></td>
                    <td class="px-5 py-3.5">
                      <!-- if mengecek apakah draft PDF tersedia sebelum menampilkan nama file. -->
                      @if($draftFile)
                        <p class="text-[11px] text-slate-600 max-w-[160px] truncate">{{ $draftFile->file_name }}</p>
                      @else
                        <p class="text-[11px] text-slate-400">Tidak ada file</p>
                      @endif
                    </td>
                    <td class="px-5 py-3.5">
                      @if($lampiranFiles->isNotEmpty())
                        <div class="flex max-w-[240px] flex-col gap-1.5">
                          @foreach($lampiranFiles as $lampiran)
                            <div class="rounded-lg bg-blue-50 px-2.5 py-1">
                              <p class="truncate text-[11px] font-medium text-blue-600">{{ \Illuminate\Support\Str::limit($lampiran->file_name, 34) }}</p>
                              <div class="mt-1 flex items-center gap-1">
                                @if ($lampiran->isPreviewableLampiran())
                                  <a href="{{ route('admin.lampiran.preview', $lampiran) }}" target="_blank" rel="noopener" class="text-[10px] font-semibold text-blue-600 hover:text-blue-800">Lihat</a>
                                @endif
                                <a href="{{ route('admin.lampiran.download', $lampiran) }}" class="text-[10px] font-semibold text-slate-600 hover:text-slate-800">Unduh</a>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      @else
                        <p class="text-[11px] text-slate-400">Tidak ada</p>
                      @endif
                    </td>
                    <td class="px-5 py-3.5">
                      <!-- if status dokumen menentukan badge tampilan antara Diajukan dan status lain. -->
                      @if(strtolower($item->status_dokumen) === 'diajukan')
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-blue-500"></span>Diajukan</span>
                      @else
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>{{ $item->status_dokumen }}</span>
                      @endif
                    </td>
                    <td class="px-5 py-3.5 flex items-center gap-2">
                      <!-- Tombol Detail membawa URL preview PDF ke modal jika file draft tersedia. -->
                      <button type="button"
                        class="btn-detail-pengajuan text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200"
                        data-preview-url="{{ $draftFile ? route('admin.pengajuan-masuk.preview-pdf', $item->dokumen_id) : '' }}"
                        data-proses-url="{{ route('admin.proses-surat', ['dokumen' => $item->dokumen_id]) }}">
                        Detail
                      </button>
                      <!-- Tombol Proses hanya muncul untuk dokumen yang masih diajukan agar Admin Surat memulai wizard proses surat. -->
                      @if(strtolower($item->status_dokumen) === 'diajukan')
                      <a href="{{ route('admin.proses-surat', ['dokumen' => $item->dokumen_id]) }}" class="inline-flex items-center text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses Surat</a>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-xs text-slate-400">Belum ada pengajuan surat biasa dengan status diajukan.</td>
                  </tr>
                  @endforelse

                </tbody>
              </table>
            </div>
          </div>

        </div>
      </main>
    </div>

    <!-- Modal detail pengajuan surat biasa ditempatkan lokal agar header global tetap bersih dari popup halaman ini. -->
    <div id="pengajuan-modal-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="relative w-full max-w-5xl rounded-2xl bg-white shadow-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
          <div>
            <h3 class="text-sm font-bold text-slate-900">Detail Pengajuan</h3>
            <p id="pengajuan-modal-jenis" class="text-[11px] text-slate-400 font-light mt-0.5"></p>
          </div>
          <button id="pengajuan-modal-close" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Perihal</p>
              <p id="pengajuan-modal-perihal" class="text-xs font-medium text-slate-800"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
              <p id="pengajuan-modal-pemohon" class="text-xs font-medium text-slate-800"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Diajukan</p>
              <p id="pengajuan-modal-tanggal" class="text-xs font-medium text-slate-800"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</p>
              <p id="pengajuan-modal-status" class="text-xs font-medium text-slate-800"></p>
            </div>
          </div>
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Ringkasan</p>
            <p id="pengajuan-modal-ringkasan" class="text-xs text-slate-600 font-light leading-relaxed"></p>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between gap-3">
          <button id="pengajuan-modal-close-btn" type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
            Tutup
          </button>
          <div class="flex items-center gap-2">
            <a id="pengajuan-modal-preview" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Cek PDF
            </a>
            <a id="pengajuan-modal-proses" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
              Proses Surat
            </a>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('pengajuan-modal-overlay');
        if (!modal) return;

        const previewButton = document.getElementById('pengajuan-modal-preview');
        const prosesButton = document.getElementById('pengajuan-modal-proses');
        const closeButtons = [
          document.getElementById('pengajuan-modal-close'),
          document.getElementById('pengajuan-modal-close-btn'),
        ];

        const setText = (id, value) => {
          const element = document.getElementById(id);
          if (element) element.textContent = value || '-';
        };

        const closeModal = () => {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        };

        const openModal = (row, button) => {
          // Data modal diambil dari atribut baris agar tidak perlu request ulang ke server.
          setText('pengajuan-modal-jenis', row.dataset.jenis);
          setText('pengajuan-modal-perihal', row.dataset.perihal);
          setText('pengajuan-modal-pemohon', row.dataset.pemohon);
          setText('pengajuan-modal-tanggal', row.dataset.tanggal);
          setText('pengajuan-modal-status', row.dataset.status);
          setText('pengajuan-modal-ringkasan', row.dataset.ringkasan);

          const previewUrl = button.dataset.previewUrl || '';
          if (previewUrl && previewButton) {
            previewButton.href = previewUrl;
            previewButton.classList.remove('opacity-50', 'cursor-not-allowed');
            previewButton.setAttribute('aria-disabled', 'false');
          } else if (previewButton) {
            previewButton.removeAttribute('href');
            previewButton.classList.add('opacity-50', 'cursor-not-allowed');
            previewButton.setAttribute('aria-disabled', 'true');
          }

          if (prosesButton) {
            prosesButton.href = button.dataset.prosesUrl || '#';
          }

          modal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        };

        closeButtons.forEach((button) => button?.addEventListener('click', closeModal));
        modal.addEventListener('click', (event) => {
          if (event.target === modal) closeModal();
        });

        previewButton?.addEventListener('click', (event) => {
          // Link preview dinonaktifkan jika dokumen belum punya draft PDF.
          if (!previewButton.getAttribute('href')) event.preventDefault();
        });

        document.getElementById('tbody-pengajuan')?.addEventListener('click', (event) => {
          const detailButton = event.target.closest('.btn-detail-pengajuan');
          if (!detailButton) return;

          const row = detailButton.closest('.doc-row');
          if (row) openModal(row, detailButton);
        });
      });
    </script>

@include('template.layouts.footer')
