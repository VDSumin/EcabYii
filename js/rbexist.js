$(document).on('click', '.blockChair', function() {
    var $select = $(this);
    var checkbox = $select.find(".checkboxCBrb");
    var text = $select.find(".textCBrb");
    var value = checkbox.val();


    if (value == 2) {
        checkbox.attr('value', 1);
        checkbox.attr('checked', true);
        $select.attr('class', 'blockChair toggle btn btn-primary');
        text.html('Да');
    } else {
        checkbox.attr('value', 2);
        checkbox.attr('checked', false);
        $select.attr('class', 'blockChair toggle btn btn-default active');
        text.html('Нет');
    }

});