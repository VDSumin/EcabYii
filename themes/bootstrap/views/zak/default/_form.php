<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:07
 */
/* @var $this DefaultController */
/* @var $model ZakZakaz */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'zakaz-form',
    'enableAjaxValidation'=>false,
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <hr/>
    <div class="row">
        <label>Ответственный</label>
        <h4><?= Fdata::model()->findByPk($model->fnpp)->getFIO(); ?></h4>
    </div>
    <hr/>

    <div class="row">
        <?php echo $form->labelEx($model,'oborud'); ?>
        <?php echo $form->dropDownList($model,'oborud',
            CHtml::listData(ZakOborud::model()->findAll(), 'npp', 'name'),
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'oborud'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'tovar'); ?>
        <?php echo $form->dropDownList($model,'tovar',
            CHtml::listData(ZakTovar::model()->findAll(), 'npp', 'name'),
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'tovar'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'kolvo'); ?>
        <?php echo $form->dropDownList($model,'kolvo',
            ['1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10'],
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'kolvo'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'struct'); ?>
        <?php echo $form->dropDownList($model,'struct',
            CHtml::listData(ZakModule::getMyFinStructList(), 'npp', 'name'),
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'struct'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'finsource'); ?>
        <?php echo $form->dropDownList($model,'finsource',
            [''=>'','1' => 'из средств подразделения', '2' => 'ЦФ в/б'],
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'finsource'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'auction'); ?>
        <?php echo $form->dropDownList($model,'auction',
            CHtml::listData(ZakAuction::model()->activeAuctionName(), 'npp', 'name'),
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'auction'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'dz'); ?>
        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'ZakZakaz[dz]',
            'options' => array(
                'showAnim' => 'fold',
                'dateFormat' => 'yy-mm-dd',
            ),
            'language' => 'ru',
            'htmlOptions' => array(
                'class' => 'form-control',
            ),
//            'value' => date('d.m.Y', strtotime(CHtml::value($model, "da")))
            'value' => CHtml::value($model, "dz")
        )); ?>
        <?php echo $form->error($model,'dz'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'invNumber'); ?>
        <?php echo $form->textField($model,'invNumber',array('size'=>60,'maxlength'=>100, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'invNumber'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'addres'); ?>
        <?php echo $form->textField($model,'addres',array('size'=>60,'maxlength'=>200, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'addres'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->