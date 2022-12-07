$('.export').click(function () {
    var $preloader = $('#preloader');
    $preloader.removeClass('hidden');
    var m = new Date();
    var $date =
        m.getUTCFullYear() + "-" +
        ("0" + (m.getUTCMonth() + 1)).slice(-2) + "-" +
        ("0" + m.getUTCDate()).slice(-2);
    if ($(this).attr('department') == 6) {
        $date = $('#datePicker').val();
    }
    const request = new XMLHttpRequest();
    const url = window.location.pathname + '?r=chiefs/export/' + $(this).attr('period') + '&id=' + $(this).attr('department') + '&date=' + $date;
    request.open('GET', url);
    request.responseType = 'blob';
    request.contentType = 'application/vnd.ms-excel';
    request.onload = function () {
        $preloader.addClass('hidden');
        var blob = this.response;
        var contentDispo = this.getResponseHeader('Content-Disposition');
        var str = contentDispo.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1];
        var fileName = decodeURIComponent(str.split("").map(function (ch) {
            return "%" + ch.charCodeAt(0).toString(16);
        }).join(""));
        SaveBlob(blob, fileName);
    };
    request.send();

    function SaveBlob(blob, fileName) {
        var a = document.createElement('a');
        a.href = window.URL.createObjectURL(blob);
        a.download = fileName;
        a.dispatchEvent(new MouseEvent('click'));
    }
});

$('.downloadReport').click(function () {
    var $preloader = $('#preloader');
    $preloader.removeClass('hidden');
    var $date = $('#datePicker').val();
    const request = new XMLHttpRequest();
    const url = window.location.pathname + '?r=chiefs/export/downloadReport' + '&date=' + $date;
    request.open('GET', url);
    request.responseType = 'blob';
    request.contentType = 'application/vnd.ms-excel';
    request.onload = function () {
        $preloader.addClass('hidden');
        var blob = this.response;
        var contentDispo = this.getResponseHeader('Content-Disposition');
        var str = contentDispo.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1];
        var fileName = decodeURIComponent(str.split("").map(function (ch) {
            return "%" + ch.charCodeAt(0).toString(16);
        }).join(""));
        SaveBlob(blob, fileName);
    };
    request.send();

    function SaveBlob(blob, fileName) {
        var a = document.createElement('a');
        a.href = window.URL.createObjectURL(blob);
        a.download = fileName;
        a.dispatchEvent(new MouseEvent('click'));
    }
});