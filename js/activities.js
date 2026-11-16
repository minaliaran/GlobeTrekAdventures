
function applyPageFilter(filter, sourceElement) {
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });

    
    document.querySelectorAll('.marker').forEach(pin => {
        pin.classList.toggle('is-active', pin.dataset.filter === filter);
    });

    
    const cards = document.querySelectorAll('.activity-card');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px) scale(0.98)';

        setTimeout(() => {
            if (filter === 'all' || card.dataset.category === filter) {
                card.style.display = 'flex';
                void card.offsetWidth;
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) scale(1)';
            } else {
                card.style.display = 'none';
            }
        }, 300);
    });
}


document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => applyPageFilter(btn.dataset.filter, btn));
});
document.querySelectorAll('.marker').forEach(pin => {
    pin.addEventListener('click', () => {
        applyPageFilter(pin.dataset.filter, pin);
        
        document.getElementById('activities-grid-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
