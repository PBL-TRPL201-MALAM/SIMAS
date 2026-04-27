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
  let localPreviewUrl = null;

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

    if (modalDownloadBtn) {
      const hasDownload = Boolean(data.downloadUrl);
      modalDownloadBtn.disabled = !hasDownload;
      modalDownloadBtn.classList.toggle('opacity-50', !hasDownload);
      modalDownloadBtn.classList.toggle('cursor-not-allowed', !hasDownload);
      modalDownloadBtn.dataset.downloadUrl = data.downloadUrl || '';
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
    const downloadUrl = modalDownloadBtn.dataset.downloadUrl || activeRowData?.downloadUrl;
    if (!downloadUrl) {
      showToast('File DOCX tidak tersedia untuk pengajuan ini.', 'error');
      return;
    }

    showToast('File DOCX sedang diunduh...', 'info');
    window.location.href = downloadUrl;
  });

  modalProsesBtn?.addEventListener('click', () => {
    if (!activeRowData) return;

    const isSk = (activeRowData.jenis || '').toLowerCase() === 'sk';
    const baseUrl = isSk ? modalProsesBtn.dataset.routeSk : modalProsesBtn.dataset.routeSurat;
    if (!baseUrl) return;

    const params = new URLSearchParams();
    if (activeRowData.dokumenId) params.set('dokumen', activeRowData.dokumenId);
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
        dokumenId: row.dataset.dokumenId,
        jenis: row.dataset.jenis,
        perihal: row.dataset.perihal,
        pemohon: row.dataset.pemohon,
        tanggal: row.dataset.tanggal,
        status: row.dataset.status,
        ringkasan: row.dataset.ringkasan,
        downloadUrl: detailBtn.dataset.downloadUrl,
      });
      return;
    }

    const prosesBtn = event.target.closest('.btn-proses');
    if (prosesBtn) {
      const row = prosesBtn.closest('tr');
      if (!row) return;
      const params = new URLSearchParams({
        dokumen: row.dataset.dokumenId || '',
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
        dokumen: row.dataset.dokumenId || '',
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
  const pdfFileSource = document.getElementById('pdf-file-source');
  const pdfFileRemove = document.getElementById('pdf-file-remove');

  const setPdfFile = (file, sourceLabel = 'PDF lokal') => {
    if (!file) return false;
    if (!file.name.toLowerCase().endsWith('.pdf')) {
      showToast('Hanya file PDF yang diperbolehkan.', 'error');
      return false;
    }
    if (file.size > 10 * 1024 * 1024) {
      showToast('Ukuran file PDF maksimal 10 MB.', 'error');
      return false;
    }

    if (pdfFileName) pdfFileName.textContent = file.name;
    if (pdfFileSource) pdfFileSource.textContent = sourceLabel;
    pdfPreview?.classList.remove('hidden');

    return true;
  };

  const restoreExistingPreviewInfo = () => {
    if (pdfFileName) pdfFileName.textContent = '';
    if (pdfFileSource) pdfFileSource.textContent = '';
    pdfPreview?.classList.add('hidden');
  };

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
  });

  pdfFileRemove?.addEventListener('click', () => {
    if (pdfInput) pdfInput.value = '';
    if (localPreviewUrl) {
      URL.revokeObjectURL(localPreviewUrl);
      localPreviewUrl = null;
    }
    restoreExistingPreviewInfo();
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

  const posisiConfig = document.getElementById('posisi-elemen-config');
  let updateProcessPreview = () => {};

  if (posisiConfig) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const pdfPreviewFrame = document.getElementById('pdf-preview-frame');
    const pdfPreviewStage = document.getElementById('pdf-preview-stage');
    const pdfPreviewEmpty = document.getElementById('pdf-preview-empty');
    const pdfPreviewCaption = document.getElementById('pdf-preview-caption');
    const pdfPreviewPageBadge = document.getElementById('pdf-preview-page-badge');
    const pdfClickOverlay = document.getElementById('pdf-click-overlay');
    const pdfMarkerLayer = document.getElementById('pdf-marker-layer');
    const pdfPageInput = document.getElementById('pdf-page-input');
    const pdfPageApply = document.getElementById('pdf-page-apply');
    const activeElementLabel = document.getElementById('aktif-elemen-label');
    const setElementButtons = Array.from(document.querySelectorAll('.btn-set-elemen'));
    const pdfZoomOut = document.getElementById('pdf-zoom-out');
    const pdfZoomIn = document.getElementById('pdf-zoom-in');
    const pdfZoomReset = document.getElementById('pdf-zoom-reset');
    const pdfZoomLabel = document.getElementById('pdf-zoom-label');
    const pdfScrollContainer = document.getElementById('pdf-scroll-container');
    const pdfPreviewCanvas = document.getElementById('pdf-preview-canvas');

    const markerPresets = {
      nomor_surat: { label: 'Nomor Surat', width: 220, height: 34, colorClass: 'border-blue-500 bg-blue-500/10 text-blue-600' },
      tanggal_surat: { label: 'Tanggal Surat', width: 180, height: 34, colorClass: 'border-emerald-500 bg-emerald-500/10 text-emerald-600' },
      tte: { label: 'QRCode / TTE', width: 80, height: 80, colorClass: 'border-amber-500 bg-amber-500/10 text-amber-600' },
    };

    const positionFields = {
      nomor_surat: {
        x: document.getElementById('input-nomor-surat-x'),
        y: document.getElementById('input-nomor-surat-y'),
      },
      tanggal_surat: {
        x: document.getElementById('input-tanggal-surat-x'),
        y: document.getElementById('input-tanggal-surat-y'),
      },
      tte: {
        x: document.getElementById('input-tte-x'),
        y: document.getElementById('input-tte-y'),
        w: document.getElementById('input-tte-w'),
        h: document.getElementById('input-tte-h'),
      },
    };

    const A4_RATIO = 1.4142;
    const ZOOM_MIN = 0.75;
    const ZOOM_MAX = 2;
    const ZOOM_STEP = 0.25;

    const saveUrl = posisiConfig.dataset.saveUrl || '';
    const startStep = Math.max(1, Math.min(3, Number(posisiConfig.dataset.startStep || 1)));
    const existingPreviewUrl = posisiConfig.dataset.previewUrl || '';
    const existingPreviewName = posisiConfig.dataset.previewName || 'PDF hasil pemeriksaan';
    let existingPositions = {};

    try {
      existingPositions = JSON.parse(posisiConfig.dataset.existingPositions || '{}');
    } catch (error) {
      existingPositions = {};
    }

    const savedPositions = { ...existingPositions };
    let activeElement = null;
    let currentPage = Math.max(1, Number(pdfPageInput?.value || 1));
    let currentPreviewUrl = existingPreviewUrl;
    let zoomLevel = 1;
    let dragState = null;
    let suppressOverlayClick = false;

    const getBaseSize = () => {
      const containerWidth = pdfScrollContainer?.clientWidth || 900;
      const baseWidth = Math.max(620, Math.min(containerWidth - 32, 980));

      return {
        width: baseWidth,
        height: Math.round(baseWidth * A4_RATIO),
      };
    };

    const updatePageBadge = () => {
      if (pdfPreviewPageBadge) {
        pdfPreviewPageBadge.textContent = `Halaman ${currentPage}`;
      }
    };

    const updateZoomLabel = () => {
      if (pdfZoomLabel) {
        pdfZoomLabel.textContent = `${Math.round(zoomLevel * 100)}%`;
      }
    };

    const getElementDimensions = (elemen) => {
      const preset = markerPresets[elemen];
      const fields = positionFields[elemen];

      return {
        width: Math.max(1, Math.round(Number(fields?.w?.value || savedPositions[elemen]?.lebar || preset.width || 1))),
        height: Math.max(1, Math.round(Number(fields?.h?.value || savedPositions[elemen]?.tinggi || preset.height || 1))),
      };
    };

    const clampPosition = (elemen, x, y) => {
      const baseSize = getBaseSize();
      const dimensions = getElementDimensions(elemen);

      return {
        x: Math.max(0, Math.min(Math.round(Number(x) || 0), Math.max(0, baseSize.width - dimensions.width))),
        y: Math.max(0, Math.min(Math.round(Number(y) || 0), Math.max(0, baseSize.height - dimensions.height))),
      };
    };

    const getPreviewUrlForPage = () => {
      if (!currentPreviewUrl) return '';
      return `${currentPreviewUrl}#page=${currentPage}&toolbar=0&navpanes=0&scrollbar=0&view=FitH`;
    };

    const updatePreviewLayout = () => {
      if (!pdfPreviewCanvas || !pdfPreviewFrame) return;

      const baseSize = getBaseSize();
      const width = Math.round(baseSize.width * zoomLevel);
      const height = Math.round(baseSize.height * zoomLevel);

      pdfPreviewCanvas.style.width = `${width}px`;
      pdfPreviewCanvas.style.height = `${height}px`;
      pdfPreviewFrame.style.width = `${width}px`;
      pdfPreviewFrame.style.height = `${height}px`;
      updateZoomLabel();
    };

    const syncFieldValues = () => {
      Object.entries(savedPositions).forEach(([elemen, posisi]) => {
        const fields = positionFields[elemen];
        const preset = markerPresets[elemen];
        if (!fields || !posisi) return;

        if (fields.x) fields.x.value = Math.round(Number(posisi.posisi_x) || 0);
        if (fields.y) fields.y.value = Math.round(Number(posisi.posisi_y) || 0);
        if (fields.w) fields.w.value = Math.round(Number(posisi.lebar) || preset.width || 0);
        if (fields.h) fields.h.value = Math.round(Number(posisi.tinggi) || preset.height || 0);
      });
    };

    const updateStatusText = () => {
      Object.entries(markerPresets).forEach(([elemen]) => {
        const statusEl = document.getElementById(`status-elemen-${elemen}`);
        const posisi = savedPositions[elemen];
        if (!statusEl) return;

        if (!posisi) {
          statusEl.textContent = 'Belum ditentukan';
          statusEl.className = 'text-[10px] text-slate-400 font-light mt-1';
          return;
        }

        statusEl.textContent = `Tersimpan di halaman ${posisi.halaman}, X: ${Math.round(Number(posisi.posisi_x))}, Y: ${Math.round(Number(posisi.posisi_y))}`;
        statusEl.className = 'text-[10px] text-blue-600 font-light mt-1';
      });
    };

    const getMarkerDisplayText = (elemen) => {
      if (elemen === 'nomor_surat') {
        return document.querySelector('[name="nomor_surat"]')?.value?.trim() || 'Nomor Surat';
      }

      if (elemen === 'tanggal_surat') {
        const rawDate = document.querySelector('[name="tanggal_surat"]')?.value?.trim();
        if (!rawDate) return 'Tanggal Surat';

        const parsedDate = new Date(rawDate);
        if (Number.isNaN(parsedDate.getTime())) {
          return rawDate;
        }

        return new Intl.DateTimeFormat('id-ID', {
          day: '2-digit',
          month: 'long',
          year: 'numeric',
        }).format(parsedDate);
      }

      return '';
    };

    const renderMarkers = () => {
      if (!pdfMarkerLayer) return;
      pdfMarkerLayer.innerHTML = '';

      Object.entries(savedPositions).forEach(([elemen, posisi]) => {
        if (!posisi || Number(posisi.halaman) !== currentPage) return;

        const preset = markerPresets[elemen];
        const marker = document.createElement('div');
        const width = getElementDimensions(elemen).width * zoomLevel;
        const height = getElementDimensions(elemen).height * zoomLevel;

        const isTextMarker = elemen === 'nomor_surat' || elemen === 'tanggal_surat';
        const isQrMarker = elemen === 'tte';
        marker.className = isTextMarker
          ? 'pointer-events-auto absolute cursor-move select-none overflow-visible'
          : 'pointer-events-auto absolute cursor-move select-none overflow-visible';
        marker.dataset.elemen = elemen;
        marker.style.left = `${Number(posisi.posisi_x) * zoomLevel}px`;
        marker.style.top = `${Number(posisi.posisi_y) * zoomLevel}px`;
        marker.style.width = `${width}px`;
        marker.style.height = `${height}px`;

        const label = document.createElement('span');
        label.className = 'absolute -top-6 left-0 rounded-md bg-white/95 px-2 py-1 text-[10px] font-semibold shadow-sm';
        label.textContent = preset.label;

        if (isTextMarker) {
          const textNode = document.createElement('span');
          textNode.className = 'pointer-events-none inline-block whitespace-nowrap bg-transparent text-[14px] font-semibold text-red-500';
          textNode.textContent = getMarkerDisplayText(elemen);
          marker.appendChild(textNode);
        }

        if (isQrMarker) {
          const qrDummy = document.createElement('div');
          qrDummy.className = 'pointer-events-none flex h-full w-full items-center justify-center rounded-md bg-red-100/35 p-1 ring-1 ring-red-300/70';
          qrDummy.innerHTML = `
            <div class="grid h-full w-full grid-cols-7 gap-[2px] rounded-sm bg-white/70 p-1">
              ${Array.from({ length: 49 }, (_, index) => {
                const filled = [
                  0, 1, 2, 4, 5, 6,
                  7, 10, 12, 13,
                  14, 16, 18, 20,
                  21, 24, 26, 27,
                  28, 30, 32, 34,
                  35, 36, 38, 40, 41,
                  42, 43, 44, 46, 47, 48,
                ];
                return `<span class="${filled.includes(index) ? 'bg-red-500/80' : 'bg-red-100/20'} rounded-[1px]"></span>`;
              }).join('')}
            </div>
          `;
          marker.appendChild(qrDummy);
        }

        marker.appendChild(label);

        marker.addEventListener('pointerdown', (event) => {
          if (!pdfClickOverlay) return;

          const markerRect = marker.getBoundingClientRect();
          dragState = {
            elemen,
            startX: event.clientX,
            startY: event.clientY,
            offsetX: event.clientX - markerRect.left,
            offsetY: event.clientY - markerRect.top,
            moved: false,
          };

          marker.setPointerCapture?.(event.pointerId);
          event.stopPropagation();
          event.preventDefault();
        });

        marker.addEventListener('click', (event) => {
          event.stopPropagation();
          if (suppressOverlayClick) {
            event.preventDefault();
          }
        });

        pdfMarkerLayer.appendChild(marker);
      });
    };

    updateProcessPreview = ({ reloadFrame = true } = {}) => {
      updatePageBadge();
      updatePreviewLayout();

      if (!currentPreviewUrl) {
        pdfPreviewStage?.classList.add('hidden');
        pdfPreviewEmpty?.classList.remove('hidden');
        if (pdfPreviewCaption) {
          pdfPreviewCaption.textContent = 'Unggah PDF di langkah 1 untuk mulai mengatur posisi elemen.';
        }
        renderMarkers();
        return;
      }

      if (reloadFrame) {
        pdfPreviewFrame?.setAttribute('src', getPreviewUrlForPage());
      }

      pdfPreviewStage?.classList.remove('hidden');
      pdfPreviewEmpty?.classList.add('hidden');
      if (pdfPreviewCaption) {
        pdfPreviewCaption.textContent = 'Scroll vertikal di area preview jika perlu, lalu klik area PDF untuk menyimpan posisi elemen aktif.';
      }
      renderMarkers();
    };

    const setActiveElement = (elemen) => {
      activeElement = elemen;
      const preset = markerPresets[elemen];

      setElementButtons.forEach((button) => {
        const isActive = button.dataset.elemen === elemen;
        button.classList.toggle('border-blue-400', isActive);
        button.classList.toggle('bg-blue-50', isActive);
      });

      if (activeElementLabel) {
        activeElementLabel.textContent = `${preset.label} aktif. Klik area preview PDF untuk menyimpan posisinya.`;
      }
    };

    const savePosition = async (payload) => {
      const response = await axios.post(saveUrl, payload, {
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
        },
      });

      return response.data?.data;
    };

    const setZoom = (nextZoom) => {
      const boundedZoom = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, nextZoom));
      zoomLevel = Number(boundedZoom.toFixed(2));
      updateProcessPreview({ reloadFrame: false });
    };

    setElementButtons.forEach((button) => {
      button.addEventListener('click', () => setActiveElement(button.dataset.elemen));
    });

    pdfPageApply?.addEventListener('click', () => {
      const requestedPage = Math.max(1, Number(pdfPageInput?.value || 1));
      currentPage = requestedPage;
      if (pdfPageInput) pdfPageInput.value = requestedPage;
      updateProcessPreview();
    });

    pdfPageInput?.addEventListener('keydown', (event) => {
      if (event.key !== 'Enter') return;
      event.preventDefault();
      pdfPageApply?.click();
    });

    pdfZoomOut?.addEventListener('click', () => setZoom(zoomLevel - ZOOM_STEP));
    pdfZoomIn?.addEventListener('click', () => setZoom(zoomLevel + ZOOM_STEP));
    pdfZoomReset?.addEventListener('click', () => setZoom(1));

    pdfClickOverlay?.addEventListener('click', async (event) => {
      if (suppressOverlayClick) {
        suppressOverlayClick = false;
        return;
      }

      if (!currentPreviewUrl) {
        showToast('Upload PDF terlebih dahulu sebelum mengatur posisi elemen.', 'error');
        return;
      }

      if (!activeElement) {
        showToast('Pilih elemen yang ingin diatur terlebih dahulu.', 'error');
        return;
      }

      const rect = pdfClickOverlay.getBoundingClientRect();
      const scaledX = Math.max(0, event.clientX - rect.left);
      const scaledY = Math.max(0, event.clientY - rect.top);
      const nextPosition = clampPosition(activeElement, scaledX / zoomLevel, scaledY / zoomLevel);

      try {
        const saved = await savePosition({
          elemen: activeElement,
          halaman: currentPage,
          posisi_x: nextPosition.x,
          posisi_y: nextPosition.y,
          lebar: getElementDimensions(activeElement).width,
          tinggi: getElementDimensions(activeElement).height,
        });

        savedPositions[activeElement] = saved;
        syncFieldValues();
        updateStatusText();
        renderMarkers();
        showToast(`${preset.label} berhasil disimpan.`, 'success');
      } catch (error) {
        const message = error.response?.data?.message || 'Posisi elemen gagal disimpan.';
        showToast(message, 'error');
      }
    });

    window.addEventListener('pointermove', (event) => {
      if (!dragState || !pdfClickOverlay) return;

      const overlayRect = pdfClickOverlay.getBoundingClientRect();
      const nextPosition = clampPosition(
        dragState.elemen,
        (event.clientX - overlayRect.left - dragState.offsetX) / zoomLevel,
        (event.clientY - overlayRect.top - dragState.offsetY) / zoomLevel,
      );

      const dimensions = getElementDimensions(dragState.elemen);
      savedPositions[dragState.elemen] = {
        ...(savedPositions[dragState.elemen] || {}),
        elemen: dragState.elemen,
        halaman: currentPage,
        posisi_x: nextPosition.x,
        posisi_y: nextPosition.y,
        lebar: dimensions.width,
        tinggi: dimensions.height,
      };

      dragState.moved = dragState.moved || Math.abs(event.clientX - dragState.startX) > 2 || Math.abs(event.clientY - dragState.startY) > 2;
      syncFieldValues();
      updateStatusText();
      renderMarkers();
      event.preventDefault();
    });

    const finishMarkerDrag = async () => {
      if (!dragState) return;

      const { elemen, moved } = dragState;
      dragState = null;

      if (!moved) return;

      suppressOverlayClick = true;
      window.setTimeout(() => {
        suppressOverlayClick = false;
      }, 150);

      try {
        const posisi = savedPositions[elemen];
        const dimensions = getElementDimensions(elemen);
        const saved = await savePosition({
          elemen,
          halaman: currentPage,
          posisi_x: posisi.posisi_x,
          posisi_y: posisi.posisi_y,
          lebar: dimensions.width,
          tinggi: dimensions.height,
        });

        savedPositions[elemen] = saved;
        syncFieldValues();
        updateStatusText();
        renderMarkers();
      } catch (error) {
        const message = error.response?.data?.message || 'Posisi elemen gagal diperbarui.';
        showToast(message, 'error');
      }
    };

    window.addEventListener('pointerup', finishMarkerDrag);
    window.addEventListener('pointercancel', finishMarkerDrag);

    ['nomor_surat', 'tanggal_surat'].forEach((fieldName) => {
      document.querySelector(`[name="${fieldName}"]`)?.addEventListener('input', () => {
        renderMarkers();
      });
    });

    const initializeExistingPreview = () => {
      if (!existingPreviewUrl) return;
      if (pdfFileName) pdfFileName.textContent = existingPreviewName;
      if (pdfFileSource) pdfFileSource.textContent = 'Tersedia di server';
      pdfPreview?.classList.remove('hidden');
    };

    const useLocalPreview = (file) => {
      if (!file || !setPdfFile(file)) {
        if (pdfInput) pdfInput.value = '';
        return;
      }

      if (localPreviewUrl) {
        URL.revokeObjectURL(localPreviewUrl);
      }

      localPreviewUrl = URL.createObjectURL(file);
      currentPreviewUrl = localPreviewUrl;
      updateProcessPreview();
    };

    pdfInput?.addEventListener('change', () => {
      const file = pdfInput.files?.[0];
      if (file) useLocalPreview(file);
    });

    pdfDropZone?.addEventListener('drop', () => {
      const file = pdfInput?.files?.[0];
      if (file) useLocalPreview(file);
    });

    pdfFileRemove?.addEventListener('click', () => {
      currentPreviewUrl = existingPreviewUrl || '';

      if (existingPreviewUrl) {
        initializeExistingPreview();
      } else {
        restoreExistingPreviewInfo();
      }

      updateProcessPreview();
    });

    window.addEventListener('resize', () => updateProcessPreview({ reloadFrame: false }));

    initializeExistingPreview();
    syncFieldValues();
    updateStatusText();
    updateProcessPreview();

    if (startStep > 1) {
      showWizardStep(prosesSteps, prosesCircles, prosesLabels, startStep - 1);
      updateProcessPreview({ reloadFrame: false });
    }
  }

  document.getElementById('proses-next-1')?.addEventListener('click', () => {
    if (posisiConfig && !((pdfInput?.files?.length || 0) > 0 || posisiConfig.dataset.previewUrl)) {
      showToast('Upload PDF terlebih dahulu sebelum lanjut ke pengaturan posisi.', 'error');
      return;
    }

    showWizardStep(prosesSteps, prosesCircles, prosesLabels, 1);
    updateProcessPreview({ reloadFrame: false });
  });

  document.getElementById('proses-back-1')?.addEventListener('click', () => showWizardStep(prosesSteps, prosesCircles, prosesLabels, 0));
  document.getElementById('proses-next-2')?.addEventListener('click', () => showWizardStep(prosesSteps, prosesCircles, prosesLabels, 2));
  document.getElementById('proses-back-2')?.addEventListener('click', () => showWizardStep(prosesSteps, prosesCircles, prosesLabels, 1));
  document.getElementById('proses-submit')?.addEventListener('click', () => {
    const submitButton = document.getElementById('proses-submit');
    if (submitButton?.type === 'submit') return;
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
