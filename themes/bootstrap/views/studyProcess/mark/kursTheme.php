<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 14.04.2020
 * Time: 11:48
 */

$this->pageTitle = Yii::app()->name . ' - Темы курсовых работ';
$this->breadcrumbs = [
    'Ведомости'=>array('/studyProcess/mark'),
    'Закрепление тем ведомости '.CHtml::value($info, 'numDoc')
];
?>

<p class="text-header" style="text-align: center; font-size: 12pt;">
    ПРИЛОЖЕНИЕ К ВЕДОМОСТИ №<strong><ins><?php echo CHtml::value($info, 'numDoc'); ?></ins></strong><br/>
    <strong>ТЕМЫ КУРСОВЫХ РАБОТ (ПРОЕКТОВ)</strong><br/>
    ФАКУЛЬТЕТ (ИНСТИТУТ): <strong><ins><?php echo CHtml::value($info, 'listFacult'); ?></ins></strong> ,
    Группа <strong><ins><?php echo CHtml::value($info, 'studGroup'); ?></ins></strong><br/>
    ДИСЦИПЛИНА <ins><strong><?php echo CHtml::value($info, 'discipline'); ?></strong></ins><br/>
</p>


<?php echo CHtml::beginForm('', 'post', array("id" => "kursTheme", 'class' => 'form-inline', 'data-form-confirm' => "modal__confirm")); ?>

<?php echo CHtml::hiddenField('AudHours_N[nrecList]', CHtml::value($info, 'nrec')); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'group-grid',
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
            'value'=>  'CHtml::tag("span",[  "rel" => "tooltip",
            "data-toggle" => "tooltip", 
            "title" =>  "Дата последнего изменения: ".CMisc::fromGalDate(CHtml::value($data, "kursThemeLastEdit", 0)), 
            "style"=>"cursor: default"],
            CHtml::value($data, "fio"))'
        ),
        'theme' => array(
            'name' => 'theme',
            'htmlOptions'=>array('style'=>'width: 400px;'),
            'header' => 'Тема курсовой работы (проекта)',
            'type' => 'raw',
            'value' =>  'ListClass::getKursName($data,
            '.CHtml::value($info, 'status').',
            "'.CHtml::value($info, 'dopStatusList').'")',
        ),
        'lecturer' => array(
            'name' => 'lecturer',
            'htmlOptions'=>array('style'=>'text-align: -webkit-center;'),
            'header' => 'Ф.И.О. Преподавателя',
            'type' => 'raw',
            'value' =>'ListClass::getLecturerList($data, '.var_export(CHtml::value($info, 'listexaminer'), true).', 
            '.CHtml::value($info, 'status').', "'.CHtml::value($info, 'dopStatusList').'", "'.CMisc::_id($PN).'", false)',
        ),
    )
));

echo CHtml::endForm();

//if(in_array(CHtml::value($info, 'status'), [0,1]) && CHtml::value($info, 'dopStatusList') == 0){
    echo CHtml::link('Сохранить темы курсовых работ', null, array('class' => 'saveButton btn btn-primary', 'onclick' => 'saveTheme()', 'style' => 'margin-bottom:20px;'));
//}else{
//    echo '<center><u>ведомость закрыта</u></center>';
//}


echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>информация обрабатывается</small></div>
</div>';
?>

<script>
    function enableLoading() {
        var $preloader = $('#preloader');
        $preloader.removeClass('hidden');
    }
    function disableLoading() {
        var $preloader = $('#preloader');
        $preloader.addClass('hidden');
    }

    function saveTheme() {
        var url = '<?= Yii::app()->createAbsoluteUrl('/studyProcess/mark/saveKursTheme'); ?>';
        var ajax_form = 'kursTheme';
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
                    notifyMessageMark('Сохранение успешно', 'Заполненые темы успешно сохранены.', 'success');
                }else{
                    notifySaveMark(responce.successFields);
                    notifyMessageMark('Ощибка при сохранение', 'Темы были сохранены не полностью, проверьте заполненые поля.', 'warning');
                }
            },
            'error' : function(responce) {
                disableLoading();
                select.prop("disabled", false);
                notifyMessageMark('Ошибка при сохранение', 'При сохранение тем произошла ошибка, попробуйте повторить сохранение позже.', 'danger');
            }
        });
    }

    function notifySaveMark($data) {
        $('.has-success').removeClass('has-success');
        $('.has-error').removeClass('has-error');
        for(var item in $data) {
            var kursTheme = document.getElementById('kursTheme_'+item);
            var examiners = document.getElementById('examiners_'+item);
            if ($data[item]['code'] == 200) {
                if(kursTheme != null) {
                    kursTheme.parentElement.className += ' has-success';
                }
                if(examiners != null) {
                    examiners.parentElement.className += ' has-success';
                }
            }else{
                if(kursTheme != null) {
                    kursTheme.parentElement.className += ' has-error';
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

</script>


