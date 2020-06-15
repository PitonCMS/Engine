// --------------------------------------------------------
// Media management
// --------------------------------------------------------

import './modules/main.js';
import './modules/mediaUpload.js';
import { enableSpinner, disableSpinner } from './modules/spinner.js';
import { getXHRPromise, postXHRPromise } from './modules/xhrPromise.js';
import { alertInlineMessage } from './modules/alert.js';

/**
 * Save Media
 * @param {Event} event
 */
const saveMedia = function(event) {
    if (event.target.dataset.formButton !== "save") return;
    const form = event.target.closest("form");

    postXHRPromise(pitonConfig.routes.adminMediaSave, new FormData(form))
        .then(() => {
            // Show save complete by disabling save button again
            let button = form.querySelector(`[data-form-button="save"]`);
            button.disabled = true;
        })
        .then(() => {
            disableSpinner();
        })
        .catch((text) => {
            console.log("Failed to save media: ", text);
            alertInlineMessage('danger', 'Failed to Save Media', text);
            disableSpinner();
        });
}

const deleteMedia = function(event) {
    if (!event.target.dataset.deleteMediaPrompt) return;
    if (!confirm(event.target.dataset.deleteMediaPrompt)) return;

        console.log("delete me")


}

document.addEventListener("click", saveMedia, false);
document.addEventListener("click", deleteMedia, false);
