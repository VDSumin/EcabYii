<?php
/* @var $requests InquiriesRequests */

$this->pageTitle = Yii::app()->name . ' - Заявки';
$this->breadcrumbs = [
    'Заявки'
];
?>

<?php if (Yii::app()->user->hasFlash('warning')): ?>
    <div class="alert alert-warning alert-dismissible show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?php echo Yii::app()->user->getFlash('warning'); ?>
    </div>
<?php elseif (Yii::app()->user->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php
$types = InquiriesResponsibles::getResponsibleTypes();
if (!is_null(InquiriesResponsibles::getDeanFaculties()) || count($types) > 1 || InquiriesTypes::getTypeString($types[0]) != InquiriesTypes::HOSTEL) {
    echo '<h2>Полученные заявки</h2>';
    $this->widget('application.widgets.grid.BGridView', array(
        'id' => 'requests-table',
        'beforeAjaxUpdate' => 'js:function() { $("#requests-table").addClass("loading");}',
        'afterAjaxUpdate' => 'js:function() { 
        $("#requests-table").removeClass("loading");
        $(\'[rel="tooltip"]\').tooltip();
        $(\'#requests-table\').delegate(".add_file_input", "click", function () {
                $(\'#InquiriesRequests_id\').val($(this).attr(\'value\'));
                var input = $(\'.inputFile\').click();
            });
        }',
        'dataProvider' => $requests->searchResponsible(),
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
                'value' => '(Skard::model()->findByPk($data->groupNpp)
                    ? Skard::model()->findByPk($data->groupNpp)->gruppa 
                    : "Ак/отпуск, дата рождения - ".date("d.m.Y", strtotime(Fdata::model()->findByPk($data->studentNpp)->rogd)))',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ),
            'type' => [
                'header' => 'Тип заявки',
                'type' => 'raw',
                'value' => 'InquiriesTypes::getTypeString($data->typeId)',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ],
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
            'start' => [
                'header' => 'С',
                'type' => 'raw',
                'value' => 'InquiriesRequests::getDate($data->startDate,1,$data->typeId)',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ],
            'finish' => [
                'header' => 'По',
                'type' => 'raw',
                'value' => 'InquiriesRequests::getDate($data->finishDate,0,$data->typeId)',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ],
            'created' => [
                'header' => 'Дата подачи',
                'type' => 'raw',
                'value' => 'date("d.m.Y H:i:s",strtotime($data->createdAt))',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ],
            'upload' => [
                'header' => 'Действия',
                'type' => 'raw',
                'value' => 'InquiriesRequests::getUploadResponsible($data->id)',
                'htmlOptions' => array('style' => 'text-align:center;'),
                'filter' => false,
            ],
        ),
    ));
    echo '<form id="InquiriesRequestsForm" name="InquiriesRequests" method="post" class="form" enctype="multipart/form-data">';
    echo CHtml::hiddenField('InquiriesRequests[id]');
    echo CHtml::fileField('InquiriesRequests[filePath]', null, array('class' => 'inputFile', 'style' => 'display:none'));
    echo CHtml::submitButton('Загрузить', array('class' => 'btn btn-primary hidden btn-submit'));
    echo '</form><hr/>';
}

if (in_array(InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::HOSTEL))->id, $types)) {
    echo '<h2>Заявления об отмене начислений платы за проживание в период выезда из общежития</h2>';
    echo '<div id="preloader" class="hidden">
        <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>мы готовим файл</small></div>
    </div>';

    echo '<div class="form-inline">';

    echo ' <div class="form-group">
    <label for="InquiriesRequests_modifiedAt"> За </label> ';
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'model' => $requests,
        'attribute' => 'modifiedAt',
        'language' => 'ru',
        'options' => array(
            'dateFormat' => 'dd.mm.yy',
            'minDate' => InquiriesRequests::getMinDate(),
            'maxDate' => 'today',
        ),
        'htmlOptions' => array(
            'class' => 'form-control',
            'placeholder' => 'Дата подтверждения'
        ),
    ));
    echo '</div> <div class="form-group">
     <label for="InquiriesRequests_hostelNumber"> Общежитие </label> 
     <input type="text" id="InquiriesRequests_hostelNumber" name="hostelNumber" class="form-control" placeholder="Номер общежития">';
    echo '</div>';

    echo ' <div class="form-group"><button type="button" style="" class="export btn btn-success">Выгрузить заявки <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></button></div>';
    echo ' <div class="form-group"><button type="button" style="" class="export_all btn btn-warning">Выгрузить все заявки<span class="glyphicon glyphicon-download-alt"  aria-hidden="true"></span></button></div>';
    echo '</div><hr/>';
    /*$this->widget('application.widgets.grid.BGridView', array(
        'id' => 'hostel-requests-table',
        'beforeAjaxUpdate' => 'js:function() { $("#hostel-requests-table").addClass("loading");}',
        'afterAjaxUpdate' => 'js:function() { 
            $("#hostel-requests-table").removeClass("loading");
            $(\'[rel="tooltip"]\').tooltip();
        }',
        'dataProvider' => $requests->searchResponsibleHostel(),
        'filter' => $requests,
        'columns' => array(
            'studentNpp' => [
                'header' => 'Студент',
                'type' => 'raw',
                'value' => '(Fdata::model()->findByPk($data->studentNpp) ? Fdata::model()->findByPk($data->studentNpp)->getFIO() : \'?\')',
            ],
            'faculty' => array(
                'header' => 'Факультет',
                'type' => 'raw',
                'value' => '(Skard::model()->findByPk($data->groupNpp) ? Skard::model()->findByPk($data->groupNpp)->fak : \'-\')',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ),
            'groupNpp' => array(
                'header' => 'Группа',
                'type' => 'raw',
                'value' => '(Skard::model()->findByPk($data->groupNpp)
                    ? Skard::model()->findByPk($data->groupNpp)->gruppa 
                    : "Ак/отпуск, дата рождения - ".date("d.m.Y", strtotime(Fdata::model()->findByPk($data->studentNpp)->rogd)))',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ),
            'hostel' => array(
                'header' => 'Договор',
                'type' => 'raw',
                'value' => 'InquiriesRequests::getHostelContract($data->additional)',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ),
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
            'reason' => array(
                'header' => 'Причина',
                'type' => 'raw',
                'value' => 'InquiriesRequests::getHostelReason($data->additional)',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ),
            'takePickup' => [
                'header' => 'Способ получения',
                'type' => 'raw',
                'value' => 'InquiriesTypes::getTakesPickup(3)[$data->takePickUp]',
                'htmlOptions' => array('style' => 'text-align:center; width: 20%;'),
            ],
            'created' => array(
                'header' => 'Дата подачи',
                'type' => 'raw',
                'value' => 'date("d.m.Y H:i:s",strtotime($data->createdAt))',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ),
            'upload' => array(
                'header' => 'Действия',
                'type' => 'raw',
                'value' => 'InquiriesRequests::getUploadResponsibleHostel($data->id)',
                'htmlOptions' => array('style' => 'text-align:center;'),
                'filter' => false,
            ),
        ),
    ));
    echo '<hr/>';
    echo '<br/>';

    var_dump($filter);*/
    $this->widget('application.widgets.grid.BGridView', array(
        'id' => 'hostel-requests-table',
        'beforeAjaxUpdate' => 'js:function() { $("#hostel-requests-table").addClass("loading");}',
        'afterAjaxUpdate' => 'js:function() { 
            $("#hostel-requests-table").removeClass("loading");
            $(\'[rel="tooltip"]\').tooltip();
        }',
        'dataProvider' => $hostel,
        'filter' => $filter,
        'columns' => array(
            'id' => [
                'header' => 'id',
                'type' => 'raw',
                'name' => 'id'
            ],
            'fio' => [
                'header' => 'ФИО',
                'type' => 'raw',
                'name' => 'fio',
            ],
            'fak' => array(
                'header' => 'Факультет',
                'type' => 'raw',
                'htmlOptions' => array('style' => 'text-align:center; width:5%'),
                'name' => 'fak',
            ),
            'group' => array(
                'header' => 'Группа',
                'type' => 'raw',
                'htmlOptions' => array('style' => 'text-align:center;'),
                'name' => 'group',
            ),
            'cont_num' => array(
                'header' => 'Договор',
                'type' => 'raw',
                'htmlOptions' => array('style' => 'text-align:center;'),
                'name' => 'cont_num',
            ),
            'hostel' => array(
                'header' => 'Общ',
                'type' => 'raw',
                'htmlOptions' => array('style' => 'text-align:center;'),
                'name' => 'hostel',
                'filter' => CHtml::activeDropDownList($filter, 'hostel', array(
                    1 => '1',
                    9 => '3',
                    5 => '5',
                    6 => '6',
                    7 => '7',
                    50 => '50 лет'),  array(
                    'prompt' => 'Выберите сектор',
                    'class' => 'form-control'
                ))
            ),
                'startDate' => array(
                    'header' => 'С',
                    'type' => 'raw',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                    'name' => 'startDate',
                    'filter' => false
                ),
                'finishDate' => array(
                    'header' => 'По',
                    'type' => 'raw',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                    'name' => 'finishDate',
                    'filter' => false
                ),
                'reason' => array(
                    'header' => 'Причина',
                    'type' => 'raw',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                    'name' => 'reason',
                    'value' => 'InquiriesTypes::getHostelReasonString(CHtml::value($data, "reason"))',
                    'filter' => CHtml::activeDropDownList($filter, 'reason', array(
                        0 => 'для прохождения дистанционного обучения',
                        1 => 'для выезда на практику',
                        2 => 'в период каникул'
                    ),  array(
                        'prompt' => 'Выберите причину',
                        'class' => 'form-control'
                    ))
                ),
                'takePickup' => [
                    'header' => 'Способ получения',
                    'type' => 'raw',
                    'htmlOptions' => array('style' => 'text-align:center; width: 20%;'),
                    'name' => 'takePickup',
                    'filter' => false
                ],
                'created' => array(
                    'header' => 'Дата подачи',
                    'type' => 'raw',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                    'name' => 'created',
                    'filter' => false
                ),
                'upload' => array(
                    'header' => 'Действия',
                    'type' => 'raw',
                    'value' => 'InquiriesRequests::getUploadResponsibleHostel(CHtml::value($data, "id"))',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                    'filter' => false,
                ),
            ),
        ));
    ?>

    <style>
        .form-group {
            margin-bottom: 15px !important;
        }
    </style>

    <script type="text/javascript">
        $('a.dropdown-item-type').click(function () {
            $('#InquiriesRequests_type').val($(this).attr('value'));
            $('#dropdownMenuType').html($(this).text() + ' <span class="caret"></span>');
        });
        $('.export').click(function () {
            var $preloader = $('#preloader');
            $preloader.removeClass('hidden');
            var $date = $('#InquiriesRequests_modifiedAt').val();
            var $number = $('#InquiriesRequests_hostelNumber').val();
            const request = new XMLHttpRequest();
            const url = window.location.pathname + '?r=inquiries/responsible/getHostelExcel&date=' + $date + '&number=' + $number;
            request.open('GET', url);
            request.responseType = 'blob';
            request.contentType = 'application/vnd.ms-excel';
            request.onload = function () {
                $preloader.addClass('hidden');
                var blob = this.response;
                var contentDispo = this.getResponseHeader('Content-Disposition');
                var str = contentDispo.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1];
                var fileName = decodeURIComponent(str.split("").map(function (ch) {
                    return "%" + ch.charCodeAt(0).toString(16);
                }).join(""));
                SaveBlob(blob, fileName);
            };
            request.send();

            function SaveBlob(blob, fileName) {
                var a = document.createElement('a');
                a.href = window.URL.createObjectURL(blob);
                a.download = fileName;
                a.dispatchEvent(new MouseEvent('click'));
            }
        });

        $('.export_all').click(function () {
            var $preloader = $('#preloader');
            $preloader.removeClass('hidden');
            var $date = $('#InquiriesRequests_modifiedAt').val();
            var $number = $('#InquiriesRequests_hostelNumber').val();
            const request = new XMLHttpRequest();
            const url = window.location.pathname + '?r=inquiries/responsible/getHostelExcelAll&date=' + $date + '&number=' + $number;
            request.open('GET', url);
            request.responseType = 'blob';
            request.contentType = 'application/vnd.ms-excel';
            request.onload = function () {
                $preloader.addClass('hidden');
                var blob = this.response;
                var contentDispo = this.getResponseHeader('Content-Disposition');
                var str = contentDispo.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1];
                var fileName = decodeURIComponent(str.split("").map(function (ch) {
                    return "%" + ch.charCodeAt(0).toString(16);
                }).join(""));
                SaveBlob(blob, fileName);
            };
            request.send();

            function SaveBlob(blob, fileName) {
                var a = document.createElement('a');
                a.href = window.URL.createObjectURL(blob);
                a.download = fileName;
                a.dispatchEvent(new MouseEvent('click'));
            }
        });
    </script>
    <?php
}
?>

<h2>Архив</h2>
<?php
$this->widget('application.widgets.grid.BGridView', array(
    'id' => 'archive-table',
    'beforeAjaxUpdate' => 'js:function() { $("#archive-table").addClass("loading");}',
    'afterAjaxUpdate' => 'js:function() { 
    $("#archive-table").removeClass("loading");
    $(\'[rel="tooltip"]\').tooltip();}',
    'dataProvider' => $requests->searchResponsibleArchive(),
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
            'value' => '(Skard::model()->findByPk($data->groupNpp)
                 ? Skard::model()->findByPk($data->groupNpp)->gruppa 
                 : "Ак/отпуск, дата рождения - ".date("d.m.Y", strtotime(Fdata::model()->findByPk($data->studentNpp)->rogd)))',
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
            'value' => 'date("d.m.Y H:i:s",strtotime($data->createdAt))',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
        'upload' => array(
            'header' => 'Действия',
            'type' => 'raw',
            'value' => 'InquiriesRequests::getUploadStudent($data->filePath,$data->id)',
            'htmlOptions' => array('style' => 'text-align:center;'),
        ),
    ),
));
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
        $(document).on('click', 'a.glyphicon-remove', function () {
            $('#modal_confirm').attr('value', $(this).attr('value'));
            $('#modal_window').modal('toggle');
        });
        $('#modal_confirm').click(function () {
            location = window.location.pathname
                + '?r=/inquiries/responsible/decline&id=' + $(this).attr('value')
                + '&comment=' + $('#modal_window textarea').val();
        });
    });
</script>
