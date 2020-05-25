// --------------------------------------------------------
// Main JS
// --------------------------------------------------------

import { enableFormControl, disableFormControl } from './modules/formControl.js';
import { dismissAlertInlineMessage } from './modules/alert.js';

/**
 * Confirm Prompt
 *
 * Default message text is a delete prompt
 * @param {string} msg
 * @return {boolean}
 */
const confirmPrompt = function(msg) {
    let message = msg || 'Are you sure you want to delete?';
    return confirm(message);
}

/**
 * Alert Message
 *
 * System notifications and alerts
 * @param {string} severity
 * @param {mixed} message
 */
const alertMessage = function(severity, message) {
    if (typeof message === 'object') {
        message = JSON.stringify(message);
    }

    alert(`${severity}: ${message}`);
}

// Form Control Events
document.querySelectorAll("form").forEach(form => {
    // Disable form controls and listen for form input changes to re-enable save controls
    let saveButtons = form.querySelectorAll(`[data-form-button="save"]`);
    if (saveButtons) {
        saveButtons.forEach(control => {
            disableFormControl(control);
        });

        // Listen for form changes to reenable controls
        form.addEventListener("input", (i) => {
            saveButtons.forEach(control => {
                enableFormControl(control);
            });
        });

    }

    // Confirm discard of changes
    form.querySelectorAll(`[data-form-button="cancel"]`).forEach(control => {
        control.addEventListener("click", (e) => {
            let userResponse = confirmPrompt("Click Ok to discard your changes, or cancel continue editing?");
            if (!userResponse) e.preventDefault();
        });
    });

    // Confirm delete
    form.querySelectorAll(`[data-delete-prompt]`).forEach(control => {
        control.addEventListener("click", (e) => {
            if (!confirmPrompt(e.target.dataset.deletePrompt)) e.preventDefault();
        });
    });
});


// // Get base modal available in all pages
const modal = document.getElementById("modal");

/**
 * Show Modal (Background)
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
const loadModalContent = function(heading, content, buttons) {
    modal.querySelector(".modal-header > h2").innerHTML = heading;
    modal.querySelector(".modal-body").innerHTML = content;
    modal.querySelector(".modal-content").classList.remove("d-none");
}

/**
 * Hide Modal and Clear Contents
 */
const hideModal = function() {
    modal.classList.add("d-none");
    modal.querySelector(".modal-content").classList.add("d-none");
    modal.querySelector(".modal-header > h2").innerHTML = "";
    modal.querySelector(".modal-body").innerHTML = "";
}

// Bind close modal events
// modal.querySelector(".close").addEventListener("click", () => {
//     hideModal();
// });
// window.addEventListener("click", (event) => {
//     if (event.target === modal) {
//         hideModal();
//     }
// });

/**
 * Opens Modal with Media Images for Select
 * @param {Element} elementTarget Media target
 */
const openMediaModal = function(elementTarget) {
    showModal();
    getXHRPromise(pitonConfig.routes.adminMediaGet)
        .then(data => {
            loadModalContent("Select Media", data);
        });

    // Add click listener to set media ID on select and dismiss
    modal.querySelector('.modal-body').addEventListener("click", (e) => {
        if (e.target.closest(".media")) {
            // Get media data and set in form
            let data = {
                "id": e.target.closest(".media").dataset.mediaId,
                "caption": e.target.closest(".media").dataset.mediaCaption,
                "filename": e.target.closest(".media").dataset.mediaPath
            }

            // Set ID, filename and relative path, an caption in target element
            elementTarget.querySelector(`input[name*="media_id"]`).value = data.id;
            elementTarget.querySelector(`img`).src = data.filename;
            elementTarget.querySelector(`img`).alt = data.caption;
            elementTarget.querySelector(`img`).title = data.caption;
            elementTarget.querySelector(`img`).classList.remove("d-none");

            hideModal();
        }
    });
}

// Media select modal
const mediaSelector = function(event) {
    if (event.target.dataset.mediaModal) {
        // Launch media modal with target element
        openMediaModal(event.target.closest(`[data-media-select="1"]`));
    } else if (event.target.dataset.mediaClear) {
        // Clear media from form
        let mediaElement = event.target.closest(`[data-media-select="1"]`);
        mediaElement.querySelector(`input[name*="media_id"]`).value = "";
        mediaElement.querySelector(`img`).src = "";
        mediaElement.querySelector(`img`).alt = "";
        mediaElement.querySelector(`img`).title = "";
        mediaElement.querySelector(`img`).classList.add("d-none");
    }
}

// Binding click events to document
document.addEventListener("click", dismissAlertInlineMessage);
document.addEventListener("click", mediaSelector);

// $('.jsDatePicker').datepicker({
//     format: pitonConfig.dateFormat,
//     weekStart: pitonConfig.weekStart,
//     todayHighlight: true,
//     orientation: 'bottom',
//     autoclose: true,
//     clearBtn: true
// });



// // --------------------------------------------------------
// // Media Page Management
// // --------------------------------------------------------
// Clear media input and remove image display
// $('.jsEditPageContainer').on('click', '.jsMediaClear', function () {
//     $(this).parents('.jsMediaInput').find('.jsMediaInputField').val('');
//     $(this).parents('.jsMediaInput').find('img').attr('src', '').addClass('d-none');

// });

// // --------------------------------------------------------
// // Media Page Management
// // --------------------------------------------------------

// // Clear media input and remove image display
// $('.jsEditPageContainer').on('click', '.jsMediaClear', function () {
//     $(this).parents('.jsMediaInput').find('.jsMediaInputField').val('');
//     $(this).parents('.jsMediaInput').find('img').attr('src', '').addClass('d-none');
// });

// // Select media for page element
// $('.jsEditPageContainer').on('click', '.jsSelectMediaFile', function () {
//     let $targetMediaInput = $(this).parents('.jsMediaInput');

//     // Set media ID and source into page form inputs when media file is selected
//     $('#mediaModal').on('click', 'img', function () {
//         $targetMediaInput.find('.jsMediaInputField').val($(this).data('mediaId'));
//         $targetMediaInput.find('img').attr('src', $(this).data('source')).removeClass('d-none');
//         $('#mediaModal').modal('hide');
//     });

//     // Fetch available media into selector modal
//     $.ajax({
//         url: pitonConfig.routes.adminMediaGet,
//         method: "GET",
//         success: function (r) {
//             $('#mediaModal').find('.modal-body').html(r.html).end().modal();
//         }
//     });
// });
