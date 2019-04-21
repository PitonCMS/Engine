// --------------------------------------------------------
// Misc Statements
// --------------------------------------------------------

$('.jsDatePicker').datepicker({
    format: pitonConfig.dateFormat,
    weekStart: pitonConfig.weekStart,
    todayHighlight: true,
    orientation: 'bottom'
});

// Delete confirm prompt
var confirmDeletePrompt = function(msg) {
    var message = msg || 'Are you sure you want to delete?';
    return confirm(message);
}

$('body').on('click', '.jsDeleteConfirm', function() {
  return confirmDeletePrompt();
});

// --------------------------------------------------------
// User Management
// --------------------------------------------------------

// Add user row
$('.jsAddUserRow').on('click', function() {
    var $userRow = $(this).parent('.jsUserForm').find('div:last').clone();
    $userRow.find('input').val('');
    $userRow.find('a').attr('href', '#');
    $(this).before($userRow);
});

// --------------------------------------------------------
// Page Management
// --------------------------------------------------------
// Add Page Block Element
$('.jsAddElement').on('click', function() {
    let $addButton = $(this);
    var $blockParent = $(this).parent('.jsBlockParent');
    var elementType = $(this).data('element-type');
    var blockKey = $(this).data('block-key');
    var elementTypeOptions = $(this).data('element-type-options');
    let elementLimit = $(this).data('element-count-limit') || 100;
    var postData = {
            blockKey: blockKey,
            elementType: elementType,
            elementTypeOptions: elementTypeOptions
    }
    postData[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue;

    $.ajax({
        url: '/admin/page/element/new',
        method: "POST",
        data: postData,
        success: function (r) {
            var $newElement = $(r.html);

            // Increment element sort value
            var lastElementSortValue = $blockParent.find('.jsElementParent:last-child .jsElementSortValue').val();

            if (!isNaN(lastElementSortValue)) {
                lastElementSortValue++;
            } else {
                lastElementSortValue = 1;
            }

            $newElement.appendTo($blockParent);
            $newElement.find('.jsElementSortValue').val(lastElementSortValue);
            $newElement.find('.jsMDE').each(function() {
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
            var newElementID = $newElement.attr('id');
            window.location.hash = newElementID;

            var $el = $('#page-edit-nav').find('.jsPageSubBlock-'+blockKey).append(
                '<a class="nav-link ml-3 my-1" href="#'+newElementID+'">New (Element)</a>'
                );
        }
    });
});

// Delete page element
$('.jsBlockParent').on('click', '.jsDeleteBlockElement', function (e) {
    e.preventDefault();
    if (!confirmDeletePrompt('Are you sure you want to delete this element?')) {
        return;
    }
    var blockElementId = $(this).data('element-id') || 'x';
    var $element = $(this).parents('.jsElementParent');
    let $blockParent = $(this).parents('.jsBlockParent');
    let elementLimit = $blockParent.find('.jsAddElement').data('element-count-limit') || 100;
    var removeElement = function() {
        $element.slideUp('normal', function () {
            $element.remove();
        });

        // If element count is now within limits for this block, enable add element button
        if ($blockParent.children('.jsElementParent').length >= elementLimit) {
            $blockParent.find('.jsAddElement').prop('disabled', false);
        }
    }
    var postData = {id: blockElementId}
    postData[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue;

    if (!isNaN(blockElementId)) {
        $.ajax({
            url: '/admin/page/element/delete',
            method: "POST",
            data: postData,
            success: function (r) {
                if (r.status === 'success') {
                    removeElement();
                }
            },
            error: function(r) {
                console.log('error')
                console.log(r)
            }
        });
    } else {
        removeElement();
    }
});

// Toggle element selector
$('.jsBlockParent').on('click', '.jsElementType input[type="radio"]', function() {
    var selectedTypeOption = $(this).data('enable-input');
    $('.jsElementOption.d-block').toggleClass('d-block d-none');

    if (selectedTypeOption === 'image' || selectedTypeOption === 'hero') {
        $(this).parents('.jsElementType').siblings('.jsMediaInput').toggleClass('d-none d-block');
        return;
    }
    if (selectedTypeOption === 'embedded') {
        $(this).parents('.jsElementType').siblings('.jsEmbeddedInput').toggleClass('d-none d-block');
        return;
    }
    if (selectedTypeOption === 'gallery' || selectedTypeOption === 'collection') {
        $(this).parents('.jsElementType').siblings('.jsCollectionInput').toggleClass('d-none d-block');
        return;
    }
});
