/**
 * dashboard-admin.js
 * Lokasi: resources/js/pages/dashboard-admin.js
 *
 * Fitur:
 * - Navigasi sidebar
 * - Modal detail pengajuan
 * - Toast notification
 * - Filter tabel pengajuan
 * - Multi-step wizard proses surat (3 step)
 * - Master Dasar Hukum (tambah & hapus)
 * - Tombol "Proses" dari tabel langsung ke wizard
 */

document.addEventListener('DOMContentLoaded', () => {

  // ================================================================
  // KONFIGURASI HALAMAN
  // ================================================================
  const PAGE_CONFIG = {
    'dashboard'          : { title: 'Dashboard',              subtitle: 'Selamat datang, Admin/TU' },
    'pengajuan-masuk'    : { title: 'Pengajuan Masuk',        subtitle: 'Daftar surat yang menunggu diproses' },
    'proses-surat'       : { title: 'Proses Surat',           subtitle: 'Upload PDF & lengkapi data — Langkah 1 dari 3' },
    'semua-surat'        : { title: 'Semua Surat Biasa',      subtitle: 'Seluruh surat biasa dalam sistem' },
    'pengajuan-sk'       : { title: 'Pengajuan SK Masuk',     subtitle: 'Daftar SK yang menunggu review' },
    'proses-sk'          : { title: 'Proses SK',              subtitle: 'Review isi SK — Langkah 1 dari 3' },
    'semua-sk'           : { title: 'Semua Surat Keputusan',  subtitle: 'Seluruh SK dalam sistem' },
    'master-dasar-hukum' : { title: 'Master Dasar Hukum',    subtitle: 'Kelola referensi dasar hukum untuk SK' },
    'profil'             : { title: 'Profil Saya',            subtitle: 'Kelola data akun kamu' },
  };

  const navLinks      = document.querySelectorAll('.nav-link');
  const quickNavs     = document.querySelectorAll('.quick-nav');
  const pages         = document.querySelectorAll('.page-content');
  const pageTitle     = document.getElementById('page-title');
  const pageSub       = document.getElementById('page-subtitle');
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebar       = document.getElementById('sidebar');


  // ================================================================
  // TOAST
  // ================================================================
  const showToast = (msg, type = 'success') => {
    const toast    = document.getElementById('toast');
    const toastMsg = document.getElementById('toast-msg');
    const toastIcon = document.getElementById('toast-icon');
    if (!toast) return;

    const icons = {
      success: `<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`,
      error:   `<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>`,
      info:    `<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
    };

    toastIcon.innerHTML = icons[type] || icons.info;
    toastMsg.textContent = msg;
    toast.classList.remove('hidden');
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(8px)';
    requestAnimationFrame(() => {
      toast.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    });
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(8px)';
      setTimeout(() => toast.classList.add('hidden'), 200);
    }, 3000);
  };


  // ================================================================
  // SWITCH HALAMAN
  // ================================================================
  const switchPage = (pageKey) => {
    if (!PAGE_CONFIG[pageKey]) return;

    pages.forEach(p => p.classList.add('hidden'));
    const target = document.getElementById(`page-${pageKey}`);
    if (target) target.classList.remove('hidden');

    if (pageTitle) pageTitle.textContent = PAGE_CONFIG[pageKey].title;
    if (pageSub)   pageSub.textContent   = PAGE_CONFIG[pageKey].subtitle;

    navLinks.forEach(link => {
      const isActive = link.dataset.page === pageKey;
      if (isActive) {
        link.classList.add('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-200');
        link.classList.remove('text-slate-600', 'hover:bg-blue-50', 'hover:text-blue-600');
      } else {
        link.classList.remove('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-200');
        if (!link.classList.contains('hover:bg-red-50')) {
          link.classList.add('text-slate-600', 'hover:bg-blue-50', 'hover:text-blue-600');
        }
      }
    });

    if (pageKey === 'proses-surat') resetProsesWizard();
    sidebar?.classList.add('-translate-x-full');
    window.scrollTo({ top: 0 });
  };

  navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const page = link.dataset.page;
      if (page) switchPage(page);
    });
  });

  quickNavs.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const page = link.dataset.page;
      if (page) switchPage(page);
    });
  });


  // ================================================================
  // MOBILE SIDEBAR
  // ================================================================
  if (sidebarToggle && sidebar) {
    sidebar.classList.add('lg:translate-x-0');
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('-translate-x-full'));
    document.addEventListener('click', (e) => {
      const isOutside = !sidebar.contains(e.target) && !sidebarToggle.contains(e.target);
      if (isOutside && window.innerWidth < 1024) sidebar.classList.add('-translate-x-full');
    });
  }


  // ================================================================
  // MODAL DETAIL
  // ================================================================
  const modalOverlay  = document.getElementById('modal-overlay');
  const modalClose    = document.getElementById('modal-close');
  const modalCloseBtn = document.getElementById('modal-close-btn');
  const modalProsesBtn = document.getElementById('modal-proses-btn');

  let activeRowData = null;

  const openModal = (data) => {
    activeRowData = data;
    if (!modalOverlay) return;
    document.getElementById('modal-jenis').textContent    = data.jenis || '—';
    document.getElementById('modal-perihal').textContent  = data.perihal || '—';
    document.getElementById('modal-pemohon').textContent  = data.pemohon || '—';
    document.getElementById('modal-tanggal').textContent  = data.tanggal || '—';
    document.getElementById('modal-status').textContent   = data.status || '—';
    document.getElementById('modal-ringkasan').textContent = data.ringkasan || '—';
    modalOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  };

  const closeModal = () => {
    modalOverlay?.classList.add('hidden');
    document.body.style.overflow = '';
  };

  modalClose?.addEventListener('click', closeModal);
  modalCloseBtn?.addEventListener('click', closeModal);
  modalOverlay?.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); });

  // Tombol "Proses Surat" di modal → langsung ke wizard
  modalProsesBtn?.addEventListener('click', () => {
    closeModal();
    goToProsesWizard(activeRowData);
  });

  // Unduh DOCX simulasi
  document.getElementById('modal-download-docx')?.addEventListener('click', () => {
    showToast('File DOCX sedang diunduh...', 'info');
  });

  // Delegasi: tombol Detail di tabel
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;
    const row = btn.closest('tr');
    if (!row) return;
    openModal({
      jenis     : row.dataset.jenis,
      perihal   : row.dataset.perihal,
      pemohon   : row.dataset.pemohon,
      tanggal   : row.dataset.tanggal,
      status    : row.dataset.status,
      ringkasan : row.dataset.ringkasan,
    });
  });

  // Delegasi: tombol Proses di tabel → langsung ke wizard
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-proses');
    if (!btn) return;
    const row = btn.closest('tr');
    if (!row) return;
    goToProsesWizard({
      perihal: row.dataset.perihal,
      pemohon: row.dataset.pemohon,
    });
  });

  // Delegasi: tombol Review & Proses SK
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-proses-sk');
    if (!btn) return;
    const row = btn.closest('tr');
    if (!row) return;
    goToProsesSkWizard({
      perihal : row.dataset.perihal,
      pemohon : row.dataset.pemohon,
      judul   : row.dataset.perihal,
    });
  });


  // ================================================================
  // GO TO PROSES WIZARD dengan data surat
  // ================================================================
  const goToProsesWizard = (data) => {
    const perihalInfo = document.getElementById('proses-perihal-info');
    if (perihalInfo) perihalInfo.textContent = `${data.perihal || '—'} — ${data.pemohon || '—'}`;
    switchPage('proses-surat');
  };

  // Tombol kembali ke daftar pengajuan dari wizard
  document.getElementById('proses-back-to-list')?.addEventListener('click', () => {
    switchPage('pengajuan-masuk');
  });


  // ================================================================
  // FILTER TABEL
  // ================================================================
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.filter-btn');
    if (!btn) return;

    const filter = btn.dataset.filter;
    const container = btn.closest('div');

    container.querySelectorAll('.filter-btn').forEach(b => {
      b.classList.remove('bg-blue-600', 'text-white', 'font-semibold');
      b.classList.add('text-slate-500', 'font-medium');
    });
    btn.classList.add('bg-blue-600', 'text-white', 'font-semibold');
    btn.classList.remove('text-slate-500', 'font-medium');

    // Filter baris berdasarkan data-filter-status
    const tbody = document.getElementById('tbody-pengajuan');
    if (!tbody) return;
    tbody.querySelectorAll('.doc-row').forEach(row => {
      const status = row.dataset.filterStatus || row.dataset.status?.toLowerCase();
      const show = filter === 'semua' || status === filter;
      row.classList.toggle('hidden', !show);
    });
  });


  // ================================================================
  // WIZARD: PROSES SURAT (3 step)
  // ================================================================
  const prosesStep1 = document.getElementById('proses-step-1');
  const prosesStep2 = document.getElementById('proses-step-2');
  const prosesStep3 = document.getElementById('proses-step-3');
  const prosesNext1 = document.getElementById('proses-next-1');
  const prosesNext2 = document.getElementById('proses-next-2');
  const prosesBack1 = document.getElementById('proses-back-1');
  const prosesBack2 = document.getElementById('proses-back-2');
  const prosesSubmit = document.getElementById('proses-submit');

  const prosesCircle1 = document.getElementById('proses-circle-1');
  const prosesCircle2 = document.getElementById('proses-circle-2');
  const prosesCircle3 = document.getElementById('proses-circle-3');
  const prosesLabel1  = document.getElementById('proses-label-1');
  const prosesLabel2  = document.getElementById('proses-label-2');
  const prosesLabel3  = document.getElementById('proses-label-3');

  const setProsesStep = (step) => {
    [[prosesCircle1, prosesLabel1], [prosesCircle2, prosesLabel2], [prosesCircle3, prosesLabel3]].forEach(([c, l], i) => {
      if (!c || !l) return;
      if (i < step) {
        c.className = 'w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0';
        c.querySelector('span').className = 'text-[11px] font-bold text-white';
        l.className = 'text-xs font-semibold text-blue-600';
      } else {
        c.className = 'w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0';
        c.querySelector('span').className = 'text-[11px] font-bold text-slate-400';
        l.className = 'text-xs font-medium text-slate-400';
      }
    });
  };

  const resetProsesWizard = () => {
    prosesStep1?.classList.remove('hidden');
    prosesStep2?.classList.add('hidden');
    prosesStep3?.classList.add('hidden');
    setProsesStep(1);
    if (pageSub) pageSub.textContent = 'Upload PDF & lengkapi data — Langkah 1 dari 3';
    // Reset file input
    const pdfInput = document.getElementById('pdf-file-input');
    if (pdfInput) pdfInput.value = '';
    document.getElementById('pdf-file-preview')?.classList.add('hidden');
  };

  // Step 1 → Step 2
  prosesNext1?.addEventListener('click', () => {
    setProsesStep(2);
    if (pageSub) pageSub.textContent = 'Atur posisi elemen — Langkah 2 dari 3';
    prosesStep1?.classList.add('hidden');
    prosesStep2?.classList.remove('hidden');
  });

  // Step 2 → Step 1
  prosesBack1?.addEventListener('click', () => {
    setProsesStep(1);
    if (pageSub) pageSub.textContent = 'Upload PDF & lengkapi data — Langkah 1 dari 3';
    prosesStep2?.classList.add('hidden');
    prosesStep1?.classList.remove('hidden');
  });

  // Step 2 → Step 3
  prosesNext2?.addEventListener('click', () => {
    setProsesStep(3);
    if (pageSub) pageSub.textContent = 'Tentukan tingkat verifikasi — Langkah 3 dari 3';
    prosesStep2?.classList.add('hidden');
    prosesStep3?.classList.remove('hidden');
  });

  // Step 3 → Step 2
  prosesBack2?.addEventListener('click', () => {
    setProsesStep(2);
    if (pageSub) pageSub.textContent = 'Atur posisi elemen — Langkah 2 dari 3';
    prosesStep3?.classList.add('hidden');
    prosesStep2?.classList.remove('hidden');
  });

  // Submit → kirim ke verifikator (simulasi)
  prosesSubmit?.addEventListener('click', () => {
    showToast('Surat berhasil dikirim ke verifikator!', 'success');
    setTimeout(() => switchPage('pengajuan-masuk'), 800);
  });


  // ================================================================
  // PDF FILE UPLOAD
  // ================================================================
  const pdfInput   = document.getElementById('pdf-file-input');
  const pdfDrop    = document.getElementById('pdf-drop-zone');
  const pdfPreview = document.getElementById('pdf-file-preview');
  const pdfName    = document.getElementById('pdf-file-name');
  const pdfRemove  = document.getElementById('pdf-file-remove');

  const setPdfFile = (file) => {
    if (!file || !file.name.toLowerCase().endsWith('.pdf')) {
      showToast('Hanya file PDF yang diperbolehkan.', 'error');
      return;
    }
    if (pdfName) pdfName.textContent = file.name;
    pdfPreview?.classList.remove('hidden');
  };

  pdfInput?.addEventListener('change', () => setPdfFile(pdfInput.files[0]));
  pdfDrop?.addEventListener('dragover', (e) => { e.preventDefault(); pdfDrop.classList.add('border-blue-400', 'bg-blue-50/50'); });
  pdfDrop?.addEventListener('dragleave', () => pdfDrop.classList.remove('border-blue-400', 'bg-blue-50/50'));
  pdfDrop?.addEventListener('drop', (e) => {
    e.preventDefault();
    pdfDrop.classList.remove('border-blue-400', 'bg-blue-50/50');
    const file = e.dataTransfer.files[0];
    if (file) {
      const dt = new DataTransfer(); dt.items.add(file);
      pdfInput.files = dt.files;
      setPdfFile(file);
    }
  });
  pdfRemove?.addEventListener('click', () => {
    if (pdfInput) pdfInput.value = '';
    pdfPreview?.classList.add('hidden');
  });


  // ================================================================
  // MASTER DASAR HUKUM — Tambah & Hapus
  // ================================================================
  const btnTambah    = document.getElementById('btn-tambah-dasar');
  const formTambah   = document.getElementById('form-tambah-dasar');
  const btnBatal     = document.getElementById('btn-batal-dasar');
  const btnSimpan    = document.getElementById('btn-simpan-dasar');
  const inputNama    = document.getElementById('input-nama-dasar');
  const inputTentang = document.getElementById('input-tentang-dasar');
  const tbodyDasar   = document.getElementById('tbody-dasar-hukum');

  let dasarCounter = 3; // data dummy sudah ada 3

  btnTambah?.addEventListener('click', () => {
    formTambah?.classList.remove('hidden');
    inputNama?.focus();
  });

  btnBatal?.addEventListener('click', () => {
    formTambah?.classList.add('hidden');
    if (inputNama)    inputNama.value = '';
    if (inputTentang) inputTentang.value = '';
  });

  btnSimpan?.addEventListener('click', () => {
    const nama    = inputNama?.value.trim();
    const tentang = inputTentang?.value.trim();

    if (!nama || !tentang) {
      showToast('Nama peraturan dan keterangan tidak boleh kosong.', 'error');
      return;
    }

    dasarCounter++;

    const tr = document.createElement('tr');
    tr.className = 'hover:bg-slate-50/40 transition-colors duration-150';
    tr.innerHTML = `
      <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">${dasarCounter}</p></td>
      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">${nama}</p></td>
      <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">${tentang}</p></td>
      <td class="px-5 py-3.5">
        <button type="button" class="btn-hapus-dasar text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200">Hapus</button>
      </td>
    `;
    tbodyDasar?.appendChild(tr);

    if (inputNama)    inputNama.value = '';
    if (inputTentang) inputTentang.value = '';
    formTambah?.classList.add('hidden');
    showToast('Dasar hukum berhasil ditambahkan!', 'success');
  });

  // Hapus baris dasar hukum
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-hapus-dasar');
    if (!btn) return;
    const row = btn.closest('tr');
    if (row) {
      row.remove();
      showToast('Dasar hukum berhasil dihapus.', 'info');
    }
  });


  // ================================================================
  // WIZARD: PROSES SK (3 step)
  // ================================================================

  // Data SK yang sedang diproses
  let skProsesData = {};

  // Elemen step indicator
  const skProsesCircle1 = document.getElementById('sk-proses-circle-1');
  const skProsesCircle2 = document.getElementById('sk-proses-circle-2');
  const skProsesCircle3 = document.getElementById('sk-proses-circle-3');
  const skProsesLabel1  = document.getElementById('sk-proses-label-1');
  const skProsesLabel2  = document.getElementById('sk-proses-label-2');
  const skProsesLabel3  = document.getElementById('sk-proses-label-3');

  // Elemen step content
  const skProsesStep1 = document.getElementById('sk-proses-step-1');
  const skProsesStep2 = document.getElementById('sk-proses-step-2');
  const skProsesStep3 = document.getElementById('sk-proses-step-3');

  // Tombol navigasi
  const skProsesNext1   = document.getElementById('sk-proses-next-1');
  const skProsesNext2   = document.getElementById('sk-proses-next-2');
  const skProsesBack1   = document.getElementById('sk-proses-back-1');
  const skProsesBack2   = document.getElementById('sk-proses-back-2');
  const skProsesSubmit  = document.getElementById('sk-proses-submit');
  const skProsesTolak   = document.getElementById('sk-proses-tolak-btn');
  const skProsesBackList = document.getElementById('sk-proses-back-to-list');

  // Fungsi set step indicator
  const setSkProsesStep = (step) => {
    [[skProsesCircle1, skProsesLabel1],
     [skProsesCircle2, skProsesLabel2],
     [skProsesCircle3, skProsesLabel3]].forEach(([c, l], i) => {
      if (!c || !l) return;
      if (i < step) {
        c.className = 'w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0';
        c.querySelector('span').className = 'text-[11px] font-bold text-white';
        l.className = 'text-xs font-semibold text-blue-600';
      } else {
        c.className = 'w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0';
        c.querySelector('span').className = 'text-[11px] font-bold text-slate-400';
        l.className = 'text-xs font-medium text-slate-400';
      }
    });
  };

  // Reset wizard SK
  const resetSkProsesWizard = () => {
    skProsesStep1?.classList.remove('hidden');
    skProsesStep2?.classList.add('hidden');
    skProsesStep3?.classList.add('hidden');
    setSkProsesStep(1);
    if (pageSub) pageSub.textContent = 'Review isi SK — Langkah 1 dari 3';
  };

  // Isi data SK dummy ke review (nanti dari backend)
  const fillSkReview = (data) => {
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || '—'; };
    set('sk-review-judul',     data.judul || data.perihal);
    set('sk-review-tentang',   data.tentang   || 'Data dari sistem (backend)');
    set('sk-review-menimbang', data.menimbang  || 'a. bahwa...\nb. bahwa...');
    set('sk-review-memutuskan', data.memutuskan || 'PERTAMA : ...\nKEDUA   : ...');

    // Mengingat — dummy list
    const mengingat = document.getElementById('sk-review-mengingat');
    if (mengingat) {
      mengingat.innerHTML = `
        <li class="flex items-start gap-1.5 text-xs text-slate-600 font-light">
          <span class="w-1 h-1 rounded-full bg-slate-400 mt-1.5 shrink-0"></span>
          UU No. 20 Tahun 2003 tentang Sistem Pendidikan Nasional
        </li>
        <li class="flex items-start gap-1.5 text-xs text-slate-600 font-light">
          <span class="w-1 h-1 rounded-full bg-slate-400 mt-1.5 shrink-0"></span>
          Peraturan Direktur Polibatam No. 01 Tahun 2023
        </li>
      `;
    }
  };

  // Go to wizard SK dari tombol tabel
  const goToProsesSkWizard = (data) => {
    skProsesData = data;

    // Set info bar
    const infoEl = document.getElementById('sk-proses-judul-info');
    if (infoEl) infoEl.textContent = `${data.judul || data.perihal || '—'} — ${data.pemohon || '—'}`;

    // Isi konten review
    fillSkReview(data);

    // Reset & buka wizard
    resetSkProsesWizard();
    switchPage('proses-sk');
  };

  // Kembali ke daftar SK
  skProsesBackList?.addEventListener('click', () => switchPage('pengajuan-sk'));


  // ── Step 1 → Step 2 ──
  skProsesNext1?.addEventListener('click', () => {
    setSkProsesStep(2);
    if (pageSub) pageSub.textContent = 'Tentukan tingkat verifikasi — Langkah 2 dari 3';
    skProsesStep1?.classList.add('hidden');
    skProsesStep2?.classList.remove('hidden');
  });

  // ── Step 2 → Step 1 ──
  skProsesBack1?.addEventListener('click', () => {
    setSkProsesStep(1);
    if (pageSub) pageSub.textContent = 'Review isi SK — Langkah 1 dari 3';
    skProsesStep2?.classList.add('hidden');
    skProsesStep1?.classList.remove('hidden');
  });

  // ── Step 2 → Step 3: isi ringkasan konfirmasi ──
  skProsesNext2?.addEventListener('click', () => {
    // Ambil jalur verifikasi yang dipilih
    const jalurEl = document.querySelector('input[name="jalur_verifikasi_sk"]:checked');
    const jalurVal = jalurEl?.value || '1';
    const jalurMap = { '1': 'Level 1', '2': 'Level 1 → Level 2', '3': 'Level 1 → Level 2 → Level 3' };

    // Ambil verifikator
    const v1 = document.querySelector('[name="sk_verifikator_1"]')?.value || '—';
    const v2 = document.querySelector('[name="sk_verifikator_2"]')?.value;
    const v3 = document.querySelector('[name="sk_verifikator_3"]')?.value;

    // Ambil catatan
    const catatan = document.getElementById('sk-catatan-admin')?.value.trim();

    // Isi konfirmasi
    const setKonfirmasi = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || '—'; };
    setKonfirmasi('sk-konfirmasi-judul',   skProsesData.judul || skProsesData.perihal);
    setKonfirmasi('sk-konfirmasi-pemohon', skProsesData.pemohon);
    setKonfirmasi('sk-konfirmasi-jalur',   jalurMap[jalurVal]);

    // Verifikator list
    const vEl = document.getElementById('sk-konfirmasi-verifikator');
    if (vEl) {
      const vList = [
        { level: 'Level 1', nama: v1 },
        ...(v2 ? [{ level: 'Level 2', nama: v2 }] : []),
        ...(v3 ? [{ level: 'Level 3', nama: v3 }] : []),
      ];
      vEl.innerHTML = vList.map(v =>
        `<p class="text-xs font-medium text-slate-700">${v.level}: <span class="font-light text-slate-500">${v.nama}</span></p>`
      ).join('');
    }

    // Catatan
    const cataWrap = document.getElementById('sk-konfirmasi-catatan-wrap');
    const cataEl   = document.getElementById('sk-konfirmasi-catatan');
    if (catatan && cataWrap && cataEl) {
      cataWrap.classList.remove('hidden');
      cataEl.textContent = catatan;
    } else {
      cataWrap?.classList.add('hidden');
    }

    setSkProsesStep(3);
    if (pageSub) pageSub.textContent = 'Konfirmasi & kirim — Langkah 3 dari 3';
    skProsesStep2?.classList.add('hidden');
    skProsesStep3?.classList.remove('hidden');
  });

  // ── Step 3 → Step 2 ──
  skProsesBack2?.addEventListener('click', () => {
    setSkProsesStep(2);
    if (pageSub) pageSub.textContent = 'Tentukan tingkat verifikasi — Langkah 2 dari 3';
    skProsesStep3?.classList.add('hidden');
    skProsesStep2?.classList.remove('hidden');
  });

  // ── Submit: kirim SK ke verifikator ──
  skProsesSubmit?.addEventListener('click', () => {
    showToast('SK berhasil dikirim ke verifikator!', 'success');
    setTimeout(() => switchPage('pengajuan-sk'), 800);
  });

  // ── Tombol kembalikan revisi ──
  skProsesTolak?.addEventListener('click', () => {
    const catatan = document.getElementById('sk-catatan-admin')?.value.trim();
    if (!catatan) {
      showToast('Isi catatan revisi terlebih dahulu.', 'error');
      document.getElementById('sk-catatan-admin')?.focus();
      return;
    }
    showToast('SK dikembalikan ke pemohon untuk revisi.', 'info');
    setTimeout(() => switchPage('pengajuan-sk'), 800);
  });

});
