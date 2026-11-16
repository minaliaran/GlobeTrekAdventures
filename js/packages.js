
document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.pkg-tab');
    const packageCards = document.querySelectorAll('.pkg-card');
    const emptyState = document.getElementById('pkg-empty');

    if (filterButtons.length === 0) return;

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.getAttribute('data-filter');

            
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            
            let visibleCount = 0;
            packageCards.forEach(card => {
                const category = card.getAttribute('data-category');
                
                if (filter === 'all' || category === filter) {
                    card.style.display = 'flex'; 
                    visibleCount++;
                    
                    
                    setTimeout(() => {
                        card.classList.add('visible');
                    }, 10);
                } else {
                    card.classList.remove('visible');
                    card.style.display = 'none';
                }
            });

            
            if (visibleCount === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
            }
        });
    });
});
