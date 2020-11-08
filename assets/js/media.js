// --------------------------------------------------------
// Media management
// --------------------------------------------------------

import './modules/main.js';
import './modules/mediaUpload.js';
import { enableSpinner, disableSpinner } from './modules/spinner.js';
import { postXHRPromise } from './modules/xhrPromise.js';
import { alertInlineMessage } from './modules/alert.js';
import { setFilterPath } from "./modules/filter.js";

setFilterPath(pitonConfig.routes.adminMediaGet);

/**
 * Save Media
 * @param {Event} event
 */
const saveMedia = function(event) {
    if (event.target.dataset.formButton !== "save") return;
    const form = event.target.closest("form");

    postXHRPromise(pitonConfig.routes.adminMediaSave, new FormData(form))
        .then(() => {
            // Show save complete by disabling save and discard buttons again
            form.querySelectorAll(`[data-form-button="save"], [data-form-button="cancel"]`)?.forEach(control => {
                control.disabled = true;
            });
        })
        .then(() => {
            disableSpinner();
        })
        .catch((error) => {
            disableSpinner();
            alertInlineMessage('danger', 'Failed to Save Media', error);
        });
}

/**
 * Delete Media Asynchronously
 * @param {Event} event
 */
const deleteMedia = function(event) {
    if (!event.target.dataset.deleteMediaPrompt) return;
    if (!confirm(event.target.dataset.deleteMediaPrompt)) return;

    let mediaCard = event.target.closest('[data-media-card="true"]');
    let mediaId = event.target.dataset.deleteMediaId;

    enableSpinner();
    postXHRPromise(pitonConfig.routes.adminMediaDelete, {"media_id": mediaId})
        .then(() => {
            mediaCard.remove();
        })
        .then(() => {
            disableSpinner();
        })
        .catch((error) => {
            disableSpinner();
            alertInlineMessage('danger', 'Failed to Delete Media', error);
        });
}

/**
 * Copy Media Path on Click
 *
 * Copies relative path to media file
 * @param {Event} event
 */
const copyMediaPath = function(event) {
    if (!event.target.dataset.mediaClickCopy) return;

    try {
        // Stop if the current browser does not support navigator clipboard
        if (!navigator.clipboard) {
            throw "Your browser does not support click to copy.";
        }

        let dataPath = event.target.dataset.mediaClickCopy;
        navigator.clipboard.writeText(dataPath);
    } catch (error) {
        alert("Error in click to copy: " + error);
    }
}

// Bind events
document.addEventListener("click", saveMedia, false);
document.addEventListener("click", deleteMedia, false);
document.addEventListener("click", copyMediaPath, false);
