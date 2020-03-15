// --------------------------------------------------------
// Navigation management
// --------------------------------------------------------

// Listen for form input changes to update button status
let navSaveIndFlag = false;
const setNavSaveIndicator = () => {
  if (!navSaveIndFlag) {
    $('.jsNavigatorForm > button.btn').attr('disabled', false);
    navSaveIndFlag = true;
  }
};
$('.jsNavigatorForm').on('input', function() {
  setNavSaveIndicator();
});

// Add new page item to nav list
let navItemKey = 0;
$('.jsNavPageSelector').on('change', function () {
  if ($(this).val() !== "x") {
    let key = (navItemKey++) + "n";
    let $new = $('.jsNewNavItem > .jsNavItem').clone();
    $new.data('level', 1).data('key', key);
    $new.addClass('nav-item-new');
    $new.find('.jsNavPageId').val($(this).val());
    $new.find('.jsNavItemTitle').html($(this).find('option:selected').text());
    if ($(this).val() != 0) {
      $new.find('.jsNavAltTitle').attr('required',false);
    }
    $new.find('ol.jsNavParent').data('parentId', key);
    $new.find('input[name^=nav]').each(function (i, e) {
      let name = $(e).attr('name');
      $(e).attr('name', name.replace(/(.+?\[)(\].+)/, "$1" + key + "$2"));
    });
    $('.jsEditNavBlock > .jsNavParent').append($new);
    setNavSaveIndicator();
  }
});

// Arrange nav items, as well as delete and disable
$('.jsEditNavBlock').on('click', '.jsNavItemCtrl', function () {
  // Get nav item to move, direction, and zero based index length of immediate siblings, and recursive level
  let $navItem = $(this).closest('.jsNavItem');
  let control = $(this).data('control');
  let navLength = $navItem.siblings('.jsNavItem').length;
  let level = $navItem.data('level');

  if (control === 'up' && $navItem.index() !== 0) {

    let $target = $navItem.prev('.jsNavItem');
    $navItem.detach();
    $target.before($navItem);

  } else if (control === 'down' && $navItem.index() !== navLength) {

    let $target = $navItem.next('.jsNavItem');
    $navItem.detach();
    $target.after($navItem);

  } else if (control === 'left' && level > 1) {

    // Find the parents-parent
    let $target = $navItem.parents('.jsNavParent:eq(1)');
    let parentId = ($target.data('parentId') == 0) ? "" : $target.data('parentId');
    $navItem.removeClass('nav-item-level-' + level).addClass('nav-item-level-' + (level - 1));
    $navItem.data('level', level - 1);
    $navItem.find('.jsNavParentId').val(parentId);
    $navItem.detach();
    $target.append($navItem);

  } else if (control === 'right' && $navItem.index() !== 0) {

    // The first nav item should not be indented. The nav immediately above at the same level item will become the parent
    let $target = $navItem.prev('.jsNavItem');
    $navItem.detach();
    $navItem.find('.jsNavParentId').val($target.children('.jsNavParent').data('parentId'));
    $navItem.data('level', level + 1);
    $navItem.removeClass('nav-item-level-' + level).addClass('nav-item-level-' + (level + 1));
    $target.children('ol.jsNavParent').append($navItem);

  } else if (control === 'delete') {
    // Get current status and reverse
    if ($navItem.find('.jsNavDeleteFlag').val() === "") {
      $navItem.find('.jsNavDeleteFlag').val("on");
      $navItem.addClass('bg-danger text-white');
    } else {
      $navItem.find('.jsNavDeleteFlag').val("");
      $navItem.removeClass('bg-danger text-white');
    }
  } else if (control === 'disable') {
    // Get current status and reverse
    if ($navItem.children('.jsNavActiveFlag').val() === "Y") {
      $navItem.children('.jsNavActiveFlag').val("N");
      $navItem.addClass('bg-warning text-white');
    } else {
      $navItem.children('.jsNavActiveFlag').val("Y");
      $navItem.removeClass('bg-warning text-white');
    }
  }
  setNavSaveIndicator();
});
