<?php
/* @var $this NewsController */
/* @var $model News */
/* @var $form CActiveForm */

$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($cs->getCoreScriptUrl() . '/jui/js/jquery-ui.min.js');
$cs->registerCssFile($cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css');
$cs->registerScriptFile($cs->getCoreScriptUrl() . '/jui/js/jquery-ui-i18n.min.js');
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'news-form',
        'enableAjaxValidation' => false,
    )); ?>

    <p class="note">Поля со <span class="required">*</span> обязательны к заполнению.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="form-group">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('class' => 'form-control')); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>
    <div class="form-group">
        <?php echo $form->labelEx($model, 'status'); ?>
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-info">
                <input type="radio" name="News[status]" id="option0" value="0" autocomplete="off"> Скрыто
            </label>
            <label class="btn btn-info active">
                <input type="radio" name="News[status]" id="option1" value="1" autocomplete="off" checked> Для всех
            </label>
            <label class="btn btn-info">
                <input type="radio" name="News[status]" id="option2" value="2" autocomplete="off"> ППС
            </label>
            <label class="btn btn-info">
                <input type="radio" name="News[status]" id="option3" value="3" autocomplete="off"> Студентам
            </label>
        </div>
        <?php echo $form->error($model, 'annonce'); ?>
    </div>

    <div class="form-group">
        <?php echo $form->labelEx($model, 'annonce'); ?>
        <?php $this->widget('ext.tinymce.TinyMce', array(
            'model' => $model,
            'attribute' => 'annonce',
            //'compressorRoute' => 'tinyMce/compressor',
            'spellcheckerUrl' => 'http://speller.yandex.net/services/tinyspell',
            'htmlOptions' => array(
                'rows' => 6,
                'cols' => 60,
            ),
        )); ?>
        <?php echo $form->error($model, 'annonce'); ?>
    </div>

    <div class="form-group">
        <?php echo $form->labelEx($model, 'content'); ?>
        <?php $this->widget('ext.tinymce.TinyMce', array(
            'model' => $model,
            'attribute' => 'content',
            //'compressorRoute' => 'tinyMce/compressor',
            'spellcheckerUrl' => 'http://speller.yandex.net/services/tinyspell',
            'htmlOptions' => array(
                'rows' => 6,
                'cols' => 60,
            ),
        )); ?>
        <?php echo $form->error($model, 'content'); ?>
    </div>

    <?php
    if(!$model->isNewRecord){
        echo "<label for=\"News_createdAt\">Дата публикации</label>";
        $this->widget('zii.widgets.jui.CJuiDatePicker',array(
            'name'=>'News[createdAt]',
            'value' => $model->createdAt,
            'language'=>'ru',
            'options'=>array(
                'dateFormat' => "yy-mm-dd  00:00:01",
                'showAnim'=>'slideDown',
                //'showAnim'=>'bounce',
                'firstDay' => 1,
            ),
            'htmlOptions'=>array(
                'class' => 'form-control',
            ),
        ));
    }
    ?>



    <div class="form-group buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class' => 'btn btn-primary')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->