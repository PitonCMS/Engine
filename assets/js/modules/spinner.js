/**
 * Enable Spinner Overlay
 * @param {void}
 */
const enableSpinner = function() {
    if (document.querySelector("body > .spinner").classList.contains("d-none")) {
        document.querySelector("body > .spinner").classList.remove("d-none")
    }
}

/**
 * Disable Spinner Overlay
 * @param {void}
 */
const disableSpinner = function() {
    if (!document.querySelector("body > .spinner").classList.contains("d-none")) {
        document.querySelector("body > .spinner").classList.add("d-none")
    }
}

export { enableSpinner, disableSpinner };