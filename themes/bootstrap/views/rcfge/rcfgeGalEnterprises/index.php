<?php
/* @var $this RcfgeGalEnterprisesController */
?>

<h1>Список предприятий</h1>

<?php
if($success){echo '<div class="alert alert-success alert-dismissable fade in" style="text-align: center;">Новое предприятие было добавлено в реестр
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>';}
?>


<?= CHtml::link('Добавить предприятие', ['create'], ['class' => 'btn btn-success']); ?>
<?= CHtml::link('Произвести проверку совпадений', ['repeater'], ['class' => 'btn btn-info', "visible" => Yii::app()->user->checkAccess(array(WebUser::ROLE_ADMIN)) ]); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'dataProvider' => $dataProvider,
    'filter' => $filter,
    'columns'=>array(
        'nrec' => array(
            'name' => 'nrec',
            'visible' => false,
        ),
        array(
            'header' => 'Название',
            'name' => 'name',
            'type' => 'raw',
            'value' => 'CHtml::value($data, "name")',
        ),
        array(
            'header' => 'Местонахождение',
            'name' => 'addr',
            'type' => 'raw',
            'value' => 'CHtml::value($data, "addr")',
        ),
        array(
            'header' => 'Сокращенное название',
            'name' => 'short',
            'type' => 'raw',
            'value' => 'CHtml::value($data, "short")',
        ),
        array(
            'header' => 'Телефон',
            'name' => 'tel',
            'type' => 'raw',
            'value' => 'CHtml::value($data, "tel")',
        ),
        array(
            'header' => 'Эл. почта',
            'name' => 'mail',
            'type' => 'raw',
            'value' => 'CHtml::value($data, "mail")',
        ),
        array(
            'class'=>'BButtonColumn',
            'template' => '{update} {delete}',
            'buttons' => array(
                'update' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Редактировать" class="glyphicon glyphicon-pencil"/>',
                    'url' => 'Yii::app()->controller->createUrl("update", array("id" => CHtml::value($data, "nrec") ))',
                    'visible' => 'Yii::app()->user->checkAccess([
                        WebUser::ROLE_ADMIN,
                        WebUser::ROLE_RCFGE
                    ])'
                ),
                'delete' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Удалить" class="glyphicon glyphicon-trash"/>',
                    'url' => 'Yii::app()->controller->createUrl("delete", array("id" => CHtml::value($data, "nrec") ))',
                    'visible' => 'Yii::app()->user->checkAccess([
                        WebUser::ROLE_ADMIN,
                        WebUser::ROLE_RCFGE
                    ])'
                ),
            ),
        ),
    ),
));

?>
