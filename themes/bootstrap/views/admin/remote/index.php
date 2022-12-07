<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 08.04.2020
 * Time: 19:11
 */
/* @var $this RemoteController */

$this->breadcrumbs = array(
    'Доступ к КН',
);

?>

<center><h2>Доступ к КН прошлых семестров студентам</h2></center>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'hostel-requests-table',
    'beforeAjaxUpdate' => 'js:function() { $("#hostel-requests-table").addClass("loading");}',
    'afterAjaxUpdate' => 'js:function() { 
            $("#hostel-requests-table").removeClass("loading");
            $(\'[rel="tooltip"]\').tooltip();
        }',
    'dataProvider' => $data,
    'filter' => $filter,
    'columns' => array(
        'id' => [
            'header' => 'id',
            'type' => 'raw',
            'name' => 'id',
            'filter' => false,
            'visible'=> false
        ],
        'fnpp' => array(
            'header' => 'fnpp',
            'type' => 'raw',
            'name' => 'fnpp',
            'htmlOptions' => array('style' => 'text-align:center; min-width: 100px; width: 150px'),
        ),
        'fio' => [
            'header' => 'ФИО',
            'type' => 'raw',
            'name' => 'fio',
        ],
        'group' => array(
            'header' => 'Группа',
            'type' => 'raw',
            'htmlOptions' => array('style' => 'text-align:center;'),
            'name' => 'group',
        ),
        'DateCreate' => array(
            'header' => 'Дата создания',
            'type' => 'raw',
            'htmlOptions' => array('style' => 'text-align:center;'),
            'name' => 'DateCreate',
            'filter' => false
        ),
        array(
            'class' => 'BButtonColumn',
            'htmlOptions' => array('style' => 'text-align:center'),
            'header' => '',
            'template' => '{add} {delete}',
            'buttons' => array(
                'add' => array(
                    'label' => '<span style="color:dodgerblue" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Добавить доступ" class="glyphicon glyphicon-plus"/>',
                    'url' => 'Yii::app()->createUrl("admin/remote/create", array("id" => CHtml::value($data, "fnpp")))',
                    'visible' => 'is_null(CHtml::value($data, "DateCreate"))',
                ),
                'delete' => array(
                    'label' => '<span style="color:red" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Убрать доступ" class="glyphicon glyphicon-remove"/>',
                    'url' => 'Yii::app()->createUrl("admin/remote/delete", array("id" => CHtml::value($data, "fnpp")))',
                    'visible' => '!is_null(CHtml::value($data, "DateCreate"))',
                ),
            ),
        ),
    ),
));
echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>Информация обрабатывается</small></div>
</div>';
?>

<hr/>