(() => {
  const buttons = document.querySelectorAll("[data-toggle-password]");
  if (!buttons.length) return;

  buttons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const inputId = btn.getAttribute("data-toggle-password");
      if (!inputId) return;

      const input = document.getElementById(inputId);
      if (!input) return;

      const nextType = input.type === "password" ? "text" : "password";
      input.type = nextType;

      const icon = btn.querySelector("i");
      if (icon) {
        icon.classList.toggle("fa-eye", nextType === "password");
        icon.classList.toggle("fa-eye-slash", nextType === "text");
      }

      btn.setAttribute(
        "aria-label",
        nextType === "password" ? "Show password" : "Hide password"
      );
    });
  });
})();

 

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('form[action="process-login.html"]') || document.querySelector('.auth-form');
    const registerForm = document.querySelector('form[action="process-register.html"]');
    const forgotForm = document.getElementById('forgot-password-form');
    const resetForm = document.getElementById('reset-password-form');

   
    if (loginForm && (window.location.pathname.includes('login.html') || window.location.pathname.includes('login.php'))) {
        loginForm.addEventListener('submit', async (e) => {
            const formData = new FormData(loginForm);
            
            try {
                
                const response = await fetch('../includes/auth_handler.php?action=login', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error('Handler not found');

                const result = await response.json();
                
                if (result.success) {
                    e.preventDefault();
                    showToast(result.message + ' Redirecting...');
                    setTimeout(() => {
                        window.location.href = (result.role === 'admin' || result.role === 'staff') 
                            ? '../admin/dashboard.php' 
                            : '../php/dashboard.php';
                    }, 1500);
                } else {
                    showToast(result.message);
                }
            } catch (error) {
                console.warn('AJAX Login handler not found, falling back to standard PHP POST.');
            }
        });
    }

   
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            
            if (formData.get('password') !== formData.get('confirm-password')) {
                showToast('Passwords do not match.');
                return;
            }

            try {
                const response = await fetch('includes/auth_handler.php?action=register', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message + ' Redirecting...');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    showToast(result.message);
                }
            } catch (error) {
                showToast('An error occurred. Please try again.');
                console.error('Auth Error:', error);
            }
        });
    }

    if (forgotForm) {
        const message = document.getElementById('forgot-message');
        const resetWrap = document.getElementById('forgot-reset-link');
        const resetAnchor = document.getElementById('forgot-reset-anchor');

        forgotForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = forgotForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            const formData = new FormData(forgotForm);

            submitButton.disabled = true;
            submitButton.innerHTML = 'Sending... <i class="fas fa-spinner fa-spin"></i>';
            message?.classList.add('is-hidden');
            resetWrap?.classList.add('is-hidden');

            try {
                const response = await fetch('../api/request_password_reset.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (message) {
                    message.className = 'auth-message ' + (result.success ? 'success' : 'error');
                    message.textContent = result.message;
                    message.classList.remove('is-hidden');
                }

                if (result.success && result.reset_link && resetAnchor && resetWrap) {
                    resetAnchor.href = result.reset_link;
                    resetWrap.classList.remove('is-hidden');
                }
            } catch (error) {
                if (message) {
                    message.className = 'auth-message error';
                    message.textContent = 'We could not process your reset request right now.';
                    message.classList.remove('is-hidden');
                }
            } finally {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    }

    if (resetForm) {
        const message = document.getElementById('reset-message');
        const tokenInput = document.getElementById('reset-token');
        const params = new URLSearchParams(window.location.search);
        const token = params.get('token') || '';

        if (tokenInput) {
            tokenInput.value = token;
        }

        if (!token) {
            if (message) {
                message.className = 'auth-message error';
                message.textContent = 'This password reset link is missing a token.';
                message.classList.remove('is-hidden');
            }
            resetForm.classList.add('is-hidden');
            return;
        }

        resetForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = resetForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            const formData = new FormData(resetForm);

            submitButton.disabled = true;
            submitButton.innerHTML = 'Saving... <i class="fas fa-spinner fa-spin"></i>';
            message?.classList.add('is-hidden');

            try {
                const response = await fetch('../api/reset_password.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (message) {
                    message.className = 'auth-message ' + (result.success ? 'success' : 'error');
                    message.textContent = result.message;
                    message.classList.remove('is-hidden');
                }

                if (result.success) {
                    resetForm.reset();
                    if (tokenInput) {
                        tokenInput.value = token;
                    }
                }
            } catch (error) {
                if (message) {
                    message.className = 'auth-message error';
                    message.textContent = 'We could not reset your password right now.';
                    message.classList.remove('is-hidden');
                }
            } finally {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    }
});
