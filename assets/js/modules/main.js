// --------------------------------------------------------
// Main JS
// --------------------------------------------------------

import { enableFormControl, disableFormControl } from './formControl.js';
import { dismissAlertInlineMessage } from './alert.js';
import { collapseToggle } from './collapse.js';

// Form Control Events
document.querySelectorAll("form").forEach(form => {
    // Disable form controls and listen for form input changes to re-enable save controls
    let saveButtons = form.querySelectorAll(`[data-form-button="save"]`);
    if (saveButtons) {
        saveButtons.forEach(control => {
            disableFormControl(control);
        });

        // Listen for form changes to reenable controls
        form.addEventListener("input", (e) => {
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
document.addEventListener("click", collapseToggle);