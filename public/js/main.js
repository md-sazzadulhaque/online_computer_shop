/* TechHub – Task 1 Client-side JS
   Student: 23-50573-1
   Covers: Registration validation, Login validation, Profile validation
*/

document.addEventListener('DOMContentLoaded', () => {

  /* ── Helpers ──────────────────────────────────────────────────────── */
  function showErr(fieldId, msg) {
    const el = document.getElementById(fieldId);
    if (!el) return;
    const grp = el.closest('.form-group');
    grp.classList.add('has-error');
    let span = grp.querySelector('.error-msg');
    if (!span) { span = document.createElement('span'); span.className = 'error-msg'; grp.appendChild(span); }
    span.textContent = msg;
  }

  function clearErr(fieldId) {
    const el = document.getElementById(fieldId);
    if (!el) return;
    const grp = el.closest('.form-group');
    grp.classList.remove('has-error');
    const span = grp.querySelector('.error-msg');
    if (span) span.textContent = '';
  }

  function isValidEmail(v) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  }

  /* ── Password Strength ────────────────────────────────────────────── */
  const pwInput = document.getElementById('password');
  const pwBar   = document.getElementById('pwStrength');
  if (pwInput && pwBar) {
    pwInput.addEventListener('input', () => {
      const v = pwInput.value;
      pwBar.className = 'pw-strength';
      if (v.length === 0) return;
      let score = 0;
      if (v.length >= 8)                   score++;
      if (/[A-Z]/.test(v))                 score++;
      if (/[0-9]/.test(v))                 score++;
      if (/[^A-Za-z0-9]/.test(v))          score++;
      if (score <= 1)      pwBar.classList.add('weak');
      else if (score === 2) pwBar.classList.add('medium');
      else                  pwBar.classList.add('strong');
    });
  }

  /* ── Registration Form ────────────────────────────────────────────── */
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    // Live validation
    ['name','email','password','confirm'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.addEventListener('blur', () => validateRegisterField(id));
    });

    registerForm.addEventListener('submit', e => {
      const ok = validateRegister();
      if (!ok) e.preventDefault();
    });

    function validateRegisterField(id) {
      const name     = document.getElementById('name')?.value.trim()    || '';
      const email    = document.getElementById('email')?.value.trim()   || '';
      const password = document.getElementById('password')?.value       || '';
      const confirm  = document.getElementById('confirm')?.value        || '';

      if (id === 'name') {
        if (name.length < 2) showErr('name', 'Name must be at least 2 characters.');
        else clearErr('name');
      }
      if (id === 'email') {
        if (!isValidEmail(email)) showErr('email', 'Please enter a valid email address.');
        else clearErr('email');
      }
      if (id === 'password') {
        if (password.length < 8) showErr('password', 'Password must be at least 8 characters.');
        else clearErr('password');
      }
      if (id === 'confirm') {
        if (confirm !== password) showErr('confirm', 'Passwords do not match.');
        else clearErr('confirm');
      }
    }

    function validateRegister() {
      let valid = true;
      const name     = document.getElementById('name')?.value.trim()    || '';
      const email    = document.getElementById('email')?.value.trim()   || '';
      const password = document.getElementById('password')?.value       || '';
      const confirm  = document.getElementById('confirm')?.value        || '';

      if (name.length < 2)       { showErr('name',     'Name must be at least 2 characters.');          valid = false; }
      else                         clearErr('name');

      if (!isValidEmail(email))  { showErr('email',    'Please enter a valid email address.');           valid = false; }
      else                         clearErr('email');

      if (password.length < 8)   { showErr('password', 'Password must be at least 8 characters.');      valid = false; }
      else                         clearErr('password');

      if (confirm !== password)  { showErr('confirm',  'Passwords do not match.');                       valid = false; }
      else                         clearErr('confirm');

      return valid;
    }
  }

  /* ── Login Form ───────────────────────────────────────────────────── */
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      let valid = true;
      const email    = document.getElementById('email')?.value.trim()  || '';
      const password = document.getElementById('password')?.value      || '';

      if (!isValidEmail(email)) { showErr('email',    'Please enter a valid email.'); valid = false; }
      else                        clearErr('email');

      if (!password)            { showErr('password', 'Password is required.');       valid = false; }
      else                        clearErr('password');

      if (!valid) e.preventDefault();
    });
  }

  /* ── Profile Form ─────────────────────────────────────────────────── */
  const profileForm = document.getElementById('profileForm');
  if (profileForm) {
    profileForm.addEventListener('submit', e => {
      let valid = true;
      const name    = document.getElementById('name')?.value.trim()          || '';
      const email   = document.getElementById('email')?.value.trim()         || '';
      const newPw   = document.getElementById('new_password')?.value         || '';
      const confirm = document.getElementById('confirm_password')?.value     || '';
      const current = document.getElementById('current_password')?.value     || '';
      const fileEl  = document.getElementById('profile_picture');

      if (name.length < 2)       { showErr('name',  'Name must be at least 2 characters.'); valid = false; }
      else                         clearErr('name');

      if (!isValidEmail(email))  { showErr('email', 'Please enter a valid email.');          valid = false; }
      else                         clearErr('email');

      // Only validate password fields if any of them are filled
      if (current || newPw || confirm) {
        if (!current)             { showErr('current_password',  'Enter your current password.');         valid = false; }
        else                        clearErr('current_password');

        if (newPw.length < 8)     { showErr('new_password',      'New password must be at least 8 chars.'); valid = false; }
        else                        clearErr('new_password');

        if (confirm !== newPw)    { showErr('confirm_password',  'Passwords do not match.');              valid = false; }
        else                        clearErr('confirm_password');
      }

      // File size check (client-side hint)
      if (fileEl && fileEl.files.length > 0) {
        const file = fileEl.files[0];
        const allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!allowed.includes(file.type)) {
          showErr('profile_picture', 'Only JPEG, PNG, GIF, WEBP images are allowed.'); valid = false;
        } else if (file.size > 2 * 1024 * 1024) {
          showErr('profile_picture', 'Image must be under 2 MB.'); valid = false;
        } else {
          clearErr('profile_picture');
        }
      }

      if (!valid) e.preventDefault();
    });

    // Live avatar preview
    const fileInput = document.getElementById('profile_picture');
    if (fileInput) {
      fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
          const existing = document.querySelector('.profile-avatar');
          if (existing) existing.src = ev.target.result;
        };
        reader.readAsDataURL(file);
      });
    }
  }

  /* ── Auto-dismiss flash messages after 4 s ────────────────────────── */
  const flash = document.querySelector('.flash');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .5s';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 500);
    }, 4000);
  }

});
