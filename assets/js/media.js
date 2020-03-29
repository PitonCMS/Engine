// --------------------------------------------------------
// Media management
// --------------------------------------------------------

// Listen for media category changes to enable save button
$('.jsEditMediaCategory').on('input', function () {
    setSaveButtonIndicator();
});

// Add category/gallery name input to media categories form
$('.jsEditMediaCategory').on('click', '.jsAddMediaCategory', function () {
    let $newInputRow = $('.jsMediaCategoryElement > .jsMediaCategory').clone();
    $newInputRow.find('button').attr('disabled', false);
    $(this).parent('div').before($newInputRow);
    setSaveButtonIndicator();
});

// Delete category from media categories form
$('.jsEditMediaCategory').on('click', 'button[type=button]', function (e) {
    e.preventDefault();
    if (!confirmPrompt()) {
        return false;
    }
    let $category = $(e.target).parent('.jsMediaCategory');
    if ($category.children('input[name^=category_id]').val() == '') {
        // Not yet saved so just remove.
        $category.fadeOut(function () {
            $(this).remove();
        });
        return;
    }
    // Otherwise continue with physical delete
    let postData = {
        "id": $(e.target).attr('value')
    }
    postData[pitonConfig.csrfTokenName] = pitonConfig.csrfTokenValue;
    $.ajax({
        url: pitonConfig.routes.adminMediaCategoryDelete,
        method: "POST",
        data: postData,
        success: function (r) {
            if (r.status === "success") {
                $category.fadeOut(function () {
                    $(this).remove();
                });
            }
        },
        error: function (r) {
            console.log('PitonCMS: There was an error submitting the form. Contact your administrator.')
        }
    });
});

// Show user that a media input changed and needs to be saved
$('.jsMediaCard form').each(function (i) {
    let $form = $(this);
    let $saveButton = $form.find('button[value=save]');
    $form.on('input', function () {
        if ($saveButton.hasClass('btn-primary')) return;
        $saveButton.removeClass('btn-outline-primary').addClass('btn-primary');
    });
});

// Save media form edits when viewing all media
$('.jsMediaCard').on('click', 'button', function (e) {
    e.preventDefault();
    let $button = $(e.target);
    let $medium = $(e.target).parents('.jsMediaCard');
    if ('delete' === $button.attr('value') && !confirmPrompt()) {
        return false;
    }
    // jQuery ignores the button value, so append that to post data
    let postData = $button.parents('form').serialize();
    $.ajax({
        url: ('delete' === $button.attr('value')) ? pitonConfig.routes.adminMediaDelete : pitonConfig.routes.adminMediaSave,
        method: "POST",
        data: postData,
        success: function (r) {
            if ('delete' === $button.attr('value') && r.status === "success") {
                $medium.fadeOut(function () {
                    $(this).remove();
                });
            } else if ("save" === $button.attr('value') && r.status === "success") {
                $button.removeClass('btn-primary').addClass('btn-outline-primary');
            }
        },
        error: function (r) {
            console.log('PitonCMS: There was an error submitting the form. Contact your administrator.')
        }
    });
});

// Upload media action
$('.jsMediaUploadForm').on('submit', function (e) {
    let processingText = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    <span class="sr-only">Loading...</span>Uploading and optimizing media...`;
    $(this).find('button').prop('disabled', true).html(processingText);
});
