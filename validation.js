document.addEventListener("DOMContentLoaded", () => {
  // Signup form validation
  const signupForm = document.querySelector("form.signup-form");
  if (signupForm) {
    signupForm.addEventListener("submit", (e) => {
      const u = signupForm.querySelector('input[name="username"]').value.trim();
      const p = signupForm.querySelector('input[name="password"]').value;
      const c = signupForm.querySelector(
        'input[name="confirm_password"]'
      ).value;

      const usernameOk = /^[a-zA-Z0-9_]{3,30}$/.test(u);
      if (!usernameOk) {
        alert("Username must be 3–30 chars, letters/numbers/_ only.");
        e.preventDefault();
        return;
      }
      if (p.length < 6) {
        alert("Password must be at least 6 characters.");
        e.preventDefault();
        return;
      }
      if (p !== c) {
        alert("Passwords do not match.");
        e.preventDefault();
        return;
      }
    });
  }

  // Login form validation
  const loginForm = document.querySelector("form.login-form");
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      const u = loginForm.querySelector('input[name="username"]').value.trim();
      const p = loginForm.querySelector('input[name="password"]').value;
      if (u.length < 1 || p.length < 1) {
        alert("Please fill both fields.");
        e.preventDefault();
      }
    });
  }

  // Services form validation
  const serviceForm = document.querySelector("form.service-form");
  if (serviceForm) {
    serviceForm.addEventListener("submit", (e) => {
      const w =
        parseInt(serviceForm.querySelector('input[name="washing"]').value) || 0;
      const i =
        parseInt(serviceForm.querySelector('input[name="ironing"]').value) || 0;
      const wi =
        parseInt(serviceForm.querySelector('input[name="washiron"]').value) ||
        0;
      const name =
        (serviceForm.querySelector('input[name="name"]') || {}).value || "";
      const phone =
        (serviceForm.querySelector('input[name="phone"]') || {}).value || "";
      const address =
        (serviceForm.querySelector('textarea[name="address"]') || {}).value ||
        "";

      if (w <= 0 && i <= 0 && wi <= 0) {
        alert("Please choose at least one service quantity.");
        e.preventDefault();
        return;
      }
      if (name.trim().length < 3) {
        alert("Please enter your name.");
        e.preventDefault();
        return;
      }
      if (!/^\d{7,15}$/.test(phone.trim())) {
        alert("Please enter a valid phone number (7-15 digits).");
        e.preventDefault();
        return;
      }
      if (address.trim().length < 5) {
        alert("Please enter a valid address.");
        e.preventDefault();
        return;
      }
    });
  }
});
