/**
 * dashboard-verifikator.js
 * Lokasi: resources/js/pages/dashboard-verifikator.js
 *
 * Fitur:
 * - Navigasi sidebar
 * - Modal verifikasi dokumen (Setujui / Tolak)
 * - Toggle tampilan alasan penolakan
 * - Toast notification
 * - Filter tabel semua surat
 * - Tombol "Verifikasi" dari tabel langsung ke modal
 */

document.addEventListener('DOMContentLoaded', () => {

  // ================================================================
  // KONFIGURASI HALAMAN
  // ================================================================
  const PAGE_CONFIG = {
    'dashboard'      : { title: 'Dashboard',               subtitle: 'Selamat datang, Verifikator' },
    'surat-menunggu' : { title: 'Surat Menunggu',           subtitle: 'Surat biasa yang menunggu verifikasi kamu' },
    'surat-disetujui': { title: 'Surat Disetujui',          subtitle: 'Surat biasa yang sudah kamu setujui' },
    'surat-ditolak'  : { title: 'Surat Ditolak',            subtitle: 'Surat biasa yang kamu tolak' },
    'surat-semua'    : { title: 'Semua Surat Biasa',        subtitle: 'Seluruh surat biasa yang terkait denganmu' },
    'sk-menunggu'    : { title: 'SK Menunggu',              subtitle: 'Surat Keputusan yang menunggu verifikasi kamu' },
    'sk-disetujui'   : { title: 'SK Disetujui',             subtitle: 'SK yang sudah kamu setujui' },
    'sk-ditolak'     : { title: 'SK Ditolak',               subtitle: 'SK yang kamu tolak' },
    'sk-semua'       : { title: 'Semua Surat Keputusan',    subtitle: 'Seluruh SK yang terkait denganmu' },
    'profil'         : { title: 'Profil Saya',              subtitle: 'Kelola data akun kamu' },
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
    const toast     = document.getElementById('toast');
    const toastMsg  = document.getElementById('toast-msg');
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
  // MODAL VERIFIKASI
  // ================================================================
  const modalOverlay  = document.getElementById('modal-overlay');
  const modalClose    = document.getElementById('modal-close');
  const modalCloseBtn = document.getElementById('modal-close-btn');
  const modalSubmit   = document.getElementById('modal-submit-btn');
  const modalUnduh    = document.getElementById('modal-unduh-btn');
  const radioSetuju   = document.getElementById('radio-setuju');
  const radioTolak    = document.getElementById('radio-tolak');
  const alasanWrap    = document.getElementById('alasan-wrap');
  const alasanInput   = document.getElementById('alasan-penolakan');
  const modalSkSection = document.getElementById('modal-sk-section');

  // Data baris yang sedang diverifikasi
  let activeRow = null;

  // Buka modal dengan data dari baris tabel
  const openModal = (row) => {
    activeRow = row;
    const data = row.dataset;

    // Isi info modal
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || '—'; };
    set('modal-jenis-badge', data.jenis);
    set('modal-perihal',     data.perihal);
    set('modal-pemohon',     data.pemohon);
    set('modal-tanggal',     data.tanggal);
    set('modal-level',       data.level);
    set('modal-ringkasan',   data.ringkasan);

    // Tampilkan section SK jika jenisnya SK
    if (data.jenis === 'SK') {
      modalSkSection?.classList.remove('hidden');
      set('modal-sk-tentang',   data.skTentang);
      // Decode HTML entities untuk data-sk-menimbang & memutuskan
      const decode = (str) => str ? str.replace(/&#10;/g, '\n').replace(/&amp;/g, '&') : '—';
      const menimbangEl = document.getElementById('modal-sk-menimbang');
      const memutuskanEl = document.getElementById('modal-sk-memutuskan');
      if (menimbangEl)  menimbangEl.textContent  = decode(data.skMenimbang);
      if (memutuskanEl) memutuskanEl.textContent = decode(data.skMemutuskan);
    } else {
      modalSkSection?.classList.add('hidden');
    }

    // Reset pilihan ke "Setujui"
    if (radioSetuju) radioSetuju.checked = true;
    alasanWrap?.classList.add('hidden');
    if (alasanInput) alasanInput.value = '';

    // Tampilkan modal
    modalOverlay?.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  };

  const closeModal = () => {
    modalOverlay?.classList.add('hidden');
    document.body.style.overflow = '';
    activeRow = null;
  };

  modalClose?.addEventListener('click', closeModal);
  modalCloseBtn?.addEventListener('click', closeModal);
  modalOverlay?.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); });

  // Toggle alasan penolakan saat pilih "Tolak"
  radioTolak?.addEventListener('change', () => {
    alasanWrap?.classList.remove('hidden');
    alasanInput?.focus();
  });
  radioSetuju?.addEventListener('change', () => {
    alasanWrap?.classList.add('hidden');
  });

  // Tombol unduh PDF simulasi
  modalUnduh?.addEventListener('click', () => {
    showToast('Dokumen PDF sedang diunduh...', 'info');
  });

  // Submit keputusan verifikasi
  modalSubmit?.addEventListener('click', () => {
    const keputusan = document.querySelector('input[name="keputusan_verifikasi"]:checked')?.value;

    if (keputusan === 'tolak') {
      const alasan = alasanInput?.value.trim();
      if (!alasan) {
        showToast('Isi alasan penolakan terlebih dahulu.', 'error');
        alasanInput?.focus();
        return;
      }
      closeModal();
      showToast('Dokumen berhasil ditolak & dikembalikan ke pemohon.', 'info');
    } else {
      closeModal();
      showToast('Dokumen berhasil disetujui!', 'success');
    }

    // Sementara: hapus baris dari tabel (simulasi)
    // Nanti diganti dengan fetch ke backend
    if (activeRow) {
      setTimeout(() => {
        activeRow?.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => activeRow?.remove(), 300);
      }, 600);
    }
  });

  // Delegasi: tombol Verifikasi di semua tabel
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-verifikasi');
    if (!btn) return;
    const row = btn.closest('tr');
    if (row) openModal(row);
  });


  // ================================================================
  // FILTER TABEL SEMUA SURAT
  // ================================================================
  document.querySelectorAll('.filter-surat').forEach(btn => {
    btn.addEventListener('click', () => {
      // Update active state
      document.querySelectorAll('.filter-surat').forEach(b => {
        b.classList.remove('bg-blue-600', 'text-white', 'font-semibold');
        b.classList.add('text-slate-500', 'font-medium');
      });
      btn.classList.add('bg-blue-600', 'text-white', 'font-semibold');
      btn.classList.remove('text-slate-500', 'font-medium');

      const filter = btn.dataset.filter;
      const tbody  = document.getElementById('tbody-surat-semua');
      if (!tbody) return;

      tbody.querySelectorAll('.surat-row').forEach(row => {
        const status = row.dataset.rowStatus;
        const show   = filter === 'semua' || status === filter;
        row.classList.toggle('hidden', !show);
      });
    });
  });

});
