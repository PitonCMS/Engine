// --------------------------------------------------------
// Upload media
// --------------------------------------------------------

import { loadModal, loadModalContent, removeModal } from './modal.js';
import { enableSpinner, disableSpinner } from './spinner.js';
import { getXHRPromise, postXHRPromise } from './xhrPromise.js';
import { alertInlineMessage } from './alert.js';

// Flag to reload page after upload, or asynchronously
const refreshPageOnUpload = document.querySelector(`[data-media-refresh="true"]`) ? true : false;

/**
 * Show Media Upload Form in Modal
 */
const showMediaUploadForm = function() {
    // Get file upload form with most current list of categories
    loadModal();
    getXHRPromise(pitonConfig.routes.adminMediaUploadFormGet)
        .then(data => {
            loadModalContent("Upload Media", data);
        })
        .catch((error) => {
            removeModal();
            alertInlineMessage("danger", "Failed To Open Media Upload Modal", error);
        });
}

/**
 * Media Upload
 * @param {Event} event
 */
const mediaUpload = function(event) {
    if (event.target.dataset.mediaUpload !== "file") return;

    enableSpinner();
    const form = document.querySelector(`form[data-media-upload="form"]`);

    postXHRPromise(pitonConfig.routes.adminMediaUploadFile, new FormData(form))
        .then(() => {
            if (refreshPageOnUpload) {
                window.location.reload();
            }
        })
        .then(() => {
            removeModal();
        })
        .then(() => {
            disableSpinner();
        })
        .catch((error) => {
            removeModal();
            disableSpinner();
            alertInlineMessage('danger', 'Failed to Upload File', error);
        });
}

// Bind page events
document.addEventListener("click", mediaUpload, false);
document.querySelectorAll(`[data-media-upload="form"]`)?.forEach(upload => {
    upload.addEventListener("click", showMediaUploadForm, false);
});
