// --------------------------------------------------------
// Page Edit JS
// --------------------------------------------------------

import './modules/main.js';
import { setCleanSlug, unlockSlug } from './modules/url.js';

// Bind set page slug from page title
document.querySelector(`[data-url-slug="source"]`).addEventListener("input", (e) => {
    setCleanSlug(e.target.value);
});

// Bind warning on unlocking page slug
document.querySelector(`[data-url-slug-lock="1"]`).addEventListener("click", (e) => {
    unlockSlug(e);
});
