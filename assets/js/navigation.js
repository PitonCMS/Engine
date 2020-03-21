// --------------------------------------------------------
// Navigation management
// --------------------------------------------------------

// Listen for form input changes to update button status
let navSaveIndFlag = false;
const setNavSaveIndicator = () => {
  if (!navSaveIndFlag) {
    $('#jsSaveNavButton').attr('disabled', false);
    navSaveIndFlag = true;
  }
};
$('.jsNavigatorForm').on('input', function() {
  setNavSaveIndicator();
});

// Add page select nav item
let navItemKey = 0;
$('.jsEditNavBlock').on('click', '.jsAddNavItem', function() {
  // Find parent UL to get parent values
  let level = $(this).closest('.jsNavParent').data('level');
  let parentId = $(this).closest('.jsNavParent').data('parentId');

  // Clone spare page select and set new values
  let $new = $('#jsPageSelect > li').clone();
  let arrayKey = (navItemKey++) + "n";
  $new.children('.jsNavParent').data('parentId', arrayKey).data('level', (level + 1));
  $new.find('input.jsNavParentId').val(parentId);
  $new.find('[name^=nav]').each(function (i, e) {
    let name = $(e).attr('name');
    $(e).attr('name', name.replace(/(.+?\[)(\].+)/, "$1" + arrayKey + "$2"));
  });
  // Remove add sub nav option if level 2 or greater
  if (level >= 1) {
    $new.children('.jsNavParent').remove();
  }
  $(this).parent('li').before($new);
  setNavSaveIndicator();
});

// Set or unset delete flag
$('.jsEditNavBlock').on('click', '.jsNavDelete', function() {
  let $navItem = $(this).closest('li.jsNavItem');
  // Get current status and reverse
  if ($navItem.find('.jsNavDeleteFlag:first').val() === "") {
    $navItem.find('.jsNavDeleteFlag:first').val("on");
    $navItem.addClass('bg-danger text-white');
  } else {
    $navItem.find('.jsNavDeleteFlag:first').val("");
    $navItem.removeClass('bg-danger text-white');
  }
  setNavSaveIndicator();
});

// Set or unset active flag
$('.jsEditNavBlock').on('click', '.jsNavActive', function() {
  let $navItem = $(this).closest('li.jsNavItem');

  // Get current status and reverse
  if ($navItem.find('.jsNavActiveFlag:first').val() === "Y") {
    $navItem.find('.jsNavActiveFlag:first').val("N");
    $navItem.addClass('navigation-disabled');
  } else {
    $navItem.find('.jsNavActiveFlag:first').val("Y");
    $navItem.removeClass('navigation-disabled');
  }
  setNavSaveIndicator();
});
