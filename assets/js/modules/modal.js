/**
 * Open, Load, and Dismiss Modal Window
 */

/**
 * Get Modal
 *
 * Returns reference to modal content node, but only after inserted into DOM by loadModalContent()
 */
const getModal = function() {
    return document.querySelector(`[data-modal="content"]`);
}

/**
 * Load Modal (Background)
 *
 * Call first if request requires processing time before content is available to load in loadModalContent
 */
const loadModal = function() {
    document.body.insertAdjacentHTML("afterbegin", pitonConfig.modalBackgroundHTML);
}

/**
 * Load Modal Content and Display
 *
 * Loads modal background if not already loaded
 * @param {string} header
 * @param {string} body
 */
const loadModalContent = function(header, body) {
    // Create new element and load modal
    let modalDiv = document.createElement("div");
    modalDiv.innerHTML = pitonConfig.modalContentHTML;
    modalDiv.querySelector(`[data-modal="header"]`).innerHTML = header;
    modalDiv.querySelector(`[data-modal="body"]`).innerHTML = body;

    // Create modal background if it does not exit
    if (document.querySelector(`[data-modal="modal"]`) === null) {
        loadModal();
    }

    // Insert into modal div as child
    document.querySelector(`[data-modal="modal"]`).appendChild(modalDiv.firstChild);
}

/**
 * Remove Modal and Contents
 */
const removeModal = function() {
    document.querySelector(`[data-modal="modal"]`)?.remove();
}

/**
 * Remove Modal (Event)
 * @param {Event} event
 */
const removeModalEvent = function(event) {
    if (!(event.target.dataset.modal === "modal" || event.target.dataset.modal === "dismiss")) return;
    removeModal();
}

// Bind modal events
document.addEventListener("click", removeModalEvent, false);

export { getModal, loadModal, loadModalContent, removeModal };
