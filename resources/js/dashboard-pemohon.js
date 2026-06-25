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
    'buat-sk'    : { title: 'Buat Pengajuan SK',  subtitle: 'Ajukan Surat Keputusan — Langkah 1 dari 2' },
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
    sidebar.classList.add('xl:translate-x-0');
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('-translate-x-full'));
    document.addEventListener('click', (e) => {
      const isOutside = !sidebar.contains(e.target) && !sidebarToggle.contains(e.target);
      if (isOutside && window.innerWidth < 1280) sidebar.classList.add('-translate-x-full');
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
    if (!file || !file.name.toLowerCase().endsWith('.pdf')) {
      showToast('Hanya file PDF yang diperbolehkan.', 'error');
      if (suratFileInput) suratFileInput.value = '';
      suratFilePreview?.classList.add('hidden');
      if (suratNextBtn) suratNextBtn.disabled = true;
      return;
    }
    if (file.size > 10 * 1024 * 1024) {
      showToast('Ukuran file PDF maksimal 10 MB.', 'error');
      if (suratFileInput) suratFileInput.value = '';
      suratFilePreview?.classList.add('hidden');
      if (suratNextBtn) suratNextBtn.disabled = true;
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
  // WIZARD: BUAT PENGAJUAN SK (2 step)
  // ================================================================
  const resetSkWizard = () => {
    const s1 = document.getElementById('sk-step-1');
    const s2 = document.getElementById('sk-step-2');
    s1?.classList.remove('hidden');
    s2?.classList.add('hidden');
  };


  // ================================================================
  // SEARCH FILTER DASAR HUKUM
  // ================================================================
  document.getElementById('sk-search-dasar')?.addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('#sk-dasar-list label').forEach(label => {
      label.classList.toggle('hidden', q !== '' && !label.textContent.toLowerCase().includes(q));
    });
  });

  // ================================================================
  // FORM SK DINAMIS (2-step wizard: Isi Data SK → Review & Kirim)
  // ================================================================
  (() => {
    const form = document.getElementById('sk-form');
    if (!form) return;

    const step1 = document.getElementById('sk-step-1');
    const step2 = document.getElementById('sk-step-2');
    const circles = [
      document.getElementById('sk-circle-1'),
      document.getElementById('sk-circle-2'),
    ];
    const labels = [
      document.getElementById('sk-label-1'),
      document.getElementById('sk-label-2'),
    ];
    const diktumLabels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'];
    const dasarHukumModal = document.getElementById('sk-dasar-hukum-modal');
    const dasarHukumSearch = document.getElementById('sk-dasar-search');
    const dasarHukumModalList = document.getElementById('sk-dasar-modal-list');
    const dasarHukumEmptySearch = document.getElementById('sk-dasar-empty-search');
    const dasarHukumOptions = Array.from(document.querySelectorAll('.sk-dasar-option'));
    const mengingatList = document.getElementById('sk-mengingat-list');
    const mengingatEmpty = document.getElementById('sk-mengingat-empty');
    const mengingatHiddenInputs = document.getElementById('sk-mengingat-hidden-inputs');
    const mengingatError = document.getElementById('sk-mengingat-error');
    let selectedDasarHukum = [];

    const alphaLabel = (index) => {
      let label = '';
      let number = index;

      do {
        label = String.fromCharCode(97 + (number % 26)) + label;
        number = Math.floor(number / 26) - 1;
      } while (number >= 0);

      return `${label}.`;
    };

    const diktumLabel = (index) => diktumLabels[index] || `KE-${index + 1}`;

    // Stepper now only has 2 steps
    const setSkFormStep = (step) => {
      [step1, step2].forEach((panel, index) => panel?.classList.toggle('hidden', index !== step - 1));
      circles.forEach((circle, index) => {
        const span = circle?.querySelector('span');
        const active = index < step;
        if (!circle || !span) return;

        circle.className = active
          ? 'w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0'
          : 'w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0';
        span.className = active
          ? 'text-[11px] font-bold text-white'
          : 'text-[11px] font-bold text-slate-400';
      });
      labels.forEach((label, index) => {
        if (!label) return;
        label.className = index < step ? 'text-xs font-semibold text-blue-600' : 'text-xs font-medium text-slate-400';
      });
    };

    const resetRowFields = (row) => {
      row.querySelectorAll('input, textarea, select').forEach((field) => {
        if (field.tagName === 'SELECT') {
          field.selectedIndex = 0;
        } else {
          field.value = '';
        }
      });
    };

    const updateRows = (listSelector, rowSelector, labelSelector, removeSelector, labeler) => {
      const rows = document.querySelectorAll(`${listSelector} ${rowSelector}`);
      rows.forEach((row, index) => {
        const label = row.querySelector(labelSelector);
        const removeBtn = row.querySelector(removeSelector);

        if (label) label.textContent = labeler(index);
        removeBtn?.classList.toggle('hidden', rows.length <= 1);
      });
    };

    const updateMenimbangRows = () => updateRows('#sk-menimbang-list', '.sk-menimbang-row', '.sk-menimbang-label', '.sk-remove-menimbang', alphaLabel);
    const updateDiktumRows = () => updateRows('#sk-diktum-list', '.sk-diktum-row', '.sk-diktum-label', '.sk-remove-diktum', diktumLabel);

    const cloneRow = (listSelector, rowSelector, updateFn) => {
      const list = document.querySelector(listSelector);
      const firstRow = list?.querySelector(rowSelector);
      if (!list || !firstRow) return;

      const clone = firstRow.cloneNode(true);
      resetRowFields(clone);
      list.appendChild(clone);
      updateFn();
    };

    const removeRow = (button, rowSelector, updateFn) => {
      const row = button.closest(rowSelector);
      const list = row?.parentElement;
      if (!row || !list || list.querySelectorAll(rowSelector).length <= 1) return;

      row.remove();
      updateFn();
    };

    const collectTextRows = (selector) => Array.from(document.querySelectorAll(selector))
      .map((field) => field.value.trim())
      .filter(Boolean);

    const renderReviewList = (id, items, labeler, emptyText) => {
      const list = document.getElementById(id);
      if (!list) return;

      list.innerHTML = '';

      if (items.length === 0) {
        const emptyItem = document.createElement('li');
        emptyItem.className = 'text-xs text-slate-400 font-light';
        emptyItem.textContent = emptyText;
        list.appendChild(emptyItem);
        return;
      }

      items.forEach((item, index) => {
        const row = document.createElement('li');
        row.className = 'flex items-start gap-2 text-xs text-slate-600 font-light';

        const label = document.createElement('span');
        label.className = id === 'review-memutuskan'
          ? 'w-24 shrink-0 font-semibold text-slate-500'
          : 'min-w-[54px] shrink-0 font-semibold text-slate-500';
        label.textContent = labeler(index);

        const text = document.createElement('span');
        text.className = 'leading-relaxed';
        text.textContent = item;

        row.append(label, text);
        list.appendChild(row);
      });
    };

    const selectedDasarIds = () => new Set(selectedDasarHukum.map((item) => String(item.id)));

    const filterDasarHukumOptions = () => {
      const keyword = (dasarHukumSearch?.value || '').trim().toLowerCase();
      let visibleCount = 0;

      dasarHukumOptions.forEach((row) => {
        const matches = keyword === '' || (row.dataset.dasarLabel || '').toLowerCase().includes(keyword);
        row.classList.toggle('hidden', !matches);
        if (matches) visibleCount++;
      });

      dasarHukumEmptySearch?.classList.toggle('hidden', visibleCount > 0 || dasarHukumOptions.length === 0);
    };

    const updateDasarHukumModalOptions = () => {
      const selectedIds = selectedDasarIds();

      dasarHukumOptions.forEach((row) => {
        const isSelected = selectedIds.has(String(row.dataset.dasarId));
        const button = row.querySelector('.sk-pilih-dasar');

        row.classList.toggle('opacity-60', isSelected);
        if (button) {
          button.disabled = isSelected;
          button.textContent = isSelected ? 'Dipilih' : 'Pilih';
        }
      });

      filterDasarHukumOptions();
    };

    const syncMengingatList = () => {
      if (!mengingatList || !mengingatHiddenInputs) return;

      mengingatList.querySelectorAll('.sk-mengingat-row').forEach((row) => row.remove());
      mengingatHiddenInputs.innerHTML = '';
      mengingatEmpty?.classList.toggle('hidden', selectedDasarHukum.length > 0);

      // Sembunyikan error validasi saat user sudah menambahkan dasar hukum
      if (selectedDasarHukum.length > 0) {
        mengingatError?.classList.add('hidden');
      }

      selectedDasarHukum.forEach((item, index) => {
        const row = document.createElement('div');
        row.className = 'sk-mengingat-row flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3';

        const number = document.createElement('span');
        number.className = 'w-7 shrink-0 pt-0.5 text-xs font-semibold text-blue-600';
        number.textContent = `${index + 1}.`;

        const label = document.createElement('p');
        label.className = 'min-w-0 flex-1 text-xs font-medium leading-relaxed text-slate-700';
        label.textContent = item.label;

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.dataset.dasarId = item.id;
        removeButton.className = 'sk-remove-mengingat shrink-0 rounded-lg border border-red-100 bg-red-50 px-2.5 py-1.5 text-[10px] font-semibold text-red-600 hover:bg-red-100 transition-all duration-200';
        removeButton.textContent = 'Hapus';

        const input = document.createElement('input');
        input.type = 'hidden';
        // Nama field mengikuti kebutuhan form; controller SK menormalkan nilainya sebelum validasi.
        input.name = 'dasar_hukum_id[]';
        input.value = item.id;

        row.append(number, label, removeButton);
        mengingatList.appendChild(row);
        mengingatHiddenInputs.appendChild(input);
      });

      updateDasarHukumModalOptions();
    };

    const openDasarHukumModal = () => {
      if (!dasarHukumModal) return;

      // Modal selalu dibuka dengan pencarian kosong agar user melihat semua dasar hukum aktif terlebih dahulu.
      if (dasarHukumSearch) dasarHukumSearch.value = '';
      updateDasarHukumModalOptions();
      dasarHukumModal.classList.remove('hidden');
      dasarHukumModal.classList.add('flex');
      document.body.style.overflow = 'hidden';
      dasarHukumSearch?.focus();
    };

    const closeDasarHukumModal = () => {
      if (!dasarHukumModal) return;

      dasarHukumModal.classList.add('hidden');
      dasarHukumModal.classList.remove('flex');
      document.body.style.overflow = '';
    };

    const addDasarHukum = (id, label) => {
      if (!id || !label) return;

      if (selectedDasarIds().has(String(id))) {
        showToast('Dasar hukum tersebut sudah dipilih.', 'info');
        return;
      }

      // Urutan array mengikuti urutan push ke selectedDasarHukum, lalu dirender ulang ke hidden input.
      selectedDasarHukum.push({ id: String(id), label });
      syncMengingatList();
      closeDasarHukumModal();
    };

    const removeDasarHukum = (id) => {
      selectedDasarHukum = selectedDasarHukum.filter((item) => String(item.id) !== String(id));
      syncMengingatList();
    };

    const collectSelectedMengingatRows = () => selectedDasarHukum.map((item) => `${item.label};`);

    updateMenimbangRows();
    updateDiktumRows();
    syncMengingatList();

    document.getElementById('sk-add-menimbang')?.addEventListener('click', () => cloneRow('#sk-menimbang-list', '.sk-menimbang-row', updateMenimbangRows));
    document.getElementById('sk-add-mengingat')?.addEventListener('click', openDasarHukumModal);
    document.getElementById('sk-add-diktum')?.addEventListener('click', () => cloneRow('#sk-diktum-list', '.sk-diktum-row', updateDiktumRows));

    form.addEventListener('click', (event) => {
      const menimbangRemove = event.target.closest('.sk-remove-menimbang');
      if (menimbangRemove) {
        removeRow(menimbangRemove, '.sk-menimbang-row', updateMenimbangRows);
        return;
      }

      const mengingatRemove = event.target.closest('.sk-remove-mengingat');
      if (mengingatRemove) {
        removeDasarHukum(mengingatRemove.dataset.dasarId);
        return;
      }

      const diktumRemove = event.target.closest('.sk-remove-diktum');
      if (diktumRemove) {
        removeRow(diktumRemove, '.sk-diktum-row', updateDiktumRows);
      }
    });

    document.getElementById('sk-dasar-modal-close')?.addEventListener('click', closeDasarHukumModal);
    dasarHukumModal?.addEventListener('click', (event) => {
      if (event.target === dasarHukumModal) closeDasarHukumModal();
    });
    dasarHukumSearch?.addEventListener('input', filterDasarHukumOptions);
    dasarHukumModalList?.addEventListener('click', (event) => {
      const button = event.target.closest('.sk-pilih-dasar');
      if (!button) return;

      const row = button.closest('.sk-dasar-option');
      addDasarHukum(row?.dataset.dasarId, row?.dataset.dasarLabel);
    });

    // Tombol "Lanjut Review" — validasi dasar hukum lalu populate review
    document.getElementById('sk-proto-next-1')?.addEventListener('click', () => {
      // Validasi: minimal 1 dasar hukum harus dipilih
      if (selectedDasarHukum.length === 0) {
        mengingatError?.classList.remove('hidden');
        // Scroll ke bagian error agar user menyadarinya
        mengingatError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }
      mengingatError?.classList.add('hidden');

      // Populate review fields
      const set = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value || '-';
      };

      set('review-judul', document.getElementById('sk-judul')?.value.trim());
      set('review-tentang', document.getElementById('sk-tentang')?.value.trim());
      set('review-menetapkan', document.getElementById('sk-menetapkan')?.value.trim());
      renderReviewList('review-menimbang', collectTextRows('.sk-menimbang-input'), alphaLabel, 'Belum ada butir menimbang.');
      renderReviewList('review-mengingat', collectSelectedMengingatRows(), (index) => `${index + 1}.`, 'Belum ada dasar hukum dipilih.');
      renderReviewList('review-memutuskan', collectTextRows('.sk-diktum-input'), diktumLabel, 'Belum ada diktum keputusan.');
      setSkFormStep(2);
    });

    // Tombol "Kembali" dari Review ke form
    document.getElementById('sk-proto-back-1')?.addEventListener('click', () => {
      setSkFormStep(1);
    });

    form.addEventListener('submit', () => {
      const submitButton = document.getElementById('sk-proto-submit-btn');
      if (!submitButton) return;

      // Saat form benar-benar dikirim, tombol dikunci agar pemohon tidak mengirim pengajuan SK dua kali.
      submitButton.disabled = true;
      submitButton.classList.add('opacity-70', 'cursor-not-allowed');
    });
  })();

});
