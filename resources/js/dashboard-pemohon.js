/**
 * dashboard-pemohon.js
 * Lokasi: resources/js/pages/dashboard-pemohon.js
 */

document.addEventListener('DOMContentLoaded', () => {

  // ================================================================
  // KONFIGURASI HALAMAN
  // ================================================================
  const PAGE_CONFIG = {
    'dashboard'  : { title: 'Dashboard',         subtitle: 'Selamat datang di SIMAS' },
    'buat-surat' : { title: 'Buat Surat Baru',   subtitle: 'Ajukan surat biasa — Langkah 1 dari 2' },
    'surat-saya' : { title: 'Surat Saya',         subtitle: 'Daftar semua surat yang diajukan' },
    'buat-sk'    : { title: 'Buat Pengajuan SK',  subtitle: 'Ajukan Surat Keputusan — Langkah 1 dari 3' },
    'sk-saya'    : { title: 'SK Saya',            subtitle: 'Daftar semua SK yang diajukan' },
    'profil'     : { title: 'Profil Saya',        subtitle: 'Kelola data akun kamu' },
  };

  const navLinks      = document.querySelectorAll('.nav-link');
  const quickNavs     = document.querySelectorAll('.quick-nav');
  const pages         = document.querySelectorAll('.page-content');
  const pageTitle     = document.getElementById('page-title');
  const pageSub       = document.getElementById('page-subtitle');
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebar       = document.getElementById('sidebar');


  // ================================================================
  // TOAST NOTIFICATION
  // ================================================================
  const showToast = (msg, type = 'success') => {
    const toast    = document.getElementById('toast');
    const toastMsg = document.getElementById('toast-msg');
    const toastIcon = document.getElementById('toast-icon');
    if (!toast || !toastMsg) return;

    const icons = {
      success: `<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`,
      error:   `<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>`,
      info:    `<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
    };

    toastIcon.innerHTML = icons[type] || icons.info;
    toastMsg.textContent = msg;
    toast.classList.remove('hidden');

    // Animasi masuk
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(8px)';
    requestAnimationFrame(() => {
      toast.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    });

    // Auto hide setelah 3 detik
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(8px)';
      setTimeout(() => toast.classList.add('hidden'), 200);
    }, 3000);
  };


  // ================================================================
  // MODAL DETAIL DOKUMEN
  // ================================================================
  const modalOverlay    = document.getElementById('modal-overlay');
  const modalClose      = document.getElementById('modal-close');
  const modalCloseBtn   = document.getElementById('modal-close-btn');
  const modalDownload   = document.getElementById('modal-download-btn');

  const openModal = (data) => {
    if (!modalOverlay) return;
    document.getElementById('modal-jenis').textContent    = data.jenis || '—';
    document.getElementById('modal-perihal').textContent  = data.perihal || '—';
    document.getElementById('modal-tanggal').textContent  = data.tanggal || '—';
    document.getElementById('modal-nomor').textContent    = data.nomor || '—';
    document.getElementById('modal-keterangan').textContent = data.keterangan || '—';

    // Status badge
    const statusEl = document.getElementById('modal-status');
    const statusMap = {
      diproses:  { text: 'Diproses',  cls: 'text-blue-600' },
      published: { text: 'Published', cls: 'text-slate-600' },
      ditolak:   { text: 'Ditolak',   cls: 'text-slate-500' },
    };
    const s = statusMap[data.status] || { text: data.status, cls: 'text-slate-600' };
    statusEl.textContent = s.text;
    statusEl.className = `text-xs font-semibold ${s.cls}`;

    // Tampilkan tombol unduh hanya jika Published
    if (modalDownload) {
      if (data.status === 'published') {
        modalDownload.classList.remove('hidden');
      } else {
        modalDownload.classList.add('hidden');
      }
    }

    modalOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  };

  const closeModal = () => {
    modalOverlay?.classList.add('hidden');
    document.body.style.overflow = '';
  };

  modalClose?.addEventListener('click', closeModal);
  modalCloseBtn?.addEventListener('click', closeModal);
  modalOverlay?.addEventListener('click', (e) => {
    if (e.target === modalOverlay) closeModal();
  });

  // Tombol unduh (simulasi — sambungkan ke route download saat backend siap)
  modalDownload?.addEventListener('click', () => {
    closeModal();
    showToast('Dokumen sedang diunduh...', 'info');
  });

  // Delegasi event: tombol "Lihat Detail" di semua tabel
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;
    const row = btn.closest('tr');
    if (!row) return;
    openModal({
      jenis      : row.dataset.jenis,
      perihal    : row.dataset.perihal,
      tanggal    : row.dataset.tanggal,
      nomor      : row.dataset.nomor,
      keterangan : row.dataset.keterangan,
      status     : row.dataset.status,
    });
  });

  // Delegasi event: tombol "Unduh" inline di tabel
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-download');
    if (!btn) return;
    showToast('Dokumen sedang diunduh...', 'info');
  });


  // ================================================================
  // FILTER STATUS (Surat Saya & SK Saya)
  // ================================================================
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.filter-btn');
    if (!btn) return;

    const filter = btn.dataset.filter;
    const target = btn.dataset.target; // 'surat' atau 'sk'

    // Update active state tombol filter
    const container = btn.closest('div');
    container.querySelectorAll('.filter-btn').forEach(b => {
      b.classList.remove('bg-blue-600', 'text-white', 'font-semibold');
      b.classList.add('text-slate-500', 'font-medium');
    });
    btn.classList.add('bg-blue-600', 'text-white', 'font-semibold');
    btn.classList.remove('text-slate-500', 'font-medium');

    // Filter baris tabel
    const tbody   = document.getElementById(`tbody-${target}`);
    const emptyEl = document.getElementById(`${target}-empty`);
    if (!tbody) return;

    const rows = tbody.querySelectorAll('.doc-row');
    let visibleCount = 0;

    rows.forEach(row => {
      const status = row.dataset.status;
      const show   = filter === 'semua' || status === filter;
      row.classList.toggle('hidden', !show);
      if (show) visibleCount++;
    });

    // Tampilkan empty state jika tidak ada data
    if (emptyEl) emptyEl.classList.toggle('hidden', visibleCount > 0);
  });


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
        link.classList.remove('text-slate-600', 'hover:bg-blue-50', 'hover:text-blue-600', 'text-slate-500');
      } else {
        link.classList.remove('bg-blue-600', 'text-white', 'shadow-sm', 'shadow-blue-200');
        if (!link.classList.contains('hover:bg-red-50')) {
          link.classList.add('text-slate-600', 'hover:bg-blue-50', 'hover:text-blue-600');
        }
      }
    });

    if (pageKey === 'buat-surat') resetSuratWizard();
    if (pageKey === 'buat-sk')    resetSkWizard();

    sidebar?.classList.add('-translate-x-full');
    window.scrollTo({ top: 0 });
  };

  navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      const page = link.dataset.page;
      if (!page) return;
      e.preventDefault();
      if (page) switchPage(page);
    });
  });

  quickNavs.forEach(link => {
    link.addEventListener('click', (e) => {
      const page = link.dataset.page;
      if (!page) return;
      e.preventDefault();
      if (page) switchPage(page);
    });
  });

  // Topbar user icon juga bisa klik ke profil
  document.querySelector('[data-page="profil"]')?.addEventListener('click', (e) => {
    e.preventDefault();
    switchPage('profil');
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
  // WIZARD: BUAT SURAT BARU (2 step)
  // ================================================================
  const suratStep1       = document.getElementById('surat-step-1');
  const suratStep2       = document.getElementById('surat-step-2');
  const suratFileInput   = document.getElementById('surat-file-input');
  const suratDropZone    = document.getElementById('surat-drop-zone');
  const suratFilePreview = document.getElementById('surat-file-preview');
  const suratFileName    = document.getElementById('surat-file-name');
  const suratFileRemove  = document.getElementById('surat-file-remove');
  const suratNextBtn     = document.getElementById('surat-next-btn');
  const suratBackBtn     = document.getElementById('surat-back-btn');
  const suratBackBtn2    = document.getElementById('surat-back-btn-2');
  const suratStep2Circle = document.getElementById('surat-step2-circle');
  const suratStep2Label  = document.getElementById('surat-step2-label');
  const suratStep2File   = document.getElementById('surat-step2-filename');
  const suratSubmitBtn   = document.getElementById('surat-submit-btn');

  const resetSuratWizard = () => {
    suratStep1?.classList.remove('hidden');
    suratStep2?.classList.add('hidden');
    if (suratFileInput) suratFileInput.value = '';
    suratFilePreview?.classList.add('hidden');
    if (suratNextBtn) suratNextBtn.disabled = true;
    if (suratStep2Circle) {
      suratStep2Circle.className = 'w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0';
      suratStep2Circle.querySelector('span').className = 'text-[11px] font-bold text-slate-400';
    }
    if (suratStep2Label) suratStep2Label.className = 'text-xs font-medium text-slate-400';
  };

  const setSuratFile = (file) => {
    if (!file || !file.name.toLowerCase().endsWith('.docx')) {
      showToast('Hanya file DOCX yang diperbolehkan.', 'error');
      return;
    }
    if (suratFileName) suratFileName.textContent = file.name;
    suratFilePreview?.classList.remove('hidden');
    if (suratNextBtn) suratNextBtn.disabled = false;
  };

  suratFileInput?.addEventListener('change', () => setSuratFile(suratFileInput.files[0]));

  suratDropZone?.addEventListener('dragover', (e) => { e.preventDefault(); suratDropZone.classList.add('border-blue-400', 'bg-blue-50/50'); });
  suratDropZone?.addEventListener('dragleave', () => suratDropZone.classList.remove('border-blue-400', 'bg-blue-50/50'));
  suratDropZone?.addEventListener('drop', (e) => {
    e.preventDefault();
    suratDropZone.classList.remove('border-blue-400', 'bg-blue-50/50');
    const file = e.dataTransfer.files[0];
    if (file) {
      const dt = new DataTransfer(); dt.items.add(file);
      suratFileInput.files = dt.files;
      setSuratFile(file);
    }
  });

  suratFileRemove?.addEventListener('click', () => {
    if (suratFileInput) suratFileInput.value = '';
    suratFilePreview?.classList.add('hidden');
    if (suratNextBtn) suratNextBtn.disabled = true;
  });

  suratNextBtn?.addEventListener('click', () => {
    const name = suratFileName?.textContent || '';
    if (!name) return;
    if (suratStep2Circle) {
      suratStep2Circle.className = 'w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0';
      suratStep2Circle.querySelector('span').className = 'text-[11px] font-bold text-white';
    }
    if (suratStep2Label) suratStep2Label.className = 'text-xs font-semibold text-blue-600';
    if (suratStep2File)  suratStep2File.textContent = name;
    if (pageSub) pageSub.textContent = 'Ajukan surat biasa — Langkah 2 dari 2';
    suratStep1?.classList.add('hidden');
    suratStep2?.classList.remove('hidden');
  });

  const goBackSurat = () => {
    suratStep2?.classList.add('hidden');
    suratStep1?.classList.remove('hidden');
    if (pageSub) pageSub.textContent = 'Ajukan surat biasa — Langkah 1 dari 2';
    if (suratStep2Circle) {
      suratStep2Circle.className = 'w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0';
      suratStep2Circle.querySelector('span').className = 'text-[11px] font-bold text-slate-400';
    }
    if (suratStep2Label) suratStep2Label.className = 'text-xs font-medium text-slate-400';
  };
  suratBackBtn?.addEventListener('click', goBackSurat);
  suratBackBtn2?.addEventListener('click', goBackSurat);

  // Submit surat (simulasi)
  suratSubmitBtn?.addEventListener('click', () => {
    showToast('Surat berhasil diajukan!', 'success');
    setTimeout(() => switchPage('surat-saya'), 800);
  });


  // ================================================================
  // WIZARD: BUAT PENGAJUAN SK (3 step)
  // ================================================================
  const skStep1  = document.getElementById('sk-step-1');
  const skStep2  = document.getElementById('sk-step-2');
  const skStep3  = document.getElementById('sk-step-3');
  const skNext1  = document.getElementById('sk-next-1');
  const skNext2  = document.getElementById('sk-next-2');
  const skBack1  = document.getElementById('sk-back-1');
  const skBack2  = document.getElementById('sk-back-2');
  const skSubmit = document.getElementById('sk-submit-btn');
  const skCircle1 = document.getElementById('sk-circle-1');
  const skCircle2 = document.getElementById('sk-circle-2');
  const skCircle3 = document.getElementById('sk-circle-3');
  const skLabel1  = document.getElementById('sk-label-1');
  const skLabel2  = document.getElementById('sk-label-2');
  const skLabel3  = document.getElementById('sk-label-3');

  const setSkStep = (step) => {
    [[skCircle1, skLabel1], [skCircle2, skLabel2], [skCircle3, skLabel3]].forEach(([c, l], i) => {
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

  const resetSkWizard = () => {
    skStep1?.classList.remove('hidden');
    skStep2?.classList.add('hidden');
    skStep3?.classList.add('hidden');
    setSkStep(1);
    if (pageSub) pageSub.textContent = 'Ajukan Surat Keputusan — Langkah 1 dari 3';
  };

  skNext1?.addEventListener('click', () => {
    setSkStep(2);
    if (pageSub) pageSub.textContent = 'Ajukan Surat Keputusan — Langkah 2 dari 3';
    skStep1?.classList.add('hidden');
    skStep2?.classList.remove('hidden');
  });

  skBack1?.addEventListener('click', () => {
    setSkStep(1);
    if (pageSub) pageSub.textContent = 'Ajukan Surat Keputusan — Langkah 1 dari 3';
    skStep2?.classList.add('hidden');
    skStep1?.classList.remove('hidden');
  });

  skNext2?.addEventListener('click', () => {
    // Isi review
    const judul      = document.getElementById('sk-judul')?.value || '—';
    const tentang    = document.getElementById('sk-tentang')?.value || '—';
    const menimbang  = document.getElementById('sk-menimbang')?.value || '—';
    const memutuskan = document.getElementById('sk-memutuskan')?.value || '—';
    const checked    = document.querySelectorAll('#sk-dasar-list input:checked');
    const mengingat  = Array.from(checked).map(cb => {
      const lbl = cb.closest('label');
      return `${lbl?.querySelector('p:first-of-type')?.textContent || ''} ${lbl?.querySelector('p:last-of-type')?.textContent || ''}`;
    });

    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('review-judul', judul);
    set('review-tentang', tentang);
    set('review-menimbang', menimbang);
    set('review-memutuskan', memutuskan);

    const listEl = document.getElementById('review-mengingat');
    if (listEl) {
      listEl.innerHTML = mengingat.length
        ? mengingat.map(m => `<li class="text-xs text-slate-600 font-light flex items-start gap-1.5"><span class="w-1 h-1 rounded-full bg-slate-400 mt-1.5 shrink-0"></span>${m}</li>`).join('')
        : '<li class="text-xs text-slate-400 font-light">Tidak ada dasar hukum dipilih.</li>';
    }

    setSkStep(3);
    if (pageSub) pageSub.textContent = 'Ajukan Surat Keputusan — Langkah 3 dari 3';
    skStep2?.classList.add('hidden');
    skStep3?.classList.remove('hidden');
  });

  skBack2?.addEventListener('click', () => {
    setSkStep(2);
    if (pageSub) pageSub.textContent = 'Ajukan Surat Keputusan — Langkah 2 dari 3';
    skStep3?.classList.add('hidden');
    skStep2?.classList.remove('hidden');
  });

  skSubmit?.addEventListener('click', () => {
    showToast('Pengajuan SK berhasil diajukan!', 'success');
    setTimeout(() => switchPage('sk-saya'), 800);
  });


  // ================================================================
  // SEARCH FILTER DASAR HUKUM
  // ================================================================
  document.getElementById('sk-search-dasar')?.addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('#sk-dasar-list label').forEach(label => {
      label.classList.toggle('hidden', q !== '' && !label.textContent.toLowerCase().includes(q));
    });
  });

});
