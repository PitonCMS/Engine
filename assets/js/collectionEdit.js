/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.. See LICENSE file for details.
 */

/**
 * Collection Group Edit JS
 */

import './modules/main.js';
import { setCleanSlug, unlockSlug } from './modules/url.js';

// Bind set page slug from page title
document.querySelector(`[data-url-slug="source"]`).addEventListener("input", (e) => {
    setCleanSlug(e.target.value);
}, false);

// Bind warning on unlocking page slug
document.querySelector(`[data-url-slug-lock="1"]`).addEventListener("click", (e) => {
    unlockSlug(e);
}, false);
