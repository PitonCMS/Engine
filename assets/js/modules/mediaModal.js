/**
 * Media Select Modal
 */

import { getModal, loadModal, loadModalContent, removeModal } from './modal.js';
import { getXHRPromise } from './xhrPromise.js';
import { alertInlineMessage } from './alert.js';

/**
 * Event to dispatch pseudo "input" event on hidden inputs
 */
const inputEvent = new Event("input", {"bubbles": true});

/**
 * Opens Modal with Media Images for Select
 * @param {Node} elementTarget Form target to assign media
 */
const openMediaModal = function(elementTarget) {
    // Load modal background first to show response as XHR request processes
    loadModal();
    getXHRPromise(pitonConfig.routes.adminMediaGet + "static")
        .then(data => {
            // Load response into modal
            loadModalContent("Select Media", data);
        })
        .then(() => {
            mediaSelectListener(elementTarget);
        })
        .catch((error) => {
            removeModal();
            alertInlineMessage("danger", "Failed to Launch Media Modal", error);
        });
}

/**
 * Media Select listener
 *
 * Binds click event to loaded media modal to listen for when a media file is selected
 * @param {Node} elementTarget
 */
const mediaSelectListener = function (elementTarget) {
    // Add click listener to set media ID on select and dismiss
    getModal().querySelector(`[data-modal="body"]`).addEventListener("click", (event) => {
        if (!event.target.closest(`[data-media-card="true"]`)) return;

        let mediaCard = event.target.closest(`[data-media-card="true"]`);
        // Get media data and set in form
        let data = {
            "id": mediaCard.dataset.mediaId,
            "caption": mediaCard.dataset.mediaCaption,
            "filename": mediaCard.dataset.mediaFilename
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

        removeModal();
    }, false);
}

/**
 * Media Select
 *
 * Launches media select modal
 * @param {Event} event
 */
const mediaSelect = function (event) {
    if (event.target.dataset.mediaModal) {
        // Launch media modal and pass in target element
        openMediaModal(event.target.closest(`[data-media-select="true"]`));
    } else if (event.target.dataset.mediaClear) {
        // Clear media from form
        let targetInput = event.target.closest(`[data-media-select="true"]`).querySelector(`input[name*="media_id"]`);
        let targetImg = event.target.closest(`[data-media-select="true"]`).querySelector("img");

        targetInput.value = "";
        targetImg.src = "";
        targetImg.alt = "";
        targetImg.title = "";
        targetImg.classList.add("d-none");

        // Dispatch event on hidden field
        targetInput.dispatchEvent(inputEvent);
    }
}

document.addEventListener("click", mediaSelect, false);
