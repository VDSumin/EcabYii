<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:07
 */
/* @var $this ExpendableController */
/* @var $model ItdepExpendableRequest */
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
        <?php echo $form->labelEx($model,'creater'); ?>
        <h4><?= $model->creater; ?></h4>
    </div>
    <hr/>

    <?php
    if(count(ItdepExpendableAuction::model()->activeAuctionName()) == 0){
        echo '<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Внимание!</strong> Составить заявку не получится, так как отсутствуют открытые для составления заявки аукционы!
</div>';
    }
    ?>
    <div class="row">
        <?php echo $form->labelEx($model,'auction'); ?>
        <?php echo $form->dropDownList($model,'auction',
            CHtml::listData(ItdepExpendableAuction::model()->activeAuctionName(), 'npp', 'name'),
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'auction'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'struct'); ?>
        <?php echo $form->dropDownList($model,'struct',
            CHtml::listData(ZakModule::getMyFinStructList(), 'npp', 'name'),
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'struct'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'device'); ?>
        <?php echo $form->dropDownList($model,'device',
            array(''=>'')+CHtml::listData(ItdepExpendableDevice::model()->findAll(), 'id', 'name'),
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'device'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'invertNumber'); ?>
        <?php echo $form->textField($model,'invertNumber',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'invertNumber'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'typeCart'); ?>
        <?php echo $form->dropDownList($model,'typeCart',
            array(''=>'')+CHtml::listData(ItdepExpendableTypecart::model()->findAll(), 'id', 'name'),
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'typeCart'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'amount'); ?>
        <?php
        //echo $form->dropDownList($model,'amount', ['1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10'], array('class' => 'form-control'));
        echo $form->textField($model,'amount',array('size'=>60,'maxlength'=>10, 'class' => 'form-control'));
        ?>
        <?php echo $form->error($model,'amount'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'placement'); ?>
        <?php echo $form->textField($model,'placement',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'placement'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'responsible'); ?>
        <?php echo $form->textField($model,'responsible',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'responsible'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'phone'); ?>
        <?php echo $form->textField($model,'phone',array('size'=>60,'maxlength'=>255, 'class' => 'form-control'
        , 'placeholder' => 'Телефон')); ?>
        <?php echo $form->error($model,'phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255, 'class' => 'form-control'
        , 'placeholder' => 'Email')); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'comment'); ?>
        <?php echo $form->textArea($model,'comment',array('class' => 'form-control', 'rows' => 5)); ?>
        <?php echo $form->error($model,'comment'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->