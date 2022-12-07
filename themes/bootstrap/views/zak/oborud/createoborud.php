<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:08
 */
/* @var $this OborudController */
/* @var $model ItdepOborudProduct */

$this->pageTitle = 'Добавление отсутствуеющего типа оборудования';

$this->breadcrumbs=array(
    'Заявки на закупку оборудования'=>array('index'),
    'Создание нового типа оборудования',
);

?>

<h1>Создание заявки на закупку оборудования</h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'product-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'info'); ?>
        <?php echo $form->textField($model,'info',array('size'=>60,'maxlength'=>100, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'info'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->