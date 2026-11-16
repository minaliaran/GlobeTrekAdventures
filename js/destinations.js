

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('dest-live-search');
    const filterButtons = document.querySelectorAll('.dest-filter-bar button');
    const sortSelect = document.getElementById('dest-sort');
    const cards = document.querySelectorAll('.dest-card');
    const countDisplay = id('current-count');

    function id(name) { return document.getElementById(name); }
    function normalize(value) {
        return (value || '').toLowerCase().trim();
    }

    function getCardCategory(card) {
        const explicit = normalize(card.dataset.category);
        if (explicit) return explicit;

        const badgeText = normalize(card.querySelector('.dest-badge')?.textContent);

        if (badgeText.includes('coastal')) return 'coastal';
        if (badgeText.includes('wildlife')) return 'wildlife';
        if (badgeText.includes('adventure')) return 'adventure';
        if (badgeText.includes('hill')) return 'hill-country';
        if (badgeText.includes('heritage') || badgeText.includes('cultural')) return 'heritage';
        if (badgeText.includes('nature')) return 'hill-country';

        return 'all';
    }


    function performFilter() {
        const searchTerm = normalize(searchInput?.value);
        const activeButton = document.querySelector('.dest-filter-bar button.active');
        const activeFilter = activeButton ? normalize(activeButton.getAttribute('data-filter')) : 'all';
        let visibleCount = 0;

        cards.forEach(card => {
            const title = normalize(card.querySelector('h3')?.textContent);
            const desc = normalize(card.querySelector('p')?.textContent);
            const location = normalize(card.querySelector('.dest-location')?.textContent);
            const category = getCardCategory(card);

            const matchesSearch = title.includes(searchTerm) || desc.includes(searchTerm) || location.includes(searchTerm);
            const matchesCategory = activeFilter === 'all' || category === activeFilter;

            if (matchesSearch && matchesCategory) {
                card.style.display = 'block';
                card.style.animation = 'fadeInUp 0.5s ease forwards';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countDisplay) countDisplay.textContent = visibleCount;
    }

   
    if (searchInput) {
        searchInput.addEventListener('input', performFilter);
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            performFilter();
        });
    });

   
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
        const grid = document.querySelector('.destinations-grid');
        const cardArray = Array.from(cards);
        const sortBy = this.value;

        if (!grid) return;

        cardArray.sort((a, b) => {
            if (sortBy === 'price-asc' || sortBy === 'price-desc') {
                const priceA = parseFloat(a.querySelector('.dest-price-value')?.textContent.replace('$', '') || '0');
                const priceB = parseFloat(b.querySelector('.dest-price-value')?.textContent.replace('$', '') || '0');
                return sortBy === 'price-asc' ? priceA - priceB : priceB - priceA;
            }
            
            if (sortBy === 'rating') {
                const rateA = parseFloat(a.querySelector('.dest-rating-text strong')?.textContent || '0');
                const rateB = parseFloat(b.querySelector('.dest-rating-text strong')?.textContent || '0');
                return rateB - rateA;
            }
            
            return 0; 
        });

       
        cardArray.forEach(card => grid.appendChild(card));
        });
    }

    performFilter();
});
