// --------------------------------------------------------
// Page Management
// --------------------------------------------------------
// Add Page Block Element
$('.jsAddElement').on('click', function () {
    let $addButton = $(this);
    let buttonText = {
        addElement: "Add Element",
        loading: `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  <span class="sr-only">Loading...</span>Loading...`
    }
    $addButton.prop('disabled', true).html(buttonText.loading);
    let $blockParent = $(this).parents('.jsBlockParent');
    let elementType = $(this).data('element-type');
    let blockKey = $(this).data('block-key');
    let elementTypeOptions = $(this).data('element-type-options');
    let elementLimit = $(this).data('element-count-limit') || 100;
    let postData = {
        blockKey: blockKey,
        elementType: elementType,
        elementTypeOptions: elementTypeOptions
    }
    postData[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue;

    $.ajax({
        url: pitonConfig.routes.adminNewElement,
        method: "POST",
        data: postData,
        success: function (r) {
            let $newElement = $(r.html);

            // Increment element sort value
            let lastElementSortValue = $blockParent.find('.jsElementParent:last-child .jsElementSortValue').val();

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

            // If number of elements matches or exceeds the limit, disable the button
            if ($blockParent.children('.jsElementParent').length >= elementLimit) {
                $addButton.prop('disabled', true);
            }

            // Scroll to new element and add to navigation
            let newElementID = $newElement.attr('id');
            window.location.hash = newElementID;
            $addButton.html(buttonText.addElement).prop('disabled', false);
            let $el = $('#page-edit-nav').find('.jsPageSubBlock-' + blockKey).append(
                '<a class="nav-link small-sidebar-text" href="#' + newElementID + '">New</a>'
            );
        }
    });
});

// Delete page element
$('.jsBlockParent').on('click', '.jsDeleteBlockElement', function (e) {
    e.preventDefault();
    if (!confirmPrompt('Are you sure you want to delete this element?')) {
        return false;
    }
    let blockElementId = $(this).data('element-id');
    let $element = $(this).parents('.jsElementParent');
    let $blockParent = $(this).parents('.jsBlockParent');
    let elementLimit = $blockParent.find('.jsAddElement').data('element-count-limit') || 100;
    let removeElement = function () {
        $element.slideUp('normal', function () {
            $('#page-edit-nav').find('a[href="#page-element-'+blockElementId+'"]').remove();
            $element.remove();
        });

        // If element count is now within limits for this block, enable add element button
        if ($blockParent.children('.jsElementParent').length >= elementLimit) {
            $blockParent.find('.jsAddElement').prop('disabled', false);
        }
    }
    let postData = {
        id: blockElementId
    }
    postData[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue;

    if (!isNaN(blockElementId)) {
        $.ajax({
            url: pitonConfig.routes.adminDeleteElement,
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
        removeElement();
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
let $pageSlug = $('.jsPageSlug');
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
$('.jsPageSlugFaLockStatus').on('click', function () {
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
      url: pitonConfig.routes.adminGetMedia,
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