function listResult(){
    var $Ladder = 0;
    var $NotLadder = 0;
    var $NotTolerance = 0;
    var $NotFill = 0;
    var $R = 0;

    $(".markType").each(function(){
        var $name = $(this).parents('tr').find(".markType").text();

        if ($name.indexOf('Зачтено') + 1){
            $Ladder++;
        } else if ($name.indexOf('Незачтено') + 1){
            $NotLadder++;
        } else if ($name.indexOf('Недопущен') + 1) {
            $NotTolerance++;
        }
    });

    $(".Rtext").each(function(){
        var $listR = parseInt($(this).parents('tr').find(".Rtext").text());

        if (isNaN($listR) === false){
            $R += $listR;
        }
    });

    $(".RJS").each(function(){
        var $listR = parseInt($(this).parents('tr').find(".RJS").val());

        if (isNaN($listR) === false){
            $R += $listR;
        }
    });

    $(".markName").each(function(){
        var $name = $(this).parents('tr').find(".markName").val();

        if ($name === "Зачтено"){
            $Ladder++;
        } else if ($name === "Незачтено"){
            $NotLadder++;
        } else if ($name === 'Недопущен') {
            $NotTolerance++;
        }
    });

    $NotFill = People - ($Ladder + $NotTolerance + $NotLadder);

    $("#L1").text($Ladder); //Количество зачетов
    $("#L2").text($NotLadder + $NotTolerance + $NotFill); //Количество незачетов
    $("#Rsumm").text($R); //Суммарный рейтинг
    $("#Ravg").text(($R/People).toFixed(2)); //Средний рейтинг

    if(document.getElementById("closeDraft")) {
        var marks = document.getElementsByClassName('markValue');
        if ($NotFill === 0 && marks.length > 0) {
            document.getElementById("closeDraft").style.display = "unset";
        } else {
            document.getElementById("closeDraft").style.display = "none";
        }
    }
}

function listResultDiff() {
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

        if($(this).parents('tr').find(".markType").find(".markValue").text() == '') {
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

    $(".markValue").each(function () {
        var el = document.getElementById($(this).prop("id"));
        var $name = el.options[el.selectedIndex].text;

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

window.onload = function(){
    //console.log(Typediffer);
    if(Typediffer == 1) {
        listResult();
    }else{
        listResultDiff();
    }
}

$('#marksForm').submit(function() {
    this.submit();
    if(Typediffer == 1) {
        listResult();
    }else{
        listResultDiff();
    }
});


$(function () {
    //Numerical only for input
    $('.RJS').on("change keyup input click", function () {
        if (this.value.match(/[^0-9]/g)) {
            this.value = this.value.replace(/[^0-9]/g, '');
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


$(document).on('blur', '.RJS', function () {
    var $R_input = $(this).parents('tr').find('.RJS');
    var $MarkName_input = $(this).parents('tr').find('.markName');
    var $MarkValue_input = $(this).parents('tr').find('.markValue');

    var $Rcw = parseInt($(this).parents('tr').find('.Rcw').text());
    var $fill = 0;

    var $R = parseInt($R_input.val(), 10);

    //Проверяем что в поле внесли число
    if (isNaN($R) === true) {
        $R_input.val('');
        $MarkName_input.val('');
        $MarkValue_input.val('');

    } else {
        if (($R < 0) || ($R > 100)) {
            $fill = 1;
        }


        //Если рейтинг правильный
        if ($fill === 0) {


            var $fill2 = 0;

            //Если аттестационный рейтинг правильный - вычисляем оценку
            if ($fill2 === 0) {
                var $marks_fill = 0;

                if (isNaN($Rcw) === true){
                    for (var j = 0; j < marksR.length; j++) {

                        if (($R >= marksR[j].rmin) && ($R <= marksR[j].rmax)) {

                            $MarkName_input.val(marksR[j].mark['name']);
                            $MarkValue_input.val(marksR[j].mark['nrec']);
                            $marks_fill = 1;

                        }
                    }

                    if ($marks_fill === 0) {
                        $MarkName_input.val('');
                        $MarkValue_input.val('');
                        $R_input.val('');
                    }
                } else {
                    if ($R < $Rcw){
                        $MarkName_input.val('');
                        $MarkValue_input.val('');
                        $R_input.val('');
                        alert('Итоговый рейтинг не может быть меньше рейтинга за КН!');
                    } else{
                        for (var j = 0; j < marksR.length; j++) {

                            if (($R >= marksR[j].rmin) && ($R <= marksR[j].rmax)) {

                                $MarkName_input.val(marksR[j].mark['name']);
                                $MarkValue_input.val(marksR[j].mark['nrec']);
                                $marks_fill = 1;

                            }
                        }

                        if ($marks_fill === 0) {
                            $MarkName_input.val('');
                            $MarkValue_input.val('');
                            $R_input.val('');
                        }
                    }
                }

            }

        }
    }

    if(Typediffer == 1) {
        listResult();
    }else{
        listResultDiff();
    }
});

$(document).on('change input', '.RJS', function () {
    var $R_input = $(this).parents('tr').find('.RJS');
    var $MarkName_input = $(this).parents('tr').find('.markName');
    var $MarkValue_input = $(this).parents('tr').find('.markValue');

    var $Rcw = parseInt($(this).parents('tr').find('.Rcw').text());
    var $fill = 0;

    var $R = parseInt($R_input.val(), 10);

    //Проверяем что в поле внесли число
    if (isNaN($R) === true) {
        $MarkName_input.val('');
        $MarkValue_input.val('');
        $R_input.val('');

    } else {
        if (($R < 0) || ($R > 100)) {
            $MarkName_input.val('');
            $MarkValue_input.val('');
            $R_input.val('');
        }
    }

    if(Typediffer == 1) {
        listResult();
    }else{
        listResultDiff();
    }
});

$(document).on('change', '.markValue', function () {
    // console.log(Typediffer);
    if(Typediffer == 1) {
        listResult();
    }else{
        listResultDiff();
    }
});
