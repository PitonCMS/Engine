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

const deleteMedia = function(event) {
    if (!event.target.dataset.deleteMediaPrompt) return;
    if (!confirm(event.target.dataset.deleteMediaPrompt)) return;

    const form = event.target.closest(".media");
    let mediaId = event.target.dataset.mediaId;

    enableSpinner();
    postXHRPromise(pitonConfig.routes.adminMediaDelete, {"media_id": mediaId})
        .then(() => {
            form.remove();
        })
        .then(() => {
            disableSpinner();
        })
        .catch((error) => {
            disableSpinner();
            alertInlineMessage('danger', 'Failed to Delete Media', error);
        });
}

document.addEventListener("click", saveMedia, false);
document.addEventListener("click", deleteMedia, false);
