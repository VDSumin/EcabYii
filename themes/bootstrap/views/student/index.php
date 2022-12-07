<h1>Выберите студента, <?= Yii::app()->user->name ?></h1>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'dataProvider' => $dataProvider,
    'filter' => $filter,
    'columns'=>array(
        'nrec' => array(
            'name' => 'nrec',
            'visible' => false,
        ),
        array(
            'header' => 'ФИО',
            'name' => 'fio',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::value($data, "fio"), array("/student/book", "id" => CHtml::value($data, "cpersons")))',
        ),
    ),
)); ?>
    