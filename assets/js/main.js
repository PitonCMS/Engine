// --------------------------------------------------------
// Main JS
// --------------------------------------------------------

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
 * Disable Form Controls
 *
 * @param {object} save   Save button element
 * @param {object} cancel Cancel|Discard button element
 */
const disableFormControls =  function(save, cancel) {
    if (save && !save.disabled) {
        save.disabled = true;
        save.classList.add("disabled");

        if (cancel) cancel.classList.add("disabled");
    }
}

/**
 * Disable Form Controls
 *
 * @param {object} save   Save button element
 * @param {object} cancel Cancel|Discard button element
 */
const enableFormControls = function(save, cancel) {
    if (save && save.disabled) {
        save.disabled = false;
        save.classList.remove("disabled");

        if (cancel) cancel.classList.remove("disabled");
    }
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

    alert(`Severity: ${severity} ${message}`);
}

/**
 * GET XHR Promise Request
 * @param {string} url    Resource URL
 * @param {string} params Query string parameters
 */
const getXHRPromise = function(url, params) {
    let queryString;

    // Accept query string or build from object
    if (typeof params === 'string') {
        queryString = params;
    } else if (typeof params === 'object') {
        queryString = Object.keys(params).map((k) => {
            return encodeURIComponent(k) + '=' + encodeURIComponent(params[k])
        }).join('&');
    }

    // Attach query string to URL
    if (queryString) {
        url += '?' + queryString;
    }

    return XHRPromise("GET", url);
}

/**
 * POST XHR Promise Request
 * @param {string} url  Resource URL
 * @param {string} data FormData object or object key value pairs
 */
const postXHRPromise = function(url, data) {
    return XHRPromise("POST", url,  data);
}

/**
 * XHR Request Promise
 * @param {string} method "GET"|"POST"
 * @param {string} url    Resource request URL
 * @param {mixed}  data   String or object
 */
const XHRPromise = function(method, url, data) {
    let xhr = new XMLHttpRequest();

    return new Promise((resolve, reject) => {
        xhr.onreadystatechange = () => {
            if (xhr.readyState !== XMLHttpRequest.DONE) return;

            if (xhr.status === 200) {
                // Successful server response
                let response = JSON.parse(xhr.responseText);
                if (response.status === "success") {
                    // Response content successful
                    resolve(response.text);
                } else {
                    // Response content failed
                    reject(alertMessage('danger', {
                        status: "error",
                        statusText: response.text
                    }));
                }
            } else {
                // Failed server response
                reject(alertMessage('danger', {
                    status: "error",
                    statusText: response.text
                }));
            }
        }

        // Setup and send
        xhr.open(method, url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(data);
    });
}

/**
 * Bind delete confirm prompt to all elements with .jsDeleteConfirm
 */
document.querySelectorAll(".jsDeleteConfirm").forEach(del => {
    del.addEventListener("click", (e) => {
        if (!confirmPrompt()) e.preventDefault();
    });
});

/**
 * Confirm logout prompt
 */
// TODO waitig on logout button to be added
// document.querySelector(".jsLogout").addEventListener("click", (e) => {
//     if (!confirmPrompt("Are you sure you want to logout?")) {
//         e.preventDefault();
//     }
// });

/**
 * Listen for form input changes to update save and discard button status
 */
document.querySelectorAll("form").forEach(form => {
    let cancelLink = form.querySelector(".jsFormCancelButton");
    let saveButton = form.querySelector(".jsFormSaveButton");

    if (saveButton) {
        disableFormControls(saveButton, cancelLink);

        // Listen for form changes to enable controls
        form.querySelectorAll("input, textarea, select").forEach(el => {
            el.addEventListener("input", () => {
                enableFormControls(saveButton, cancelLink);
            });
        });

        // Cancel/Discard button should have confirm prompt before reloading page
        if (cancelLink) {
            cancelLink.addEventListener("click", (e) => {
                let userResponse = confirmPrompt("Click Ok to discard your changes, or cancel continue editing?");
                if (!userResponse) e.preventDefault();
            });
        }
    }
});

// Dismissable alert
document.querySelectorAll(".jsDismissAlert").forEach(dismiss => {
    let alertContainer = dismiss.closest('.container');
    alertContainer.addEventListener("click", () => {
        alertContainer.remove();
    });
});



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
