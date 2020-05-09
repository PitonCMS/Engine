// --------------------------------------------------------
// Main JS
// --------------------------------------------------------

// $('.jsDatePicker').datepicker({
//     format: pitonConfig.dateFormat,
//     weekStart: pitonConfig.weekStart,
//     todayHighlight: true,
//     orientation: 'bottom',
//     autoclose: true,
//     clearBtn: true
// });

/**
 * Confirm Prompt
 *
 * Confirmation message
 * @param {string} msg
 * @return {boolean}
 */
const confirmPrompt = function (msg) {
    let message = msg || 'Are you sure you want to delete?';
    return confirm(message);
}

/**
 * Bind delete confirm prompt to all elements with .jsDeleteConfirm
 */
document.querySelectorAll(".jsDeleteConfirm").forEach(del => {
    del.addEventListener("click", (e) => {
        if (!confirmPrompt()) {
            e.preventDefault();
        }
    });
});

/**
 * Confirm logout prompt
 */
document.querySelector(".jsLogout").addEventListener("click", (e) => {
    if (!confirmPrompt("Are you sure you want to logout?")) {
        e.preventDefault();
    }
});

/**
 * Listen for form input changes to update save button status
 */
document.querySelectorAll("button[value=save]").forEach(button => {
    button.disabled = true;
    button.style.cursor = 'default';
    let formElement = button.closest("form");

    if (formElement) {
        formElement.querySelectorAll("input").forEach(el => {
            el.addEventListener("input", () => {
                if (button.disabled) {
                    button.disabled = false;
                    button.style.cursor = 'pointer';
                }
            });
        });
    }
});

/**
 * XHR GET Request Promise
 * @param {string} url   Resource request URL
 * @param {mixed} params String or object
 */
const getXHRPromise = function(url, params) {
    let xhr = new XMLHttpRequest();
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
                // TODO show page alert
                reject({
                    status: "error",
                    statusText: response.text
                });
            }
        } else {
            // Failed server response
            // TODO show page alert
            reject({
                status: xhr.status,
                statusText: xhr.statusText
            });
      }
    }

    // Setup and send
    xhr.open("GET", url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
  });
}

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
