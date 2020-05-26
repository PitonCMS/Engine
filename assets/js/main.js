// --------------------------------------------------------
// Main JS
// --------------------------------------------------------

import { enableFormControl, disableFormControl } from './modules/formControl.js';
import { dismissAlertInlineMessage } from './modules/alert.js';

// Toggle block collapse
document.querySelectorAll(`[data-collapse="toggle"]`).forEach(toggle => {
    const collapseTarget = toggle.parentElement.querySelector(`[data-collapse="target"]`);
    toggle.addEventListener("click", () => {
        if (collapseTarget.classList.contains("collapsed")) {
            collapseTarget.classList.remove("collapsed");
        } else {
            collapseTarget.classList.add("collapsed");
        }
    });
});

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
            let userResponse = confirm("Click Ok to discard your changes, or cancel continue editing?");
            if (!userResponse) e.preventDefault();
        });
    });

    // Confirm delete
    form.querySelectorAll(`[data-delete-prompt]`).forEach(control => {
        control.addEventListener("click", (e) => {
            if (!confirm(e.target.dataset.deletePrompt)) e.preventDefault();
        });
    });
});

// Binding click events to document
document.addEventListener("click", dismissAlertInlineMessage);
// document.addEventListener("click", mediaSelect);

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
