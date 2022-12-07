function SaveBlob(blob, fileName) {
    var a = document.createElement('a');
    a.href = window.URL.createObjectURL(blob);
    a.download = fileName;
    a.dispatchEvent(new MouseEvent('click'));
}

$(document).ready(function () {
    $('[data-toggle="dropdown"]').dropdown();
    $('[data-toggle="tooltip"]').tooltip();
    $('.export').click(function () {
        var $preloader = $('#p_prldr');
        $preloader.removeClass('hidden');
        const request = new XMLHttpRequest();
        const url = window.location.pathname
            + '?r=/cases/default/download&curr=' + $('select[name="curr"]').val()
            + '&dis=' + $('#discipline').val()
            + '&chair=' + $('#chair').val();
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
    });
});