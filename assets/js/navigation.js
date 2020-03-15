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
$('.jsEditNavBlock').on('click', '.jsAddPageSelect', function() {
  // Find UL parent and get nav ID
  let $target = $(this).closest('ul.jsNavParent');
  let parentId = $target.data('parentId');

  // Clone spare page select and set values
  let $new = $('#jsPageSelect > li').clone();
  let key = (navItemKey++) + "n";
  $new.children('ul.jsNavParent').data('parentId', parentId);
  $new.find('input.jsNavParentId').val(parentId);
  $new.find('[name^=nav]').each(function (i, e) {
    let name = $(e).attr('name');
    $(e).attr('name', name.replace(/(.+?\[)(\].+)/, "$1" + key + "$2"));
  });
  $(this).parent('li').before($new);
  setNavSaveIndicator();
});

// Set/unset delete flag
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

// Set/unset active flag
$('.jsEditNavBlock').on('click', '.jsNavActive', function() {
  let $navItem = $(this).closest('li.jsNavItem');
  console.log($navItem)
  // Get current status and reverse
  if ($navItem.find('.jsNavActiveFlag:first').val() === "Y") {
    $navItem.find('.jsNavActiveFlag:first').val("N");
    $navItem.addClass('bg-warning text-white');
  } else {
    $navItem.find('.jsNavActiveFlag:first').val("Y");
    $navItem.removeClass('bg-warning text-white');
  }
  setNavSaveIndicator();
});
