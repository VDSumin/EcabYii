//Функция для подсчета количества оценок и рейтинга
function listResult() {
    var $Five = 0;
    var $Four = 0;
    var $Three = 0;
    var $Two = 0;
    var $NotCome = 0;
    var $NotTolerance = 0;
    var $NotFill = 0;
    var $R = 0;

    $(".markType").each(function () {
        var $name = $(this).parents('tr').find(".markType").text();

        if ($name.indexOf('Отлично') + 1) {
            $Five++;
        } else if ($name.indexOf('Хорошо') + 1) {
            $Four++;
        } else if ($name.indexOf('Удовлетворительно') + 1) {
            $Three++;
        } else if ($name.indexOf('Неудовлетворительно') + 1) {
            $Two++;
        } else if ($name.indexOf('Неявка') + 1) {
            $NotCome++;
        } else if ($name.indexOf('Неявка по у/п') + 1) {
            $NotCome++;
        } else if ($name.indexOf('Недопущен') + 1) {
            $NotTolerance++;
        }
    });

    $(".Rtext").each(function () {
        var $listR = parseInt($(this).parents('tr').find(".Rtext").text());

        if (isNaN($listR) === false) {
            $R += $listR;
        }
    });

    $(".RJS").each(function () {
        var $listR = parseInt($(this).parents('tr').find(".RJS").val());

        if (isNaN($listR) === false) {
            $R += $listR;
        }
    });

    $(".markName").each(function () {
        var $name = $(this).parents('tr').find(".markName").val();

        if ($name === "Отлично") {
            $Five++;
        } else if ($name === "Хорошо") {
            $Four++;
        } else if ($name === "Удовлетворительно") {
            $Three++;
        } else if ($name === "Неудовлетворительно") {
            $Two++;
        } else if ($name === "Неявка") {
            $NotCome++;
        } else if ($name === "Неявка по у/п") {
            $NotCome++;
        } else if ($name === "Недопущен") {
            $NotTolerance++;
        }
    });

    $NotFill = People - ($Five + $Four + $Three + $Two + $NotCome + $NotTolerance);

    $("#M5").text($Five); //Количество отличных оценок
    $("#M4").text($Four); //Количество оценок хорошо
    $("#M3").text($Three); //Количество оценок удовлетворительно
    $("#M2").text($Two); //Количество оценко неудовлетворительно
    $("#M1").text($NotCome); //Количество неявок
    $("#L1").text($Five + $Four + $Three);
    $("#L2").text($NotCome + $NotTolerance + $NotFill + $Two);
    $("#Rsumm").text($R); //Суммарный рейтинг
    $("#Ravg").text(($R / People).toFixed(2)); //Средний рейтинг

    if(document.getElementById("closeDraft")) {
        var marks = document.getElementsByClassName('markValue');
        if ($NotFill === 0 && marks.length > 0) {
            document.getElementById("closeDraft").style.display = "unset";
        } else {
            document.getElementById("closeDraft").style.display = "none";
        }
    }
}


window.onload = function () {
    listResult();
}

$('#marksForm').submit(function () {
    this.submit();
    listResult();
});

$(function () {
    //Numerical only for input
    $('.RsemJS, .RJS').on("change keyup input click", function () {
        if (this.value.match(/[^0-9]/g)) {
            this.value = this.value.replace(/[^0-9]/g, '');
        }
    });
    $('.RaJS').on("change keyup input click", function () {
        if (this.value.match(/[^0-9,\-]/g)) {
            this.value = this.value.replace(/[^0-9,\-]/g, '');
        }
    });

    //Not submit by enter
    $('#marksForm').on('keyup keypress', function(e) {
        var code = e.keyCode || e.which;

        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });

});

var $system = 0;

$(document).on('blur', '.RsemJS, .RaJS', function () {
    var $Rsem_input = $(this).parents('tr').find('.RsemJS');
    var $Ra_input = $(this).parents('tr').find('.RaJS');
    var $R_input = $(this).parents('tr').find('.RJS');
    var $MarkName_input = $(this).parents('tr').find('.markName');
    var $MarkValue_input = $(this).parents('tr').find('.markValue');

    var $Rcw = parseInt($(this).parents('tr').find('.Rcw').text());
    var $fill = 0;

    var $Rsem = parseInt($Rsem_input.val(), 10);

    //Проверяем что в поле внесли число
    if (isNaN($Rsem) === true && !/(0x)?800100000000242[eE]/.test($MarkValue_input.val())) {
        $Rsem_input.val('');
        $MarkName_input.val('');
        $MarkValue_input.val('');
        $Ra_input.prop('readonly', true);
        $Ra_input.val('');
        $R_input.val('');

    } else {
        //Проверяем введенный рейтинг на правильность
        for (var j = 0; j < marksSem.length; j++) {

            if (($Rsem < marksSem[j].rmin) || ($Rsem > marksSem[j].rmax)) {

                if (typeof marksSem[j].error !== 'undefined') {
                    $Rsem_input.val('');
                    $MarkName_input.val('');
                    $MarkValue_input.val('');
                    $Ra_input.prop('readonly', true);
                    $Ra_input.val('');
                    $R_input.val('');
                    $fill = 1;

                }
            }
        }

        if($Rsem < $Rcw && !/(0x)?800100000000242[eE]/.test($MarkValue_input.val())){
            $Rsem_input.val('');
            $MarkName_input.val('');
            $MarkValue_input.val('');
            $Ra_input.prop('readonly', true);
            $Ra_input.val('');
            $R_input.val('');
            $fill = 1;
            notifyMessage("Внимание", "Текущий рейтинг не может быть меньше рейтинга за КН!");
        }
        /*if($Rsem < 40 && $Rsem > 0){
            $MarkName_input.val('');
            $MarkValue_input.val('');
            $Ra_input.prop('readonly', true);
            $Ra_input.val('');
            $R_input.val('');
            $fill = 1;
            notifyMessage("Внимание", "При текущем рейтинге меньше 40, студент к аттестации не допускается!");
        }*/

        //Если рейтинг правильный
        if ($fill === 0) {

            if ($Ra_input.prop('readonly') === true && !$Rsem_input.prop('readonly')) {
                $Ra_input.removeProp('readonly');
            }
           

            var $Ra = parseInt($Ra_input.val(), 10);
            
            //Проверяем чтобы аттестационный рейтинг был числом
            if (isNaN($Ra) === true && !/(0x)?800100000000242[eE]/.test($MarkValue_input.val())) {
                $Ra_input.val(0);
                $system = 1;
                $MarkName_input.val('');
                $MarkValue_input.val('');
                $R_input.val('');
            }

            $Ra = parseInt($Ra_input.val(), 10);

            var $fill2 = 0;
             //если портал ставит 0 в поле Ra
            //Проверка: аттестационный рейтинг находится в правильном диапазоне
            for (var j = 0; j < marksA.length; j++) {

                if (($Ra < marksA[j].rmin) || ($Ra > marksA[j].rmax)) {

                    if (typeof marksA[j].error !== 'undefined') {
                        $Ra_input.val(0);
                        $MarkName_input.val('');
                        $MarkValue_input.val('');
                        $R_input.val('');
                        $fill2 = 1;
                        notifyMessage("Внимание", marksA[j].error);
                    }
                }
            }

            if($Rsem < 40 && $Rsem > 0 && !(($Ra === -1) || ($Ra === 0)) && !/(0x)?800100000000242[eE]/.test($MarkValue_input.val())){
                $MarkName_input.val('');
                $MarkValue_input.val('');
                $Ra_input.val('');
                $R_input.val('');
                $fill2 = 1;
                notifyMessage("Внимание", "При семестровом рейтинге меньше 40, рейтинг аттестационный<br /> может быть равен \"0\" или \"-1\"");
            }

            //Если аттестационный рейтинг правильный - вычисляем оценку
            if ($fill2 === 0) {
                if ($system === 0){
                    var $R = $Rsem + $Ra;
                    var $marks_fill = 0;

                    if ($Ra === -1) {
                        $R = $Rsem;
                    }

                    if (($Rsem === 39) && (($Ra < -1) || ($Ra > 0))) {
                        $Ra_input.val(0);
                        $MarkName_input.val('');
                        $MarkValue_input.val('');
                        $R_input.val('');
                    }

                    if ((isNaN($Rcw) === false) && ($R < $Rcw) && ($system === 0) && !/(0x)?800100000000242[eE]/.test($MarkValue_input.val())) {
                        //$Rsem_input.val('');
                        $MarkName_input.val('');
                        $MarkValue_input.val('');
                        $Ra_input.prop('readonly', true);
                        $Ra_input.val('');
                        $R_input.val('');
                        notifyMessage("Внимание", "Итоговый рейтинг не может быть меньше рейтинга за КН!");
                    } else if ((isNaN($Rcw) === true) || ((isNaN($Rcw) === false) && ($R >= $Rcw))) {
                        for (var j = 0; j < marksR.length; j++) {

                            if (($R >= marksR[j].rmin) && ($R <= marksR[j].rmax) && (($Ra >= marksR[j].ra_min) && ($Ra <= marksR[j].ra_max))) {

                                $MarkName_input.val(marksR[j].mark['name']);
                                $MarkValue_input.val(marksR[j].mark['nrec']);
                                $R_input.val($R);

                                $marks_fill = 1;

                            }
                        }

                        if ($marks_fill === 0 && !/(0x)?800100000000242[eE]/.test($MarkValue_input.val())) {
                            //$Rsem_input.val('');
                            $MarkName_input.val('');
                            $MarkValue_input.val('');
                            $Ra_input.prop('readonly', true);
                            $Ra_input.val('');
                            $R_input.val('');
                        }
                    }
                } else {
                    $system = 0;
                }

            }

        }
    }

    listResult();
});

function notifyMessage($title, $message) {
    $.notify({
        title: "<center><strong><h4>" + $title + "</h4></strong></center>",
        message: "<center>" + $message + "</center>",
    }, {
        type: 'danger',
        delay: 5000,
        placement: {
            from: 'bottom',
            align: 'center'
        },
        offset: {
            y: 80
        }
    });
}

$(document).on('change input', '.RsemJS', function () {
    var $Rsem_input = $(this).parents('tr').find('.RsemJS');
    var $Ra_input = $(this).parents('tr').find('.RaJS');
    var $R_input = $(this).parents('tr').find('.RJS');
    var $MarkName_input = $(this).parents('tr').find('.markName');
    var $MarkValue_input = $(this).parents('tr').find('.markValue');

    var $Rcw = parseInt($(this).parents('tr').find('.Rcw').text());
    var $fill = 0;

    var $Rsem = parseInt($Rsem_input.val(), 10);

    //Проверяем что в поле внесли число
    if (isNaN($Rsem) === true && !/(0x)?800100000000242[eE]/.test($MarkValue_input.val())) {
        $Rsem_input.val('');
        $MarkName_input.val('');
        $MarkValue_input.val('');
        $Ra_input.prop('readonly', true);
        $Ra_input.val('');
        $R_input.val('');

    } else {
        //Проверяем введенный рейтинг на правильность
        for (var j = 0; j < marksSem.length; j++) {

            if (($Rsem < marksSem[j].rmin) || ($Rsem > marksSem[j].rmax)) {

                if (typeof marksSem[j].error !== 'undefined') {
                    $Rsem_input.val('');
                    $MarkName_input.val('');
                    $MarkValue_input.val('');
                    $Ra_input.prop('readonly', true);
                    $Ra_input.val('');
                    $R_input.val('');
                    $fill = 1;
                    notifyMessage("Внимание", marksSem[j].error);
                }
            }
        }

        //Если рейтинг правильный
        if ($fill === 0) {

            if ($Ra_input.prop('readonly') === true) {
                $Ra_input.removeProp('readonly');
            }


            var $Ra = parseInt($Ra_input.val(), 10);

            //Проверяем чтобы аттестационный рейтинг был числом
            if (isNaN($Ra) === true && !/(0x)?800100000000242[eE]/.test($MarkValue_input.val())) {
                $Ra_input.val(0);
                $system = 1;
                $MarkName_input.val('');
                $MarkValue_input.val('');
                $R_input.val('');
            }

        }
    }

    listResult();
});
