document.addEventListener('DOMContentLoaded', () => {
    const tabBar = document.getElementById('guide-tabs-bar');
    const tabs = Array.from(document.querySelectorAll('.guide-tab'));
    const sections = Array.from(document.querySelectorAll('.guide-section[id]'));

    if (!tabBar || tabs.length === 0 || sections.length === 0) {
        return;
    }

    function getScrollOffset() {
        const navbarHeight = document.getElementById('navbar')?.offsetHeight || 0;
        const tabBarHeight = tabBar.offsetHeight || 0;
        return navbarHeight + tabBarHeight + 24;
    }

    function setActiveTab(id) {
        tabs.forEach((tab) => {
            const isActive = tab.dataset.target === id;
            tab.classList.toggle('active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    }

    tabs.forEach((tab) => {
        tab.setAttribute('aria-selected', tab.classList.contains('active') ? 'true' : 'false');
        tab.addEventListener('click', () => {
            const targetId = tab.dataset.target;
            const targetSection = document.getElementById(targetId);

            if (!targetSection) {
                return;
            }

            const top = targetSection.getBoundingClientRect().top + window.scrollY - getScrollOffset();
            window.scrollTo({ top, behavior: 'smooth' });
            setActiveTab(targetId);
        });
    });

    const observer = new IntersectionObserver((entries) => {
        const visibleEntry = entries
            .filter((entry) => entry.isIntersecting)
            .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

        if (visibleEntry) {
            setActiveTab(visibleEntry.target.id);
        }
    }, {
        rootMargin: '-25% 0px -55% 0px',
        threshold: [0.2, 0.35, 0.5],
    });

    sections.forEach((section) => observer.observe(section));
});
