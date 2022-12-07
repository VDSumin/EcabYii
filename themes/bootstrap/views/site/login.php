<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */
?>



<div class="container">
<div class="form-signin">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
         
        <?php echo $form->textField($model,'username', array('class' => "form-control", 'type' => "email", 'id' => "inputEmail", 'placeholder' => "Логин")); ?>
        <?php echo $form->error($model,'username'); ?>
           
        <?php echo $form->passwordField($model,'password', array('class' => "form-control", 'type' => "password", 'id' => "inputPassword", 'placeholder'=>"Пароль" )); ?>
        <?php echo $form->error($model,'password'); ?>
   
    <div class="checkbox">
        <?php echo $form->checkBox($model,'rememberMe', array('id' => '')); ?>
        <?php echo $form->label($model,'rememberMe', array('id' => '')); ?>
        <?php echo $form->error($model,'rememberMe'); ?>
    </div>

    <div>
        <?php echo CHtml::submitButton('Вход', array('class' => 'btn btn-lg btn-primary btn-block')); ?>
    </div>

<?php $this->endWidget(); ?>
</div><!-- form -->
</div>