import { getModal, showModal, showModalContent, hideModal } from './modal.js';
import { getXHRPromise } from './xhrPromise.js';
import { alertInlineMessage } from './alert.js';

/**
 * Event to dispatch pseudo "input" event on hidden inputs
 */
const inputEvent = new Event("input", {"bubbles": true});

/**
 * Opens Modal with Media Images for Select
 * @param {Element} elementTarget Media target
 */
const openMediaModal = function(elementTarget) {
    showModal();
    getXHRPromise(pitonConfig.routes.adminMediaGet)
        .then(data => {
            showModalContent("Select Media", data);
        })
        .catch((error) => {
            hideModal();
            alertInlineMessage("danger", "Failed to Launch Media Modal", error);
        });

    // Add click listener to set media ID on select and dismiss
    getModal().querySelector(`[data-modal="content"]`).addEventListener("click", (e) => {
        if (e.target.closest(`[data-media="1"]`)) {
            // Get media data and set in form
            let data = {
                "id": e.target.closest(`[data-media="1"]`).dataset.mediaId,
                "caption": e.target.closest(`[data-media="1"]`).dataset.mediaCaption,
                "filename": e.target.closest(`[data-media="1"]`).dataset.mediaPath
            }

            // Set ID, filename and relative path, an caption in target element
            let targetInput = elementTarget.querySelector(`input[name*="media_id"]`);
            let targetImg = elementTarget.querySelector("img");

            targetInput.value = data.id;
            targetImg.src = data.filename;
            targetImg.alt = data.caption;
            targetImg.title = data.caption;
            targetImg.classList.remove("d-none");

            // Dispatch input event on hidden field
            targetInput.dispatchEvent(inputEvent);

            hideModal();
        }
    }, false);
}

// Media select modal
const mediaSelect = function(event) {
    if (event && event.target.dataset.mediaModal) {
        // Launch media modal with target element
        openMediaModal(event.target.closest(`[data-media-select="1"]`));
    } else if (event.target.dataset.mediaClear) {
        // Clear media from form
        let targetInput = event.target.closest(`[data-media-select="1"]`).querySelector(`input[name*="media_id"]`);
        let targetImg = event.target.closest(`[data-media-select="1"]`).querySelector("img");

        targetInput.value = "";
        targetImg.src = "";
        targetImg.alt = "";
        targetImg.title = "";
        targetImg.classList.add("d-none");

        // Dispatch event on hidden field
        targetInput.dispatchEvent(inputEvent);
    }
}

export { mediaSelect };