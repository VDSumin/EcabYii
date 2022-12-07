<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:07
 */
/* @var $this ApiKeysController */
/* @var $model ApiKeys */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'api-keys-form',
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
        <?php echo $form->labelEx($model,'fio'); ?>
        <?php echo $form->textField($model,'fio',array('size'=>60,'maxlength'=>100, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'fio'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'glogin'); ?>

        <div class="input-group">
            <?php echo $form->textField($model,'glogin',array('size'=>60,'maxlength'=>100, 'class' => 'form-control')); ?>
            <span  class="input-group-btn">
                <?= CHtml::button('Получить ключ для Галактического логина', array('class' => 'ask_api_key btn btn-default')) ?>
            </span >
        </div>
        <?php echo $form->error($model,'glogin'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'apikey'); ?>
        <?php echo $form->textField($model,'apikey',array('size'=>60,'maxlength'=>100, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'apikey'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
    $(document).on('click', '.ask_api_key', function(e) {
        var fieldLogin = $(this).parent().parent().find('#ApiKeys_glogin');
        var filedKey = $(this).parent().parent().parent().parent().find('#ApiKeys_apikey');

        var $select = $(this);
        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('admin/apikeys/askApikey'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'login='+fieldLogin.val(),
            'success' : function(responce) {
                if (responce.success) {
                    filedKey.val(responce.text);
                    filedKey.parent().addClass('bg-success');
                    setTimeout(function(){
                        filedKey.parent().removeClass('bg-success');
                    }, 2000);
                } else {
                    filedKey.parent().addClass('bg-danger');
                    setTimeout(function(){
                        filedKey.parent().removeClass('bg-danger');
                    }, 2000);
                }
            }
        });
    });
</script>