/**
 * Media Select Modal
 *
 * Allows selecting media for use.
 * Loads modal with available media with search and filter controls.
 */

import { setFilterPath } from "./filter.js";
import { loadModal, loadModalContent, removeModal } from './modal.js';
import { getXHRPromise } from './xhrPromise.js';
import { alertInlineMessage } from './alert.js';

// Set filter query end point
setFilterPath(pitonConfig.routes.adminMediaGet + "static");

/**
 * Event to dispatch pseudo "input" event on hidden inputs
 */
const inputEvent = new Event("input", {"bubbles": true});

/**
 * Target element
 */
let targetElement = null;

/**
 * Set Target Element
 *
 * This is the element to set the selected media
 * @param {HTMLElement} element
 */
const setTargetElement = function (element) {
    targetElement = element;
}

/**
 * Get Target Element
 *
 * This is the element to set the selected media
 * @param void
 */
const getTargetElement = function () {
    return targetElement;
}

/**
 * Opens Modal with Media Images for Select
 * @param void
 */
const openMediaModal = function() {
    // Load modal background first to show response as XHR request processes
    loadModal();
    getXHRPromise(pitonConfig.routes.adminMediaGet + "static")
        .then(data => {
            // Get the media controls and wrapper
            getXHRPromise(pitonConfig.routes.adminMediaControlsGet)
                .then(controls => {
                    // Create element to inject HTML string into to get this live
                    let container = document.createElement("div");
                    container.classList.add("modal-container");
                    container.dataset.mediaSelectModal = true;
                    container.insertAdjacentHTML("afterbegin", controls);

                    // Find the query filter content div to inject media results from first query
                    container.querySelector(`[data-filter="content"]`).insertAdjacentHTML("afterbegin", data);

                    return container;
                })
                .then(mediaHtml => {
                    loadModalContent("Select Media", mediaHtml);
                });
        })
        .catch((error) => {
            removeModal();
            alertInlineMessage("danger", "Failed to Launch Media Modal", error);
        });
}

/**
 * Media Select listener
 *
 * Binds click event to loaded media modal to listen for when a media card file is selected
 * @param {Event} event
 */
const mediaSelectListener = function (event) {
    if (!(event.target.closest(`[data-media-card="true"]`) && event.target.closest(`[data-media-select-modal]`))) return;

    let mediaCard = event.target.closest(`[data-media-card="true"]`);
    // Get media data and set in form
    let data = {
        "id": mediaCard.dataset.mediaId,
        "caption": mediaCard.dataset.mediaCaption,
        "filename": mediaCard.dataset.mediaFilename
    }

    // Set ID, filename and relative path, an caption in target element
    let targetInput = getTargetElement().querySelector(`input[name*="media_id"]`);
    let targetImg = getTargetElement().querySelector("img");

    targetInput.value = data.id;
    targetImg.src = data.filename;
    targetImg.alt = data.caption;
    targetImg.title = data.caption;
    targetImg.classList.remove("d-none");

    // Dispatch input event on hidden field
    targetInput.dispatchEvent(inputEvent);

    removeModal();
}

/**
 * Media Select
 *
 * Launches media select modal
 * @param {Event} event
 */
const mediaSelect = function (event) {
    if (event.target.dataset.mediaModal) {
        // Save reference to target element and load modal
        setTargetElement(event.target.closest(`[data-media-select="true"]`))
        openMediaModal();
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
document.addEventListener("click", mediaSelectListener, false);
