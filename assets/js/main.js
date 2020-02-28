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

$('body').on('click', '.jsDeleteConfirm', function () {
    return confirmPrompt();
});

$('.jsLogout').on('click', function () {
    return confirmPrompt('Are you sure you want to logout?');
});

// --------------------------------------------------------
// Media Page Management
// --------------------------------------------------------

// Clear media input and remove image display
$('.jsEditPageContainer').on('click', '.jsMediaClear', function () {
    $(this).parents('.jsMediaInput').find('.jsMediaInputField').val('').trigger("input");
});

// Listen for media input changes by user to update media img display
$('.jsEditPageContainer').on('input', '.jsMediaInputField', function() {
    let src = $(this).val();
    let $img = $(this).parents('.jsMediaInput').find('img');
    $img.attr('src', src);
    if (src.length > 0) {
        $img.removeClass('d-none').addClass('d-block');
    } else {
        $img.removeClass('d-block').addClass('d-none');
    }
})

// Select media for page element
$('.jsEditPageContainer').on('click', '.jsSelectMediaFile', function () {
    let $input = $(this).parents('.jsMediaInput').find('input.jsMediaInputField');

    $('#mediaModal').on('click', 'img', function () {
        $input.val($(this).data('source')).trigger("input");
        $('#mediaModal').modal('hide');
    });

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

