<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:07
 */
/* @var $this AccessController */
/* @var $model MonitorAccess */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'monitoring',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'fnpp'); ?>
        <?php echo $form->textField($model,'fnpp', array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'fnpp'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'struct'); ?>
        <?php echo $form->textField($model,'struct',array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'struct'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->