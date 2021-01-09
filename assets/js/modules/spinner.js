/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

/**
 * Enable or Disable Spinner Module
 */

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
