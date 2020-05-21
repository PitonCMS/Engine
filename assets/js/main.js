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
 * Enable Form Control
 *
 * @param {object} control Control button element
 */
const enableFormControl = function(control) {
    if (control && control.disabled) {
        control.disabled = false;
        control.classList.remove("disabled");
    }
}

/**
 * Disable Form Control
 *
 * @param {object} control Control button element
 */
const disableFormControl =  function(control) {
    if (control && !control.disabled) {
        control.disabled = true;
        control.classList.add("disabled");
    }
}

/**
 * Enable Spinner Overlay
 * @param {void}
 */
const enableSpinner = function() {
    if (!document.querySelector("body").classList.contains("spinner")) {
        document.querySelector("body").classList.add("spinner")
    }
}

/**
 * Disable Spinner Overlay
 * @param {void}
 */
const disableSpinner = function() {
    if (document.querySelector("body").classList.contains("spinner")) {
        document.querySelector("body").classList.remove("spinner")
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

    alert(`${severity}: ${message}`);
}

/**
 * Dismiss Inline Alert
 * @param {Event} event
 */
const dismissAlertInlineMessage = function(event) {
    if (event.target.dataset.dismiss === "alert") {
        event.target.closest("div.alert").remove();
    }
}

/**
 * Display Inline HTML Message Alert
 * @param {string} severity Severity color code
 * @param {string} heading  Message heading
 * @param {mixed} message   Message list
 */
const alertInlineMessage = function(severity, heading, message) {
    // Create element and insert alert HTML and update with alert data
    let container = document.createElement("div");
    container.innerHTML = pitonConfig.alertInlineHTML;
    container.querySelector("div").classList.add("alert-" + severity);
    container.querySelector("div").classList.remove("d-none");
    container.querySelector("h4").innerHTML = heading;

    // Stringify message
    if (typeof message === 'object') {
        message = message.join("<br>");
    }
    container.querySelector(".alert__message").innerHTML = message;

    // Insert into main or body
    let mainContainer = document.querySelector("main.main-content");
    mainContainer.insertAdjacentHTML('afterbegin', container.innerHTML);
    window.scrollTo(0,0);
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
    data = data || {};
    data[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue

    // Serialize data
    postData = Object.keys(data).map((k) => {
        return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');

    return XHRPromise("POST", url,  postData);
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

            try {
                if (xhr.status === 200) {
                    // Successful server response
                    let response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        // Response content successful
                        resolve(response.text);
                    } else {
                        // Response successful but application failed
                        reject(alertInlineMessage('danger', 'Error', [response.text]));
                    }
                } else {
                    // Failed server runtime response
                    reject(alertInlineMessage('danger', 'Error', [response.text]));
                }
            } catch (error) {
                reject(alertInlineMessage('danger', 'Error', [error]));
            }
        }

        // Setup and send
        xhr.open(method, url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(data);
    });
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
        form.addEventListener("input", () => {
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



// Binding click events to document
document.addEventListener("click", dismissAlertInlineMessage);


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
