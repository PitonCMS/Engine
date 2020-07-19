// --------------------------------------------------------
// Page Edit JS
// --------------------------------------------------------

import './modules/main.js';
import { enableSpinner, disableSpinner } from './modules/spinner.js';
import { getXHRPromise, postXHRPromise } from './modules/xhrPromise.js';
import { mediaSelect } from './modules/mediaModal.js';
import { setCleanSlug, unlockSlug } from './modules/url.js';
import { dragStartHandler, dragEnterHandler, dragOverHandler, dragLeaveHandler, dragDropHandler, dragEndHandler } from './modules/drag.js';
import { alertInlineMessage } from './modules/alert.js';

/**
 * Set Element Title
 */
const setElementTitleText = function(event) {
    if (!event.target.matches(`input[name*="element_title"]`)) return;

    let title = event.target.value;
    let elementTitle = event.target.closest(`[data-element="parent"]`).querySelector(".secondary-title");
    elementTitle.innerHTML = title;
}

/**
 * Text CK Editor
 * @param {object} textElement
 */
const initEditor = function(textElement) {
    ClassicEditor
        .create(textElement, {
            toolbar: ["heading","bold","italic","link","bulletedList","numberedList","blockQuote","undo","redo"]
        })
        .then(editor => {
            editor.model.document.on('change:data', (e) => {
                textElement.dispatchEvent(new Event("input", {"bubbles": true}));
            });

            // Displays toolbar options to include in the toolbar config above
            // console.log(Array.from( editor.ui.componentFactory.names()));
        })
        .catch(error => {
            console.error(error);
        });
}

/**
 * Enable Draggable
 * Use with mouseup event to re-enable draggable=true on parent element
 * @param {Event} event
 */
const enableDraggable = function(event) {
    if (event.target.closest(`[data-drag-handle="true"]`)) return;
    event.target.closest(`[data-element="parent"]`).setAttribute("draggable", true);
}

/**
 * Disable Draggable
 * Use with mousedown event to disable draggable=true on parent element
 * @param {Event} event
 */
const disableDraggable = function(event) {
    if (event.target.closest(`[data-drag-handle="true"]`)) return;
    event.target.closest(`[data-element="parent"]`).setAttribute("draggable", false);
}

// Add new event.target event
document.querySelectorAll(`[data-element-select-block]`).forEach(block => {
    // Track element count and limit to enable or disable new elements
    let blockKey = block.dataset.elementSelectBlock;
    let blockElementCount = parseInt(block.dataset.elementCount ?? 0);
    let blockElementCountLimit = parseInt(block.dataset.elementCountLimit);
    let newElementDropdown = block.querySelector(`[data-collapse-toggle*="newElementButton"]`).parentElement;

    const addElementToggleState = function (increment) {
        blockElementCount = blockElementCount + increment;

        if (blockElementCount >= blockElementCountLimit) {
            // Disable
            newElementDropdown.classList.add("dropdown-disabled");
        } else {
            // Enable
            newElementDropdown.classList.remove("dropdown-disabled");
        }
    }

    // New element
    block.querySelectorAll(`a[data-element="add"]`).forEach(addEl => {
        addEl.addEventListener("click", (e) => {
            e.preventDefault();

            // Check element limit
            if (blockElementCount >= blockElementCountLimit) {
                return;
            }

            // Get new element
            enableSpinner();

            // Get query string and XHR Promise
            let query = {
                "template": addEl.dataset.elementTemplate,
                "blockKey": blockKey
            }

            getXHRPromise(pitonConfig.routes.adminPageElementGet, query)
                .then(response => {
                    let container = document.createElement("div");
                    let targetBlock = document.getElementById("block-" + blockKey);
                    container.innerHTML = response;

                    // Update element count
                    addElementToggleState(1);

                    container.querySelector(`[data-element="parent"]`).classList.add("new-element");
                    targetBlock.insertAdjacentHTML('beforeend', container.innerHTML);

                    // Unable to initalize SimpleMDE on the unattached HTML fragment until we insert it
                    let newEditor = targetBlock.lastElementChild.querySelector(`textarea[data-cke="true"]`);
                    initEditor(newEditor);

                    // Get new block ID for window scroll
                    let windowTarget = container.querySelector(`[data-element="parent"]`).getAttribute("id");

                    return windowTarget;
                })
                .then(() => {
                    disableSpinner();
                }).catch((error) => {
                    disableSpinner();
                    alertInlineMessage("danger", "Failed to Add Element", error);
                });
        }, false);
    });

    // Delete element
    block.addEventListener("click", (event) => {
        if (!event.target.dataset.deleteElementPrompt) return;
        // Confirm delete
        if (!confirm(event.target.dataset.deleteElementPrompt)) return;

        // Get element ID and element
        let elementId = parseInt(event.target.dataset.elementId);
        let element = event.target.closest(`[data-element="parent"]`);

        if (isNaN(elementId)) {
            // Element has not been saved to DB, just remove from DOM
            element.remove();
        } else {
            // Element has been saved, do a hard delete
            enableSpinner();
            postXHRPromise(pitonConfig.routes.adminPageElementDelete, {"elementId": elementId})
                .then(() => {
                    element.remove();
                })
                .then(() => {
                    disableSpinner();
                })
                .catch((error) => {
                    disableSpinner();
                    alertInlineMessage("danger", "Failed to Delete Element", error);
                });
        }

        addElementToggleState(-1);

    }, false);
});

// Bind CK Editor to selected textareas on page load
document.querySelectorAll(`textarea[data-cke="true"]`).forEach(editor => {
    initEditor(editor);
});

// Bind set page slug from page title
document.querySelector(`[data-url-slug="source"]`).addEventListener("input", (e) => {
    setCleanSlug(e.target.value);
}, false);

// Bind warning on unlocking page slug
document.querySelector(`[data-url-slug-lock="1"]`).addEventListener("click", (e) => {
    unlockSlug(e);
}, false);

// Bind page edit listeners for events that bubble
document.addEventListener("click", mediaSelect, false);
document.addEventListener("change", setElementTitleText, false);

// Draggable page elements
document.querySelectorAll(`[data-draggable="children"]`).forEach(zone => {
    zone.addEventListener("mousedown", disableDraggable, false);
    zone.addEventListener("mouseup", enableDraggable, false);
    zone.addEventListener("dragstart", dragStartHandler, false);
    zone.addEventListener("dragenter", dragEnterHandler, false);
    zone.addEventListener("dragover", dragOverHandler, false);
    zone.addEventListener("dragleave", dragLeaveHandler, false);
    zone.addEventListener("drop", dragDropHandler, false);
    zone.addEventListener("dragend", dragEndHandler, false);
});
