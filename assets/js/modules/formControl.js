// --------------------------------------------------------
// Form Controls. Enables controls on form input, and prompts on reset and delete
// --------------------------------------------------------

// Form Control Events
document.querySelectorAll("form").forEach(form => {
    // Disable form controls and listen for form input changes to re-enable save controls
    let controls = form.querySelectorAll('[data-form-button="save"], [data-form-button="cancel"]');

    if (controls) {
        controls.forEach(control => {
            control.disabled = true;
        });

        // Listen for form changes to reenable controls
        form.addEventListener("input", (e) => {
            controls.forEach(control => {
                control.disabled = false;
            });
        }, false);
    }

    // Confirm discard of changes
    form.querySelectorAll(`[data-form-button="cancel"]`).forEach(control => {
        control.addEventListener("click", (event) => {
            if(!confirm("Click Ok to discard your changes, or Cancel continue editing.")) {
                event.preventDefault();
                return;
            }

            // Reload page if a url was provided
            if (event.target.dataset.formResetHref) {
                event.preventDefault();
                window.location = event.target.dataset.formResetHref;
            } else {
                // Otherwise let type="reset" reset form as default event
                controls.forEach(control => {
                    control.disabled = true;
                });
            }
        }, false);
    });

    // Confirm delete
    form.querySelectorAll(`[data-delete-prompt]`).forEach(control => {
        control.addEventListener("click", (e) => {
            if (!confirm(e.target.dataset.deletePrompt)) e.preventDefault();
        }, false);
    });
});
