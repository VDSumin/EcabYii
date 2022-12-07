<?php
/* @var $this RcfgeGalEnterprisesController */
/* @var $modelKat Katorg */
/* @var $modelAdr Addressn */
/* @var $form CActiveForm */

$cs = Yii::app()->getClientScript();
/* @var $cs CClientScript */
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/jsTree/themes/default/style.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jsTree/jstree.js');
$data = CJSON::encode([
    'depurl' => Yii::app()->controller->createAbsoluteUrl('department', ['nrec' => Sterr::TYPE_ATD]),
    'deproot' => Sterr::TYPE_ATD,
]);
$cs->registerScript('jstree-data', "var jstreedata = {$data}", CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jstree.init.js');

?>


<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'user-department-form',
        'enableAjaxValidation'=>false,
    )); /* @var $form CActiveForm */?>

    <p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

    <?php echo $form->errorSummary($modelAdr); ?>

    <div class=<?php if($addressError3){echo '"form-group has-error"';}else{echo '"form-group"';} ?>>
        <?php echo $form->labelEx($modelKat, 'name', array('class' => 'control-label')); ?>
        <?php echo $form->textField($modelKat, 'name', array('class' => 'form-control', 'placeholder' => 'Полное наименование организации')); ?>
        <?php echo $form->error($modelKat, 'name'); ?>
    </div>

    <div class=<?php if($addressError2){echo '"form-group has-error"';}else{echo '"form-group"';} ?>>
        <?php echo $form->labelEx($modelKat, 'shortname', array('class' => 'control-label')); ?>
        <?php echo $form->textField($modelKat, 'shortname', array('class' => 'form-control', 'placeholder' => 'Сокращенное наименование организации')); ?>
        <?php echo $form->error($modelKat, 'shortname'); ?>
    </div>

    <div class="form-group">
        <?php echo $form->labelEx($modelKat, 'tel'); ?>
        <?php echo $form->textField($modelKat, 'tel', array('class' => 'form-control', 'placeholder' => 'Телефонный номер')); ?>
        <?php echo $form->error($modelKat, 'tel'); ?>
    </div>

    <div class="form-group">
        <?php echo $form->labelEx($modelKat, 'email'); ?>
        <?php echo $form->textField($modelKat, 'email', array('class' => 'form-control', 'placeholder' => 'Адрес электронной почты')); ?>
        <?php echo $form->error($modelKat, 'email'); ?>
    </div>

    <div class="form-group">
        <?php echo $form->labelEx($modelKat, 'sjuridicalid'); ?>
        <?php echo $form->textField($modelKat, 'sjuridicalid', array('class' => 'form-control', 'placeholder' => 'Введите дополнительную информацию о предприятии')); ?>
        <?php echo $form->error($modelKat, 'sjuridicalid'); ?>
    </div>

    <div class=<?php if($addressError1){echo '"form-group has-error"';}else{echo '"form-group"';} ?>>
        <?php echo $form->labelEx($modelKat, 'cjuridicaladdr', array('class' => 'control-label')); ?>
        <?= $form->hiddenField($modelAdr,'csterr', ['id' => 'department-val']); ?>
        <?php echo (($modelAdr->csterr == MyModel::_id('8000000000000000') or $modelAdr->csterr == '' or $modelAdr->csterr == '0x' or $modelAdr->csterr == null))?
            $form->textField($modelAdr, '', array('class' => 'autocomplete form-control ', 'name' => 'Katorg[sterrname]', 'placeholder' => 'Начните вводить название населенного пункта')):
            $form->textField($modelAdr->sterrTemp, 'fname', array('class' => 'autocomplete form-control ', 'name' => 'Katorg[sterrname]', 'placeholder' => 'Начните вводить название населенного пункта')); ?>
        <?php echo $form->error($modelAdr,'csterr'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($modelKat->isNewRecord() ? 'Добавить организацию' : 'Обновить организацию', array('class' => 'btn btn-primary')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<?php
Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
?>

<script>
    $(function () {
        $( ".autocomplete" ).autocomplete({
            source: "<?= Yii::app()->createAbsoluteUrl('rcfge/RcfgeGalEnterprises/fulllist'); ?>",
            select: function( event, ui ) {
                $(this).val(ui.item.label);
                var hidden = $(this).parents("div:first").find("input[type=hidden]");
                hidden.val(ui.item.value);
                hidden.data('label', ui.item.label);
                return false;
            },
            change: function( event, ui ) {
                var hidden = $(this).parents("div:first").find("input[type=hidden]");
                $(this).val(hidden.val() ? hidden.data('label') : '');
            },
            search  : function(){$(this).parents('form').addClass('loading');},
            response: function( event, ui ){$(this).parents('form').removeClass('loading');
                if(ui.content[0]){
                    $(this).css({background :'white', opacity: 1.0});}
                else{
                    $(this).css({background :'pink', opacity: 0.7, color: 'black'});
                }
            }
        }).on("blur", function() {
            if(!$(this).data("no-blur")) {
                $(this).css({background :'white', opacity: 1.0});
            }
        });
    });
</script>