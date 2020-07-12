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
    if (event.target.matches(`input[name^="element_title"]`)) {
        let title = event.target.value;
        let elementTitle = event.target.closest(`[data-element="parent"]`).querySelector(".secondary-title");
        elementTitle.innerHTML = title;
    }
}

/**
 * Markdown Editor
 * @param {object} element
 */
const initMarkdownEditor = function(element) {
    return new SimpleMDE({
        element: element,
        forceSync: true,
        promptURLs: true,
        toolbar: [
          "bold", "italic", "|", "heading-2", "heading-3", "|", "unordered-list", "ordered-list", "|",
          "horizontal-rule", "table", "|", "link",
        //   {
        //     name: "image",
        //     // action: getMediaForMDE,
        //     className: "fa fa-picture-o",
        //     title: "Media"
        //   },
          "guide"
        ]
      });
}

// Add Page Block Element
document.querySelectorAll(`a[data-element="add"]`).forEach(addEl => {
    addEl.addEventListener("click", (e) => {
        e.preventDefault();
        // let limit = parseInt(addEl.dataset.elementCountLimit) || 100;
        // let count = parseInt(addEl.dataset.elementCount) || 0;

        // // Check element limit
        // if (count >= limit) {
        //     alert('This Block has the maximum number of Elements allowed by the design');
        //     return;
        // }

        // Get new element
        enableSpinner();

        // Get query string and XHR Promise
        let query = {
            "template": addEl.dataset.elementTemplate,
            "blockKey": addEl.dataset.blockKey
        }

        getXHRPromise(pitonConfig.routes.adminPageElementGet, query)
            .then(response => {
                let container = document.createElement("div");
                let targetBlock = document.getElementById("block-" + addEl.dataset.blockKey);
                container.innerHTML = response;

                // Set element order number and update count in add element data-element-count
                // addEl.dataset.elementCount = ++count;

                container.querySelector(`[data-element="parent"]`).classList.add("new-element");
                targetBlock.insertAdjacentHTML('beforeend', container.innerHTML);

                // Unable to initalize SimpleMDE on the unattached HTML fragment until we insert it
                let newEditor = targetBlock.lastElementChild.querySelector(`textarea[data-mde="1"]`);
                initMarkdownEditor(newEditor);

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

// Get Page Edit block
const pageEditNode = document.querySelector(`[data-page-edit="1"]`);

// Delete element
if (pageEditNode) {
    pageEditNode.addEventListener("click", (event) => {
        if (event.target.dataset.deleteElementPrompt) {
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
        }
    }, false);
}

// Bind Markdown Editor to selected textareas on page load
if (pageEditNode) {
    pageEditNode.querySelectorAll(`textarea[data-mde="1"]`).forEach(editor => {
        initMarkdownEditor(editor);
    });
}

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
    zone.addEventListener("dragstart", dragStartHandler, false);
    zone.addEventListener("dragenter", dragEnterHandler, false);
    zone.addEventListener("dragover", dragOverHandler, false);
    zone.addEventListener("dragleave", dragLeaveHandler, false);
    zone.addEventListener("drop", dragDropHandler, false);
    zone.addEventListener("dragend", dragEndHandler, false);
});

/*

// Bind Markdown Editor to Textareas
let getMediaForMDE = function (editor) {
    // Bind media click once, and load media in modal
    $('#mediaModal').unbind().on('click', 'img', function () {
      let imgsrc = $(this).data('source');
      let imgalt = $(this).data('caption');
      let output = '![' + imgalt + '](' + imgsrc + ') ';
      editor.codemirror.replaceSelection(output);
      editor.codemirror.focus();

      $('#mediaModal').modal('hide');
    });

    $.ajax({
      url: pitonConfig.routes.adminMediaGet,
      method: "GET",
      success: function (r) {
        $('#mediaModal').find('.modal-body').html(r.html).end().modal();
      }
    });
  };

  [].forEach.call(document.getElementsByClassName('jsMDE'), element => {
    let simplemde = new SimpleMDE({
      element: element,
      forceSync: true,
      promptURLs: true,
      toolbar: [
        "bold", "italic", "|", "heading-2", "heading-3", "|", "unordered-list", "ordered-list", "|",
        "horizontal-rule", "table", "|", "link",
        {
          name: "image",
          action: getMediaForMDE,
          className: "fa fa-picture-o",
          title: "Media"
        },
        "guide"
      ]
    });
  });

/* */
