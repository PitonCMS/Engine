// --------------------------------------------------------
// Message management
// --------------------------------------------------------
import "./modules/main.js";
import { setFilterPath, applyFilters } from "./modules/filter.js";
import { postXHRPromise, getXHRPromise } from "./modules/xhrPromise.js";
import { disableSpinner, enableSpinner } from "./modules/spinner.js";

setFilterPath(pitonConfig.routes.adminMessageGet);
const unreadMessageCountBadge = document.querySelector(`[data-message="count"]`);

/**
 * Update Unread Message Count in Sidebar
 */
const updateUnreadMessageCount = function() {
    getXHRPromise(pitonConfig.routes.adminMessageCountGet)
        .then(data => {
            unreadMessageCountBadge.innerHTML = data;
        });
}

/**
 * Update Message
 * For Read, Archive status toggle, and Delete
 * @param {Event} event
 */
const updateMessage = function (event) {
    if (!event.target.dataset.messageControl) return;

    let messageParent = event.target.closest(`[data-message="parent"]`);
    let data = {"messageId": messageParent.dataset.messageId};

    // Process control request
    if (event.target.dataset.messageControl === 'delete') {
        // Message delete
        if (!confirm(event.target.dataset.messageDeletePrompt)) return;
        data["control"] = "delete";
    } else if (event.target.dataset.messageControl === 'archive') {
        // Toggle archive
        data["control"] = "archive";
    } else if (event.target.dataset.messageControl === 'read') {
        // Toggle read
        data["control"] = "read";
    }

    enableSpinner();
    postXHRPromise(pitonConfig.routes.adminMessageSave, data)
        .then(() => {
            applyFilters();
            updateUnreadMessageCount();
        })
        .then(() => {
            disableSpinner();
        });
}

// Bind event handlers to page
document.addEventListener("click", updateMessage, false);

/*
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
*/
