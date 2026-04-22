/**
 * app.js
 * Lokasi: resources/js/app.js
 *
 * Fitur:
 * - Carousel login (panel kanan)
 * - Toggle show/hide password
 * - Client-side validation form
 * - Loading state saat submit
 */

document.addEventListener('DOMContentLoaded', () => {

  // ================================================================
  // CAROUSEL LOGIN (panel kanan halaman login)
  // ================================================================
  (() => {
    const track     = document.getElementById('login-carousel-track');
    const container = document.getElementById('login-carousel-container');
    const dots      = document.querySelectorAll('.login-dot');
    const captions  = document.querySelectorAll('.login-caption');

    if (!track || dots.length === 0) return;

    const TOTAL    = dots.length;
    const INTERVAL = 4000;
    let current    = 0;
    let timer      = null;
    let isPaused   = false;

    const goTo = (index) => {
      current = (index + TOTAL) % TOTAL;

      // Geser track
      track.style.transform = `translateX(-${current * 100}%)`;

      // Update dots
      dots.forEach((dot, i) => {
        if (i === current) {
          dot.classList.add('active', 'bg-white/90');
          dot.classList.remove('bg-white/30');
        } else {
          dot.classList.remove('active', 'bg-white/90');
          dot.classList.add('bg-white/30');
        }
      });

      // Update caption
      captions.forEach((cap, i) => {
        cap.classList.toggle('visible', i === current);
      });
    };

    const startAuto = () => {
      if (timer) clearInterval(timer);
      timer = setInterval(() => {
        if (!isPaused) goTo(current + 1);
      }, INTERVAL);
    };

    // Pause saat hover
    container?.addEventListener('mouseenter', () => { isPaused = true; });
    container?.addEventListener('mouseleave', () => { isPaused = false; });

    // Dots click
    dots.forEach(dot => {
      dot.addEventListener('click', () => {
        goTo(parseInt(dot.dataset.loginDot));
        startAuto();
      });
    });

    // Touch swipe (mobile)
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

    // Init
    goTo(0);
    startAuto();
  })();


  // ================================================================
  // ELEMEN FORM LOGIN
  // ================================================================
  const form          = document.getElementById('login-form');
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  const toggleBtn     = document.getElementById('toggle-password');
  const iconEye       = document.getElementById('icon-eye');
  const iconEyeOff    = document.getElementById('icon-eye-off');
  const submitBtn     = document.getElementById('submit-btn');
  const btnText       = document.getElementById('btn-text');
  const btnSpinner    = document.getElementById('btn-spinner');
  const usernameError = document.getElementById('username-error');
  const passwordError = document.getElementById('password-error');


  // ================================================================
  // TOGGLE PASSWORD — show / hide
  // ================================================================
  if (toggleBtn && passwordInput) {
    toggleBtn.addEventListener('click', () => {
      const isHidden = passwordInput.type === 'password';
      passwordInput.type = isHidden ? 'text' : 'password';
      iconEye?.classList.toggle('hidden', isHidden);
      iconEyeOff?.classList.toggle('hidden', !isHidden);
      toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
  }


  // ================================================================
  // HELPER — tampilkan & sembunyikan pesan error per field
  // ================================================================
  const showError = (el, msg) => {
    if (!el) return;
    el.textContent = msg;
    el.classList.remove('hidden');
  };

  const clearError = (el) => {
    if (!el) return;
    el.textContent = '';
    el.classList.add('hidden');
  };

  const setInputState = (input, isError) => {
    if (!input) return;
    if (isError) {
      input.classList.add('border-red-300', 'bg-red-50/30', 'focus:border-red-400', 'focus:ring-red-100');
      input.classList.remove('border-slate-200', 'focus:border-blue-400', 'focus:ring-blue-100');
    } else {
      input.classList.remove('border-red-300', 'bg-red-50/30', 'focus:border-red-400', 'focus:ring-red-100');
      input.classList.add('border-slate-200', 'focus:border-blue-400', 'focus:ring-blue-100');
    }
  };


  // ================================================================
  // VALIDASI — client-side sebelum submit
  // ================================================================
  const validate = () => {
    let isValid = true;

    const username = usernameInput?.value.trim();
    if (!username) {
      showError(usernameError, 'Nama pengguna tidak boleh kosong.');
      setInputState(usernameInput, true);
      isValid = false;
    } else {
      clearError(usernameError);
      setInputState(usernameInput, false);
    }

    const password = passwordInput?.value;
    if (!password) {
      showError(passwordError, 'Kata sandi tidak boleh kosong.');
      setInputState(passwordInput, true);
      isValid = false;
    } else if (password.length < 6) {
      showError(passwordError, 'Kata sandi minimal 6 karakter.');
      setInputState(passwordInput, true);
      isValid = false;
    } else {
      clearError(passwordError);
      setInputState(passwordInput, false);
    }

    return isValid;
  };


  // ================================================================
  // CLEAR ERROR saat pengguna mulai mengetik
  // ================================================================
  usernameInput?.addEventListener('input', () => {
    clearError(usernameError);
    setInputState(usernameInput, false);
  });

  passwordInput?.addEventListener('input', () => {
    clearError(passwordError);
    setInputState(passwordInput, false);
  });


  // ================================================================
  // SUBMIT — validasi + loading state
  // ================================================================
  form?.addEventListener('submit', (e) => {
    const isValid = validate();
    if (!isValid) { e.preventDefault(); return; }
    if (submitBtn)  submitBtn.disabled = true;
    if (btnText)    btnText.textContent = 'Memproses...';
    if (btnSpinner) btnSpinner.classList.remove('hidden');
  });


  // ================================================================
  // AUTO-FOCUS — fokus ke field pertama saat halaman dimuat
  // ================================================================
  usernameInput?.focus();

});
