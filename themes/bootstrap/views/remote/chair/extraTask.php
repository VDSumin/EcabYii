<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 24.04.2020
 * Time: 23:23
 */

$this->pageTitle = Yii::app()->name . ' - Дополнительные задания';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Дополнительные задания'
];
Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
?>

<style>
    .ui-autocomplete { z-index:2147483647; }
</style>

<h1>Контактная работа</h1>
В данном модуле вы можете выдать доступ преподователю к выдаче заданий по дисциплине для группы.
<br /><br />
<?= CHtml::link('Добавить дополнительное задание', null, array('class' => 'btn btn-default addExtraTask')); ?>

<div class="alert alert-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Внимание!</strong> Выданные дополнительные задания преподователи смогут увидеть в ближайшее время.
</div>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'extraTaskTable',
    'dataProvider' => $dataProvider,
    'filter' => $filter,
    'beforeAjaxUpdate' => 'js:function() { enableLoading(); } ',
    'afterAjaxUpdate' => 'js:function() { disableLoading(); } ',
    'columns'=>array(
        array(
            'header' => '№',
            'htmlOptions' => array('style' => 'width: 10px; text-align: center;'),
            'type' => 'raw',
            'value' => '$row+1 . CHtml::hiddenField("taskId", CHtml::value($data, \'id\'), array("class" => "taskId"))',
        ),
        'discipline' => array(
            'header' => 'Дисциплина',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'discipline',
        ),
        'group' => array(
            'header' => 'Группа',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'group',
        ),
        'teacher' => array(
            'header' => 'Преподаватель',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'teacher',
        ),
        'create_date' => array(
            'header' => 'Дата добавления',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'create_date',
        ),
        array(
            'class' => 'BButtonColumn',
            'htmlOptions' => array('style' => 'text-align:center'),
            'header' => 'Действия',
            'template' => '{del}',
            'buttons' => array(
                'edit' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Изменить" class="glyphicon glyphicon-pencil editExtraTask"/>',
                    //'url' =>'Yii::app()->createUrl("/remote/dean/extraList", array("id" => CHtml::value($data, "id")))',
                ),
                'del' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Удалить" class="glyphicon glyphicon-trash deleteExtraTask"/>',
                    //'url' =>'Yii::app()->createUrl("/remote/dean/extraList", array("id" => CHtml::value($data, "id")))',
                ),
            )
        ),

    ),

));

echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>информация обрабатывается</small></div>
</div>';
?>

<div class="modal fade" id="modalExtraTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalExtraTaskLabel"></h4>
            </div>
            <div id="modalExtraTaskContentFromJs" class="modal-body">

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

    $(document).on('click', '.addExtraTask', function(e) {
        e.preventDefault();
        $('#modalExtraTask #modalExtraTaskLabel').html('Добавление записи');

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('remote/chair/addExtraTask'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'request=true',
            'success' : function(responce) {
                if (responce.success) {
                    $('#modalExtraTask #modalExtraTaskContentFromJs').html(responce.text);
                    $('#modalExtraTask').modal('toggle');
                } else {
                    $('#modalExtraTask #modalExtraTaskLabel').html('Ошибка');
                    $('#modalExtraTask #modalExtraTaskContentFromJs').html('Произошла ошибка');
                    $('#modalExtraTask').modal('toggle');
                }
            }
        });
    });

    $(document).on('click', '.editExtraTask', function(e) {
        e.preventDefault();
        $('#modalExtraTask #modalExtraTaskLabel').html('Изменение записи');
        var id = $(this).parent().parent().parent().find('.taskId').val();

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('remote/chair/editExtraTask'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'id=' + id,
            'success' : function(responce) {
                if (responce.success) {
                    $('#modalExtraTask #modalExtraTaskContentFromJs').html(responce.text);
                    $('#modalExtraTask').modal('toggle');
                }
            }
        });
    });

    $(document).on('click', '.deleteExtraTask', function(e) {
        e.preventDefault();
        if(confirm('Вы уверены что хотите удалить выбранную запись?')){
            var id = $(this).parent().parent().parent().find('.taskId').val();
            $.ajax({
                'url': "<?= Yii::app()->createAbsoluteUrl('remote/chair/deleteExtraTask'); ?>",
                'type': 'post',
                'dataType': 'json',
                'data': 'id=' + id,
                'success' : function(responce) {
                    if (responce.success) {
                        notifyMessageField('Успешно удалено', 'Запись успешно удалена!', 'success');
                        $.fn.yiiGridView.update('extraTaskTable');
                    }
                }
            });
        }
    });

    function notifyMessageField($title, $message, $color) {
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

    $(document).on('click', '.isaveExtraTask', function(e) {
        var group = document.getElementById('group').value;
        var discipline = document.getElementById('discipline').value;
        var teacher = document.getElementById('teacher').value;
        if(group == '' || discipline  == '' || teacher  == ''){
            alert('Выбраны не все пункты!');return;
        }
        $.ajax({
            'url' : "<?= Yii::app()->createAbsoluteUrl('remote/chair/saveExtraTask'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'group=' + group + '&discipline='+ discipline + '&teacher='+ teacher,
            'success' : function(responce) {
            if (responce.success) {
                $('#modalExtraTask').modal('toggle');
                $.fn.yiiGridView.update('extraTaskTable');
                notifyMessageField('Запись сохранена', 'Редактируемая запись успешно сохранена', 'success');
            }
        }
    });
    });

    $(document).on('click', '.usaveExtraTask', function(e) {
        var discipline = document.getElementById('discipline').value;
        var teacher = document.getElementById('teacher').value;
        if(discipline  == '' || teacher  == ''){
            alert('Выбраны не все пункты!');return;
        }
        $.ajax({
            'url' : "<?= Yii::app()->createAbsoluteUrl('remote/chair/saveExtraTask'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'group=' + ".$model->group." + '&discipline='+ discipline + '&teacher='+ teacher + '&id=' + ".$model->id.",
            'success' : function(responce) {
            if (responce.success) {
                $('#modalExtraTask').modal('toggle');
                $.fn.yiiGridView.update('extraTaskTable');
                notifyMessageField('Запись изменена', 'Редактируемая запись успешно сохранена', 'success');
            }
        }
    });

    });

</script>




