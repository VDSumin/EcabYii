<?php
$this->pageTitle = Yii::app()->name . ' - Составление рабочих программ';

$baseUrl = Yii::app()->theme->getBaseUrl();
$cs = Yii::app()->clientScript->registerCssFile($baseUrl . '/css/cases.css');
$js = Yii::app()->clientScript->registerScriptFile($baseUrl . '/js/cases.js');
?>
<link type="text/css">
<h1>Выберите дисциплину</h1>

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

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'discipline-grid',
    'dataProvider' => $dataProvider,
    'filter' => $filter,
    'columns' => array(
        'discipline' => array(
            'header' => 'Название дисциплины',
            'name' => 'discipline',
            'type' => 'raw',
            'value' => '$data["discipline"]'
        ),
        'chair' => array(
            'header' => 'Кафедра',
            'name' => 'chair',
            'type' => 'raw',
           // 'htmlOptions' => array("style" => "text-align: center"),
            'value' => '$data["chair"]',
            'filter' => CHtml::activeDropDownList($filter, 'chair', (new CaseForm)->GetChairsName((new WebUser)->getFnpp()),  array(
                'prompt' => 'Выберите кафедру',
                'class' => 'form-control'
            ))
        ),
//        'firstName',
//        'lastName',
//        'createdAt',
//        'lastVisitAt',
        array(
            'class' => 'BButtonColumn',
            'template' => '{create}',
//            'htmlOptions' => array('style' => 'white-space:nowrap'),

            'buttons' => array(
                'create' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Перейти к составлению" class="glyphicon glyphicon-pencil"/>',
                    'url' => 'Yii::app()->controller->createUrl("create", array("nrec" => $data["nrec"],"chair" => $data["cchair"]))',
                ),
            )
        ),
    ),
));
?>
