// --------------------------------------------------------
// URL JS
// --------------------------------------------------------
const pageSlug = document.querySelector(`[data-url-slug="target"]`);

/**
 * Clean URL Slug
 * @param {string} value
 */
const setCleanSlug = function(value) {
    // Do not change the home page
    if (pageSlug.value === 'home') return;

    if (pageSlug.dataset.urlSlugStatus === 'unlock') {
        value = value.replace(/&/g, 'and');
        value = value.replace(`'`, '');
        value = value.replace(/[^a-z0-9]+/gi, '-');
        value = value.replace(/-+$/gi, '');

        pageSlug.value = value;
    }
}

const unlockSlug = function(event) {
    // Do not change the home page
    if (pageSlug.value === 'home') {
        alert("You cannot change the home page slug.");
        return;
    };

    const message = 'Are you sure you want to change the URL Slug? This can impact links and search engine results.';

    if (event.target.classList && event.target.classList.contains("fa-lock")) {
        if (!confirm(message)) return;

        // Continue to unlock and enable input
        event.target.classList.replace("fa-lock", "fa-unlock");
        pageSlug.readOnly = false;
        pageSlug.dataset.urlSlugStatus = 'unlock';
    }
}

export { setCleanSlug, unlockSlug };