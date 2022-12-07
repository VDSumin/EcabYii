$(function () {

    //Numerical only for input
    $('#tf_audHours, .tf_tHours, .tf_pHours, .ratingweek').on("change keyup input click", function () {
        if (this.value.match(/[^0-9]/g)) {
            this.value = this.value.replace(/[^0-9]/g, '');
        }
    });

    var planOld = 0;

    function rating() {
        var itog = 0;
        var R_27 = 0;
        var R23_27 = 0;
        var R18_23 = 0;
        var R18 = 0;
        var i = 0;



        $('.ratingweekTd').each(function () {
            var r = parseInt($(this).parents('tr').find('.ratingweekTd').text());


            if (isNaN(r) === false) {
                if (r >= 27){
                    R_27++;
                }else if ((r >= 23) && (r < 27)){
                    R23_27++;
                }else if ((r >= 18) && (r < 23)){
                    R18_23++;
                }else if (r < 18){
                    R18++;
                }
                itog+=r;
                i++;
            }

        }) ;

        $('.ratingweek').each(function () {
            var r = parseInt($(this).parents('tr').find('.ratingweek').val());
            if (isNaN(r) === false) {
                if (r >= 27){
                    R_27++;
                }else if ((r >= 23) && (r < 27)){
                    R23_27++;
                }else if ((r >= 18) && (r < 23)){
                    R18_23++;
                }else if (r < 18){
                    R18++;
                }
                itog+=r;
                i++;
            }

        }) ;

        $("#Rsr").text((itog/i).toFixed(2));
        $("#R27").text(R_27);
        $("#R23_27").text(R23_27);
        $("#R18_23").text(R18_23);
        $("#R18").text(R18);
    }

    function hoursDis(){

        var r = parseInt($('#tf_audHours').val());
        var summ = 0;
        var i = 0;

        if ((isNaN(r) === false) && (r > 0)) {
            $('.tf_tHours').each(function () {
                /*$(this).parents('tr').find('.tf_tHours').removeProp('disabled');*/
                i++;
                var tHours = parseInt($(this).parents('tr').find('.tf_tHours').val());
                if (isNaN(tHours) === false) {
                    summ += tHours;
                }
            });
            var percent = ((summ/(i*r))*100).toFixed(0);
            $('#summ').text(summ);
            $('#percent').text(percent);

            /* $('.tf_pHours').each(function () {
                 $(this).parents('tr').find('.tf_pHours').removeProp('disabled');
             });*/
        } else {
            $('.tf_tHours').each(function () {
                $(this).parents('tr').find('.tf_tHours').prop('disabled', 'disabled');

            });
            $('.tf_pHours').each(function () {
                $(this).parents('tr').find('.tf_pHours').prop('disabled', 'disabled');
            });
        }
    }

    function Sigma(){
        var summ = 0;
        var i = 0;
        var r = parseInt($('#tf_audHours').val());

        $('.tf_tHours').each(function () {
            i++;
            var tHours = parseInt($(this).parents('tr').find('.tf_tHours').val());
            if (isNaN(tHours) === false) {
                summ += tHours;
            }
        });

        var percent = ((summ/(i*r))*100).toFixed(0);
        $('#summ').text(summ);
        $('#percent').text(percent);
    }

    $('.ratingweek').on('blur',function () {
        var r = parseInt($(this).val());

        if (isNaN(r) === true) {
            $(this).val(0);
        } else if (isNaN(r) === false) {
            if (r < 0 || r > 60){
                $(this).val(0);
                alert('Рейтинг за контрольную неделю должен быть от 0 до 60');
            }
        }
        rating();
    });

    $('.tf_tHours').on('blur',function () {
        var plan = parseInt($('#tf_audHours').val());
        var tHours = parseInt($(this).parents('tr').find('.tf_tHours').val());

        if (isNaN(tHours) === false) {
            if (tHours <= plan) {
                var pHours = ((tHours / plan * 100).toFixed(0));
                $(this).parents('tr').find('.tf_pHours').val(pHours);
                Sigma();
            } else {
                $(this).parents('tr').find('.tf_tHours').val(0);
            }
        } else {
            $(this).parents('tr').find('.tf_tHours').val(0);
        }
    });

    $('.tf_tHours').on('focus',function () {
        var tHours = parseInt($(this).parents('tr').find('.tf_tHours').val());

        if ((isNaN(tHours) === false) && (tHours === 0)) {
            $(this).parents('tr').find('.tf_tHours').val('');
        }
    });

    $('.ratingweek').on('focus',function () {
        var tHours = parseInt($(this).parents('tr').find('.ratingweek').val());

        if ((isNaN(tHours) === false) && (tHours === 0)) {
            $(this).parents('tr').find('.ratingweek').val('');
        }
    });


    $('#tf_audHours').on('focus',function () {

        planOld = parseInt($('#tf_audHours').val());

    });

    window.onload = function() {
        rating();
        hoursDis();
    };


    $('#ratingForm').submit(function(ev) {
        var error = 0;
        ev.preventDefault(); // to stop the form from submitting
        /* Validations go here */
        var $hours = $('#tf_audHours').val();

        if (isNaN($hours) === true) {
            alert('Необходимо заполнить количество часов по расписанию');
            error++;
        } else if (isNaN($hours) === false){
            if ($hours <=0 ){
                error++;
                alert('Необходимо заполнить количество часов по расписанию');
            }

        }

        var summ = parseInt($('#summ').text());
        var percent = parseInt($('#percent').text());
        var Rsr = parseFloat($('#Rsr').text());

        if ((isNaN(Rsr) === true)){
            alert('Проверьте заполнение полей');
            error++;
        } else if ((Rsr <= 0)){
            alert('Проверьте заполнение полей');
            error++;
        }

        if (error == 0){
            this.submit(); // If all the validations succeeded
        }

    });


});