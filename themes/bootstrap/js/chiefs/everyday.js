function Confirm(id) {
    if (confirm('Вы уверены?')) {
        var $preloader = $('#preloader');
        $preloader.removeClass('hidden');
        $.ajax({
            type: 'POST',
            url: window.location.href,
            async: false,
            data: {id},
            success: function (msg) {
                if (parseFloat(msg)) {
                    $preloader.addClass('hidden');
                } else {
                    location = window.location.href
                }
            },
        });
    }
}

function SetStatus(fnpp, status) {
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, status},
        success: ChangeCaption(fnpp, status)
    });
}

function ChangeCaption(fnpp, status) {
    if (status === 2) {
        $('#country_' + fnpp).removeAttr('disabled');
    } else {
        $('#country_' + fnpp).attr('disabled', 'disabled');
        $('#country_' + fnpp).val('');
        SetCountry(fnpp);
    }
    status = status + 1;
    $('#dropdownMenuStatus_' + fnpp).html($('li:nth-child(' + status + ') a').last().text() + ' <span class="caret"></span>');
    $('#dropdownMenuStatus_' + fnpp).addClass('btn-info');
}

function CountryChange(fnpp) {
    SetCountry(fnpp);
}

function SetCountry(fnpp) {
    country = $('#country_' + fnpp).val();
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, country},
    });
}

function SetAbroad(fnpp, bool) {
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, bool},
        success: ChangeBoolButton(fnpp, bool)
    });
}

function ChangeBoolButton(fnpp, bool) {
    if (bool === 1) {
        $('#country2_' + fnpp).removeAttr('disabled');
        $('#btn-group_' + fnpp + ' .btn').first().addClass('active');
        $('#btn-group_' + fnpp + ' .btn').last().removeClass('active');
    } else {
        $('#btn-group_' + fnpp + ' .btn').first().removeClass('active');
        $('#btn-group_' + fnpp + ' .btn').last().addClass('active');
        $('#country2_' + fnpp).attr('disabled', 'disabled');
        $('#country2_' + fnpp).val('');
        SetCountry2(fnpp);
    }
    $('#btn-group_' + fnpp + ':nth-child(1) .btn').addClass('btn-info');
    // $('#btn-group_' + fnpp + ':nth-child(2) .btn').addClass('btn-info');
}

function Country2Change(fnpp) {
    SetCountry2(fnpp);
}

function SetCountry2(fnpp) {
    country2 = $('#country2_' + fnpp).val();
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, country2},
    });
}

function SetAdditional(fnpp) {
    additional = $('#additional_' + fnpp).val();
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, additional},
    });
}

function SetFormat(fnpp, format) {
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, format},
        success: ChangeFormatCaption(fnpp, format)
    });
}

function ChangeFormatCaption(fnpp, format) {
    if (format === 4) {
        $('#dropdownMenuReason_' + fnpp).removeAttr('disabled');
    } else {
        $('#dropdownMenuReason_' + fnpp).attr('disabled', 'disabled');
        $('#dropdownMenuReason_' + fnpp).html('<span class="caret"></span>');
        SetReasonId(fnpp, 0);
    }
    format = format + 1;
    $('#dropdownMenuFormat_' + fnpp).html($("[aria-labelledby=dropdownMenuFormat_" + fnpp + "] li:nth-child(" + format + ") a").text() + ' <span class="caret"></span>');
    $('#dropdownMenuFormat_' + fnpp).addClass('btn-info');
}

function SetReasonId(fnpp, reasonId) {
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, reasonId},
        success: ChangeReasonIdCapton(fnpp, reasonId)
    });
}

function ChangeReasonIdCapton(fnpp, reasonId) {
    // reasonId = reasonId + 1;
    $('#dropdownMenuReason_' + fnpp).html($('[aria-labelledby=dropdownMenuReason_' + fnpp + '] li:nth-child(' + reasonId + ') a').last().text() + ' <span class="caret"></span>');
    $('#dropdownMenuReason_' + fnpp).addClass('btn-info');
}

function SetCategory(fnpp, category) {
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, category},
        success: ChangeCategoryCaption(fnpp, category)
    });
}

function ChangeCategoryCaption(fnpp, category) {
    category = category + 1;
    $('#dropdownMenuCategory_' + fnpp).html($("[aria-labelledby=dropdownMenuCategory_" + fnpp + "] li:nth-child(" + category + ") a").text() + ' <span class="caret"></span>');
    $('#dropdownMenuCategory_' + fnpp).addClass('btn-info');
}

function SetCovidStatus(fnpp, covidStatus) {
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, covidStatus},
        success: ChangeCovidStatusCaption(fnpp, covidStatus)
    });
}

function ChangeCovidStatusCaption(fnpp, covidStatus) {
    // + 2 для перескакивания в списке через ревакцинацию
    covidStatus = covidStatus == 10 ? 2 :covidStatus + 2;
    $('#dropdownMenuCovid_' + fnpp).html($("[aria-labelledby=dropdownMenuCovid_" + fnpp + "] li:nth-child(" + covidStatus + ") a").text() + ' <span class="caret"></span>');
    $('#dropdownMenuCovid_' + fnpp).addClass('btn-default');
}
/* вешает отправку и не обновляет
function SetCovidDate(fnpp) {
    ChangeCovidDate(fnpp);
}

function ChangeCovidDate(fnpp) {
    date = $(fnpp).val();
    console.log($(fnpp).val());
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {fnpp, date},
    });
}*/

$('#copy_modal').click(function () {
    $('#modal_confirm').attr('value', $('#ChiefReportsDay_createdAt').val());
    $('#modal_date').html($('#ChiefReportsDay_createdAt').val());
    $('#modal_type').html('мы копируем данные');
    $('#modal_window').modal('toggle');
});

$('#modal_confirm').click(function () {
    $('#modal_window').modal('toggle');
    var $preloader = $('#preloader');
    $preloader.removeClass('hidden');
    var copy = $(this).attr('value');
    $.ajax({
        type: 'POST',
        url: window.location.href,
        async: false,
        data: {copy},
        success: function (msg) {
            if (parseFloat(msg)) {
                $preloader.addClass('hidden');
            } else {
                location = window.location.href
            }
        },
    });
});