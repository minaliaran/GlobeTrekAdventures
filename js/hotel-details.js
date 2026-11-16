console.log('Hotel details script file detected');



document.addEventListener('DOMContentLoaded', function () {
    const lightbox = document.getElementById('hotel-lightbox');
    if (!lightbox) return;

    const lightboxImg = lightbox.querySelector('img');
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const galleryWraps = document.querySelectorAll('.gallery-item-wrap');
    const heroSection = document.querySelector('.hotel-details-hero[data-hero-image]');

    if (heroSection) {
        const heroImage = heroSection.getAttribute('data-hero-image');
        if (heroImage) {
            heroSection.style.backgroundImage = `url("${heroImage}")`;
        }
    }

    function openHotelLightbox(url) {
        if (!lightboxImg) return;
        lightboxImg.src = url;
        lightbox.classList.add('active');
    }

    window.openLightbox = openHotelLightbox;

    galleryWraps.forEach((wrap) => {
        const img = wrap.querySelector('.hotel-gallery-image');
        wrap.addEventListener('click', function () {
            if (img) {
                openHotelLightbox(img.dataset.lightboxSrc || img.src);
            }
        });

        if (img) {
            img.addEventListener('error', function () {
                const fallback = img.dataset.fallbackSrc;
                if (fallback && img.src !== fallback) {
                    img.src = fallback;
                }
            });
        }
    });

   
    const slider = document.getElementById('hotel-gallery-slider');
    const prevBtn = document.getElementById('gallery-prev');
    const nextBtn = document.getElementById('gallery-next');

    if (slider && prevBtn && nextBtn) {
        console.log('Gallery slider initialized');

        const getScrollAmount = () => {
            return slider.clientWidth * 0.8;
        };

        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Prev clicked, current scroll:', slider.scrollLeft);
            slider.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Next clicked, current scroll:', slider.scrollLeft);
            slider.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
        });

        const updateButtons = () => {
            const scrollLeft = Math.ceil(slider.scrollLeft);
            const scrollWidth = slider.scrollWidth;
            const clientWidth = slider.clientWidth;
            const maxScroll = scrollWidth - clientWidth;
            
          
            prevBtn.style.display = scrollLeft > 20 ? 'flex' : 'none';
           
            nextBtn.style.display = (maxScroll > 20 && scrollLeft < maxScroll - 20) ? 'flex' : 'none';
        };

        slider.addEventListener('scroll', updateButtons, { passive: true });
        window.addEventListener('resize', updateButtons);
        
        const observer = new ResizeObserver(updateButtons);
        observer.observe(slider);
        
        setTimeout(updateButtons, 100);
        setTimeout(updateButtons, 1000);
    }
 else {
        console.error('Gallery slider elements not found', { slider, prevBtn, nextBtn });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            lightbox.classList.remove('active');
        });
    }

    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) {
            lightbox.classList.remove('active');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            lightbox.classList.remove('active');
        }
    });
});
