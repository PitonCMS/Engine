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
