document.addEventListener('DOMContentLoaded', () => {

  // ================================================================
  // NAVBAR: mobile menu toggle
  // ================================================================
  const menuToggle = document.getElementById('menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => mobileMenu.classList.add('hidden'));
    });
  }

  // ================================================================
  // NAVBAR: tambah shadow saat scroll
  // ================================================================
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('navbar-scrolled', window.scrollY > 20);
    }, { passive: true });
  }

  // ================================================================
  // SMOOTH SCROLL untuk anchor links
  // ================================================================
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ================================================================
  // CAROUSEL
  // ================================================================
  const track      = document.getElementById('carousel-track');
  const container  = document.getElementById('carousel-container');
  const prevBtn    = document.getElementById('carousel-prev');
  const nextBtn    = document.getElementById('carousel-next');
  const dots       = document.querySelectorAll('.carousel-dot');
  const captions   = document.querySelectorAll('.slide-caption');

  if (!track) return; // Halaman tanpa carousel, stop di sini

  const TOTAL      = dots.length;   // jumlah slide
  const INTERVAL   = 4000;          // auto-play ms
  let current      = 0;
  let autoTimer    = null;
  let isPaused     = false;

  // ── Fungsi utama: pindah ke slide tertentu ──
  const goTo = (index) => {
    // Wrap index
    current = (index + TOTAL) % TOTAL;

    // Geser track
    track.style.transform = `translateX(-${current * 100}%)`;

    // Update dots
    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === current);
      // Reset semua dot ke kecil, aktifkan dot yang aktif
      if (i === current) {
        dot.classList.add('bg-blue-600');
        dot.classList.remove('bg-slate-300');
      } else {
        dot.classList.remove('bg-blue-600');
        dot.classList.add('bg-slate-300');
      }
    });

    // Update caption
    captions.forEach((cap, i) => {
      cap.classList.toggle('visible', i === current);
    });
  };

  // ── Auto-play ──
  const startAuto = () => {
    stopAuto();
    autoTimer = setInterval(() => {
      if (!isPaused) goTo(current + 1);
    }, INTERVAL);
  };

  const stopAuto = () => {
    if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
  };

  // ── Prev / Next ──
  prevBtn?.addEventListener('click', () => { goTo(current - 1); startAuto(); });
  nextBtn?.addEventListener('click', () => { goTo(current + 1); startAuto(); });

  // ── Dots ──
  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      goTo(parseInt(dot.dataset.dot));
      startAuto();
    });
  });

  // ── Pause on hover ──
  container?.addEventListener('mouseenter', () => { isPaused = true; });
  container?.addEventListener('mouseleave', () => { isPaused = false; });

  // ── Touch / swipe support (mobile) ──
  let touchStartX = 0;
  container?.addEventListener('touchstart', (e) => {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });
  container?.addEventListener('touchend', (e) => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) {
      goTo(diff > 0 ? current + 1 : current - 1);
      startAuto();
    }
  }, { passive: true });

  // ── Init ──
  goTo(0);
  startAuto();

});
