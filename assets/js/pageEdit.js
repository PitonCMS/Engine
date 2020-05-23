// --------------------------------------------------------
// Page Edit JS
// --------------------------------------------------------

/**
 * Remove Element
 * @param {object} element
 */
const removeElement = function(element) {
    element.remove();
}

/**
 * Markdown Editor
 * @param {object} element
 */
const mdEditor = function(element) {
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

// Toggle block collapse
document.querySelectorAll(`[data-collapse="toggle"]`).forEach(toggle => {
    const collapseTarget = toggle.parentElement.querySelector(`[data-collapse="target"]`);
    toggle.addEventListener("click", () => {
        if (collapseTarget.classList.contains("collapsed")) {
            collapseTarget.classList.remove("collapsed");
        } else {
            collapseTarget.classList.add("collapsed");
        }
    });
});

// Add Page Block Element
document.querySelectorAll(`a[data-element="add"]`).forEach(addEl => {
    addEl.addEventListener("click", (e) => {
        e.preventDefault();
        let limit = parseInt(addEl.dataset.elementCountLimit) || 100;
        let count = parseInt(addEl.dataset.elementCount) || 0;

        // Check element limit
        if (count >= limit) {
            alertMessage('Info', 'This Block has the maximum number of Elements allowed by the design');
            return;
        }

        // Get new element
        enableSpinner();

        // Get query string and XHR Promise
        let query = {
            "pageTemplate": document.querySelector(`input[name="template"]`).value,
            "blockKey": addEl.dataset.blockKey
        }

        getXHRPromise(pitonConfig.routes.adminPageElementGet, query)
            .then(response => {
                let container = document.createElement("div");
                let targetBlock = document.getElementById("block-" + addEl.dataset.blockKey);
                container.innerHTML = response;

                // Set element order number and update count in add element
                addEl.dataset.elementCount = ++count;

                // Setting .value = addEl.dataset.elementCount in this fragment updates the DOM, but not the HTML
                container.querySelector(`input[name^="element_sort"]`).setAttribute('value', addEl.dataset.elementCount);
                mdEditor(container.querySelector(`textarea[data-mde="1"]`));
                targetBlock.insertAdjacentHTML('beforeend', container.innerHTML);

                // Get new block ID for window scroll
                let windowTarget = container.querySelector(`[data-element="parent"]`).getAttribute("id");

                return windowTarget;
            })
            .then(target => {
                // TODO Smooth scroll leaving room for navs
                window.location.hash = target;
            })
            .then(() => {
                disableSpinner();
            });
    });
});

// Get Page Edit block
const pageEditNode = document.querySelector(`[data-page-edit="1"]`);

// Delete element
if (pageEditNode) {
    pageEditNode.addEventListener("click", (event) => {
        if (event.target.dataset.deleteElementPrompt) {
            // Confirm delete
            if (!confirmPrompt(event.target.dataset.deleteElementPrompt)) return;

            // Get element ID and element
            let elementId = parseInt(event.target.dataset.elementId);
            let element = event.target.closest(`[data-element="parent"]`);

            if (isNaN(elementId)) {
                // Element has not been saved to DB, just remove from DOM
                removeElement(element);
            } else {
                // Element has been saved, do a hard delete
                enableSpinner();
                let data = {
                    "elementId": elementId
                }

                postXHRPromise(pitonConfig.routes.adminPageElementDelete, data)
                    .then(() => {
                        removeElement(element);
                    })
                    .then(() => {
                        disableSpinner();
                    });
            }
        }
    });
}

// Enable additional inputs on elements when selected
if (pageEditNode) {
    pageEditNode.addEventListener("click", (event) => {
        if (event.target.dataset.elementEnableInput) {
            let elementParent = event.target.closest(`[data-element="parent"]`);
            let requiredOption = event.target.dataset.elementEnableInput;

            // Get special inputs and set visible or hide class
            elementParent.querySelectorAll(`[data-element-input-option]`).forEach(option => {
                if (requiredOption === option.dataset.elementInputOption) {
                    option.classList.remove("d-none");
                    option.classList.add("d-block");
                } else {
                    option.classList.add("d-none");
                    option.classList.remove("d-block");
                }

            });
        }
    });
}

// Bind Markdown Editor to selected textareas
if (pageEditNode) {
    pageEditNode.querySelectorAll(`textarea[data-mde="1"]`).forEach(editor => {
        mdEditor(editor);
    });
}

/*
// Clean Page URL slug from title
let $pageSlug = $('.jsUrlSlug');
$('.jsPageTitle').on('change', function () {
    if ($pageSlug.val() === 'home') return;
    if (pitonConfig.pageSlugLocked !== 'lock') {
    let slug = this.value;
        slug = slug.replace(/&/g, 'and');
        slug = slug.replace(`'`, '');
        slug = slug.toLowerCase();
        slug = slug.replace(/[^a-z0-9]+/gi, '-');
        slug = slug.replace(/-+$/gi, '');
        $pageSlug.val(slug);
    }
});

// Unlock Page URL slug on request
$('.jsUrlSlugFaLockStatus').on('click', function () {
    // Ignore if home page
    if ($pageSlug.val() === 'home') return;
    if (pitonConfig.pageSlugLocked === 'lock' && confirmPrompt('Are you sure you want to change the URL Slug? This can impact links and search engines.')) {
        pitonConfig.pageSlugLocked = 'unlock';
        $pageSlug.attr('readonly', false);
        $(this).find('i.fas').toggleClass('fa-lock fa-unlock');
    }
});

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
