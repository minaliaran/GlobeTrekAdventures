

(function () {
    'use strict';

    const SITE_CONTACT = {
        branchLabel: 'Main Branch',
        city: 'Negombo, Sri Lanka',
        phone: '+94 11 234 5678',
        email: 'hello@globetrek.lk'
    };

  
    const DESTINATION_ROUTE_MAP = {
        anuradhapura: { folder: 'anuradhapura', page: 'anuradhapura.html' },
        arugambay: { folder: 'arugambay', page: 'arugambay.html' },
        badulla: { folder: 'badulla', page: 'badulla.html' },
        bentota: { folder: 'bentota', page: 'bentota.html' },
        colombo: { folder: 'colombo', page: 'colombo.html' },
        dambulla: { folder: 'dambulla', page: 'dambulla.html' },
        ella: { folder: 'ella', page: 'ella.html' },
        galle: { folder: 'galle', page: 'galle.html' },
        haputale: { folder: 'haputale', page: 'haputale.html' },
        hikkaduwa: { folder: 'hikkaduwa', page: 'hikkaduwa.html' },
        kalpitiya: { folder: 'kalpitiya', page: 'kalpitiya.html' },
        kandy: { folder: 'kandy', page: 'kandy.html' },
        mirissa: { folder: 'mirissa', page: 'mirissa.html' },
        negambo: { folder: 'negambo', page: 'negombo.html' },
        negombo: { folder: 'negambo', page: 'negombo.html' },
        nuwaraeliya: { folder: 'nuwaraeliya', page: 'nuwaraeliya.html' },
        pasikuda: { folder: 'pasikuda', page: 'pasikuda.html' },
        polonnaruwa: { folder: 'polonnaruwa', page: 'polonnaruwa.html' },
        ratnapura: { folder: 'ratnapura', page: 'ratnapura.html' },
        sigiriya: { folder: 'sigiriya', page: 'sigiriya.html' },
        thrincomalee: { folder: 'thrincomalee', page: 'trincomalee.html' },
        trincomalee: { folder: 'thrincomalee', page: 'trincomalee.html' },
        udawalawe: { folder: 'udawalawe', page: 'udawalawe.html' },
        yala: { folder: 'yala', page: 'yala.html' }
    };

    const DESTINATION_FOLDERS = new Set(Object.values(DESTINATION_ROUTE_MAP).map(r => r.folder));
    DESTINATION_FOLDERS.add('thingstodo');

    function getPagePrefix() {
        const path = window.location.pathname.replace(/\\/g, '/');
        
        // Match /globeTrekadventures/ case-insensitively for XAMPP / local Apache environments
        const rootMatch = path.match(/\/globetrekadventures\//i);
        if (rootMatch) {
            const afterRoot = path.substring(rootMatch.index + rootMatch[0].length);
            const depth = (afterRoot.match(/\//g) || []).length;
            return depth > 0 ? '../'.repeat(depth) : '';
        }
        
        // Fallback: If hosted directly at root or virtual host, calculate depth from path segments
        const segments = path.split('/').filter(Boolean);
        if (segments.length > 1) {
            return '../'.repeat(segments.length - 1);
        }
        return '';
    }

    function resolveDestinationPath(slug, prefix = '') {
        const normalized = (slug || '')
            .toLowerCase()
            .trim()
            .replace(/\\/g, '/')
            .split('/')
            .pop()
            .replace(/\.html$/i, '')
            .replace(/\s+/g, '-');

        const route = DESTINATION_ROUTE_MAP[normalized];
        if (route) {
            return `${prefix}destinations/${route.folder}/${route.page}`;
        }

        return normalized ? `${prefix}destinations/${normalized}/${normalized}.html` : `${prefix}destinations/destinations.html`;
    }

    function getCurrentPageKey() {
        const pathname = (window.location.pathname || '').toLowerCase();
        const normalizedPath = pathname.replace(/\\/g, '/');
        const page = normalizedPath.split('/').pop() || 'index.php';

        if (page === '' || page === 'index.php') return 'home';
        if (page === 'about.html' || page === 'about.php') return 'about';
        if (page === 'destinations.html' || page === 'things-to-do.html' || page === 'packages.html' || page === 'accommodation.html' || page === 'packages.php' || page === 'accommodation.php') return 'explore';
        if (page === 'gallery.html') return 'gallery';
        if (page === 'transport.html') return 'transport';
        if (page === 'guide.html') return 'guide';
        if (page === 'contact.html') return 'contact';
        if (page === 'booking.html' || page === 'booking.php') return 'booking';
        if (page === 'login.php' || page === 'login.html') return 'login';
        if (page === 'register.php' || page === 'register.html') return 'register';

        const segments = normalizedPath.split('/').filter(Boolean);
        const parentFolder = segments.length > 1 ? segments[segments.length - 2] : '';

        if (DESTINATION_FOLDERS.has(parentFolder) || parentFolder === 'thingstodo' || parentFolder === 'packages') {
            return 'explore';
        }

        return '';
    }

    function applyActiveNavState() {
        const currentPageKey = getCurrentPageKey();
        if (!currentPageKey) return;

        const isServicePage = ['transport', 'guide', 'booking', 'contact'].includes(currentPageKey);

        document.querySelectorAll('#navbar [data-nav-key]').forEach((link) => {
            const key = link.dataset.navKey;
            const isActive = (key === currentPageKey) || (key === 'services' && isServicePage);
            link.classList.toggle('active', isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    window.GlobeTrekRoutes = {
        getPagePrefix,
        resolveDestinationPath
    };

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        if (!toast) return;
        toast.textContent = message;
        toast.className = 'toast show ' + type;
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    function ensureFontAwesome() {
        const hasFontAwesome = Array.from(document.styleSheets).some((sheet) => {
            const href = sheet.href || '';
            return href.includes('font-awesome') || href.includes('fontawesome');
        });
        if (hasFontAwesome) return;
        const faLink = document.createElement('link');
        faLink.rel = 'stylesheet';
        faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
        document.head.appendChild(faLink);
    }

    function ensureFavicon() {
        const svgIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                <rect width="64" height="64" rx="14" fill="#0b0b0b"/>
                <circle cx="32" cy="32" r="22" fill="none" stroke="#10b981" stroke-width="4"/>
                <text x="32" y="40"
                      text-anchor="middle"
                      font-family="Inter, Segoe UI, Arial, sans-serif"
                      font-size="22"
                      font-weight="800"
                      fill="#ffffff">GT</text>
            </svg>
        `.trim();
        const dataUrl = `data:image/svg+xml,${encodeURIComponent(svgIcon)}`;

        let favicon = document.querySelector('link[rel="icon"]');
        if (!favicon) {
            favicon = document.createElement('link');
            favicon.setAttribute('rel', 'icon');
            document.head.appendChild(favicon);
        }
        favicon.setAttribute('type', 'image/svg+xml');
        favicon.setAttribute('href', dataUrl);
    }

    function ensureResponsiveCSS() {
        const hasResponsive = Array.from(document.styleSheets).some((sheet) => {
            const href = sheet.href || '';
            return href.includes('responsive.css');
        });
        if (hasResponsive) return;
        const respLink = document.createElement('link');
        respLink.rel = 'stylesheet';
        respLink.href = getPagePrefix() + 'css/responsive.css';
        document.head.appendChild(respLink);
    }



    function renderStandardFooter(prefix) {
        return `
            <div class="footer-top">
                <div class="footer-brand">
                    <div class="footer-logo-wrap">
                        <img src="${prefix}images/logo.jpg" alt="GlobeTrek" class="footer-logo">
                        <span class="footer-brand-name">GlobeTrek</span>
                    </div>
                    <p>Your trusted travel partner for exploring the beautiful island of Sri Lanka. Discover hidden gems and unforgettable adventures with us.</p>
                    <div class="footer-socials">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Destinations</h4>
                    <ul>
                        <li><a href="${prefix}destinations/sigiriya/sigiriya.html">Sigiriya Rock</a></li>
                        <li><a href="${prefix}destinations/ella/ella.html">Ella Highlands</a></li>
                        <li><a href="${prefix}destinations/kandy/kandy.html">Kandy City</a></li>
                        <li><a href="${prefix}destinations/galle/galle.html">Galle Fort</a></li>
                        <li><a href="${prefix}destinations/mirissa/mirissa.html">Mirissa Beach</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="${prefix}index.php">Home</a></li>
                        <li><a href="${prefix}php/about.php">About Us</a></li>
                        <li><a href="${prefix}php/packages.php">Tour Packages</a></li>
                        <li><a href="${prefix}gallery.html">Gallery</a></li>
                        <li><a href="${prefix}contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> ${SITE_CONTACT.city}</li>
                        <li><i class="fas fa-phone"></i> ${SITE_CONTACT.phone}</li>
                        <li><i class="fas fa-envelope"></i> ${SITE_CONTACT.email}</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; ${new Date().getFullYear()} GlobeTrek Adventures • Designed by Minali Arandara</p>
            </div>`;
    }

    function standardizePageShell() {
        const prefix = getPagePrefix();

        
        const navbar = document.getElementById('navbar');
        if (navbar && !navbar.dataset.standardized) {
            navbar.dataset.standardized = 'true';
            navbar.style.display = 'flex';
            navbar.style.visibility = 'visible';
            navbar.style.opacity = '1';

            navbar.innerHTML = `
                <div class="nav-left-group">
                    <div class="nav-logo">
                        <a href="${prefix}index.php" class="logo-link">
                            <img src="${prefix}images/logo.jpg" alt="Logo" class="logo-img">
                        </a>
                    </div>
                    <nav class="nav-menu" id="nav-menu">
                        <div class="nav-links-wrapper">
                            <ul class="nav-links">
                                <li><a href="${prefix}index.php" data-nav-key="home">Home</a></li>
                                <li><a href="${prefix}php/about.php" data-nav-key="about">About</a></li>
                                <li class="dropdown">
                                    <a href="#" data-nav-key="explore">Explore</a>
                                    <div class="dropdown-panel">
                                        <ul>
                                            <li><a href="${prefix}destinations/destinations.html">Destinations</a></li>
                                            <li><a href="${prefix}thingstodo/things-to-do.html">Things to Do</a></li>
                                            <li><a href="${prefix}php/packages.php">Packages</a></li>
                                            <li><a href="${prefix}php/accommodation.php">Accommodation</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li><a href="${prefix}gallery.html" data-nav-key="gallery">Gallery</a></li>
                                <li class="dropdown">
                                    <a href="#" data-nav-key="services">Services</a>
                                    <div class="dropdown-panel">
                                        <ul>
                                            <li><a href="${prefix}transport.html" data-nav-key="transport">Transportation</a></li>
                                            <li><a href="${prefix}guide.html" data-nav-key="guide">Travel Guide</a></li>
                                            <li><a href="${prefix}php/booking.php" data-nav-key="booking">Booking</a></li>
                                            <li><a href="${prefix}contact.html" data-nav-key="contact">Contact</a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>

                <div class="nav-search-central">
                    <div class="nav-search-trigger" id="search-btn">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <div class="nav-actions" id="nav-actions">
                    <div id="auth-links">
                        <a href="${prefix}php/login.php" class="nav-btn-ghost">Login</a>
                        <a href="${prefix}php/register.php" class="nav-btn-ghost">Register</a>
                    </div>
                    <button class="btn-hamburger" id="menu-btn"><span></span><span></span><span></span></button>
                </div>
            `;
            applyActiveNavState();

            
            fetch(`${prefix}php/get_auth_status.php`)
                .then(res => res.json())
                .then(data => {
                    const authLinks = document.getElementById('auth-links');
                    if (data.isLoggedIn && authLinks) {
                        const user = data.user;
                        let dashboardPage = 'php/dashboard.php';
                        if (user.role === 'admin') dashboardPage = 'admin/dashboard.php';
                        if (user.role === 'staff') dashboardPage = 'admin/staff_dashboard.php';

                        const isDashboardPage = document.body.classList.contains('dashboard-body');
                        if (isDashboardPage) {
                             
                        }

                        authLinks.innerHTML = `
                            <div class="nav-profile-wrap">
                                <a href="${prefix}${dashboardPage}" class="nav-avatar-link">
                                    <img src="${prefix}${user.profile_photo || 'images/default-avatar.png'}" alt="Profile" class="nav-avatar">
                                </a>
                            </div>
                        `;
                    }
                })
                .catch(err => console.error('Auth check failed:', err));
        } else if (navbar) {
            applyActiveNavState();
        }


        
        let searchOverlay = document.getElementById('search-overlay');
        if (!searchOverlay) {
            searchOverlay = document.createElement('div');
            searchOverlay.className = 'search-overlay';
            searchOverlay.id = 'search-overlay';
            searchOverlay.innerHTML = `
                <div class="search-box">
                    <span class="search-close" id="close-search"><i class="fas fa-times"></i></span>
                    <input type="text" placeholder="Search destinations, packages, guides..." id="search-input" />
                    <i class="fas fa-search search-icon"></i>
                </div>
            `;
            document.body.appendChild(searchOverlay);
        }

        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'toast';
            document.body.appendChild(toast);
        }

        
        const searchBtn = document.getElementById('search-btn');
        const closeSearch = document.getElementById('close-search');
        const searchInput = document.getElementById('search-input');
        const searchIcon = searchOverlay ? searchOverlay.querySelector('.search-icon') : null;

        const executeSearch = (inputElement = null) => {
            const targetInput = inputElement || searchInput;
            if (!targetInput) return;
            const term = targetInput.value.trim().toLowerCase();
            if (!term) return;

            const normalized = term
                .replace(/[^a-z0-9\s-]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
            const collapsed = normalized.replace(/\s+/g, '');

            const destinationKey = Object.keys(DESTINATION_ROUTE_MAP).find((key) => {
                const aliasNorm = key.toLowerCase().trim();
                const aliasCollapsed = aliasNorm.replace(/\s+/g, '');
                return normalized === aliasNorm || collapsed === aliasCollapsed;
            });

            if (destinationKey) {
                window.location.href = resolveDestinationPath(destinationKey, prefix);
                return;
            }

            const routes = [
                { keys: ['package', 'tour'], href: 'php/packages.php' },
                { keys: ['hotel', 'accommodation', 'stay'], href: 'php/accommodation.php' },
                { keys: ['transport', 'train', 'bus', 'taxi', 'transfer'], href: 'transport.html' },
                { keys: ['gallery', 'photo', 'image'], href: 'gallery.html' },
                { keys: ['guide', 'travel guide', 'visa', 'budget'], href: 'guide.html' },
                { keys: ['destination', 'place', 'city'], href: 'destinations/destinations.html' },
                { keys: ['thing', 'activity', 'adventure'], href: 'thingstodo/things-to-do.html' },
                { keys: ['contact', 'support', 'help'], href: 'contact.html' },
                { keys: ['booking', 'book'], href: 'php/booking.php' }
            ];

            const match = routes.find((route) => route.keys.some((key) => term.includes(key)));
            if (match) {
                window.location.href = `${prefix}${match.href}`;
                return;
            }

            showToast('Search not found. Please try another keyword.', 'error');
        };

        if (searchBtn && closeSearch) {
            searchBtn.addEventListener('click', () => {
                searchOverlay.classList.add('active');
                if (searchInput) searchInput.focus();
            });
            closeSearch.addEventListener('click', () => searchOverlay.classList.remove('active'));
        }

        
        const menuBtn = document.getElementById('menu-btn');
        const navMenu = document.getElementById('nav-menu');
        const navWrapper = navMenu ? navMenu.querySelector('.nav-links-wrapper') : null;
        
        if (menuBtn && navWrapper) {
            menuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                navWrapper.classList.toggle('is-open');
                menuBtn.classList.toggle('is-active');
            });
            
            document.addEventListener('click', (e) => {
                if (navWrapper.classList.contains('is-open') && !navWrapper.contains(e.target) && e.target !== menuBtn) {
                    navWrapper.classList.remove('is-open');
                    menuBtn.classList.remove('is-active');
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    executeSearch();
                }
            });
        }

        if (searchIcon) {
            searchIcon.addEventListener('click', () => executeSearch());
        }

     
        const heroSearchForm = document.getElementById('hero-search-form');
        const heroSearchInput = document.getElementById('hero-search-input');
        if (heroSearchForm && heroSearchInput) {
            heroSearchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                executeSearch(heroSearchInput);
            });
        }

       
        const footer = document.querySelector('footer.footer');
        if (footer && footer.innerHTML.trim() === "") {
            footer.innerHTML = renderStandardFooter(prefix);
            footer.dataset.standardized = 'true';
        }
    }

   

    document.addEventListener('DOMContentLoaded', () => {
        ensureFontAwesome();
        ensureFavicon();
        ensureResponsiveCSS();
        standardizePageShell();


        const navbar = document.getElementById('navbar');
        const links = document.querySelectorAll("nav a");

        
        const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-fade, .reveal-stagger');
        if (reveals.length > 0) {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                root: null,
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            reveals.forEach(reveal => revealObserver.observe(reveal));
        }

        const updateScrollEffects = () => {
            const scrollY = window.scrollY;

           
            if (navbar) {
                if (scrollY > 80) {
                    navbar.classList.add("scrolled");
                } else {
                    navbar.classList.remove("scrolled");
                }
            }

           
            let current = "";
            document.querySelectorAll("section").forEach((section) => {
                const sectionTop = section.offsetTop;
                if (scrollY >= sectionTop - 100) {
                    const id = section.getAttribute("id");
                    if (id) current = id;
                }
            });

            if (current) {
                links.forEach((a) => {
                    a.classList.remove("active");
                    const href = a.getAttribute("href");
                    if (href && href.includes(current)) {
                        a.classList.add("active");
                    }
                });
            }

         
            const scrollTop = document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (scrollTop / height) * 100;
            const scrollProgress = document.getElementById("scroll-progress");
            if (scrollProgress) {
                scrollProgress.style.width = scrolled + "%";
            }

         
            const video = document.querySelector(".hero-video");
            if (video) {
                video.style.transform = `scale(1.2) translateY(${scrollY * 0.2}px)`;
            }
        };

        window.addEventListener('scroll', updateScrollEffects, { passive: true });
        updateScrollEffects();

    
        const menuBtn = document.getElementById('menu-btn');
        const navMenu = document.getElementById('nav-menu');
        if (menuBtn && navMenu) {
            menuBtn.addEventListener('click', () => {
                const isOpen = navMenu.classList.toggle('open');
                menuBtn.classList.toggle('active', isOpen);
                document.body.classList.toggle('mobile-menu-open', isOpen);
            });

            navMenu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 1024 && !link.closest('.dropdown')) {
                        navMenu.classList.remove('open');
                        menuBtn.classList.remove('active');
                        document.body.classList.remove('mobile-menu-open');
                    }
                });
            });
        }

        const mobileDropdownLinks = document.querySelectorAll('.dropdown > a');
        mobileDropdownLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                if (window.innerWidth > 1024) return;

                const dropdown = link.parentElement;
                if (!dropdown) return;

                event.preventDefault();
                const isOpen = dropdown.classList.toggle('open');

                document.querySelectorAll('.dropdown.open').forEach((item) => {
                    if (item !== dropdown) {
                        item.classList.remove('open');
                    }
                });

                link.setAttribute('aria-expanded', String(isOpen));
            });
        });

        document.addEventListener('click', (event) => {
            if (window.innerWidth > 1024) return;

            const navMenu = document.getElementById('nav-menu');
            const menuBtn = document.getElementById('menu-btn');
            if (!navMenu || !menuBtn) return;

            if (!navMenu.contains(event.target) && !menuBtn.contains(event.target)) {
                navMenu.classList.remove('open');
                menuBtn.classList.remove('active');
                document.body.classList.remove('mobile-menu-open');
                document.querySelectorAll('.dropdown.open').forEach((dropdown) => {
                    dropdown.classList.remove('open');
                    const trigger = dropdown.querySelector(':scope > a');
                    if (trigger) {
                        trigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) {
                document.querySelectorAll('.dropdown.open').forEach((dropdown) => {
                    dropdown.classList.remove('open');
                    const trigger = dropdown.querySelector(':scope > a');
                    if (trigger) {
                        trigger.setAttribute('aria-expanded', 'false');
                    }
                });
                document.body.classList.remove('mobile-menu-open');
            }
        });

       
        document.querySelectorAll('.carousel-arrow').forEach(btn => {
            btn.addEventListener('click', () => {
                const track = document.getElementById('dest-carousel');
                if (track) {
                    const dir = btn.classList.contains('left') ? -1 : 1;
                    track.scrollBy({ left: dir * 340 * 2, behavior: 'smooth' });
                }
            });
        });

        
        document.querySelectorAll('.passenger-counter').forEach(counter => {
            const valueEl = counter.querySelector('.counter-value');
            const [minusBtn, plusBtn] = counter.querySelectorAll('.counter-btn');
            if (valueEl && minusBtn && plusBtn) {
                let val = parseInt(valueEl.textContent);
                minusBtn.addEventListener('click', () => { if (val > 1) { val--; valueEl.textContent = val; } });
                plusBtn.addEventListener('click', () => { if (val < 20) { val++; valueEl.textContent = val; } });
            }
        });

       
        const nlBtn = document.getElementById('nl-btn');
        const nlInput = document.getElementById('nl-email');
        if (nlBtn && nlInput) {
            nlBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                const email = nlInput.value.trim();
                if (!email) {
                    showToast('Please enter your email.', 'error');
                    return;
                }

                nlBtn.disabled = true;
                nlBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                try {
                    const response = await fetch(getPagePrefix() + 'api/process_newsletter.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `email=${encodeURIComponent(email)}`
                    });
                    const result = await response.json();

                    if (result.success) {
                        showToast(result.message, 'success');
                        nlBtn.innerHTML = 'Subscribed! <i class="fas fa-check"></i>';
                        nlInput.value = '';
                    } else {
                        showToast(result.message, 'error');
                        nlBtn.innerHTML = 'Subscribe <i class="fas fa-paper-plane"></i>';
                        nlBtn.disabled = false;
                    }
                } catch (error) {
                    showToast('Connection error. Try again.', 'error');
                    nlBtn.innerHTML = 'Subscribe <i class="fas fa-paper-plane"></i>';
                    nlBtn.disabled = false;
                }
            });
        }

        
        const contactForm = document.getElementById('premium-contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(contactForm);
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Sending... <i class="fas fa-spinner fa-spin"></i>';

                try {
                    const response = await fetch(getPagePrefix() + 'api/process_contact.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.success) {
                        showToast(result.message, 'success');
                        contactForm.reset();
                        submitBtn.innerHTML = 'Sent Successfully! <i class="fas fa-check"></i>';
                    } else {
                        showToast(result.message, 'error');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                } catch (error) {
                    showToast('Message failed to send. Please check your connection.', 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        }
    });

   
    const hubPins = document.querySelectorAll(".hub-pin");
    const hubFeaturedCard = document.querySelector(".hub-featured-card");

    if (hubPins && hubFeaturedCard) {
        hubPins.forEach((pin) => {
            pin.addEventListener("click", (e) => {
                e.preventDefault();

                const destName = pin.getAttribute("data-name");
                const destImg = pin.getAttribute("data-img");
                const destFolder = pin.getAttribute("data-dest");

               
                hubFeaturedCard.innerHTML = `
                    <div class="hub-card-img">
                        <img src="${destImg}" alt="${destName}">
                    </div>
                    <div class="hub-card-body">
                        <span class="tag">${destName}</span>
                        <h3>${destName}</h3>
                        <p>Discover the beauty and wonder of ${destName}. Explore attractions, accommodations, and unforgettable experiences in this amazing destination.</p>
                        <a href="${resolveDestinationPath(destFolder, getPagePrefix())}" class="hub-card-link">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                `;

               
                hubPins.forEach((p) => p.classList.remove("active"));
                pin.classList.add("active");
            });
        });
    }





})();


document.addEventListener('DOMContentLoaded', function () {
    const mapPins = document.querySelectorAll('.map-pin-custom');
    const cardImg = document.getElementById('dynamic-card-img');
    const cardTitle = document.getElementById('dynamic-card-title');
    const cardSubtitle = document.getElementById('dynamic-card-subtitle');
    const cardDesc = document.getElementById('dynamic-card-desc');
    const cardLink = document.getElementById('dynamic-card-link');

    if (!mapPins.length) return;

    mapPins.forEach(pin => {
        pin.addEventListener('click', function (e) {
            e.preventDefault();
            
            mapPins.forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');

          
            const title = this.getAttribute('data-title');
            const img = this.getAttribute('data-img');
            const desc = this.getAttribute('data-desc');
            const loc = this.getAttribute('data-loc');

          
            if (cardImg) {
                cardImg.classList.add('map-card-img-faded');
                setTimeout(() => {
                    cardImg.src = img;
                    cardImg.classList.remove('map-card-img-faded');
                }, 150);
            }

            if (cardTitle) cardTitle.textContent = title;
            if (cardSubtitle) cardSubtitle.textContent = title + " DESTINATION";
            if (cardDesc) cardDesc.textContent = desc;
            if (cardLink && window.GlobeTrekRoutes) {
                cardLink.href = window.GlobeTrekRoutes.resolveDestinationPath(
                    loc,
                    window.GlobeTrekRoutes.getPagePrefix()
                );
            }
        });
    });
});
