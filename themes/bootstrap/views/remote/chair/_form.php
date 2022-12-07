<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 16.03.2020
 * Time: 18:39
 */

?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'rcfge-enterprises-form',
        'enableAjaxValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    )); ?>


    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?= "<b>Дисциплина - </b>" . uDiscipline::model()->findByAttributes(array('nrec' => $model->discipline) )->name; ?>
    </div>

    <div class="row">
        <?= "<b>Группа - </b>" . AttendanceGalruzGroup::model()->findByPk($model->group)->name; ?>
    </div>

    <div class="row">
        <?= $form->labelEx($model,'comment'); ?>
        <?= $form->textArea($model,'comment',array( 'class' => 'form-control', 'rows' => '7', 'placeholder' => 'Комментарий к работе', 'style' => 'resize: vertical;')); ?>
        <?= $form->error($model,'comment'); ?>
    </div>

    <div class="row">
    <?php if(!$model->isNewRecord): ?>

        <?php echo RemoteModule::listfile($model->id, 'list'); ?>

    <?php endif;?>
    </div>

    <div id="rows_file">
    </div>


    <div class="row">
        <input type="button" class="add_file_input btn btn-success" value="Добавить файл">
    </div>

<?php if($model->isNewRecord && RemoteModule::getAllGroup( $model->group, bin2hex($model->discipline), 'count') > 0): ?>

    <div class="row form-check">
        <label class="form-check-label" for="forAllGroup"> <input type="checkbox" class="form-check-input" id="forAllGroup" name="checkboxAll"> Добавить для групп <?= RemoteModule::getAllGroup($model->group, bin2hex($model->discipline), 'list'); ?></label>
    </div>

<?php endif;?>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array(
            'class' => 'btn btn-primary',
            'style' => 'margin-right: 10px; vertical-align: top',
        )); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>

<script>
    $(document).on('click', '.add_file_input', function() {
        var list = document.getElementById('rows_file');
        var div = document.createElement('div');
        var count = document.getElementsByClassName('file').length;
        div.className = "row";
        div.innerHTML = '<label style="margin-top:10px" for="RemoteTaskList">Файл (до 10 Мб)</label> <input class="file" type="file" value="" name="RemoteFiles[' + count + ']" id="RemoteTaskList">';

        list.appendChild(div);
    });
</script>



