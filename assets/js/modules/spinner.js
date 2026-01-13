/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.. See LICENSE file for details.
 */

/**
 * Enable or Disable Spinner Module
 */

import { pitonConfig } from './config.js';

/**
 * Enable Spinner Overlay
 * @param {void}
 */
const enableSpinner = function() {
    document.body.insertAdjacentHTML("afterbegin", pitonConfig.spinnerHTML);
}

/**
 * Disable Spinner Overlay
 * @param {void}
 */
const disableSpinner = function() {
    document.querySelector('[data-spinner="true"]')?.remove();
}

export { enableSpinner, disableSpinner };
