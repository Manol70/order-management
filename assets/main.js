document.addEventListener('turbo:load', function() {
    // Премахване на параметъра ajax от URL-а, ако е налице
    const url = new URL(window.location.href);
    url.searchParams.delete('ajax');
    history.replaceState({}, '', url.toString());
});


