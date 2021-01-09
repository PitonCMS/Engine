/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

/**
 * Display system alert messages
 */

const alertContainer = document.querySelector(`[data-alert-modal="1"]`);

/**
 * Dismiss Inline Alert
 *
 * @param {Event} event
 */
const dismissAlertInlineMessage = function(event) {
    if (event.target.dataset.dismiss === "alert") {
        event.target.closest(`[data-alert="container"]`)?.remove();
    }
}

/**
 * Display Inline HTML Message Alert
 *
 * @param {string} severity Severity color code
 * @param {string} heading  Message heading
 * @param {mixed} message   Message text or object
 */
const alertInlineMessage = function(severity, heading, message) {
    // Create element and insert alert HTML and update with alert data
    let container = document.createElement("div");
    container.innerHTML = pitonConfig.alertInlineHTML;
    container.querySelector(`[data-alert="container"]`).classList.add("alert-" + severity);
    container.querySelector(`[data-alert="heading"]`).innerHTML = heading;

    // Stringify message
    if (Array.isArray(message) && message !== null) {
        message = message.join("<br>");
    } else if (message instanceof Error) {
        message = message.message;
    } else if (typeof message === "object" && message !== null) {
        message = Object.values(message).join("<br>");
    } else {
        message = String(message);
    }

    container.querySelector(`[data-alert="content"]`).innerHTML = message;

    // Insert into modal-alert container
    if (alertContainer) {
        alertContainer.insertAdjacentHTML('afterbegin', container.innerHTML);
        window.scrollTo(0,0);
    } else {
        // If alert container does not exist, then use standard JS alert
        alert(container.innerHTML);
    }

}

document.addEventListener("click", dismissAlertInlineMessage, false);

export { alertInlineMessage };