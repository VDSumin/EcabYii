<?php
/* @var $this DefaultController */
/* @var $row Fdata  */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Группы факультета'
];

?>

<h1>Контактная работа</h1>
Здесь отображаются группы Вашего факультета, которые есть в расписание.

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
        'name' => array(
            'header' => 'Группа',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'name',
        ),
        'course' => array(
            'header' => 'Курс',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'course',
        ),
        'wformed' => array(
            'header' => 'ФО',
            'htmlOptions' => array('style' => 'text-align: center;'),
            'name' => 'wformed',
            'type' => 'raw',
            'filter' => CHtml::activeDropDownList($filter, 'wformed', array(
                0 => 'Очная',
                1 => 'Заочная',
                2 => 'Вечерняя'
            ), array(
                'id' => false,
                'prompt' => '',
                'class' => 'form-control'
            )),
            'value' => '(CHtml::value($data, "wformed") == "0" ? "Очная" :
            (CHtml::value($data, "wformed") == "1" ? "Заочная" :
            (CHtml::value($data, "wformed") == "2" ? "Вечерняя" : "")))',
        ),
        array(
            'class' => 'BButtonColumn',
            'htmlOptions' => array('style' => 'text-align:center'),
            'header' => 'Аттестация',
            'template' => '{list}',
            'buttons' => array(
                'list' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Просмотр" class="glyphicon glyphicon-search"/>',
                    'url' =>'Yii::app()->createUrl("/remote/dean/listGroupTask", array("id" => CHtml::value($data, "id")))',
                ),
            )
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
