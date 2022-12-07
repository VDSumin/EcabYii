<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 08.04.2020
 * Time: 19:11
 */
/* @var $this AccessController */
/* @var $model MonitorAccess */

$this->pageTitle = 'Права доступа';

$this->breadcrumbs = array(
    'Права доступа',
);

?>

<center><h1>Модуль мониторинг</h1></center>

<?= CHtml::link('Добавить запись', array('create'), array('class' => 'btn btn-success')); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'apikeys-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'emptyText' => 'Список пуст',
    'beforeAjaxUpdate' => 'js:function() { enableLoading(); } ',
    'afterAjaxUpdate' => 'js:function() { disableLoading(); } ',
    'columns'=>array(
        'id',
        'fnpp',
        'fio' => array(
            'header' => 'ФИО',
            'type' => 'raw',
            'value' => 'Fdata::model()->findByPk($data["fnpp"])->getFIO()',
        ),
        'struct',
        'department' => array(
            'header' => 'Подразделение(название)',
            'type' => 'raw',
            'value' => 'StructD_rp::model()->findByPk($data["struct"])->name',
        ),
        'createdBy' => array(
            'header' => 'Кто выдал права',
            'type' => 'raw',
            'value' => 'Fdata::model()->findByPk($data["createdBy"])->getFIO()',
        ),
        'createDate',
        array(
            'class'=>'BButtonColumn',
            'template' => '{update} {delete}',
        ),
    )
));
echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>Информация обрабатывается</small></div>
</div>';
?>

<hr/>

<div class="form">
    <center><h2>Создать запись</h2></center>
    <?php
    $newmodel = new MonitorAccess;
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'monitoring',
        'action' => ['create'],
        'enableAjaxValidation'=>false,
    )); ?>



    <?php echo $form->errorSummary($newmodel); ?>

    <div class="row">
        <?php echo $form->labelEx($newmodel,'fnpp'); ?>
        <?php echo $form->textField($newmodel,'fnpp', array('class' => 'form-control')); ?>
        <?php echo $form->error($newmodel,'fnpp'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($newmodel,'struct'); ?>
        <?php echo $form->textField($newmodel,'struct',array('class' => 'form-control')); ?>
        <?php echo $form->error($newmodel,'struct'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($newmodel->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<hr/>


