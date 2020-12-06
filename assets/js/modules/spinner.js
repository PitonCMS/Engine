/**
 * Enable or Disable Spinner
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
