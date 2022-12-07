<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Список кафедры' => array('/remote/chair'),
    Fdata::model()->findByPk($person)->getFIO() => array('/remote/chair/PersonList', 'id' => $person),
    'Список выданных работ' => array('/remote/chair/taskList', 'fnpp'=>$person, 'group'=>$model->group, 'discipline'=>bin2hex($model->discipline)),
    'Исправить задание'
];

?>

<h1>Исправить задание</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>


