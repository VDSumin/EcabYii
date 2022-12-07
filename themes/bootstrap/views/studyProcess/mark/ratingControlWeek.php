<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 29.03.2020
 * Time: 21:14
 */
/* @var $this MarkController */

$this->pageTitle = Yii::app()->name . ' - Ведомость контрольной недели';
$this->breadcrumbs = [
    'Ведомости'=>array('/studyProcess/mark'),
    'Ведомость контрольной недели '.CHtml::value($info, 'numDoc')
];
?>

<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/ratingWeek.js?2', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/modalStatusFiles.js?2', CClientScript::POS_END);
?>

<script>
    var linkComment = <?php echo CJSON::encode(Yii::app()->createAbsoluteUrl('/studyProcess/mark/updateCommentAtFile'));?>;
    var linkUpdate = <?php echo CJSON::encode(Yii::app()->createAbsoluteUrl('/studyProcess/mark/updateStateAtFile'));?>;
</script>

<div id="headername"><p style="text-align: center; font-size: 12pt;">

        Федеральное государственное бюджетное образовательное <br/>
        учреждение высшего образования<br/>
        «ОМСКИЙ ГОСУДАРСТВЕННЫЙ ТЕХНИЧЕСКИЙ УНИВЕРСИТЕТ»<br/>
        <strong> Ведомость текущего контроля <br />
            успеваемость студентов (КОНТРОЛЬНАЯ НЕДЕЛЯ)
            №<ins><?php echo CHtml::value($info, 'numDoc'); ?></ins></strong><br/>
        ФАКУЛЬТЕТ (ИНСТИТУТ): <strong><ins><?php echo CHtml::value($info, 'listFacult'); ?></ins></strong>,
        Группа <strong><ins><?php echo CHtml::value($info, 'studGroup'); ?></ins></strong><br/>
        текущая аттестация за <strong><ins><?php echo CHtml::value($info, 'semester'); ?></ins></strong> семестр по дисциплине (модулю, виду учеб.занятий): <br/>
        <strong><?php echo CHtml::value($info, 'discipline'); ?></strong><br/><?php echo CHtml::value($info, 'listChair'); ?>

</p></div>
<?php echo CHtml::beginForm('', 'post', array("id" => "ratingControlWeek", 'class' => 'form-inline', 'data-form-confirm' => "modal__confirm")); ?>
<div style="text-align: center">
    Получить часы посещаемости из Электронного журнала посещаемости по <b style="color: red">дату контрольной недели</b>
    <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'AudHours_N[dateCW]',
            'value' => CMisc::fromGalDate(CHtml::value($info, 'dateOfCurHours'), 'Y-m-d'),
            'language' => 'ru',
            'options' => array(
                'dateFormat' => "yy-mm-dd",
                'minDate' => '2016-09-01',
                'showAnim' => 'slideDown',
                'showOtherMonths' => true,
                'firstDay' => 1,
            ),
            'htmlOptions' => array(
                'style' => 'text-align: center; display: inline; width: auto;',
                'class' => 'form-control',
            ),
        ));
        echo " ";
        //echo CHtml::submitButton('Выгрузить посещаемость', array('class' => 'btn btn-primary', 'onclick' => 'alert("Над этим надо ещё подучать");'));
    if (CHtml::value($info, 'status') == 1) :
        ?>
        <span rel='tooltip' data-toggle='tooltip' title='Информация' class='glyphicon glyphicon-info-sign' style="color:black"
              onclick="alert('Вам необходимо выбрать дату прохождения контрольной недели, на эту дату выгрузится количество пройденных часов за семестр.')" type="button">
</span>
    <?php else : ?>
        <span rel='tooltip' data-toggle='tooltip' title='Информация' class='glyphicon glyphicon-info-sign' style="color:black"
              onclick="alert('Ведомость закрыта. Рейтинг недоступен для заполнения')" type="button">
</span>
    <?php endif; ?>
</div>
<div style="text-align: center;" >
    Аудиторных часов всего: Ауд.  =  <b><?php echo CHtml::value($info, 'audHoursList') ?></b>.
        Прошло по расписанию на момент КН: Tрасп =
    <?php echo CHtml::textField('AudHours_N[audHoursCurr]',
        CHtml::value($info, 'audHoursCurr') , array(
                'class' => 'form-control',
                'id' => 'tf_audHours',
                'style' => 'text-align: center; display: inline; width: auto;',
                'readonly' => 'readonly',
    )); echo CHtml::hiddenField('AudHours_N[nrecList]', CHtml::value($info, 'nrec'));
    ?>
</div>

<p style="font-weight: bold; color: red; text-align: center">Внимание! Все числа должны быть целыми. При попытке записать дробные значения, они буду округлены автоматически.</p>
<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'group-grid',
    'dataProvider' => $dataProvider,
    'columns'=>array(
        array(
            'header' => '№',
            'htmlOptions' => array('style' => 'text-align: center; width: 15px'),
            'value' => '$row+1',
        ),
        'fio' => array(
            'name' => 'fio',
            'header' => 'Ф.И.О. Студента',
            'htmlOptions' => array('style' => 'width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "fio")',
        ),
        'tHours' => array(
            'name' => 'tHours',
            'header' => 'Всего часов(Т)',
            'htmlOptions' => array('style' => 'text-align: -webkit-center; width: 15%'),
            'type' => 'raw',
            'value' => 'ListClass::getWRating("tHours", 
                CHtml::value($data, "markStudNrec"),
                CHtml::value($data, "totalStudHours"),
                "tHoursclass",
                "readonly")',
        ),
        'pHours' => array(
            'name' => 'pHours',
            'header' => '% от плана КН',
            'htmlOptions' => array('style' => 'text-align: -webkit-center; width: 15%'),
            'type' => 'raw',
            'value' => 'ListClass::getWRating("pHours", 
                CHtml::value($data, "markStudNrec"),
                CHtml::value($data, "percent"),
                "pHoursclass",
                "readonly")',
        ),
        'Rcw' => array(
            'name' => 'Rcw',
            'htmlOptions'=>array('style'=>'text-align: -webkit-center; width: 20%', 'class' => 'ratingweekTd'),
            'header' => 'Текущий накопленный рейтинг Rкн',
            'type' => 'raw',
            'value' => 'ListClass::getWRating("wrating", 
                CHtml::value($data, "markStudNrec"),
                CHtml::value($data, "rating"),
                "ratingweek",
                (CHtml::value($data, "rating") != 0) ? "readonly" : "",
                (CHtml::value($data, "rating") != 0) ? "disabled" : "")'
        ),
        array(
            'name' => 'filesDis',
            'htmlOptions' => array('style' => 'text-align: -webkit-center; width: 25%'),
            'header' => 'Файлы работы',
            'type' => 'raw',
            'value' => 'ListClass::getTotalInfoOfFilesByDisAndSemester(CHtml::value($data, "studPersonNrec"),
            '.CHtml::value($info, 'numDoc').', \''.CHtml::value($info, "disciplineNrec").'\',
            '.CHtml::value($info, 'semester').', CHtml::value($data, "dbDipNrecNrec"),
            '.CHtml::value($info, 'typeList').' )',
            'visible' => (!in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))),
        ),
    ),

));
echo CHtml::endForm();

$this->renderPartial('/mark/_tableStatCW');

if (CHtml::value($info, 'status') == 1) {
    echo CHtml::link('Подписать и закрыть', null, array('class' => 'saveButton btn btn-primary', 'onclick' => 'saveRaiting()', 'style' => 'margin-bottom:20px;'));
}

echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>информация обрабатывается</small></div>
</div>';
?>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Файлы, загруженные студентом</h4>
            </div>
            <div id="modalContentFromJs" class="modal-body">
                ...
            </div>
        </div>
    </div>
</div>

<script>
    function enableLoading() {
        var $preloader = $('#preloader');
        $preloader.removeClass('hidden');
    }
    function disableLoading() {
        var $preloader = $('#preloader');
        $preloader.addClass('hidden');
    }
    function saveRaiting() {
        var url = '<?= Yii::app()->createAbsoluteUrl('/studyProcess/mark/saveRatingControlWeek'); ?>';
        var ajax_form = 'ratingControlWeek';
        var select = $(this);

        enableLoading();
        select.prop("disabled", true);
        $.ajax({
            'url': url,
            'type': 'post',
            'dataType': 'json',
            'data': $("#"+ajax_form).serialize(),
            'success' : function(responce) {
                disableLoading();
                select.prop("disabled", false);
                if (responce.success) {
                    notifySaveMark(responce.successFields);
                }
            },
            'error' : function(responce) {
                disableLoading();
                select.prop("disabled", false);
            }
        });
    }
    function notifySaveMark($data) {
        for(var item in $data) {
            var input = document.getElementById('wrating_'+item);
            if(input != null) {
                if ($data[item]['code'] == 200) {
                    if (input.value != 0) {
                        input.setAttribute('readonly', 'readonly');
                        input.setAttribute('disabled', 'disabled');
                        input.parentElement.className += ' has-success';
                    }
                } else {
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                    input.parentElement.className += ' has-error';
                }
            }
        }

    }

    $(document).on('change', '.hasDatepicker', function(e) {
        var $select = $(this);
        var $id = document.getElementById('AudHours_N_nrecList').value;
        var $hours = document.getElementById('tf_audHours');
        enableLoading();
        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('studyProcess/mark/audHoursControlWeek'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'idList=' + $id+'&DateCW='+$select.val(),
            'success' : function(responce) {
                disableLoading();
                var curColor = $hours.style.backgroundColor;
                if (responce.success) {
                    $hours.value = responce.text;
                    $hours.style.background = "#00FF00";

                    var response = responce.thours;
                    var L = document.getElementsByClassName('tHoursclass');
                    for (i = 0; i < L.length; i++) {
                        if(response[L[i].id]){
                            L[i].value = response[L[i].id];
                        }else{
                            L[i].value = 0;
                        }
                    }
                    response = responce.phours;
                    L = document.getElementsByClassName('pHoursclass');
                    for (i = 0; i < L.length; i++) {
                        if(response[L[i].id]){
                            L[i].value = response[L[i].id];
                        }else{
                            L[i].value = 0;
                        }

                    }
                    L = document.getElementsByClassName('ratingweek');
                    for (i = 0; i < L.length; i++) {
                        L[i].disabled = false;
                    }
                    $.notify({
                        title: 'Часы успешно выгружены',
                        message: 'Не забудьте сохранить данные в ведомости, для этого необходимо нажать кнопку "Подписать и закрыть" внизу страницы.',
                    },{
                        type: 'info',
                        icon_type: 'class',
                        delay: 5000,
                        placement: {
                            from: 'bottom',
                            align: 'center'
                        },
                        offset: {
                            y: 80
                        }
                    });
                    setTimeout(function(){
                        $hours.style.background = curColor;
                    }, 400);
                }else{
                    $hours.style.background = "#FF0000";
                    $select.val('');
                    setTimeout(function(){
                        $hours.style.background = curColor;
                    }, 1000);
                }
            },
            'error' : function(responce) {
                disableLoading();
            }
        });
    });

    /*Убрать после использования, временное решение*/
    $(document).on('click', '#tf_audHours', function(e) {
        if (e.ctrlKey && e.shiftKey && e.altKey){
            var L = document.getElementsByClassName('ratingweek');
            for (i = 0; i < L.length; i++) {
                L[i].disabled = false;
                L[i].removeAttribute('readonly');
            }
        }
    });

    $(document).on('click', '.modalWin', function(e) {
        e.preventDefault();
        var $select = $(this);
        var studVal = $select.parents('tr').find('.studVal'). val();

        $select.prop("disabled", true);

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('studyProcess/mark/loadlistfile'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'studVal=' + studVal,
            'success' : function(responce) {
                if (responce.success) {
                    $('#modalContentFromJs').html(responce.success);
                    $('#myModal').modal('show');
                    $select.prop("disabled", false);
                } else {
                    $select.prop("disabled", false);
                }
            }
        });
    });

    $('#myModal').on('show.bs.modal', function (e) {
        $('[rel="tooltip"]').tooltip();

    })

    $('#myModal').on('hidden.bs.modal', function (e) {
        $('#modalContentFromJs').html('');
        $.fn.yiiGridView.update("tableMarks", {
            complete:function() {
                $("[data-toggle='toggle']").bootstrapToggle('destroy');
                $("[data-toggle='toggle']").bootstrapToggle();
                $('[rel="tooltip"]').tooltip();
            }
        });

    });


</script>

