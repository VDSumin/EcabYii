<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 29.03.2020
 * Time: 21:14
 */

$this->pageTitle = Yii::app()->name . ' - Аттестационная ведомость';
$this->breadcrumbs = [
    'Ведомости'=>array('/studyProcess/mark'),
    'Аттестационная ведомость '.CHtml::value($info, 'numDoc')
];
?>

<?php
if  (in_array (CHtml::value($info, 'typeList'), array(uList::TYPE_EXAM, uList::TYPE_PRACTICE))) {
    Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/mark/ratingExam.js?2', CClientScript::POS_END);
} else {
    if (in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))) {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/mark/rating.js?2', CClientScript::POS_END);
    }else {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/mark/ratingLadder2.js?2', CClientScript::POS_END);
    }
}

Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/rbexist.js?2', CClientScript::POS_END);

if (in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))) {
    Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/dip.js', CClientScript::POS_END);
}
Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/modalStatusFiles.js?2', CClientScript::POS_END);

Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/jquery.min.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/jquery-ui.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerCSSFile(Yii::app()->getBaseUrl(true) . '/css/jquery-ui.css', CClientScript::POS_HEAD);
?>
<!-- Импорт jquery UI для autocomplete
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css"/>
<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>-->

<script>
    var marksR = <?php echo CJSON::encode(uMark::getRating(CHtml::value($info, 'typeList'), CHtml::value($info, 'typeDiffer'), 'R'));?>;
    var marksSem = <?php echo CJSON::encode(uMark::getRating(CHtml::value($info, 'typeList'), CHtml::value($info, 'typeDiffer'), 'Rsem'));?>;
    var marksA = <?php echo CJSON::encode(uMark::getRating(CHtml::value($info, 'typeList'), CHtml::value($info, 'typeDiffer'), 'Ra'));?>;
    var People = <?php echo CJSON::encode($dataProvider->totalItemCount);?>;
    var Typediffer = <?php echo CJSON::encode(CHtml::value($info, 'typeDiffer'));?>;
    var linkComment = <?php echo CJSON::encode(Yii::app()->createAbsoluteUrl('/studyProcess/mark/updateCommentAtFile'));?>;
    var linkUpdate = <?php echo CJSON::encode(Yii::app()->createAbsoluteUrl('/studyProcess/mark/updateStateAtFile'));?>;
</script>

<div id="headername"><p style="text-align: center; font-size: 12pt;">

        Федеральное государственное бюджетное образовательное <br/>
        учреждение высшего образования<br/>
        «ОМСКИЙ ГОСУДАРСТВЕННЫЙ ТЕХНИЧЕСКИЙ УНИВЕРСИТЕТ»<br/>
        Аттестационная ведомость №<strong><ins><?= CHtml::value($info, 'numDoc') ?></ins></strong><br/>
        ФАКУЛЬТЕТ (ИНСТИТУТ): <strong><ins><?= CHtml::value($info, 'listFacult') ?></ins></strong>,
        Группа <strong><ins><?= CHtml::value($info, 'studGroup') ?></ins></strong><br/>
        промежуточная аттестация за <strong><ins><?= CHtml::value($info, 'semester') ?></ins></strong>
        семестр по дисциплине (модулю, виду учеб.занятий): <br/>
        <strong><?= CHtml::value($info, 'discipline')." (".CHtml::value($info, 'disciplineAbbr').")" ?></strong><br/>
        Объем часов: Всего
        = <strong><ins><?= CHtml::value($info, 'audHoursTotalList') ?></ins></strong>
        Ауд. = <strong><ins><?= CHtml::value($info, 'audHoursList') ?></ins></strong>.
        Форма аттестации: <strong><ins><?= CHtml::value($info, 'formAttestationList') ?></ins></strong>

    </p>
</div>

<p class="text-header">
    <strong>
        <a href="https://omgtu.ru/educational_activities/dokumenty_smk/Pologeniya/П_81.10-2019_Положение о проведении текущего контроля успеваемости и промежуточной аттестации обучающихся.pdf"
           target="_blank" style="color: #ff0000;">
            О проведении текущего контроля успеваемости и промежуточной аттестации обучающихся по программам
            бакалавриата, специалитета, магистратуры
        </a>
    </strong>
</p>

<div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <center><strong>Внимание!</strong> Работа данного модуля проводится в тестовом режиме, в случае возникновения ошибок писать на почту:<br/>
        ias@omgtu.tech</center>
</div>
<?php if (CHtml::value($info,"typeList") == 9){
    echo '<div class="alert alert-danger alert-dismissible" role = "alert" >
    <button type = "button" class="close" data - dismiss = "вфтпук" aria - label = "Close" ><span aria - hidden = "true" >&times;</span ></button >
    <strong > Внимание!</strong > Если необходимое наименование предприятия не отображается в выпадающем списке, 
    добавление нового предприятия доступно только на портале "Учебный процесс".</div>';
    /*echo '<div '. CHtml::link('Добавить новое предприятие',array("/rcfge/RcfgeGalEnterprises/index"),
        array("style" => "color:red;", "target" => "_blank")). '</div>';*/

}?>


<?php echo CHtml::beginForm('', 'post', array("id" => "ratingMarks", 'class' => 'form-inline', 'data-form-confirm' => "modal__confirm")); ?>

<div>
    <label> Дата проведения аттестации </label>
    <?php  $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'name' => 'AudHours_N[dateDoc]',
        'options' => array(
            'showAnim' => 'fold',
            'dateFormat' => 'dd.mm.yy',
            'showOtherMonths' => true,
            'firstDay' => 1,
        ),
        'language' => 'ru',
        'htmlOptions' => array(
            'class' => 'form-control',
            'style' => 'text-align:center; width: auto; display: inline-block;',
            'disabled'=>((in_array(CHtml::value($info, 'status'), [0,1]) //&& CHtml::value($info, 'dopStatusList') == 0
            )?false:true),
        ),
        'value' => CMisc::fromGalDate(CHtml::value($info, 'dateList'), 'd.m.Y')
    ));
    echo CHtml::hiddenField('AudHours_N[nrecList]', CHtml::value($info, 'nrec'));
    echo CHtml::hiddenField('AudHours_N[dop]', CHtml::value($info, 'dopStatusList')); ?>
<?php
if (in_array(CHtml::value($info, 'status'), [0, 1]) &&  CHtml::value($info, 'dopStatusList') == 1) {
    echo CHtml::button('Сохранить дату аттестации', array('class' => 'btn btn-primary', 'onclick' => 'saveAttDate()', 'name' => 'button', 'id' => 'saveAttestDate', 'style' => 'display: none; margin-left:20px;'));
}
?>
</div>

<?php
$this->widget('application.widgets.grid.BGridView', array(
    'id' => 'tableMarks',
    'dataProvider' => $dataProvider,
    'columns'=>array(
        array(
            'header' => '№',
            'htmlOptions' => array('style' => 'width: 10px; text-align: center;'),
            'value' => '$row+1',
        ),
        'fio' => array(
            'name' => 'fio',
            'header' => 'Ф.И.О. Студента',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "fio")',
        ),
        'recordBook' => array(
            'name' => 'recordBook',
            'htmlOptions'=>array('style'=>'width: 80px; text-align: center;', "readonly" => "true"),
            'header' => '№ зачетной книжки',
            'type' => 'raw',
            'value' => 'ListClass::getRecordBook($data)',
        ),
        'numberProto' => array(
            'name' => 'numberProto',
            'htmlOptions'=>array('style'=>'width: 30px; text-align: center;'),
            'header' => '№ протокола',
            'type' => 'raw',
            'value' => 'ListClass::getDipNumberData($data)',
            'visible' => in_array(CHtml::value($info,"typeList"), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT)),
        ),
        'dataProto' => array(
            'name' => 'dataProto',
            'htmlOptions'=>array('style'=>'text-align: center;'),
            'header' => 'Дата протокола',
            'type' => 'raw',
            'value' => 'ListClass::getDipProtoData($data)',
            'visible' => in_array(CHtml::value($info,"typeList"), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT)),
        ),
        'enterprise' => array(
            'name' => 'enterprise',
            'htmlOptions'=>array('style'=>'width: 40px; text-align: -webkit-center;'),
            'header' => 'Наименование предприятия',
            'type' => 'raw',
            'value' => 'CHtml::hiddenField("comp[".CHtml::value($data, "markStudNrec")."][nrec]", CHtml::value($data, "markStudNrec"), array("class" => "field-comp-nrec")) .
            CHtml::textField("comp[".CHtml::value($data, "markStudNrec")."][name]", CHtml::value($data, "enterprise"), array("style" => "width: 150px;"
            ,"class" => "form-group form-control field-comp _table-ulist autocompletecomp"))',
            'visible' => (in_array(CHtml::value($info,"typeList"), array(uList::TYPE_PRACTICE))),
        ),
        'begin' => array(
            'name' => 'begin',
            'htmlOptions'=>array('style'=>'width: 40px; text-align: -webkit-center;'),
            'header' => 'Дата начала',
            'type' => 'raw',
            'value' => 'CHtml::textField("comp[".CHtml::value($data, "markStudNrec")."][datebegin]", CHtml::value($data, "begin")
            , array("style" => "width: 100px; text-align:center;", 
            "class" => "form-group form-control date-field field-datebegin _table-ulist datepicker"
            ,"readonly" => ((strlen(CHtml::value($data, "enterprise")))?false:true)
            ,"disabled" => ((strlen(CHtml::value($data, "enterprise")))?false:true)))',
            'visible' => (in_array(CHtml::value($info,"typeList"), array(uList::TYPE_PRACTICE))),
        ),
        'end' => array(
            'name' => 'end',
            'htmlOptions'=>array('style'=>'width: 40px; text-align: -webkit-center;'),
            'header' => 'Дата окончания',
            'type' => 'raw',
            'value' => 'CHtml::textField("comp[".CHtml::value($data, "markStudNrec")."][dateend]", CHtml::value($data, "end"), array("style" => "width: 100px; text-align:center;", 
            "class" => "form-group form-control date-field field-dateend _table-ulist datepicker"
            ,"readonly" => ((strlen(CHtml::value($data, "begin")))? false:true)
            ,"disabled" => ((strlen(CHtml::value($data, "begin")))? false:true)))',
            'visible' => (in_array(CHtml::value($info,"typeList"), array(uList::TYPE_PRACTICE))),
        ),
        'Rcw' => array(
            'name' => 'Rcw',
            'htmlOptions'=>array('style'=>'width: 40px; text-align: -webkit-center;'),
            'header' => 'Rкн',
            'type' => 'raw',
            'value' => 'CHtml::value($data, "rating")',
            'visible' => (in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_LADDER, uList::TYPE_EXAM))
                && CHtml::value($info, 'listFacult') != 'Заочного обучения'
                && CMisc::_id(CHtml::value($info, 'disciplineNrec')) != uDiscipline::DIS_GOS_EXAM),
        ),
        'Rsem' => array (
            'name' => 'Rsem',
            'htmlOptions'=>array('style'=>'width: 40px; text-align: -webkit-center;'),
            'header' => 'Rт',
            'type' => 'raw',
            'value' => 'ListClass::getValueRating($data,
            '.CHtml::value($info, 'status').',
            ' . var_export($marks, true) . ',
            "ratingsem",
            "RsemJS form-group form-control _table-ulist", 
            '.CHtml::value($info, 'typeList').',
            "'.CHtml::value($info, 'nreckurs').'",
            "'.CHtml::value($info, 'dopStatusList').'",
            ("'.CHtml::value($info, 'numDoc').'" != CHtml::value($data, "markListNumDoc"))
            )',
            'visible' => in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_EXAM, uList::TYPE_PRACTICE)) && CMisc::_id(CHtml::value($info, 'disciplineNrec')) != uDiscipline::DIS_GOS_EXAM
        ),
        'Ra' => array (
            'name' => 'Ra',
            'htmlOptions'=>array('style'=>'width: 40px; text-align: -webkit-center;'),
            'header' => 'Ra',
            'type' => 'raw',
            'value' => 'ListClass::getValueRating($data,
            '.CHtml::value($info, 'status').',
            ' . var_export($marks, true) . ',
            "ratingatt",
            "RaJS form-group form-control _table-ulist", 
            '.CHtml::value($info, 'typeList').',
            "'.CHtml::value($info, 'nreckurs').'",
            "'.CHtml::value($info, 'dopStatusList').'",
            ("'.CHtml::value($info, 'numDoc').'" != CHtml::value($data, "markListNumDoc"))
            )',
            'visible' => in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_EXAM, uList::TYPE_PRACTICE)) && CMisc::_id(CHtml::value($info, 'disciplineNrec')) != uDiscipline::DIS_GOS_EXAM
        ),
        'R' => array (
            'name' => 'R',
            'htmlOptions'=>array('style'=>'width: 40px; text-align: -webkit-center;', 'class' => 'Rtext'),
            'header' => 'R',
            'type' => 'raw',
            'value' => 'ListClass::getValueRating($data,
            '.CHtml::value($info, 'status').',
            ' . var_export($marks, true) . ',
            "ratingres",
            "RJS form-group form-control _table-ulist", 
            '.CHtml::value($info, 'typeList').',
            "'.CHtml::value($info, 'nreckurs').'",
            "'.CHtml::value($info, 'dopStatusList').'",
            ("'.CHtml::value($info, 'numDoc').'" != CHtml::value($data, "markListNumDoc"))
            )',
            'visible' => !in_array(CHtml::value($info, 'typeList'),
                    array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))
                && CMisc::_id(CHtml::value($info, 'disciplineNrec')) != uDiscipline::DIS_GOS_EXAM
        ),
        'mark' => array(
            'name' => 'mark',
            'htmlOptions'=>array('style'=>'width: 170px; text-align: -webkit-center;' , 'class' => 'markType'),
            'header' => 'Оценка',
            'type' => 'raw',
            'value' => 'ListClass::getMarkField($data,
            ' . var_export($marks, true) . ',
            '.CHtml::value($info, 'typeList').',
            "'.CHtml::value($info, 'disciplineNrec').'",
            "'.CHtml::value($info, 'nreckurs').'",
            "'.CHtml::value($info, 'dopStatusList').'",
            '.CHtml::value($info, 'status').',
            ("'.CHtml::value($info, 'numDoc').'" != CHtml::value($data, "markListNumDoc"))
            )',
        ),
        'lecturer' => array(
            'name' => 'lecturer',
            'htmlOptions'=>array('style'=>'text-align: -webkit-center;'),
            'header' => 'Ф.И.О. Преподавателя',
            'type' => 'raw',
            'value' =>'ListClass::getLecturerList($data, '.var_export(CHtml::value($info, 'listexaminer'), true).', 
            '.CHtml::value($info, 'status').', "'.CHtml::value($info, 'dopStatusList').'", "'.CMisc::_id($PN).'",
            ("'.CHtml::value($info, 'numDoc').'" != CHtml::value($data, "markListNumDoc")))',
            'visible' => !in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT)),
        ),
        'titleDip' => array(
            'name' => 'titleDip',
            'htmlOptions'=>array('style'=>'width: 250px; text-align: center;'),
            'header' => 'Тема работы',
            'value' => 'CHtml::value($data, "titleDip")',
            'visible' => in_array(CHtml::value($info,"typeList"), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT)),
        ),
        'rbExist' => array(
            'name' => 'rbExist',
            'htmlOptions'=>array('style'=>'text-align: center;'),
            'header' => 'Предъявлена зачетка',
            'type' => 'raw',
            'value' => 'ListClass::showCBrbExist(CHtml::value($data, "markStudNrec"), CHtml::value($data, "recordBookExist"), 
            '.CHtml::value($info, "status").', "'.CHtml::value($info, 'dopStatusList').'",
            ("'.CHtml::value($info, 'numDoc').'" != CHtml::value($data, "markListNumDoc")))',
            'visible' => !in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))
                && in_array(CHtml::value($info, 'status'), [0, 1]),
        ),
        array(
            'name' => 'filesDis',
            'htmlOptions' => array('style' => 'text-align: -webkit-center;'),
            'header' => 'Файлы работы',
            'type' => 'raw',
            'value' => 'ListClass::getTotalInfoOfFilesByDisAndSemester(CHtml::value($data, "studPersonNrec"),
            '.CHtml::value($info, 'numDoc').', \''.CHtml::value($info, "disciplineNrec").'\',
            '.CHtml::value($info, 'semester').', CHtml::value($data, "dbDipNrecNrec"),
            '.CHtml::value($info, 'typeList').' )',
            'visible' => (!in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))),
//            'visible' => false,
        ),
    )
));
echo CHtml::endForm();

$this->renderPartial('_tableStat');

if ((in_array(CHtml::value($info, 'status'), [0, 1]) && CHtml::value($info, 'dopStatusList') == 0)
/*&& ((CHtml::value($info, 'typeList') != uList::TYPE_PRACTICE ))*/
) {
    echo CHtml::link('Сохранить оценки', null, array('class' => 'saveButton btn btn-primary', 'onclick' => 'saveRaiting(false)', 'style' => 'margin-bottom:20px;'));

    echo "  " . CHtml::submitButton('Закрыть и сдать ведомость в деканат', array('class' => 'saveButton btn btn-primary', 'onclick' => 'closeRaiting()', 'name' => 'button', 'id' => 'closeDraft', 'style' => 'display: none; margin-bottom:20px;'));
} else {
    echo '<center><u>ведомость закрыта</u></center>';
    if (in_array(CHtml::value($info, 'status'), [0, 1])) {
        echo "<br/>" . CHtml::submitButton('Закрыть и сдать ведомость в деканат', array('class' => 'saveButton btn btn-primary', 'onclick' => 'closeRaiting()', 'name' => 'button', 'id' => 'closeDraft', 'style' => 'display: none; margin-bottom:20px;'));
    }
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

    function closeRaiting() {
        if(!confirm('Закрыть ведомость? После этого вы не сможете изменять рейтинг.')){
            return;
        }
        document.getElementById('AudHours_N_dop').value = 1;
        saveRaiting(true);
        $('input').attr('readonly','readonly');
        $('select').attr('disabled','disabled');
        $('.saveButton').css({display : 'none'});
    }

    function saveRaiting(close = false) {
        var url = '<?php
            if(in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))) {
                echo Yii::app()->createAbsoluteUrl('/studyProcess/mark/saveMarkDip');
            }else{
                echo Yii::app()->createAbsoluteUrl('/studyProcess/mark/saveMark');
            }
            /*elseif(CHtml::value($info, 'typeList') == 9){
                echo Yii::app()->createAbsoluteUrl('/studyProcess/mark/saveMark');
            }*/
            ?>';
        var ajax_form = 'ratingMarks';
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
                    notifySaveMark(responce.successFields, close);
                    notifyMessageMark('Сохранение успешно', 'Заполненный вами рейтинг и оценки сохранены.', 'success');
                }else{
                    notifySaveMark(responce.successFields, close);
                    notifyMessageMark('Ошибка при сохранении', 'Некоторые оценки могли не сохраниться, ошибочные поля подсвечены.', 'warning');
                }
            },
            'error' : function(responce) {
                disableLoading();
                select.prop("disabled", false);
                notifyMessageMark('Ошибка при сохранении', 'При передаче рейтинга на сервер произошла ошибка, попробуйте повторить отправку позже.', 'danger');
            }
        });
    }

    function notifySaveMark($data, $close) {
        $('.has-success').removeClass('has-success');
        $('.has-error').removeClass('has-error');
        for(var item in $data) {
            var res = document.getElementById('ratingres_'+item);
            var rsem = document.getElementById('ratingsem_'+item);
            var ratt = document.getElementById('ratingatt_'+item);
            var mark = document.getElementById('marksName_'+item);
            var markValue = document.getElementById('marks_'+item);
            var examiners = document.getElementById('examiners_'+item);
            if ($data[item]['code'] == 200) {
                if(res != null) {
                    res.parentElement.className += ' has-success';
                }
                if(rsem != null) {
                    rsem.parentElement.className += ' has-success';
                }
                if(ratt != null) {
                    ratt.parentElement.className += ' has-success';
                }
                if(mark != null) {
                    mark.parentElement.className += ' has-success';
                }
                if(markValue != null) {
                    markValue.parentElement.className += ' has-success';
                }
                if(examiners != null) {
                    examiners.parentElement.className += ' has-success';
                }
            }else{
                if(res != null) {
                    res.parentElement.className += ' has-error';
                }
                if(rsem != null) {
                    rsem.parentElement.className += ' has-error';
                }
                if(ratt != null) {
                    ratt.parentElement.className += ' has-error';
                }
                if(mark != null) {
                    mark.parentElement.className += ' has-error';
                }
                if(markValue != null) {
                    markValue.parentElement.className += ' has-error';
                }
                if(examiners != null) {
                    examiners.parentElement.className += ' has-error';
                }
            }
        }
    }

    function notifyMessageMark($title, $message, $color) {
        $.notify({
            title: "<center><strong><h4>" + $title + "</h4></strong></center>",
            message: "<center>" + $message + "</center>",
        }, {
            type: $color,
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
        $.fn.yiiGridView.update("tableMarks");
    });

    $(document).on('change', '#AudHours_N_dateDoc', function(e) {
       if (document.getElementById("saveAttestDate")) {
           document.getElementById("saveAttestDate").style.display = "unset";
       }
    });

    function saveAttDate() {
        var url = '<?php
            if(in_array(CHtml::value($info, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))) {
                echo Yii::app()->createAbsoluteUrl('/studyProcess/mark/saveMarkDip');
            }else{
                echo Yii::app()->createAbsoluteUrl('/studyProcess/mark/saveMark');
            }
            ?>';
        var select = $(this);
        var AudHours_N_dop = document.getElementById('AudHours_N_dop').value;
        var AudHours_N_dateDoc = document.getElementById('AudHours_N_dateDoc').value;
        var AudHours_N_nrecList = document.getElementById('AudHours_N_nrecList').value;

        enableLoading();
        select.prop("disabled", true);
        $.ajax({
            'url': url,
            'type': 'post',
            'dataType': 'json',
            'data': 'AudHours_N[dop]='+AudHours_N_dop+'&AudHours_N[dateDoc]='+AudHours_N_dateDoc+'&AudHours_N[nrecList]='+AudHours_N_nrecList,
            'success' : function(responce) {
                disableLoading();
                select.prop("disabled", false);
                if (responce.success) {
                    notifySaveMark(responce.successFields, close);
                    notifyMessageMark('Сохранение успешно', 'Дата аттестации успешно сохранена.', 'success');
                }else{
                    notifySaveMark(responce.successFields, close);
                    notifyMessageMark('Ошибка при сохранении', 'Произошла ошибка при сохранение даты, проверьте корректность введенных данных.', 'warning');
                }
            },
            'error' : function(responce) {
                disableLoading();
                select.prop("disabled", false);
                notifyMessageMark('Ошибка при сохранении', 'При передаче рейтинга на сервер произошла ошибка, попробуйте повторить отправку позже.', 'danger');
            }
        });
    }
    function validDate(date){ // date в формате 31.12.2014
        var valid = /^[0-3][0-9].[0|1][0-9].(19|20)[0-9]{2}/;
        if(!valid.test(date)){
            return false; // неккоректная дата
        }
        var d_arr = date.split('.');
        var d = new Date(d_arr[2]+'/'+d_arr[1]+'/'+d_arr[0]+''); // дата в формате 2014/12/31
        if (d_arr[2]!=d.getFullYear() || d_arr[1]!=(d.getMonth() + 1) || d_arr[0]!=d.getDate()) {
            return false; // неккоректная дата
        }
        return true;
    }

    function comparingDates(date1, date2){
        var d_arr1 = date1.split('.');
        var d_arr2 = date2.split('.');
        var d1 = new Date(d_arr1[2]+'/'+d_arr1[1]+'/'+d_arr1[0]+'');
        var d2 = new Date(d_arr2[2]+'/'+d_arr2[1]+'/'+d_arr2[0]+'');
        if (d1 >= d2) {
            return false;
        }
        return true;
    }

    $(document).on('change', '.field-dateend', function () {
        var value = $(this).parents("tr").find(".field-dateend").val();
        var valueold = $(this).parents("tr").find(".field-datebegin").val();
        if(validDate(value) && validDate(valueold)){
            if(comparingDates(value, valueold)){
                $(this).parents("tr").find(".field-dateend").val("");
            }
        }
    });

    $( ".autocompletecomp" ).autocomplete({
        source: "<?= Yii::app()->createAbsoluteUrl('studyProcess/mark/supervisorscomp'); ?>",
        delay: 500,
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            hidden.val(ui.item.value);
            hidden.data('label', ui.item.label);
            var value = $(this).parents("tr").find(".field-comp").val();
            if(value != ''){
                $(this).parents("tr").find(".field-datebegin").removeProp('readonly');
                $(this).parents("tr").find(".field-datebegin").removeProp('disabled');
            }else{
                $(this).parents("tr").find(".field-datebegin").prop('readonly', true);
                $(this).parents("tr").find(".field-datebegin").prop('disabled', true);
                $(this).parents("tr").find(".field-datebegin").val("");
                $(this).parents("tr").find(".field-dateend").prop('readonly', true);
                $(this).parents("tr").find(".field-dateend").prop('disabled', true);
                $(this).parents("tr").find(".field-dateend").val("");
            }
            return false;
        },
        change: function( event, ui ) {
            var hidden = $(this).parents("td:first").find("input[type=hidden]");
            $(this).val(hidden.val() ? hidden.data('label') : '');
        }
    });

    jQuery(function($) {
        $.datepicker.setDefaults($.datepicker.regional["ru"]);
        $(".datepicker").datepicker();

        $('.field-datebegin').datepicker("option", "onClose", function() {
            var value = $(this).val();
            $('.field-datebegin:not([readonly])').each(function(){
                if (!$(this).val().length) {
                    $(this).val(value);
                    if (validDate($(this).val())) {
                        $(this).parents("tr").find(".field-dateend").removeProp('readonly');
                        $(this).parents("tr").find(".field-dateend").removeProp('disabled');
                    }
                }
            });
            value = $(this).parents("tr").find(".field-datebegin").val();
            if(validDate(value)){
                $(this).parents("tr").find(".field-dateend").removeProp('readonly');
                $(this).parents("tr").find(".field-dateend").removeProp('disabled');
            }else{
                $(this).parents("tr").find(".field-dateend").prop('readonly', true);
                $(this).parents("tr").find(".field-dateend").prop('disabled', true);
                $(this).parents("tr").find(".field-dateend").val("");
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
    });

</script>

