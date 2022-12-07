<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 08.04.2020
 * Time: 19:11
 */

$this->pageTitle = Yii::app()->name . ' - Каталог оборудования';

$this->breadcrumbs = array(
    'Заявки на закупку оборудования' => ['/zak/oborud'],
    'Каталог оборудования',
);

?>

<center><h1>Каталог оборудования</h1></center>

<?= CHtml::link('Добавить запись', array('create'), array('class' => 'btn btn-success')); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'zak-cart-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'emptyText' => 'Список пуст',
    'afterAjaxUpdate' => 'js:function() {
        $(".js-cart-change").chosen({disable_search_threshold: 10});
    }',
    'columns'=>array(
        'id',
        'name',
        'info',
        array(
            'class'=>'BButtonColumn',
            'template' => '{update} {delete}',
        ),
    )
));
echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>Информация обрабатывается</small></div>
</div>';
?>
