if ($('#leftCol').length > 0) {
    var stickyTop = $('#leftCol').offset().top; 
    var left = $('#leftCol').offset().left; 
    $(window).scroll(function () { 
        var windowTop = $(window).scrollTop();
        if (stickyTop < windowTop + 80) {
            $('#leftCol').css({
                position: 'fixed',
                top: '80px',
                left: left
            });
        } else {
            $('#leftCol').css('position', 'static');
        }

    });
}

var url = window.location;
// Will only work if string in href matches with location
$('ul.nav a[href="'+ url +'"]').parent().addClass('active');

// Will also work for relative and absolute hrefs
$('ul.nav a').filter(function() {
    return this.href == url;
}).parent().addClass('active');