<?php
$this->pageTitle = Yii::app()->name . ' - Составление рабочих программ';

$baseUrl = Yii::app()->theme->getBaseUrl();
$css = Yii::app()->clientScript->registerCssFile($baseUrl . '/css/cases.css');
$js = Yii::app()->clientScript->registerScriptFile($baseUrl . '/js/cases.js');
?>

<h1>Шаблон рабочей программы по дисциплине</h1>
<h3><?= $disciplineName ?></h3>

<?php if (Yii::app()->user->hasFlash('success')): ?>

    <div class="alert alert-success alert-dismissible show" role="alert">
        <?php echo Yii::app()->user->getFlash('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

<?php elseif (Yii::app()->user->hasFlash('error')): ?>

    <div class="alert alert-danger alert-dismissible show" role="alert">
        <?php echo Yii::app()->user->getFlash('error'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<div class="form">

    <?= CHtml::hiddenField('chair', $chair) ?>
    <?= CHtml::hiddenField('discipline', $discipline) ?>

    <?php if ($specialities): ?>
        <div class="row">
            <?= CHtml::label('Выберите учебный план', 'curr'); ?>
            <?= CHtml::dropdownList("curr", '',
                CHtml::listData($specialities, "nrec", "codeName"),
                array(
                    "id" => false,
                    "size" => "1",
                    "class" => "form-control "
                )); ?>
        </div>
    <?php else: ?>
        <div class="alert alert-danger alert-dismissible show" role="alert">
            <?php echo 'Учебные планы не найдены'; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="row">
            <?= CHtml::label('Форма обучения', null); ?>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" id="eduForm1" value="">
                <?= CHtml::label('Очная', 'eduForm1'); ?>
            </div>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" id="eduForm2" value="">
                <?= CHtml::label('Заочная', 'eduForm2'); ?>
            </div>
            <div class="checkbox checkbox-inline">
                <input type="checkbox" id="eduForm3" value="">
                <?= CHtml::label('Вечерняя', 'eduForm3'); ?>
            </div>
        </div>
    <?php endif; ?>


    <!--<div class="row">
        <?= CHtml::label('Знать', 'form1'); ?>
        <?= CHtml::textField('form1', '', ['class' => 'form-control']); ?>
    </div>
    <div class="row">
        <?= CHtml::label('Уметь', 'form2'); ?>
        <?= CHtml::textField('form2', '', ['class' => 'form-control']); ?>
    </div>
    <div class="row">
        <?= CHtml::label('Владеть', 'form3'); ?>
        <?= CHtml::textField('form3', '', ['class' => 'form-control']); ?>
    </div>-->

    <button class="btn btn-success export" type="button">Экспорт в Word <span
                class="glyphicon glyphicon-download-alt"></span></button>
</div>
<div id="p_prldr" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>мы готовим ваш файл</small></div>
</div>