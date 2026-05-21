@include('template.layouts.header', ['pageTitle' => 'Semua Surat'])
@include('admin.partials.detail-modal')
@include('template.sidebar.admin')

    <!-- View ini menerima $suratList dari AdminSemuaSuratControllerindex, berisi semua dokumen jenis SURAT_BIASA. -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Semua Surat</h1>
          <p class="text-[11px] text-slate-400 font-light">Daftar seluruh surat biasa</p>
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
          <!-- Flash message ini menampilkan hasil publish atau error saat pembuatan PDF final QR gagal. -->
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

          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Semua Surat Biasa</h2>
            <!-- Filter status memakai data-filter-status pada baris tabel dan diproses oleh JavaScript frontend. -->
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1">
              <button data-filter="semua" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white bg-blue-600 transition-all duration-200">Semua</button>
              <button data-filter="diajukan" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diajukan</button>
              <button data-filter="diproses" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Diproses</button>
              <button data-filter="verifikasi" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Verifikasi</button>
              <button data-filter="revisi" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Revisi</button>
              <button data-filter="published" data-target="semua-surat" class="filter-btn rounded-lg px-3 py-1.5 text-[11px] font-medium text-slate-500 hover:bg-slate-50 transition-all duration-200">Published</button>
            </div>
          </div>

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Pemohon</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Perihal</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Nomor Surat</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tanggal Pengajuan</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbody-pengajuan" class="divide-y divide-slate-50">
                  <!-- forelse menampilkan seluruh surat dan tetap memberi pesan ketika belum ada data. -->
                  @forelse ($suratList as $dokumen)
                    <!-- Blok php menyiapkan label status, filter, file preview, dan catatan revisi dari relasi dokumen. -->
                    @php
                      $status = $dokumen->status_dokumen;
                      $statusLabel = ucwords(strtolower(str_replace('_', ' ', $status)));
                      $filterStatus = match ($status) {
                          'DIAJUKAN' => 'diajukan',
                          'DIPROSES' => 'diproses',
                          'MENUNGGU_VERIFIKASI' => 'verifikasi',
                          'DITOLAK', 'PERLU_REVISI' => 'revisi',
                          'SIAP_PUBLISH', 'PUBLISHED' => 'published',
                          default => 'semua',
                      };
                      $hasPreviewablePdf = $dokumen->dokumenFiles->contains(
                          fn ($file) => in_array($file->file_type, ['FINAL_PDF', 'PREVIEW_VERIFIKASI_PDF', 'DRAFT_PDF'], true)
                      );
                      $finalPdf = $dokumen->dokumenFiles->firstWhere('file_type', 'FINAL_PDF');
                      $latestRejectedVerification = $dokumen->verifikasi
                          ->first(fn ($verifikasi) => $verifikasi->status_verifikasi === 'DITOLAK' && filled($verifikasi->catatan));
                      $latestRevisionHistory = $dokumen->riwayatDokumen
                          ->first(fn ($riwayat) => in_array($riwayat->status_baru, ['PERLU_REVISI', 'DITOLAK'], true) && filled($riwayat->catatan));
                      $revisionNoteSource = '';
                      $revisionNoteText = '';
                      $revisionNoteActor = '';
                      if (in_array($status, ['PERLU_REVISI', 'DITOLAK'], true)) {
                          if (filled($dokumen->suratBiasa?->catatan_admin)) {
                              $revisionNoteSource = 'Catatan Admin Surat';
                              $revisionNoteText = $dokumen->suratBiasa->catatan_admin;
                          } elseif ($latestRejectedVerification) {
                              $revisionNoteSource = 'Catatan Verifikator';
                              $revisionNoteText = $latestRejectedVerification->catatan;
                              $revisionNoteActor = $latestRejectedVerification->verifikator?->nama ?? 'Verifikator';
                          } elseif ($latestRevisionHistory) {
                              $revisionNoteSource = str_contains($latestRevisionHistory->aksi ?? '', 'VERIFIKATOR')
                                  ? 'Catatan Verifikator'
                                  : 'Catatan Admin Surat';
                              $revisionNoteText = $latestRevisionHistory->catatan;
                              $revisionNoteActor = $latestRevisionHistory->actor?->nama ?? '';
                          }
                      }
                      $lampiranFiles = $dokumen->dokumenFiles->where('file_type', 'LAMPIRAN');
                      $positionElements = $dokumen->posisiElemenDokumen->pluck('elemen')->unique();
                      $positionsReady = collect(['nomor_surat', 'tanggal_surat', 'tte'])
                          ->every(fn ($elemen) => $positionElements->contains($elemen));
                      $processStep = $status === 'DIPROSES' && $positionsReady ? 3 : 2;
                      $previewUrl = $hasPreviewablePdf ? route('admin.semua-surat.preview-final', $dokumen) : '';
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
                        data-dokumen-id="{{ $dokumen->dokumen_id }}"
                        data-filter-status="{{ $filterStatus }}"
                        data-jenis="Surat Biasa"
                        data-perihal="{{ $dokumen->suratBiasa?->hal ?? '-' }}"
                        data-pemohon="{{ $dokumen->pemohon?->nama ?? '-' }}"
                        data-tanggal="{{ optional($dokumen->created_at)->format('d M Y H:i') ?? '-' }}"
                        data-status="{{ $statusLabel }}"
                        data-status-code="{{ $status }}"
                        data-ringkasan="{{ $dokumen->suratBiasa?->ringkasan_isi ?? '-' }}"
                        data-revision-note-source="{{ $revisionNoteSource }}"
                        data-revision-note="{{ $revisionNoteText }}"
                        data-allow-process="{{ in_array($status, ['PERLU_REVISI', 'DITOLAK'], true) ? 'false' : 'true' }}">
                      <td class="px-5 py-3.5">
                        <p class="text-xs font-medium text-slate-800">{{ $dokumen->pemohon?->nama ?? '-' }}</p>
                      </td>
                      <td class="px-5 py-3.5">
                        <p class="text-xs text-slate-600 max-w-[220px]">{{ $dokumen->suratBiasa?->hal ?? '-' }}</p>
                      </td>
                      <td class="px-5 py-3.5">
                        <p class="text-[11px] text-slate-500 font-light">{{ $dokumen->suratBiasa?->nomor_surat ?? '-' }}</p>
                      </td>
                      <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeClasses }}">
                          <span class="w-1 h-1 rounded-full bg-current opacity-70"></span>{{ $statusLabel }}
                        </span>
                      </td>
                      <td class="px-5 py-3.5">
                        <p class="text-[11px] text-slate-500 font-light">{{ optional($dokumen->created_at)->format('d M Y H:i') ?? '-' }}</p>
                      </td>
                      <td class="px-5 py-3.5">
                        <div class="flex flex-wrap items-center gap-2">
                          <!-- if status dokumen menentukan tombol aksi yang boleh muncul pada fase alur surat. -->
                          @if ($status === 'DIAJUKAN')
                            <a href="{{ route('admin.proses-surat', ['dokumen' => $dokumen->dokumen_id, 'step' => 1]) }}" class="inline-flex items-center text-[11px] font-semibold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg transition-all duration-200">Proses</a>
                          @elseif ($status === 'DIPROSES')
                            <a href="{{ route('admin.proses-surat', ['dokumen' => $dokumen->dokumen_id, 'step' => $processStep]) }}" class="inline-flex items-center text-[11px] font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg transition-all duration-200">Lihat Proses</a>
                          @elseif ($status === 'MENUNGGU_VERIFIKASI')
                            <a href="{{ route('admin.proses-surat', ['dokumen' => $dokumen->dokumen_id, 'step' => 3]) }}" class="inline-flex items-center text-[11px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition-all duration-200">Lihat Proses</a>
                          @elseif ($status === 'SIAP_PUBLISH')
                            <!-- Tombol Publish hanya muncul saat status SIAP_PUBLISH setelah semua level verifikasi menyetujui. -->
                            <form action="{{ route('admin.surat.publish', $dokumen) }}" method="POST" class="inline-flex">
                              <!-- csrf wajib karena publish mengubah status dokumen menjadi PUBLISHED. -->
                              @csrf
                              <button type="submit" class="inline-flex items-center text-[11px] font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition-all duration-200">Publish</button>
                            </form>
                          @elseif ($status === 'PUBLISHED')
                            @if ($finalPdf)
                              <!-- Tombol Lihat membuka PDF inline, sedangkan Unduh Final memaksa download file final. -->
                              <a href="{{ route('admin.semua-surat.preview-final', $dokumen) }}" target="_blank" class="inline-flex items-center text-[11px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition-all duration-200">Lihat Final</a>
                              <a href="{{ route('admin.semua-surat.download-final', $dokumen) }}" class="inline-flex items-center text-[11px] font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg transition-all duration-200">Unduh Final</a>
                            @else
                              <span class="inline-flex items-center text-[11px] font-medium text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">File final belum ada</span>
                            @endif
                          @elseif (in_array($status, ['PERLU_REVISI', 'DITOLAK'], true))
                            <button type="button"
                              class="btn-detail inline-flex items-center text-[11px] font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg transition-all duration-200"
                              data-preview-url="{{ $previewUrl }}">
                              Lihat Detail
                            </button>
                          @else
                            <button type="button"
                              class="btn-detail inline-flex items-center text-[11px] font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg transition-all duration-200"
                              data-preview-url="{{ $previewUrl }}">
                              Lihat Detail
                            </button>
                          @endif
                        </div>

                        @if ($lampiranFiles->isNotEmpty())
                          <div class="mt-2 flex max-w-[340px] flex-wrap gap-1.5">
                            @foreach ($lampiranFiles as $lampiran)
                              <!-- Lampiran hanya ditampilkan sebagai file pendukung dan tidak mempengaruhi preview/final PDF. -->
                              <div class="inline-flex max-w-[240px] items-center gap-1.5 rounded-lg bg-blue-50 px-2.5 py-1 text-[10px] font-medium text-blue-600">
                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 10-5.657-5.657l-6.586 6.586a6 6 0 108.485 8.485L20.5 13" /></svg>
                                <span class="min-w-0 flex-1 truncate">{{ $lampiran->file_name }}</span>
                                @if ($lampiran->isPreviewableLampiran())
                                  <a href="{{ route('admin.lampiran.preview', $lampiran) }}" target="_blank" rel="noopener" class="ml-auto shrink-0 font-semibold text-blue-600 hover:text-blue-800">Lihat</a>
                                @endif
                                <a href="{{ route('admin.lampiran.download', $lampiran) }}" class="shrink-0 font-semibold text-slate-600 hover:text-slate-800">Unduh</a>
                              </div>
                            @endforeach
                          </div>
                        @endif

                        <!-- Catatan revisi ditampilkan jika dokumen menunggu perbaikan dari pemohon. -->
                        @if (in_array($status, ['PERLU_REVISI', 'DITOLAK'], true) && filled($revisionNoteText))
                          <div class="mt-2 rounded-xl border border-red-100 bg-red-50/70 px-3 py-2.5 max-w-[320px]">
                            <div class="flex items-center gap-1.5">
                              <span class="inline-flex h-1.5 w-1.5 rounded-full bg-red-500"></span>
                              <p class="text-[10px] font-semibold uppercase tracking-wider text-red-500">
                                Catatan Revisi
                              </p>
                            </div>

                            <p class="mt-1.5 text-[11px] font-light leading-relaxed text-red-700">
                              {{ $revisionNoteText }}
                            </p>

                            <p class="mt-1.5 text-[10px] font-light text-red-400">
                              {{ $revisionNoteSource }}{{ $revisionNoteActor ? ' - ' . $revisionNoteActor : '' }}
                            </p>
                          </div>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-600">Belum ada data surat biasa.</p>
                        <p class="mt-1 text-[11px] font-light text-slate-400">Data pengajuan akan muncul di sini setelah pemohon mengirim surat.</p>
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

@include('template.layouts.footer')
