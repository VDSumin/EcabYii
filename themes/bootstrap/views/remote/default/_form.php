<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 16.03.2020
 * Time: 18:39
 */

?>

<div class="form">

    <?php $group = AttendanceGalruzGroup::model()->findByPk($model->group);
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'rcfge-enterprises-form',
        'enableAjaxValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    )); ?>


    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?= "<b>Дисциплина - </b>" . uDiscipline::model()->findByAttributes(array('nrec' => $model->discipline) )->name; ?>
    </div>

    <div class="row">
        <?= "<b>Группа - </b>" . $group->name; ?>
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


    <?php
    if (bin2hex($group->cfaculty) != '8000000000001687') {
        echo '<div class="row">
        <input type="button" class="add_file_input btn btn-success" value="Добавить файл">
    </div>';
    }else{
        echo '<div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
            </button>По служебной записке декана ИЗО для устранения противоречивой информации с СДО "Прометей" для групп ИЗО загрузка файлов не доступна            
                </div>';
    }
    ?>

<?php if($model->isNewRecord && RemoteModule::getAllGroup( $model->group, bin2hex($model->discipline), 'count') > 0): ?>

    <div class="row form-check">
        <label class="form-check-label" for="forAllGroup"> <input type="checkbox" class="form-check-input" id="forAllGroup" name="checkboxAll"> Добавить для всех групп расписания <?= RemoteModule::getAllGroup($model->group, bin2hex($model->discipline), 'list'); ?></label>
    </div>
    <div class="row form-check" id="hide">
        ИЛИ<br>
        <label class="form-check-label" for="forStreamLabel"> <input type="checkbox" class="form-check-input" id="forStream" name="checkboxStream"> Добавить для групп в потоке <?= RemoteModule::getStream($model->group, bin2hex($model->discipline), 'list'); ?></label>
    </div>
<?php elseif (RemoteModule::getAllGroupExtra($model->group, bin2hex($model->discipline), 'count') > 0): ?>
    <div class="row form-check">
        <label class="form-check-label" for="forAllGroup"> <input type="checkbox" class="form-check-input" id="forAllGroup" name="checkboxAll">
            <?php echo $model->isNewRecord ?  'Добавить':'Изменить';?>
            для всех групп <?= RemoteModule::getAllGroupExtra($model->group, bin2hex($model->discipline), 'list'); ?></label>
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

    $(document).ready(function () {
        $('#forAllGroup').change(function () {
            if (!this.checked)
                //  ^
                $('#hide').fadeIn('slow');
            else
                $('#hide').fadeOut('fast');
        });
    });


</script>



