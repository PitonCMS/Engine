// --------------------------------------------------------
// Page Management
// --------------------------------------------------------

/**
 * Listen for page list status filter changes and reload
 */
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


/*
// Add Page Block Element
$('.jsAddElement').on('click', function () {
    let $addButton = $(this);
    let buttonText = {
        addElement: "Add Element",
        loading: `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  <span class="sr-only">Loading...</span>Loading...`
    }
    $addButton.prop('disabled', true).html(buttonText.loading);
    let elementType = $(this).data('elementType');
    let blockKey = $(this).data('blockKey');
    let elementTypeOptions = $(this).data('elementTypeOptions');
    let elementLimit = $(this).data('elementCountLimit') || 100;
    let currentElementCount = $(this).data('elementCount');
    let $blockParent = $('#' + blockKey);
    let postData = {
        blockKey: blockKey,
        elementType: elementType,
        elementTypeOptions: elementTypeOptions
    }
    postData[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue;

    $.ajax({
        url: pitonConfig.routes.adminPageElementNew,
        method: "POST",
        data: postData,
        success: function (r) {
            let $newElement = {};
            if (r.status == "success") {
                $newElement = $(r.html);
            } else {
                console.log('PitonCMS: Exception getting new element');
                return;
            }

            // Increment element sort value
            let lastElementSortValue = $blockParent.find('.jsElementParent:last .jsElementSortValue').val();

            if (!isNaN(lastElementSortValue)) {
                lastElementSortValue++;
            } else {
                lastElementSortValue = 1;
            }

            $newElement.appendTo($blockParent);
            $newElement.find('.jsElementSortValue').val(lastElementSortValue);
            $newElement.find('.jsMDE').each(function () {
                simplemde = new SimpleMDE({
                    element: this,
                    forceSync: true
                });
            });

            // Hide no content message
            $blockParent.find('.jsNoElementFlag').removeClass('d-block').addClass('d-none');

            // If number of elements matches or exceeds the limit, disable the button
            $addButton.data('elementCount', ++currentElementCount);
            if (currentElementCount >= elementLimit) {
                $addButton.html(buttonText.addElement).prop('disabled', true).attr('title', 'Page block has the maximum number of elements allowed by design.');
            } else {
                $addButton.html(buttonText.addElement).prop('disabled', false).attr('title','');
            }

            // Scroll to new element and add to navigation
            window.location.hash = $newElement.attr('id');
        }
    });
});

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
