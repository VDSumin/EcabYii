<?php
/* @var $this DefaultController */
/* @var $row Fdata  */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Группы факультета' => array('/remote/dean'),
    'Группа '.$group->name
];

?>

<h1>Контактная работа</h1>
Здесь отображаются все выданные задания для выбранной Вами группы.<br/>
<b>Группа <?= $group->name ?></b>

<div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Внимание!</strong> Выданные задания отсортированы по дате выдаче.
</div>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'tableMarks',
    'dataProvider' => $dataProvider,
    'filter' => $filter,
    'beforeAjaxUpdate' => 'js:function() { enableLoading(); } ',
    'afterAjaxUpdate' => 'js:function() { disableLoading(); } ',
    'columns'=>array(
        array(
            'header' => '№',
            'htmlOptions' => array('style' => 'width: 10px; text-align: center;'),
            'value' => '$row+1',
        ),
        'discipline' => array(
            'header' => 'Дисциплина',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'discipline',
        ),
        'comment' => array(
            'header' => 'Комментарий',
            'htmlOptions' => array('style' => 'width:35%; text-align: center;'),
            'name' => 'comment',
            'type' => 'raw',
            'value' => '\'<textarea class="form-control" rows="7" placeholder="Комментарий к работе отсутствует" 
            style="resize: vertical; background-color: white;" readonly>\'.CHtml::value($data, "comment").\'</textarea>\'',
        ),
        'files' => array(
            'header' => 'Файлы',
            'htmlOptions' => array('style' => 'width:20%; text-align: center;'),
            'name' => 'files',
            'type' => 'raw',
            'filter'=>false,
            'value' => 'RemoteModule::listfile(CHtml::value($data, "id"))',
        ),
        'teacher' => array(
            'header' => 'Преподаватель',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'teacher',
            'type' => 'raw',
        ),
        'create_date' => array(
            'header' => 'Дата выдачи',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'create_date',
        ),



    ),

));

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
</script>
