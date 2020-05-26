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
 * @param {string} heading
 * @param {string} content
 * @param {object} buttons
 */
const showModalContent = function(heading, content, buttons) {
    modal.querySelector(`[data-modal="heading"]`).innerHTML = heading;
    modal.querySelector(`[data-modal="content"]`).innerHTML = content;
    modal.classList.remove("d-none");
    modal.querySelector(`[data-modal="container"]`).classList.remove("d-none");
}

/**
 * Hide Modal and Clear Contents
 */
const hideModal = function() {
    modal.classList.add("d-none");
    modal.querySelector(`[data-modal="content"]`).classList.add("d-none");
    modal.querySelector(`[data-modal="heading"]`).innerHTML = "";
    modal.querySelector(`[data-modal="container"]`).innerHTML = "";
}

// Bind close modal events
modal.querySelector(`[data-dismiss="modal"]`).addEventListener("click", () => {
    hideModal();
});
window.addEventListener("click", (event) => {
    if (event.target === modal) {
        hideModal();
    }
});

export { getModal, showModal, showModalContent, hideModal };
