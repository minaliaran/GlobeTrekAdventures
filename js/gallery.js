document.addEventListener('DOMContentLoaded', () => {
    const lightbox = document.getElementById('gallery-lightbox');
    const lightboxImage = lightbox?.querySelector('img');
    const closeButton = lightbox?.querySelector('.lightbox-close');
    const galleryCards = document.querySelectorAll('.gallery-card[data-gallery-image]');

    if (!lightbox || !lightboxImage || !closeButton) {
        return;
    }

    function openLightbox(url) {
        lightboxImage.src = url;
        lightbox.classList.add('active');
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
    }

    galleryCards.forEach((card) => {
        card.addEventListener('click', () => {
            openLightbox(card.dataset.galleryImage || '');
        });
    });

    closeButton.addEventListener('click', closeLightbox);

    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });
});
