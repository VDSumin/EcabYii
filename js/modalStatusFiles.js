$(document).on('blur', '#commentFieldText', function(e) {
    e.preventDefault();
    var $select = $(this);
    var $value = $(this).val();
    var $id = $select.parents('tr').find('#commentFieldId'). val();

    $select.prop("disabled", true);

    $.ajax({
        'url': linkComment,
        'type': 'post',
        'dataType': 'json',
        'data': {
            id : $id,
            value : $value
        },
        'success' : function(responce) {
            if (responce.success) {
                $select.removeClass('btn-danger');
                $select.addClass('btn-success');
                $select.css({opacity: 0.7});
                setTimeout(function(){
                    $select.removeClass('btn-success');
                    $select.css({opacity: 1});
                }, 3000);
                $select.prop("disabled", false);
            } else {
                $select.removeClass('btn-success');
                $select.addClass('btn-danger');
                $select.css({opacity: 0.7});
                setTimeout(function(){
                    $select.removeClass('btn-danger');
                    $select.css({opacity: 1});
                }, 3000);
                $select.prop("disabled", false);
            }
        }
    });
});

$(document).on('click', '#stateOfFile', function(e) {
    e.preventDefault();

    var $select = $(this);
    var $state = 0;
    if ($(this).hasClass("glyphicon-ok")){
        $state = 1;
    }
    if ($(this).hasClass("glyphicon-remove")){
        $state = 2;
    }

    var $btn = $select.parents('tr').find('.btn-mine');
    var $id = $select.parents('tr').find('#commentFieldId').val();
    var $tr = $select.parents('tr');

    $select.prop("disabled", true);

    $.ajax({
        'url': linkUpdate,
        'type': 'post',
        'dataType': 'json',
        'data': {
            id : $id,
            state : $state
        },
        'success' : function(responce) {
            if (responce.success) {
                $btn.removeClass('btn-danger');
                $btn.removeClass('btn-success');
                $btn.removeClass('btn-info');
                if ($state === 1){
                    $("#tooltipId").attr('title', 'Работа проверена');
                    $btn.addClass('btn-success');

                }
                if ($state === 2){
                    $("#tooltipId").attr('title', 'Работа отклонена');
                    $btn.addClass('btn-danger');

                }
                if ($state === 0){
                    $("#tooltipId").attr('title', 'Работа не проверялась');
                    $btn.addClass('btn-info');

                }
                $select.prop("disabled", false);
            } else {
                $tr.addClass('danger');
                $tr.css({opacity: 0.8});
                setTimeout(function(){
                    $tr.removeClass('danger');
                    $tr.css({opacity: 1});
                }, 2000);
                $select.prop("disabled", false);
            }
        }
    });
});
