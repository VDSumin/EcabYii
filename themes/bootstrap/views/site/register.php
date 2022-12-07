<?php
/* @var $this SiteController */
/* @var $model User */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Регистрация';
$this->breadcrumbs=array(
	'Регистрация',
);

Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'); 

Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/register.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('ppsUrl', 'var ppsUrl = "' . Yii::app()->createAbsoluteUrl('studyProcess/theme/supervisers') . '";', CClientScript::POS_END);

?>

<h1>Регистрация</h1>

<p>Пожалуйста, заполните поля</p>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'register-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

    <p class="note"><span class="required">*</span> Обязательные поля.</p>

    <div class="row">
        <?php echo $form->labelEx($model,'login'); ?>
        <?php echo $form->textField($model,'login'); ?>
        <?php echo $form->error($model,'login'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email'); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'firstName'); ?>
        <?php echo $form->textField($model,'firstName'); ?>
        <?php echo $form->error($model,'firstName'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'lastName'); ?>
        <?php echo $form->textField($model,'lastName'); ?>
        <?php echo $form->error($model,'lastName'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'role'); ?>
        <?php echo $form->dropDownList($model, 'role', array(), array('id' => 'js-roles')); ?>
        <?php echo $form->error($model,'role'); ?>
    </div>

    <?php /*
    <div id="js-additional<?= WebUser::ROLE_CHIEF ?>" class="row js-additional">
        <?php echo $form->labelEx($model,'chairNrec'); ?>
        <?php echo Chair::getDropDownList($model, 'chairNrec', array('prompt' => 'Выберите кафедру')); ?>
        <?php echo $form->error($model,'chairNrec'); ?>
    </div>
    */ ?>
    <div class="row">
        <?php echo $form->labelEx($model,'personNrec'); ?>
        <?php echo $form->hiddenField($model,'personNrec'); ?>
        <?php echo CHtml::textField('fio', '', array('class' => 'autocomplete')); ?>
        <?php echo $form->error($model,'personNrec'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'passw'); ?>
        <?php echo $form->passwordField($model,'passw'); ?>
        <?php echo $form->error($model,'passw'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'confirmPassword'); ?>
        <?php echo $form->passwordField($model,'confirmPassword'); ?>
        <?php echo $form->error($model,'confirmPassword'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Регистрация'); ?>
    </div>

<?php $this->endWidget(); ?>
</div><!-- form -->
