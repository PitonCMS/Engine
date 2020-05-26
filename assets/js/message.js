// --------------------------------------------------------
// Message management
// --------------------------------------------------------
// +/- Message count
let changeMessageCount = (sign) => {
    if (sign) {
        let count = parseInt($('.jsMessageCount').html() || 0);
        if ('+' === sign) {
            count++
        } else if ('-' === sign) {
            if (--count == 0) count = null;
        }
        $('.jsMessageCount').html(count);
    }
}
let removeMessage = ($message, sign) => {
    $message.slideUp(function () {
        $message.remove();
    });
    changeMessageCount(sign);
}
$('.jsMessageWrap').on('click', 'button', function (e) {
    e.preventDefault();
    let request = $(e.target).attr('value');
    if ('delete' === request && !confirm()) {
        return false;
    }
    let isRead = $(e.target).data('isRead');
    let $message = $(e.target).parents('.jsMessageWrap');
    let postData = $message.find('form').serialize();
    $.ajax({
        url: (request == 'delete') ? pitonConfig.routes.adminMessageDelete : pitonConfig.routes.adminMessageSave,
        method: "POST",
        data: postData,
        success: function (r) {
            if (r.status === "success") {
                if ('toggle' === request) {
                    let updown = (isRead === 'Y') ? '+' : '-';
                    removeMessage($message, updown);
                } else if ('delete' === request) {
                    let updown = (isRead === 'N') ? '-' : undefined;
                    removeMessage($message, updown);
                }
            }
        },
        error: function (r) {
            console.log('PitonCMS: There was an error submitting the form. Contact your administrator.')
        }
    });
});
