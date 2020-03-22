// --------------------------------------------------------
// Main JS
// --------------------------------------------------------

$('.jsDatePicker').datepicker({
    format: pitonConfig.dateFormat,
    weekStart: pitonConfig.weekStart,
    todayHighlight: true,
    orientation: 'bottom',
    autoclose: true,
    clearBtn: true
});

// Delete confirm prompt
let confirmPrompt = function (msg) {
    let message = msg || 'Are you sure you want to delete?';
    return confirm(message);
}

// Listen for any delete click event with this class
$('body').on('click', '.jsDeleteConfirm', function () {
    return confirmPrompt();
});

// Confirm logout
$('.jsLogout').on('click', function () {
    return confirmPrompt('Are you sure you want to logout?');
});

// Listen for form input changes to update save button status
let saveButtonFlag = false;
const setSaveButtonIndicator = (buttonId) => {
  if (!saveButtonFlag) {
    buttonId = buttonId || '#jsSaveButtonInd';
    $(buttonId).attr('disabled', false);
    saveButtonFlag = true;
  }
};

// --------------------------------------------------------
// Media Page Management
// --------------------------------------------------------

// Clear media input and remove image display
$('.jsEditPageContainer').on('click', '.jsMediaClear', function () {
    $(this).parents('.jsMediaInput').find('.jsMediaInputField').val('');
    $(this).parents('.jsMediaInput').find('img').attr('src', '').addClass('d-none');
});

// Select media for page element
$('.jsEditPageContainer').on('click', '.jsSelectMediaFile', function () {
    let $targetMediaInput = $(this).parents('.jsMediaInput');

    // Set media ID and source into page form inputs when media file is selected
    $('#mediaModal').on('click', 'img', function () {
        $targetMediaInput.find('.jsMediaInputField').val($(this).data('mediaId'));
        $targetMediaInput.find('img').attr('src', $(this).data('source')).removeClass('d-none');
        $('#mediaModal').modal('hide');
    });

    // Fetch available media into selector modal
    $.ajax({
        url: pitonConfig.routes.adminGetMedia,
        method: "GET",
        success: function (r) {
            $('#mediaModal').find('.modal-body').html(r.html).end().modal();
        }
    });
});

// Enable Popovers
$(function () {
    $('[data-toggle="popover"]').popover({
        container: 'html',
    })
})
$('.popover-dismiss').popover({
    trigger: 'focus',
});
