document.addEventListener('DOMContentLoaded', () => {
  const toast = document.getElementById('toast');
  const toastMsg = document.getElementById('toast-msg');
  const toastIcon = document.getElementById('toast-icon');
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebar-toggle');

  const icons = {
    success: `<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`,
    error: `<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>`,
    info: `<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
  };

  const showToast = (message, type = 'success') => {
    if (!toast || !toastMsg || !toastIcon) return;
    toastIcon.innerHTML = icons[type] || icons.info;
    toastMsg.textContent = message;
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
    }, 2500);
  };

  if (sidebarToggle && sidebar) {
    sidebar.classList.add('xl:translate-x-0');
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('-translate-x-full'));
    document.addEventListener('click', (event) => {
      const outsideSidebar = !sidebar.contains(event.target) && !sidebarToggle.contains(event.target);
      if (outsideSidebar && window.innerWidth < 1280) {
        sidebar.classList.add('-translate-x-full');
      }
    });
  }

  const modalOverlay = document.getElementById('modal-overlay');
  const modalClose = document.getElementById('modal-close');
  const modalCloseBtn = document.getElementById('modal-close-btn');
  const modalProsesBtn = document.getElementById('modal-proses-btn');
  const modalDownloadBtn = document.getElementById('modal-download-docx');
  let activeRowData = null;

  const setText = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.textContent = value || '-';
  };

  const closeModal = () => {
    modalOverlay?.classList.add('hidden');
    document.body.style.overflow = '';
  };

  const openModal = (data) => {
    activeRowData = data;
    setText('modal-jenis', data.jenis);
    setText('modal-perihal', data.perihal);
    setText('modal-pemohon', data.pemohon);
    setText('modal-tanggal', data.tanggal);
    setText('modal-status', data.status);
    setText('modal-ringkasan', data.ringkasan);

    if (modalProsesBtn) {
      const label = (data.jenis || '').toLowerCase() === 'sk' ? 'Proses SK' : 'Proses Surat';
      const textNode = Array.from(modalProsesBtn.childNodes).find((node) => node.nodeType === Node.TEXT_NODE);
      if (textNode) {
        textNode.textContent = ` ${label}`;
      } else {
        modalProsesBtn.append(` ${label}`);
      }
    }

    modalOverlay?.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  };

  modalClose?.addEventListener('click', closeModal);
  modalCloseBtn?.addEventListener('click', closeModal);
  modalOverlay?.addEventListener('click', (event) => {
    if (event.target === modalOverlay) closeModal();
  });

  modalDownloadBtn?.addEventListener('click', () => {
    showToast('File DOCX sedang diunduh...', 'info');
  });

  modalProsesBtn?.addEventListener('click', () => {
    if (!activeRowData) return;

    const isSk = (activeRowData.jenis || '').toLowerCase() === 'sk';
    const baseUrl = isSk ? modalProsesBtn.dataset.routeSk : modalProsesBtn.dataset.routeSurat;
    if (!baseUrl) return;

    const params = new URLSearchParams();
    if (activeRowData.perihal) params.set(isSk ? 'judul' : 'perihal', activeRowData.perihal);
    if (activeRowData.pemohon) params.set('pemohon', activeRowData.pemohon);
    if (activeRowData.ringkasan) params.set('ringkasan', activeRowData.ringkasan);

    window.location.href = `${baseUrl}?${params.toString()}`;
  });

  document.addEventListener('click', (event) => {
    const detailBtn = event.target.closest('.btn-detail');
    if (detailBtn) {
      const row = detailBtn.closest('tr');
      if (!row) return;
      openModal({
        jenis: row.dataset.jenis,
        perihal: row.dataset.perihal,
        pemohon: row.dataset.pemohon,
        tanggal: row.dataset.tanggal,
        status: row.dataset.status,
        ringkasan: row.dataset.ringkasan,
      });
      return;
    }

    const prosesBtn = event.target.closest('.btn-proses');
    if (prosesBtn) {
      const row = prosesBtn.closest('tr');
      if (!row) return;
      const params = new URLSearchParams({
        perihal: row.dataset.perihal || '',
        pemohon: row.dataset.pemohon || '',
        ringkasan: row.dataset.ringkasan || '',
      });
      window.location.href = `${modalProsesBtn?.dataset.routeSurat || ''}?${params.toString()}`;
      return;
    }

    const prosesSkBtn = event.target.closest('.btn-proses-sk');
    if (prosesSkBtn) {
      const row = prosesSkBtn.closest('tr');
      if (!row) return;
      const params = new URLSearchParams({
        judul: row.dataset.perihal || '',
        pemohon: row.dataset.pemohon || '',
        ringkasan: row.dataset.ringkasan || '',
      });
      window.location.href = `${modalProsesBtn?.dataset.routeSk || ''}?${params.toString()}`;
    }
  });

  document.addEventListener('click', (event) => {
    const filterBtn = event.target.closest('.filter-btn');
    if (!filterBtn) return;

    const wrapper = filterBtn.closest('div');
    wrapper?.querySelectorAll('.filter-btn').forEach((btn) => {
      btn.classList.remove('bg-blue-600', 'text-white', 'font-semibold');
      btn.classList.add('text-slate-500', 'font-medium');
    });

    filterBtn.classList.add('bg-blue-600', 'text-white', 'font-semibold');
    filterBtn.classList.remove('text-slate-500', 'font-medium');

    const tbody = document.getElementById('tbody-pengajuan');
    if (!tbody) return;
    const filter = filterBtn.dataset.filter;

    tbody.querySelectorAll('.doc-row').forEach((row) => {
      const status = row.dataset.filterStatus || row.dataset.status?.toLowerCase();
      row.classList.toggle('hidden', !(filter === 'semua' || status === filter));
    });
  });

  const pdfInput = document.getElementById('pdf-file-input');
  const pdfDropZone = document.getElementById('pdf-drop-zone');
  const pdfPreview = document.getElementById('pdf-file-preview');
  const pdfFileName = document.getElementById('pdf-file-name');
  const pdfFileRemove = document.getElementById('pdf-file-remove');

  const setPdfFile = (file) => {
    if (!file) return;
    if (!file.name.toLowerCase().endsWith('.pdf')) {
      showToast('Hanya file PDF yang diperbolehkan.', 'error');
      return;
    }
    if (pdfFileName) pdfFileName.textContent = file.name;
    pdfPreview?.classList.remove('hidden');
  };

  pdfInput?.addEventListener('change', () => setPdfFile(pdfInput.files?.[0]));
  pdfDropZone?.addEventListener('dragover', (event) => {
    event.preventDefault();
    pdfDropZone.classList.add('border-blue-400', 'bg-blue-50/50');
  });
  pdfDropZone?.addEventListener('dragleave', () => {
    pdfDropZone.classList.remove('border-blue-400', 'bg-blue-50/50');
  });
  pdfDropZone?.addEventListener('drop', (event) => {
    event.preventDefault();
    pdfDropZone.classList.remove('border-blue-400', 'bg-blue-50/50');
    const file = event.dataTransfer.files?.[0];
    if (!file || !pdfInput) return;
    const transfer = new DataTransfer();
    transfer.items.add(file);
    pdfInput.files = transfer.files;
    setPdfFile(file);
  });
  pdfFileRemove?.addEventListener('click', () => {
    if (pdfInput) pdfInput.value = '';
    pdfPreview?.classList.add('hidden');
  });

  const setStepState = (circles, labels, currentStep) => {
    circles.forEach((circle, index) => {
      const label = labels[index];
      const isActive = index < currentStep;
      if (!circle || !label) return;
      circle.className = isActive
        ? 'w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center shrink-0'
        : 'w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center shrink-0';
      const span = circle.querySelector('span');
      if (span) span.className = isActive ? 'text-[11px] font-bold text-white' : 'text-[11px] font-bold text-slate-400';
      label.className = isActive ? 'text-xs font-semibold text-blue-600' : 'text-xs font-medium text-slate-400';
    });
  };

  const prosesSteps = [
    document.getElementById('proses-step-1'),
    document.getElementById('proses-step-2'),
    document.getElementById('proses-step-3'),
  ];
  const prosesCircles = [
    document.getElementById('proses-circle-1'),
    document.getElementById('proses-circle-2'),
    document.getElementById('proses-circle-3'),
  ];
  const prosesLabels = [
    document.getElementById('proses-label-1'),
    document.getElementById('proses-label-2'),
    document.getElementById('proses-label-3'),
  ];

  const showWizardStep = (steps, circles, labels, stepIndex) => {
    steps.forEach((step, index) => step?.classList.toggle('hidden', index !== stepIndex));
    setStepState(circles, labels, stepIndex + 1);
  };

  document.getElementById('proses-next-1')?.addEventListener('click', () => showWizardStep(prosesSteps, prosesCircles, prosesLabels, 1));
  document.getElementById('proses-back-1')?.addEventListener('click', () => showWizardStep(prosesSteps, prosesCircles, prosesLabels, 0));
  document.getElementById('proses-next-2')?.addEventListener('click', () => showWizardStep(prosesSteps, prosesCircles, prosesLabels, 2));
  document.getElementById('proses-back-2')?.addEventListener('click', () => showWizardStep(prosesSteps, prosesCircles, prosesLabels, 1));
  document.getElementById('proses-submit')?.addEventListener('click', () => {
    showToast('Surat berhasil dikirim ke verifikator!', 'success');
  });

  const btnTambahDasar = document.getElementById('btn-tambah-dasar');
  const formTambahDasar = document.getElementById('form-tambah-dasar');
  const btnBatalDasar = document.getElementById('btn-batal-dasar');
  const btnSimpanDasar = document.getElementById('btn-simpan-dasar');
  const inputNamaDasar = document.getElementById('input-nama-dasar');
  const inputTentangDasar = document.getElementById('input-tentang-dasar');
  const tbodyDasar = document.getElementById('tbody-dasar-hukum');
  let dasarCounter = 3;

  btnTambahDasar?.addEventListener('click', () => formTambahDasar?.classList.remove('hidden'));
  btnBatalDasar?.addEventListener('click', () => {
    formTambahDasar?.classList.add('hidden');
    if (inputNamaDasar) inputNamaDasar.value = '';
    if (inputTentangDasar) inputTentangDasar.value = '';
  });
  btnSimpanDasar?.addEventListener('click', () => {
    const nama = inputNamaDasar?.value.trim();
    const tentang = inputTentangDasar?.value.trim();
    if (!nama || !tentang || !tbodyDasar) {
      showToast('Nama peraturan dan keterangan tidak boleh kosong.', 'error');
      return;
    }

    dasarCounter += 1;
    const row = document.createElement('tr');
    row.className = 'hover:bg-slate-50/40 transition-colors duration-150';
    row.innerHTML = `
      <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">${dasarCounter}</p></td>
      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">${nama}</p></td>
      <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">${tentang}</p></td>
      <td class="px-5 py-3.5"><button type="button" class="btn-hapus-dasar text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200">Hapus</button></td>
    `;
    tbodyDasar.appendChild(row);
    formTambahDasar?.classList.add('hidden');
    if (inputNamaDasar) inputNamaDasar.value = '';
    if (inputTentangDasar) inputTentangDasar.value = '';
    showToast('Dasar hukum berhasil ditambahkan!', 'success');
  });

  document.addEventListener('click', (event) => {
    const hapusBtn = event.target.closest('.btn-hapus-dasar');
    if (!hapusBtn) return;
    hapusBtn.closest('tr')?.remove();
    showToast('Dasar hukum berhasil dihapus.', 'info');
  });

  const skSteps = [
    document.getElementById('sk-proses-step-1'),
    document.getElementById('sk-proses-step-2'),
    document.getElementById('sk-proses-step-3'),
  ];
  const skCircles = [
    document.getElementById('sk-proses-circle-1'),
    document.getElementById('sk-proses-circle-2'),
    document.getElementById('sk-proses-circle-3'),
  ];
  const skLabels = [
    document.getElementById('sk-proses-label-1'),
    document.getElementById('sk-proses-label-2'),
    document.getElementById('sk-proses-label-3'),
  ];

  document.getElementById('sk-proses-next-1')?.addEventListener('click', () => showWizardStep(skSteps, skCircles, skLabels, 1));
  document.getElementById('sk-proses-back-1')?.addEventListener('click', () => showWizardStep(skSteps, skCircles, skLabels, 0));
  document.getElementById('sk-proses-next-2')?.addEventListener('click', () => {
    const setKonfirmasi = (id, value) => {
      const el = document.getElementById(id);
      if (el) el.textContent = value || '-';
    };

    const judul = document.getElementById('sk-review-judul')?.textContent?.trim();
    const pemohon = document.querySelector('#sk-proses-judul-info')?.textContent?.split(' - ')[1]?.trim();
    const jalur = document.querySelector('input[name="jalur_verifikasi_sk"]:checked')?.value || '1';
    const jalurLabel = { '1': 'Level 1', '2': 'Level 1 -> Level 2', '3': 'Level 1 -> Level 2 -> Level 3' }[jalur];
    const v1 = document.querySelector('[name="sk_verifikator_1"]')?.value || '-';
    const v2 = document.querySelector('[name="sk_verifikator_2"]')?.value;
    const v3 = document.querySelector('[name="sk_verifikator_3"]')?.value;
    const catatan = document.getElementById('sk-catatan-admin')?.value.trim();

    setKonfirmasi('sk-konfirmasi-judul', judul);
    setKonfirmasi('sk-konfirmasi-pemohon', pemohon);
    setKonfirmasi('sk-konfirmasi-jalur', jalurLabel);

    const verifikatorWrap = document.getElementById('sk-konfirmasi-verifikator');
    if (verifikatorWrap) {
      const list = [
        `Level 1: ${v1}`,
        ...(v2 ? [`Level 2: ${v2}`] : []),
        ...(v3 ? [`Level 3: ${v3}`] : []),
      ];
      verifikatorWrap.innerHTML = list.map((item) => `<p class="text-xs font-medium text-slate-700">${item}</p>`).join('');
    }

    const catatanWrap = document.getElementById('sk-konfirmasi-catatan-wrap');
    const catatanEl = document.getElementById('sk-konfirmasi-catatan');
    if (catatan && catatanWrap && catatanEl) {
      catatanWrap.classList.remove('hidden');
      catatanEl.textContent = catatan;
    } else {
      catatanWrap?.classList.add('hidden');
    }

    showWizardStep(skSteps, skCircles, skLabels, 2);
  });
  document.getElementById('sk-proses-back-2')?.addEventListener('click', () => showWizardStep(skSteps, skCircles, skLabels, 1));
  document.getElementById('sk-proses-submit')?.addEventListener('click', () => {
    showToast('SK berhasil dikirim ke verifikator!', 'success');
  });
  document.getElementById('sk-proses-tolak-btn')?.addEventListener('click', () => {
    const catatan = document.getElementById('sk-catatan-admin')?.value.trim();
    if (!catatan) {
      showToast('Isi catatan revisi terlebih dahulu.', 'error');
      return;
    }
    showToast('SK dikembalikan ke pemohon untuk revisi.', 'info');
  });
});
