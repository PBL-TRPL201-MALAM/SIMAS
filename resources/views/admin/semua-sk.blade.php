@include('template.layouts.header', ['pageTitle' => 'Semua SK'])
@include('template.sidebar.admin')

    {{-- Halaman ini menampilkan seluruh dokumen SURAT_KEPUTUSAN dari database, bukan data contoh. --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Semua SK</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar seluruh surat keputusan</p>
        </div>
        <a href="{{ route('admin.profil') }}"
          class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-4">
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

          @if ($errors->any())
            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3">
              <p class="text-xs font-semibold text-red-700">Proses SK belum bisa disimpan:</p>
              <ul class="mt-1 space-y-1 text-[11px] text-red-600 font-light">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Semua Surat Keputusan</h2>
            <span class="text-[11px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">{{ $skList->count() }} dokumen</span>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Nomor SK</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal Pengajuan</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbody-semua-sk" class="divide-y divide-slate-50">
                  @forelse($skList as $dokumen)
                    @php
                      $sk = $dokumen->suratKeputusan;
                      $status = $dokumen->status_dokumen;
                      $statusLabel = ucwords(strtolower(str_replace('_', ' ', $status)));
                      $menimbangText = $sk?->skMenimbang?->sortBy('urutan')->pluck('isi_menimbang')->implode("\n") ?: '-';
                      $mengingatText = $sk?->dasarHukum?->map(function ($dasarHukum) {
                          return $dasarHukum->labelMengingat();
                      })->implode("\n") ?: '-';
                      $memutuskanText = $sk?->skMemutuskan?->sortBy('urutan')->pluck('isi_memutuskan')->implode("\n") ?: '-';
                      $finalPdf = $dokumen->dokumenFiles->firstWhere('file_type', 'FINAL_PDF');
                      $canOpenProcess = in_array($status, ['DIAJUKAN', 'DIPROSES'], true);
                      $badgeClasses = match ($status) {
                          'DIAJUKAN' => 'text-blue-600 bg-blue-50',
                          'DIPROSES' => 'text-amber-600 bg-amber-50',
                          'MENUNGGU_VERIFIKASI' => 'text-violet-600 bg-violet-50',
                          'SIAP_PUBLISH' => 'text-emerald-600 bg-emerald-50',
                          'PUBLISHED' => 'text-slate-600 bg-slate-100',
                          'PERLU_REVISI', 'DITOLAK' => 'text-red-600 bg-red-50',
                          default => 'text-slate-600 bg-slate-100',
                      };
                    @endphp
                    <tr class="doc-row hover:bg-slate-50/40 transition-colors duration-150"
                      data-jenis="SK"
                      data-perihal="{{ $sk?->judul_sk ?? '-' }}"
                      data-pemohon="{{ $dokumen->pemohon?->nama ?? '-' }}"
                      data-nomor="{{ $sk?->nomor_sk ?? '-' }}"
                      data-tanggal-sk="{{ optional($sk?->tanggal_sk)->format('d M Y') ?? '-' }}"
                      data-tanggal-pengajuan="{{ optional($dokumen->created_at)->format('d M Y H:i') ?? '-' }}"
                      data-status="{{ $statusLabel }}"
                      data-status-code="{{ $status }}"
                      data-tempat="{{ $sk?->tempat_penetapan ?? '-' }}"
                      data-sk-tentang="{{ $sk?->tentang ?? '-' }}"
                      data-sk-menimbang="{{ $menimbangText }}"
                      data-sk-mengingat="{{ $mengingatText }}"
                      data-sk-memutuskan="{{ $memutuskanText }}"
                      data-catatan-admin="{{ $sk?->catatan_admin ?? '' }}"
                      data-proses-url="{{ $canOpenProcess ? route('admin.proses-sk', ['dokumen' => $dokumen->dokumen_id]) : '' }}">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">{{ $dokumen->pemohon?->nama ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[220px]">{{ $sk?->judul_sk ?? '-' }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ $sk?->nomor_sk ?? '-' }}</p></td>
                      <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeClasses }}">
                          <span class="w-1 h-1 rounded-full bg-current opacity-70"></span>{{ $statusLabel }}
                        </span>
                      </td>
                      <td class="px-5 py-3.5"><p class="text-[11px] text-slate-400 font-light">{{ optional($dokumen->created_at)->format('d M Y H:i') }}</p></td>
                      <td class="px-5 py-3.5">
                        <div class="flex flex-wrap items-center gap-2">
                          <button type="button" class="btn-detail-sk text-[11px] font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">Detail</button>
                          {{-- Aksi tabel mengikuti status dokumen agar SK siap publish/published tidak kembali ke proses review. --}}
                          @if($canOpenProcess)
                            <a href="{{ route('admin.proses-sk', ['dokumen' => $dokumen->dokumen_id]) }}" class="inline-flex items-center text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Lihat Proses</a>
                          @elseif($status === 'MENUNGGU_VERIFIKASI')
                            {{-- Dokumen sedang di verifikator/penandatangan, Admin hanya bisa melihat detail. --}}
                            <span class="inline-flex items-center text-[11px] font-medium text-violet-600 bg-violet-50 px-2.5 py-1 rounded-lg">Menunggu Verifikasi</span>
                          @elseif($status === 'SIAP_PUBLISH')
                            {{-- Publish SK membuat PDF final dari template Blade dan menyimpan record FINAL_PDF. --}}
                            <form action="{{ route('admin.sk.publish', $dokumen) }}" method="POST" class="inline-flex">
                              @csrf
                              <button type="submit" class="inline-flex items-center text-[11px] font-semibold text-white bg-emerald-600 hover:bg-emerald-700 px-2.5 py-1 rounded-lg transition-all duration-200">Publish</button>
                            </form>
                          @elseif($status === 'PUBLISHED' && $finalPdf)
                            <a href="{{ route('admin.semua-sk.preview-final', $dokumen) }}" target="_blank" rel="noopener" class="inline-flex items-center text-[11px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition-all duration-200">Lihat Dokumen</a>
                            <a href="{{ route('admin.semua-sk.download-final', $dokumen) }}" class="inline-flex items-center text-[11px] font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg transition-all duration-200">Unduh Dokumen</a>
                          @elseif(in_array($status, ['PERLU_REVISI', 'DITOLAK'], true))
                            <span class="inline-flex items-center text-[11px] font-medium text-red-600 bg-red-50 px-2.5 py-1 rounded-lg">Menunggu Revisi Pemohon</span>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-600">Belum ada data SK.</p>
                        <p class="mt-1 text-[11px] font-light text-slate-400">Data pengajuan SK akan muncul setelah Pemohon mengirim pengajuan.</p>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </main>
    </div>

    {{-- Modal detail SK ditempatkan di halaman Semua SK agar popup tidak lagi bergantung pada header global. --}}
    <div id="semua-sk-modal-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="relative w-full max-w-5xl rounded-2xl bg-white shadow-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
          <div>
            <h3 class="text-sm font-bold text-slate-900">Detail SK</h3>
            <p id="semua-sk-modal-jenis" class="text-[11px] text-slate-400 font-light mt-0.5"></p>
          </div>
          <button id="semua-sk-modal-close" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <div class="max-h-[78vh] overflow-y-auto px-6 py-5 space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Judul SK</p>
              <p id="semua-sk-modal-judul" class="text-xs font-medium text-slate-800"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pemohon</p>
              <p id="semua-sk-modal-pemohon" class="text-xs font-medium text-slate-800"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</p>
              <p id="semua-sk-modal-status" class="text-xs font-semibold text-slate-700"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Nomor SK</p>
              <p id="semua-sk-modal-nomor" class="text-xs font-medium text-slate-800"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal SK</p>
              <p id="semua-sk-modal-tanggal-sk" class="text-xs font-medium text-slate-800"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Pengajuan</p>
              <p id="semua-sk-modal-tanggal-pengajuan" class="text-xs font-medium text-slate-800"></p>
            </div>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tempat Penetapan</p>
              <p id="semua-sk-modal-tempat" class="text-xs text-slate-600 font-light leading-relaxed"></p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tentang</p>
              <p id="semua-sk-modal-tentang" class="text-xs text-slate-600 font-light leading-relaxed"></p>
            </div>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Menimbang</p>
              <div id="semua-sk-modal-menimbang" class="space-y-1.5"></div>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Mengingat</p>
              <div id="semua-sk-modal-mengingat" class="space-y-1.5"></div>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
              <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Memutuskan</p>
              <div id="semua-sk-modal-memutuskan" class="space-y-1.5"></div>
            </div>
          </div>
          <div id="semua-sk-modal-catatan-wrap" class="hidden rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
            <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider mb-1">Catatan Admin</p>
            <p id="semua-sk-modal-catatan" class="text-xs text-amber-700 font-light leading-relaxed whitespace-pre-line"></p>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between gap-3">
          <button id="semua-sk-modal-close-btn" type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
            Tutup
          </button>
          <a id="semua-sk-modal-proses" class="hidden items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            Lihat Proses
          </a>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('semua-sk-modal-overlay');
        if (!modal) return;

        const prosesButton = document.getElementById('semua-sk-modal-proses');
        const closeButtons = [
          document.getElementById('semua-sk-modal-close'),
          document.getElementById('semua-sk-modal-close-btn'),
        ];

        const setText = (id, value) => {
          const element = document.getElementById(id);
          if (element) element.textContent = value || '-';
        };

        const renderTextList = (id, text) => {
          const container = document.getElementById(id);
          if (!container) return;

          const items = String(text || '-')
            .split(/\r?\n/)
            .map((item) => item.trim())
            .filter(Boolean);

          container.innerHTML = '';

          if (items.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'text-xs text-slate-500 font-light leading-relaxed';
            empty.textContent = '-';
            container.appendChild(empty);
            return;
          }

          // Setiap baris SK dirender sebagai paragraf kecil agar isi panjang tetap mudah dibaca.
          items.forEach((item) => {
            const paragraph = document.createElement('p');
            paragraph.className = 'text-xs text-slate-600 font-light leading-relaxed';
            paragraph.textContent = item;
            container.appendChild(paragraph);
          });
        };

        const closeModal = () => {
          modal.classList.add('hidden');
          document.body.style.overflow = '';
        };

        const openModal = (row) => {
          // Detail SK memakai dataset dari baris tabel, jadi tombol tidak membuat query tambahan.
          setText('semua-sk-modal-jenis', row.dataset.jenis);
          setText('semua-sk-modal-judul', row.dataset.perihal);
          setText('semua-sk-modal-pemohon', row.dataset.pemohon);
          setText('semua-sk-modal-status', row.dataset.status);
          setText('semua-sk-modal-nomor', row.dataset.nomor);
          setText('semua-sk-modal-tanggal-sk', row.dataset.tanggalSk);
          setText('semua-sk-modal-tanggal-pengajuan', row.dataset.tanggalPengajuan);
          setText('semua-sk-modal-tempat', row.dataset.tempat);
          setText('semua-sk-modal-tentang', row.dataset.skTentang);
          renderTextList('semua-sk-modal-menimbang', row.dataset.skMenimbang);
          renderTextList('semua-sk-modal-mengingat', row.dataset.skMengingat);
          renderTextList('semua-sk-modal-memutuskan', row.dataset.skMemutuskan);

          const catatan = (row.dataset.catatanAdmin || '').trim();
          const catatanWrap = document.getElementById('semua-sk-modal-catatan-wrap');
          const catatanText = document.getElementById('semua-sk-modal-catatan');
          if (catatan && catatanWrap && catatanText) {
            catatanText.textContent = catatan;
            catatanWrap.classList.remove('hidden');
          } else {
            catatanWrap?.classList.add('hidden');
            if (catatanText) catatanText.textContent = '';
          }

          if (prosesButton) {
            // Tombol proses di modal hanya ditampilkan untuk status yang masih perlu review/verifikasi.
            if (row.dataset.prosesUrl) {
              prosesButton.href = row.dataset.prosesUrl;
              prosesButton.classList.remove('hidden');
              prosesButton.classList.add('inline-flex');
            } else {
              prosesButton.removeAttribute('href');
              prosesButton.classList.remove('inline-flex');
              prosesButton.classList.add('hidden');
            }
          }

          modal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        };

        closeButtons.forEach((button) => button?.addEventListener('click', closeModal));
        modal.addEventListener('click', (event) => {
          if (event.target === modal) closeModal();
        });

        document.getElementById('tbody-semua-sk')?.addEventListener('click', (event) => {
          const detailButton = event.target.closest('.btn-detail-sk');
          if (!detailButton) return;

          const row = detailButton.closest('.doc-row');
          if (row) openModal(row);
        });
      });
    </script>

@include('template.layouts.footer')
