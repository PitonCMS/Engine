// --------------------------------------------------------
// User Management
// --------------------------------------------------------

// Add user row
let userIdx = 0;
$('.jsAddUserRow').on('click', function () {
    let $userRow = $('.jsUserForm').find('.jsUser:first').clone();
    $userRow.find('input[name^=user_id], input[name^=email]').val('').prop('required',false);
    $userRow.find('input[type="checkbox"]').attr('checked',false).attr('id', function(i, val) {
        return val + '-' + userIdx;
    });
    $userRow.find('input').each(function() {
        $(this).attr('name', function(i, val) {
            return val.slice(0, -1) + '-' + userIdx + val.slice(-1);
        });
    });
    $userRow.find('label').attr('for', function(i, val) {
        return val + '-' + userIdx;
    });
    userIdx++;
    $('.jsUser').parent().append($userRow);
});
