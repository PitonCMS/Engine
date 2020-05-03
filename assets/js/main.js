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

// Delete confirm prompt message
const confirmPrompt = function (msg) {
    let message = msg || 'Are you sure you want to delete?';
    return confirm(message);
}

// Bind form delete click event on the jsDeleteConfirm class
document.querySelectorAll(".jsDeleteConfirm").forEach(del => {
    del.addEventListener("click", (e) => {
        if (!confirmPrompt()) {
            e.preventDefault();
        }
    });
});

// Confirm logout prompt
document.querySelector(".jsLogout").addEventListener("click", (e) => {
    if (!confirmPrompt("Are you sure you want to logout?")) {
        e.preventDefault();
    }
});

// Listen for form input changes to update save button status
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
