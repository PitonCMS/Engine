// --------------------------------------------------------
// Page Management
// --------------------------------------------------------

/**
 *
 * @param {object} element
 */
const removeElement = function(element) {
    element.remove();
}

// Listen for page list status filter changes and reload
const pageListFilter = document.querySelector('.jsPageStatusFilter');
if (pageListFilter) {
    // If this page has a status filter, get the container div reference
    const pageList = document.querySelector('.list-items-wrapper');

    pageListFilter.addEventListener("change", (f) => {
        let filter  = pageListFilter.options[pageListFilter.selectedIndex].value;

        if (filter !== 'x') {
            // Remove existing page rows
            while (pageList.firstChild) {
                pageList.removeChild(pageList.lastChild);
            }

            // Get server data
            getXHRPromise(pitonConfig.routes.adminPageGet, {'pageStatus': filter})
                .then((data) => {
                    pageList.insertAdjacentHTML('afterbegin', data);
                }).catch(function (error) {
                    console.log('Something went wrong', error);
                });
        }
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

// Listen for delete page element bubbling event
const pageEditNode = document.querySelector(`[data-page-edit="1"]`);
if (pageEditNode) {
    pageEditNode.addEventListener("click", (e) => {
        if (e.target.dataset.elementDelete) {
            // Confirm delete
            if (!confirmPrompt("Are you sure you want to permanently delete this element?")) return;

            // Get element ID and element
            let elementId = parseInt(e.target.dataset.elementId);
            let element = e.target.closest(`[data-element="parent"]`);

            if (isNaN(elementId)) {
                // Element has not been saved, just remove from DOM
                removeElement(element);
            } else {
                // Element has been saved, do a hard delete
                let data = {
                    "elementId": elementId
                }

                // delete element
                enableSpinner();

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
/*
// Delete page element
$('.jsBlockParent').on('click', '.jsDeleteBlockElement', function (e) {
    e.preventDefault();
    if (!confirmPrompt('Are you sure you want to delete this element?')) {
        return false;
    }
    let blockElementId = $(this).data('elementId');
    let $element = $(this).parents('.jsElementParent');
    let blockKey = $(this).parents('.jsBlockParent:first').attr('id');
    let elementLimit = $('#' + blockKey).data('elementCountLimit') || 100;
    let elementCount = $('#' + blockKey).data('elementCount') || 1;
    let removeElement = function () {
        $element.slideUp('normal', function () {
            $element.remove();
        });
    }
    let postData = {
        id: blockElementId
    }
    postData[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue;

    if (!isNaN(blockElementId)) {
        // Physical delete
        $.ajax({
            url: pitonConfig.routes.adminPageElementDelete,
            method: "POST",
            data: postData,
            success: function (r) {
                if (r.status === 'success') {
                    removeElement();
                }
            },
            error: function (r) {
                console.log('PitonCMS: There was an error deleting this element. Contact your administrator.')
            }
        });
    } else {
        // Only element delete
        removeElement();
    }

    // Reset add element button if within count limit
    $('#button-' + blockKey).data('elementCount', --elementCount);
    if (elementCount < elementLimit) {
        $('#button-' + blockKey).prop('disabled', false).attr('title', '');
    }

    // Show no content message if elementCount is zero
    if (elementCount === 0) {
        $(this).parents('.jsBlockParent:first').find('.jsNoElementFlag').removeClass('d-none').addClass('d-block');
    }
});

// Toggle element selector
$('.jsBlockParent').on('click', '.jsElementType input[type="radio"]', function () {
    let selectedTypeOption = $(this).data('enable-input');
    let $elementParent = $(this).parents('.jsElementParent');
    $elementParent
        .find('.jsElementOption.d-block').toggleClass('d-block d-none')
        .find('select').prop('required', false);

    if (selectedTypeOption === 'image' || selectedTypeOption === 'hero') {
        $(this).parents('.jsElementType').siblings('.jsMediaInput').toggleClass('d-none d-block');
        return;
    }
    if (selectedTypeOption === 'embedded') {
        $(this).parents('.jsElementType').siblings('.jsEmbeddedInput').toggleClass('d-none d-block');
        return;
    }
    if (selectedTypeOption === 'collection') {
        $(this).parents('.jsElementType').siblings('.jsCollectionInput').toggleClass('d-none d-block').find('select').prop('required', true);
        return;
    }
    if (selectedTypeOption === 'gallery') {
        $(this).parents('.jsElementType').siblings('.jsGalleryInput').toggleClass('d-none d-block').find('select').prop('required', true);
        return;
    }
});

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

// Listen for changes to the edit collection form
$('.jsCollectionGroup').on('input', function () {
    setSaveButtonIndicator();
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
