<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:07
 */
/* @var $form CActiveForm */
?>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/chosen/chosen.jquery.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerCssFile(Yii::app()->theme->getBaseUrl(true) . '/js/chosen/chosen.min.css'); ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'oborud-product-form',
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


<script>
    $(function() {
        $('.js-cart-change').chosen({disable_search_threshold: 10});
    });
</script>
