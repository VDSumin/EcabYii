<?php

$this->pageTitle = Yii::app()->name . ' - Заявки';
$this->breadcrumbs = [
    'Заявки'
];
?>

    <h2>Типы заявок</h2>
<?php
$this->widget('application.widgets.grid.BGridView', array(
    'id' => 'types-table',
    'beforeAjaxUpdate' => 'js:function() { $("#types-table").addClass("loading");}',
    'afterAjaxUpdate' => 'js:function() { $("#types-table").removeClass("loading");
    $(\'[rel="tooltip"]\').tooltip();}',
    'dataProvider' => $types->search(),
    'filter' => $types,
    'columns' => array(
        'id' => array(
            'header' => 'ID',
            'type' => 'raw',
            'value' => '$data->id',
            'htmlOptions' => array('style' => 'text-align:center;width:1%'),
        ),
        'name' => array(
            'header' => 'Тип заявки',
            'type' => 'raw',
            'value' => '$data->name',
        ),
        'createdAt' => array(
            'header' => 'Дата создания',
            'type' => 'raw',
            'value' => '$data->createdAt',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
//        'modifiedAt' => [
//            'name' => 'modifiedAt',
//            'type' => 'raw',
//            'value' => '$data->modifiedAt',
//        ],
        array(
            'header' => 'Действия',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::tag("span", [
                "class" => "glyphicon glyphicon-remove",
                "rel" => "tooltip",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                "title" => "Удалить",
            ]), array("deleteType", "id" => $data->id))',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
    ),
));
?>
    <h2>Создать новый тип заявок</h2>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'types-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($types, 'name'); ?>
        <?php echo $form->textField($types, 'name', array('class' => 'form-control')); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Создать', array('class' => 'btn btn-primary')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->

<hr/>
    <h2>Ответственные</h2>
<?php
$this->widget('application.widgets.grid.BGridView', array(
    'id' => 'responsibles-table',
    'beforeAjaxUpdate' => 'js:function() { $("#requests-table").addClass("loading");}',
    'afterAjaxUpdate' => 'js:function() { 
        $("#requests-table").removeClass("loading");
        $(\'[rel="tooltip"]\').tooltip();
        $(\'#requests-table\').delegate(".add_file_input", "click", function () {
                $(\'#InquiriesRequests_id\').val($(this).attr(\'value\'));
                var input = $(\'.inputFile\').click();
            });
        }',
    'dataProvider' => $responsibles->search(),
    'filter' => $responsibles,
    'columns' => array(
        'id' => array(
            'header' => 'ID',
            'type' => 'raw',
            'value' => '$data->id',
            'htmlOptions' => array('style' => 'text-align:center;width:1%'),
        ),
        'typeId' => array(
            'header' => 'Тип заявки',
            'type' => 'raw',
            'value' => 'InquiriesTypes::getTypeString($data->typeId)',
        ),
        'responsibleNpp' => array(
            'header' => 'Ответственный',
            'type' => 'raw',
            'value' => '(Fdata::model()->findByPk($data->responsibleNpp) ? Fdata::model()->findByPk($data->responsibleNpp)->getFIO() : \'?\')',
        ),
        'actions' => array(
            'header' => 'Действия',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::tag("span", [
                "class" => "glyphicon glyphicon-remove",
                "rel" => "tooltip",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                "title" => "Удалить",
            ]), array("deleteResponsible", "id" => $data->id))',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
    ),
));
?>
    <h2>Прикрепить нового ответственного</h2>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'responsibles-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($responsibles, 'typeId'); ?>
        <?php echo $form->textField($responsibles, 'typeId', array('class' => 'form-control', 'placeholder' => 'id')); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($responsibles, 'responsibleNpp'); ?>
        <?php echo $form->textField($responsibles, 'responsibleNpp', array('class' => 'form-control', 'placeholder' => 'fnpp')); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Прикрепить', array('class' => 'btn btn-primary')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->


    <hr/>
    <h2>Все заявки</h2>
<?php
$this->widget('application.widgets.grid.BGridView', array(
    'id' => 'requests-table',
    'beforeAjaxUpdate' => 'js:function() { $("#requests-table").addClass("loading");}',
    'afterAjaxUpdate' => 'js:function() { $("#requests-table").removeClass("loading");
    $(\'[rel="tooltip"]\').tooltip();}',
    'dataProvider' => $requests->search(),
    'filter' => $requests,
    'columns' => array(
        'studentNpp' => array(
            'header' => 'Студент',
            'type' => 'raw',
            'value' => '(Fdata::model()->findByPk($data->studentNpp) ? Fdata::model()->findByPk($data->studentNpp)->getFIO() : \'?\')',
        ),
        'groupNpp' => array(
            'header' => 'Группа',
            'type' => 'raw',
            'value' => '(Skard::model()->findByPk($data->groupNpp) ? Skard::model()->findByPk($data->groupNpp)->gruppa : $data->groupNpp)',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
        'type' => array(
            'header' => 'Тип заявки',
            'type' => 'raw',
            'value' => 'InquiriesTypes::getTypeString($data->typeId)',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
        'additional' => [
            'header' => 'Дополнительно',
            'type' => 'raw',
            'value' => '$data->additional',
            'htmlOptions' => array('style' => 'text-align:center; width: 20%;'),
        ],
        'takePickup' => [
            'header' => 'Способ получения',
            'type' => 'raw',
            'value' => 'InquiriesTypes::getTakesPickup(3)[$data->takePickUp]',
            'htmlOptions' => array('style' => 'text-align:center; width: 20%;'),
        ],
        'start' => array(
            'header' => 'С',
            'type' => 'raw',
            'value' => 'InquiriesRequests::getDate($data->startDate,1,$data->typeId)',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
        'finish' => array(
            'header' => 'По',
            'type' => 'raw',
            'value' => 'InquiriesRequests::getDate($data->finishDate,0,$data->typeId)',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
        'created' => array(
            'header' => 'Дата подачи',
            'type' => 'raw',
            'value' => '$data->createdAt',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
        'upload' => array(
            'header' => 'Действия',
            'type' => 'raw',
            'value' => 'InquiriesRequests::getUploadAdmin($data->filePath,$data->id)',
        ),
    ),
));
echo '<form id="InquiriesRequestsForm" name="InquiriesRequests" method="post" class="form" enctype="multipart/form-data">';
echo CHtml::hiddenField('InquiriesRequests[id]');
echo CHtml::fileField('InquiriesRequests[filePath]', null, array('class' => 'inputFile', 'style' => 'display:none'));
echo CHtml::submitButton('Загрузить', array('class' => 'btn btn-primary hidden btn-submit'));
echo '</form><hr/>';
?>

<div class="modal fade" id="modal_window" tabindex="-1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal_label">Комментарий</h4>
            </div>
            <div class="modal-body">
                <?php
                echo '<p>Укажите причину отказа</p>';
                echo '<div class="form-group"><textarea class="form-control"></textarea></div>';
                echo '<button id="modal_confirm" type="button" class="btn btn-primary">Подтвердить</button>';
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('[rel="tooltip"]').tooltip();
        $('#requests-table').delegate(".add_file_input", "click", function () {
            $('#InquiriesRequests_id').val($(this).attr('value'));
            $('.inputFile').click();
        });
        $('.inputFile').change(function () {
            var reader = new FileReader();
            reader.onload = function (image) {
            };
            reader.readAsDataURL(this.files[0]);
            $('.btn-submit').click();
        });
        $('a.glyphicon-remove').click(function () {
            $('#modal_confirm').attr('value', $(this).attr('value'));
            $('#modal_window').modal('toggle');
        });
        $('#modal_confirm').click(function () {
            location = window.location.pathname
                + '?r=/inquiries/admin/decline&id=' + $(this).attr('value')
                + '&comment=' + $('#modal_window textarea').val();
        });
    });
</script>
