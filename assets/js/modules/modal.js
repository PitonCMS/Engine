// Get base modal HTML available in all pages
const modal = document.getElementById("modal");

/**
 * Get Modal
 * Returns modal object
 */
const getModal = function() {
    return modal;
}

/**
 * Show Modal (Background)
 * Call first if request requires processing before content is available
 */
const showModal = function() {
    modal.classList.remove("d-none");
}

/**
 * Load Modal Content and Display
 * @param {string} header
 * @param {string} body
 * @param {object} buttons
 */
const showModalContent = function(header, body, buttons) {
    modal.querySelector(`[data-modal="header"]`).innerHTML = header;
    modal.querySelector(`[data-modal="body"]`).innerHTML = body;
    modal.classList.remove("d-none");
    modal.querySelector(`[data-modal="content"]`).classList.remove("d-none");
}

/**
 * Hide Modal and Clear Contents
 */
const hideModal = function() {
    modal.classList.add("d-none");
    modal.querySelector(`[data-modal="content"]`).classList.add("d-none");
    modal.querySelector(`[data-modal="header"]`).innerHTML = "";
    modal.querySelector(`[data-modal="body"]`).innerHTML = "";
}

// Bind close modal events
modal.querySelector(`[data-dismiss="modal"]`).addEventListener("click", () => {
    hideModal();
}, false);

window.addEventListener("click", (event) => {
    if (event.target === modal) {
        hideModal();
    }
}, false);

export { getModal, showModal, showModalContent, hideModal };
