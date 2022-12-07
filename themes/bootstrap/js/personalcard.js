$(document).on('click', '.fieldCheckRight', function(e) {
    e.preventDefault();
    var $select = $(this);
    var pnrec = $('#pnrec').val();
    var name = $(this).prop('id');
    $select.prop("disabled", true);

    $.ajax({
       'url': updateFieldCheckRightUrl,
       'type': 'post',
       'dataType': 'json',
       'data': pnrec + '=' + name,
       'success' : function(responce) {
            if (responce.success == 1) {
                $select.removeClass('btn-danger');
                $select.removeClass('glyphicon-remove');
                $select.addClass('btn-success');
                $select.addClass('glyphicon-ok');
                $select.prop("disabled", false);
            } else {
                $select.removeClass('btn-success');
                $select.removeClass('glyphicon-ok');
                $select.addClass('btn-danger');
                $select.addClass('glyphicon-remove');
                $select.prop("disabled", false);
            }
       }
   });
});

function updateData() {
    var type = null;
    var $element = $(this);
    if ($(this).hasClass('hasDatepicker')) {
        type = 'date';
    }
    if ($(this).hasClass('nrec')) {
        type = 'nrec';
    }
    if ($(this).hasClass('textdata')) {
        type = 'textdata';
    }

    var form = $(this).parents('form:first');
    var action = form.prop('action');

    if(
        ($(this).val()=='____________' && $(this).prop('name')=='GalStudentPersonalcard[innManualNmb]') ||
        ($(this).val()=='___-___-___ __' && $(this).prop('name')=='GalStudentPersonalcard[snilsManualNmb]')
    )
    {
        $(this).val('');
    }

    var data = $('#pnrec').prop('name') + '=' + $('#pnrec').val() + '&'
               + $(this).prop('name') + '=' + $(this).val() + '&'
               + 'GalStudentPersonalcard[type]=' + type;

    $.ajax({
        url: action,
        data: data,
        method: 'post',
        dataType: 'json',
        beforeSend:  function() {
            $element.prop( "disabled", true );
        },
        success: function(responce) {
            if (responce.success) {
                $element.css({background:'#C6D880'});
                setTimeout(function(){
                    $element.css({background :''});
                }, 1000);
            } else {
                $element.css({background:'#e7c3c3'});
                setTimeout(function(){
                    $element.css({background :''});
                }, 1000);

            }

            $element.prop( "disabled", false );
        },
        error: function() {
            $element.prop( "disabled", false );
            $element.css({background:'#e7c3c3'});
            setTimeout(function(){
                $element.css({background :''});
            }, 1000);
        }

    });
}

function updateDataAutoComplete($view, $hidden) {
    var type = 'nrec';
    var form = $view.parents('form:first');
    var action = form.prop('action');
    var data = $('#pnrec').prop('name') + '=' + $('#pnrec').val() + '&'
        + $hidden.prop('name') + '=' + $hidden.val() + '&'
        + 'GalStudentPersonalcard[type]=' + type;

    $.ajax({
        url: action,
        data: data,
        method: 'post',
        dataType: 'json',
        beforeSend:  function() {
            $view.prop( "disabled", true );
        },
        success: function(responce) {
            if (responce.success) {
                $view.css({background:'#C6D880'});
                setTimeout(function(){
                    $view.css({background :''});
                }, 1000);
            } else {
                $view.css({background:'#e7c3c3'});
                setTimeout(function(){
                    $view.css({background :''});
                }, 1000);

            }

            $view.prop( "disabled", false );
        },
        error: function() {
            $view.prop( "disabled", false );
            $view.css({background:'#e7c3c3'});
            setTimeout(function(){
                $view.css({background :''});
            }, 1000);
        }

    });
}

$(function() {
    $(".js-mask").each(function() {
        var mask = $(this).data('mask');
        if (mask.toString().length > 0) {
            $(this).mask(mask.toString());
        }
    });

    $('.js-date-update').datepicker($.extend({}, $.datepicker.regional[ "ru" ], {onClose: updateData}));
    $(document).on('blur', 'input.js-update:not(.js-date-update)', updateData);
    $(document).on('change', 'select.js-update-drop', updateData);



    $('input.js-update:not(.js-date-update)').keypress(function(e){
        //отлавливаем нажатие клавиш
        if (e.keyCode == 13) { //если нажали Enter, то true
            updateData;
        }
    });

    $("#kinder1Btn").click(function(){
        var $element = $(this);

        $('tr').each(function () {
            var hidcont = $(this).next('tr');

            if (hidcont.hasClass('kinder2Hid') && hidcont.hasClass('hidcont') &&  hidcont.is(':hidden')){

                hidcont.show();
                $element.val('-');

            }else if (hidcont.hasClass('kinder2Hid') && hidcont.hasClass('hidcont') && hidcont.is(':not(:hidden)')){

                hidcont.hide();
                $element.val('+');
            }

            if (hidcont.hasClass('kinder3Hid') && hidcont.hasClass('hidcont') && hidcont.is(':not(:hidden)')){

                hidcont.hide();
                $element.val('+');
            }

        }) ;

    });

    $("#kinder2Btn").click(function(){
        var $element = $(this);
        $('tr').each(function () {
            var hidcont = $(this).next('tr');

            if (hidcont.hasClass('kinder3Hid') && hidcont.hasClass('hidcont') &&  hidcont.is(':hidden')){

                hidcont.show();
                $element.val('-');
            }else if (hidcont.hasClass('kinder3Hid') && hidcont.hasClass('hidcont') && hidcont.is(':not(:hidden)')){

                hidcont.hide();
                $element.val('+');
            }

        }) ;


    });




    $( ".autoSchool" ).autocomplete({
        source: schoolFindUrl,
        focus: function( event, ui ) {
            $(this).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            hidden.val(ui.item.value);
            hidden.data('label', ui.item.label);
            updateDataAutoComplete($(this), hidden);
            return false;
        },
        change: function( event, ui ) {
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            $(this).val(hidden.val() ? hidden.data('label') : '');
        }
    });

    $( ".autoEduDoc" ).autocomplete({
        source: EduDocFindUrl,
        focus: function( event, ui ) {
            $(this).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            hidden.val(ui.item.value);
            hidden.data('label', ui.item.label);
            updateDataAutoComplete($(this), hidden);
            return false;
        },
        change: function( event, ui ) {
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            $(this).val(hidden.val() ? hidden.data('label') : '');
        }
    });

    $( ".autoEduLevel" ).autocomplete({
        source: EduLevelFindUrl,
        focus: function( event, ui ) {
            $(this).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            hidden.val(ui.item.value);
            hidden.data('label', ui.item.label);
            updateDataAutoComplete($(this), hidden);
            return false;
        },
        change: function( event, ui ) {
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            $(this).val(hidden.val() ? hidden.data('label') : '');
        }
    });

    $( ".autoAddr" ).autocomplete({
        source: AddrFindUrl,
        focus: function( event, ui ) {
            $(this).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            hidden.val(ui.item.value);
            hidden.data('label', ui.item.label);
            updateDataAutoComplete($(this), hidden);
            return false;
        },
        change: function( event, ui ) {
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            $(this).val(hidden.val() ? hidden.data('label') : '');
        }
    });


    $( ".autoGr" ).autocomplete({
        deferRequestBy: 300,
        extraParams: {type : 1},
        source: GrFindUrl,
        focus: function( event, ui ) {
            $(this).val( ui.item.label );
            				return false;
            },
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            hidden.val(ui.item.value);
            hidden.data('label', ui.item.label);
            updateDataAutoComplete($(this), hidden);
            return false;
        },
        change: function( event, ui ) {
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            $(this).val(hidden.val() ? hidden.data('label') : '');
        }
    });

    $( ".autoPass" ).autocomplete({
        deferRequestBy: 300,
        extraParams: {type : 1},
        source: PassFindUrl,
        focus: function( event, ui ) {
            $(this).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            hidden.val(ui.item.value);
            hidden.data('label', ui.item.label);
            updateDataAutoComplete($(this), hidden);
            return false;
        },
        change: function( event, ui ) {
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            $(this).val(hidden.val() ? hidden.data('label') : '');
        }
    });


});
