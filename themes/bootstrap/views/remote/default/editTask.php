<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Список выданных работ' => array('/remote/default/taskList', 'group'=>$model->group, 'discipline'=>bin2hex($model->discipline)),
    'Исправить задание'
];

?>

<h1>Исправить задание</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>


