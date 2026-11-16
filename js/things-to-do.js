(function () {
    'use strict';

    function applyPageFilter(filter) {
        document.querySelectorAll('.filter-btn').forEach((btn) => {
            btn.classList.toggle('active', btn.dataset.filter === filter);
        });

        document.querySelectorAll('.marker').forEach((pin) => {
            pin.classList.toggle('is-active', filter !== 'all' && pin.dataset.filter === filter);
        });

        document.querySelectorAll('.activity-card').forEach((card) => {
            const isVisible = filter === 'all' || card.dataset.category === filter;

            card.style.opacity = '0';
            card.style.transform = 'translateY(10px) scale(0.98)';

            window.setTimeout(() => {
                card.style.display = isVisible ? '' : 'none';

                if (isVisible) {
                    void card.offsetWidth;
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0) scale(1)';
                }
            }, 180);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.filter-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                applyPageFilter(btn.dataset.filter);
            });
        });

        document.querySelectorAll('.marker').forEach((pin) => {
            pin.addEventListener('click', function () {
                applyPageFilter(pin.dataset.filter);

                const target = document.getElementById('activities-grid-section');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    });
}());
