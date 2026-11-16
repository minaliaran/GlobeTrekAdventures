document.addEventListener('DOMContentLoaded', () => {
    const redirectNode = document.querySelector('[data-redirect-target]');
    if (!redirectNode) {
        return;
    }

    const target = redirectNode.getAttribute('data-redirect-target');
    const preserveQuery = redirectNode.getAttribute('data-preserve-query') === 'true';

    if (!target) {
        return;
    }

    const nextUrl = preserveQuery ? target + window.location.search : target;
    window.location.replace(nextUrl);
});
