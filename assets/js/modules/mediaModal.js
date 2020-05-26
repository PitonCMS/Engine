import { getModal, showModal, showModalContent, hideModal } from './modal.js';
import { getXHRPromise } from './xhrPromise.js';

/**
 * Opens Modal with Media Images for Select
 * @param {Element} elementTarget Media target
 */
const openMediaModal = function(elementTarget) {
    showModal();
    getXHRPromise(pitonConfig.routes.adminMediaGet)
        .then(data => {
            showModalContent("Select Media", data);
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
            elementTarget.querySelector(`input[name*="media_id"]`).value = data.id;
            elementTarget.querySelector("img").src = data.filename;
            elementTarget.querySelector("img").alt = data.caption;
            elementTarget.querySelector("img").title = data.caption;
            elementTarget.querySelector("img").classList.remove("d-none");

            hideModal();
        }
    });
}

// Media select modal
const mediaSelect = function(event) {
    if (event && event.target.dataset.mediaModal) {
        // Launch media modal with target element
        openMediaModal(event.target.closest(`[data-media-select="1"]`));
    } else if (event.target.dataset.mediaClear) {
        // Clear media from form
        let mediaElement = event.target.closest(`[data-media-select="1"]`);
        mediaElement.querySelector(`input[name*="media_id"]`).value = "";
        mediaElement.querySelector("img").src = "";
        mediaElement.querySelector("img").alt = "";
        mediaElement.querySelector("img").title = "";
        mediaElement.querySelector("img").classList.add("d-none");
    }
}

export { mediaSelect };