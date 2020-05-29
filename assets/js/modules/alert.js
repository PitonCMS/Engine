/**
 * Dismiss Inline Alert
 * @param {Event} event
 */
const dismissAlertInlineMessage = function(event) {
    if (event.target.dataset.dismiss === "alert") {
        event.target.closest(`[data-alert="container"]`).remove();
    }
}

/**
 * Display Inline HTML Message Alert
 * @param {string} severity Severity color code
 * @param {string} heading  Message heading
 * @param {mixed} message   Message list
 */
const alertInlineMessage = function(severity, heading, message) {
    // Create element and insert alert HTML and update with alert data
    let container = document.createElement("div");
    container.innerHTML = pitonConfig.alertInlineHTML;
    container.querySelector(`[data-alert="container"]`).classList.add("alert-" + severity);
    container.querySelector(`[data-alert="heading"]`).innerHTML = heading;

    // Stringify message
    if (typeof message === 'object') {
        message = message.join("<br>");
    }
    container.querySelector(`[data-alert="content"]`).innerHTML = message;

    // Insert into main or body
    let mainContainer = document.querySelector("main.main-content");
    mainContainer.insertAdjacentHTML('afterbegin', container.innerHTML);
    window.scrollTo(0,0);
}

export { alertInlineMessage, dismissAlertInlineMessage };