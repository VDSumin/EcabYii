<?php
/* @var $this DefaultController */

Yii::app()->clientScript->registerScript('updateYearEdu', 'var updateYearEdu = "' . Yii::app()->createAbsoluteUrl('individualplan/default/updateYearEdu') . '";', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/individualplan.js', CClientScript::POS_END);


$this->pageTitle = Yii::app()->name . ' - Индивидуальный план';
$this->breadcrumbs = [
    'Индивидуальный план'
];

$this->menu = $menu;
?>

<h1>Индивидуальный план</h1>
В меню слева представлен список кафедр, на которых Вы работаете или работали. В поле ниже укажите учебный год, для которого
необходимо загрузить информацию.

<h3>Учебный год</h3>
<?= CHtml::dropDownList('yearEdu', $yearEdu, $yearList, ['class' => 'form-control yearEdu']); ?>

