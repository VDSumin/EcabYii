<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:07
 */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'oborud-product-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'date'); ?>
        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'ItdepOborudAuction[date]',
            'options' => array(
                'showAnim' => 'fold',
                'dateFormat' => 'yy-mm-dd',
            ),
            'language' => 'ru',
            'htmlOptions' => array(
                'class' => 'form-control',
            ),
//            'value' => date('d.m.Y', strtotime(CHtml::value($model, "da")))
            'value' => CHtml::value($model, "date")
        )); ?>
        <?php echo $form->error($model,'dz'); ?>
    </div>

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

    <div class="row">
        <?php echo $form->labelEx($model,'status'); ?>
        <?php
        $arrayState = ['0' => 'Черновик', '1' =>  'В работе', '2' => 'Завершено'];
        echo $form->dropDownList($model,'status',
            $arrayState,
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'status'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

