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
        console.log(marks);
        if ($NotFill === 0 && marks.length > 0) {
            document.getElementById("closeDraft").style.display = "unset";
        } else {
            document.getElementById("closeDraft").style.display = "none";
        }
    }
}

window.onload = function(){
    listResult();
}

$('#marksForm').submit(function() {
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
    if (isNaN($Rsem) === true) {
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
                    alert(marksSem[j].error);
                }
            }
        }
        
        //Если рейтинг правильный
        if ($fill === 0) {
            
            if ($Rsem <= 39) {
                $Ra_input.prop('readonly', true);
                $Ra_input.val(0);
            } else {
                if ($Ra_input.prop('readonly') === true) {
                    $Ra_input.removeProp('readonly');
                }
            }

            

            var $Ra = parseInt($Ra_input.val(), 10);
            //Проверяем чтобы аттестационный рейтинг был числом
            if (isNaN($Ra) === true) {
                $Ra_input.val(0);
                $MarkName_input.val('');
                $MarkValue_input.val('');
                $R_input.val('');
            } else if (($Rsem !== 60) && ($Ra === -1)) {
                $Ra_input.val(0);
                $MarkName_input.val('');
                $MarkValue_input.val('');
                $R_input.val('');
            }

            $Ra = parseInt($Ra_input.val(), 10);

            var $fill2 = 0;
            //Проверка: аттестационный рейтинг находится в правильном диапазоне
            for (var j = 0; j < marksA.length; j++) {

                if (($Ra < marksA[j].rmin) || ($Ra > marksA[j].rmax)) {

                    if (typeof marksA[j].error !== 'undefined') {
                        $Ra_input.val(0);
                        $MarkName_input.val('');
                        $MarkValue_input.val('');
                        $R_input.val('');
                        $fill2 = 1;
                        alert(marksA[j].error);
                    }
                }
            }

            //Если аттестационный рейтинг правильный - вычисляем оценку
            if ($fill2 === 0) {
                var $R = $Rsem + $Ra;
                var $marks_fill = 0;
                if ((isNaN($Rcw) === false) && ($R < $Rcw)) {
                   // $Rsem_input.val('');
                    $MarkName_input.val('');
                    $MarkValue_input.val('');
                    $Ra_input.prop('readonly', true);
                    $Ra_input.val('');
                    $R_input.val('');
                    alert('Итоговый рейтинг не может быть меньше рейтинга за КН!');
                } else if ((isNaN($Rcw) === true) || ((isNaN($Rcw) === false) && ($R >= $Rcw))) {
                    for (var j = 0; j < marksR.length; j++) {

                        if (($R >= marksR[j].rmin) && ($R <= marksR[j].rmax) && (($Ra >= marksR[j].ra_min) && ($Ra <= marksR[j].ra_max))) {

                            $MarkName_input.val(marksR[j].mark['name']);
                            $MarkValue_input.val(marksR[j].mark['nrec']);
                            $R_input.val($R);
                            $marks_fill = 1;

                        }
                    }

                    if ($marks_fill === 0) {
                        $Rsem_input.val('');
                        $MarkName_input.val('');
                        $MarkValue_input.val('');
                        $Ra_input.prop('readonly', true);
                        $Ra_input.val('');
                        $R_input.val('');
                    }
                }

            }

        }
    }

    listResult();
});

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
    if (isNaN($Rsem) === true) {
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
                    alert(marksSem[j].error);
                }
            }
        }

        //Если рейтинг правильный
        if ($fill === 0) {

            if ($Rsem <= 39) {
                $Ra_input.prop('readonly', true);
                $Ra_input.val(0);
            } else {
                if ($Ra_input.prop('readonly') === true) {
                    $Ra_input.removeProp('readonly');
                }
            }

        }
    }

    listResult();
});
