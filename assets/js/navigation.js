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
    // Get nav item to move, direction, and zero index length of immediate siblings, and recursive level
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
      // If ol is nested child of a jsNavItem, move up one level
      let $target = $navItem.closest('.jsNavParent').closest('.jsNavItem');
      $navItem.detach();
      let parentId = ($target.data('level') === 1) ? "" : $target.data('key');
      $navItem.removeClass('nav-item-level-' + level).addClass('nav-item-level-' + (level - 1));
      $navItem.data('level', level - 1)
      $navItem.find('.jsNavParentId').val(parentId);
      $target.after($navItem);
      // Clean up, if there is an empty <ol> left in this li, then remove
      let $empty = $target.find('.jsNavParent');
      if ($empty.is(':empty')) {
        $empty.remove();
      }
    } else if (control === 'right' && level <= 3 && $navItem.index() !== 0) {
      // The first child should not be indented
      // If the above sibling has a child <ol> then insert into that
      let $target = $navItem.prev('.jsNavItem');
      $navItem.find('.jsNavParentId').val($target.data('key'));
      $navItem.data('level', level + 1);
      $navItem.removeClass('nav-item-level-' + level).addClass('nav-item-level-' + (level + 1));
      if ($target.children('ol.jsNavParent').length === 1) {
        $navItem.detach();
        $target.children('ol.jsNavParent').append($navItem);
      } else if ($navItem.index() !== 0) {
        // Otherwise wrap in <ol> and insert into <li> above
        $target = $navItem.prev('.jsNavItem');
        $navItem.detach();
        $target.append($navItem);
        $navItem.wrap('<ol class="jsNavParent"></ol>');
      }
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
