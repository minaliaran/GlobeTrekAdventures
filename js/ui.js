

(function() {
    'use strict';

    window.UI = {
        
        openModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        },

        closeModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        },

        toggleModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('active');
            }
        },

      
        openLightbox: function(imageUrl, modalId = 'lightbox') {
            const modal = document.getElementById(modalId);
            const img = modal?.querySelector('img');
            if (img) {
                img.src = imageUrl;
                modal.classList.add('active');
            }
        },

        closeLightbox: function(modalId = 'lightbox') {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
            }
        },

        
        addClass: function(element, className) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            element?.classList.add(className);
        },

        removeClass: function(element, className) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            element?.classList.remove(className);
        },

        toggleClass: function(element, className) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            element?.classList.toggle(className);
        },

        hasClass: function(element, className) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            return element?.classList.contains(className) || false;
        },

       
        setText: function(selector, text) {
            const element = document.querySelector(selector);
            if (element) element.textContent = text;
        },

        setHTML: function(selector, html) {
            const element = document.querySelector(selector);
            if (element) element.innerHTML = html;
        },

        getText: function(selector) {
            return document.querySelector(selector)?.textContent || '';
        },

        
        setAttribute: function(selector, attr, value) {
            const element = document.querySelector(selector);
            if (element) element.setAttribute(attr, value);
        },

        getAttribute: function(selector, attr) {
            return document.querySelector(selector)?.getAttribute(attr) || '';
        },

        
        show: function(selector) {
            const element = document.querySelector(selector);
            if (element) element.style.display = 'block';
        },

        hide: function(selector) {
            const element = document.querySelector(selector);
            if (element) element.style.display = 'none';
        },

        toggle: function(selector) {
            const element = document.querySelector(selector);
            if (element) {
                element.style.display = element.style.display === 'none' ? 'block' : 'none';
            }
        },

        
        getFormData: function(formSelector) {
            const form = document.querySelector(formSelector);
            if (!form) return null;
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            return data;
        },

        resetForm: function(formSelector) {
            const form = document.querySelector(formSelector);
            if (form) form.reset();
        },

        validateEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

       
        showAlert: function(message, type = 'info', containerId = 'alerts') {
            const container = document.getElementById(containerId);
            if (!container) return;

            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <span>${message}</span>
                <button class="close-btn" onclick="this.parentElement.remove()">×</button>
            `;
            container.appendChild(alert);

            setTimeout(() => alert.remove(), 5000);
        },

        
        scrollToElement: function(selector, offset = 0) {
            const element = document.querySelector(selector);
            if (element) {
                const top = element.offsetTop - offset;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        },

        scrollToTop: function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        
        setLoading: function(element, isLoading = true) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            if (isLoading) {
                element?.setAttribute('disabled', 'disabled');
                element?.classList.add('loading');
            } else {
                element?.removeAttribute('disabled');
                element?.classList.remove('loading');
            }
        },

        fadeIn: function(element, duration = 300) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            element.style.opacity = '0';
            element.style.display = 'block';
            let opacity = 0;
            const interval = setInterval(() => {
                opacity += 0.05;
                element.style.opacity = opacity.toString();
                if (opacity >= 1) clearInterval(interval);
            }, duration / 20);
        },

        fadeOut: function(element, duration = 300) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            let opacity = 1;
            const interval = setInterval(() => {
                opacity -= 0.05;
                element.style.opacity = opacity.toString();
                if (opacity <= 0) {
                    element.style.display = 'none';
                    clearInterval(interval);
                }
            }, duration / 20);
        },

        
        on: function(selector, event, callback) {
            document.addEventListener(event, (e) => {
                if (e.target.matches(selector)) {
                    callback(e);
                }
            });
        },

      
        initLazyLoad: function() {
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            });
            images.forEach(img => imageObserver.observe(img));
        }
    };

    
    if (document.readyState !== 'loading') {
        UI.initLazyLoad();
    } else {
        document.addEventListener('DOMContentLoaded', () => UI.initLazyLoad());
    }

})();
