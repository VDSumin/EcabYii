$(document).on('change', '.yearEdu', function() {

    var $element = $(this);
    var val = $element.val();

    $.ajax({
        url: updateYearEdu,
        data: 'yearEdu' + '=' + val,
        method: 'post',
        dataType: 'json',
        beforeSend:  function() {
            $element.prop( "disabled", true );
        },
        success: function(responce) {
            if (responce.success) {
                $element.css({background:'#00FF00'});
                $element.css({opacity: 0.7});
                setTimeout(function(){
                    $element.css({background :'inherit'});
                    $element.css({opacity: 1.0});
                }, 1000);
            } else {
                $element.css({background:'#FF0000'});
                $element.css({opacity: 0.7});
                setTimeout(function(){
                    $element.css({background :'inherit'});
                    $element.css({opacity: 1.0});
                }, 1000);

            }

            $element.prop( "disabled", false );
        },
        error: function() {
            $element.prop( "disabled", false );
            $element.css({background:'#FF0000'});
            $element.css({opacity: 0.7});
            setTimeout(function(){
                $element.css({background :'inherit'});
                $element.css({opacity: 1.0});
            }, 1000);
        }

    });
});

