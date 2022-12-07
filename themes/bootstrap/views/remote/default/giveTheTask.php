<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Выдать задание'
];

?>

<h1>Выдать задание</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>


