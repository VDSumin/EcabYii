<?php
/* @var $this NewsController */
/* @var $model News */

$this->breadcrumbs = array(
    'Новости',
);

?>

<h1>Управление новостями</h1>

<?= CHtml::link('Добавить новость', array('news/create'), array('class' => 'btn btn-default')); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'news-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'id',
        'title',
        'status' => array(
            'name' => 'status',
            'type' => 'raw',
            'value' => 'News::GetStatus($data->status)'
        ),
        'createdAt',
        array(
            'class' => 'BButtonColumn',
        ),
    ),
)); ?>
