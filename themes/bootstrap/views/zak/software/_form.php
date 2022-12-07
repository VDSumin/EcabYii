<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:07
 */
/* @var $this SoftwareController */
/* @var $model ItdepSoftwareRequest */
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
    if(count(ItdepSoftwareAuction::model()->activeAuctionName()) == 0){
        echo '<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Внимание!</strong> Составить заявку не получится, так как отсутствуют открытые для составления заявки аукционы!
</div>';
    }
    ?>
    <div class="row">
        <?php echo $form->labelEx($model,'auction'); ?>
        <?php echo $form->dropDownList($model,'auction',
            CHtml::listData( ItdepSoftwareAuction::model()->activeAuctionName(), 'npp', 'name'),
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
        <?php echo $form->labelEx($model,'softName'); ?>
        <?php echo $form->textField($model,'softName',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'softName'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'versionSW'); ?>
        <?php echo $form->textField($model,'versionSW',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'versionSW'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'editionSW'); ?>
        <?php echo $form->textField($model,'editionSW',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'editionSW'); ?>
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
        <?php echo $form->labelEx($model,'kindOfActivity'); ?>
        <?php echo $form->dropDownList($model,'kindOfActivity',
            [''=>'', 'Учебно-образовательская'=>'Учебно-образовательская', 'Научная'=>'Научная', 'Административная'=>'Административная', 'Хозяйственная'=>'Хозяйственная', 'Прочее'=>'Прочее'],
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'kindOfActivity'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'purpose'); ?>
        <?php echo $form->textField($model,'purpose',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'purpose'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'placement'); ?>
        <?php echo $form->textField($model,'placement',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'placement'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'finsource'); ?>
        <?php echo $form->dropDownList($model,'finsource',
            [''=>'', '1' => 'из средств подразделения', '2' => 'ЦФ в/б', '3' => 'гос. закупка'],
            array('class' => 'form-control')); ?>
        <?php echo $form->error($model,'finsource'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'responsible'); ?>
        <?php echo $form->textField($model,'responsible',array('size'=>60,'maxlength'=>255, 'class' => 'form-control')); ?>
        <?php echo $form->error($model,'responsible'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'contacts'); ?>
        <?php echo $form->textField($model,'contacts',array('size'=>60,'maxlength'=>255, 'class' => 'form-control'
        , 'placeholder' => 'Телефон, Email')); ?>
        <?php echo $form->error($model,'contacts'); ?>
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