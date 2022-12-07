<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Мои заявки';
$this->breadcrumbs = [
    'Мои заявки'
];
?>

<?php if (Yii::app()->user->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?php echo Yii::app()->user->getFlash('error'); ?>
    </div>
<?php elseif (Yii::app()->user->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>

<h2>Новая заявка</h2>
<?php
$request = new InquiriesRequests();
$request->unsetAttributes();
echo CHtml::beginForm(array('add'), 'post', array('class' => 'form-inline'));
$groups = InquiriesRequests::getGroups();
if (count($groups) > 1) {
    echo CHtml::hiddenField('InquiriesRequests[groupNpp]');
    echo '<div class="dropdown form-group">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuGroup" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    Выберите группу
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuGroup">';
    foreach ($groups as $group) {
        echo '<li><a class="dropdown-item-group" value="' . $group['npp'] . '">' . $group['gruppa'] . '</a></li>';
    }
    echo '</ul>
</div>';
} else {
    echo CHtml::hiddenField('InquiriesRequests[groupNpp]', $groups[0]['npp']);
}

echo CHtml::hiddenField('InquiriesRequests[typeId]');
echo ' <div class="dropdown form-group">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    Выберите тип заявки
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenu">';
foreach (InquiriesTypes::getTypes() as $type) {
    if (
        $type['name'] != InquiriesTypes::HOSTEL
        || HostelContract::model()->findByAttributes(array('fnpp' => Yii::app()->user->getFnpp(), 'status' => 1))
    )
        echo '<li><a class="dropdown-item" value="' . $type['id'] . '">' . $type['name'] . '</a></li>';
}
echo '</ul>
</div>';


echo ' <div class="hidden form-group">
    <label for="InquiriesRequests_startMonth"> C </label> ';
echo CHtml::hiddenField('InquiriesRequests[startMonth]', (int)date('m', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
echo ' <div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuStartMonth" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    ' . InquiriesRequests::getMonthes(1)[(int)date('m', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")))] . '
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuStartMonth">';
foreach (InquiriesRequests::getMonthes(1) as $month => $i) {
    echo '<li><a class="dropdown-item-startMonth" value="' . $month . '">' . $i . '</a></li>';
}
echo '</ul></div>';
echo CHtml::hiddenField('InquiriesRequests[startYear]', date('Y', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
echo ' <div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuStartYear" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    ' . date('Y', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))) . '
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuStartYear">';
for ($i = (int)date('Y'); $i > 2017; $i--) {
    echo '<li><a class="dropdown-item-startYear" value="' . $i . '">' . $i . '</a></li>';
}
echo '</ul></div>';
echo '</div>';


echo ' <div class="hidden form-group">
    <label for="InquiriesRequests_finishMonth"> По </label> ';
echo CHtml::hiddenField('InquiriesRequests[finishMonth]', (int)date('m', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
echo ' <div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuFinishMonth" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    ' . InquiriesRequests::getMonthes(0)[(int)date('m', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")))] . '
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuFinishMonth">';
foreach (InquiriesRequests::getMonthes(0) as $month => $i) {
    echo '<li><a class="dropdown-item-finishMonth" value="' . $month . '">' . $i . '</a></li>';
}
echo '</ul></div>';
echo CHtml::hiddenField('InquiriesRequests[finishYear]', date('Y', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
echo ' <div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuFinishYear" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    ' . date('Y', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))) . '
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuFinishYear">';
for ($i = (int)date('Y'); $i > 2017; $i--) {
    echo '<li><a class="dropdown-item-finishYear" value="' . $i . '">' . $i . '</a></li>';
}
echo '</ul></div>';
echo '</div>';

echo '<div class="form-group">
 <input type="text" class="form-control hidden" id="InquiriesRequests_place" placeholder="Место требования" value="" name="InquiriesRequests[place]">
 </div>';

echo ' <div class="hidden form-group">
    <label for="InquiriesRequests_startDate"> C </label> ';
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'model' => $request,
    'attribute' => 'startDate',
    'language' => 'ru',
    'options' => array(
        'dateFormat' => 'dd.mm.yy',
    ),
    'htmlOptions' => array(
        'class' => 'form-control',
        'placeholder' => 'Начало периода'
    ),
));
echo '</div> <div class="hidden form-group">
     <label for="InquiriesRequests_finishDate"> По </label> ';
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'model' => $request,
    'attribute' => 'finishDate',
    'language' => 'ru',
    'options' => array(
        'dateFormat' => 'dd.mm.yy',
    ),
    'htmlOptions' => array(
        'class' => 'form-control',
        'placeholder' => 'Конец периода'
    ),
));
echo '</div>';
echo CHtml::hiddenField('InquiriesRequests[reason]');
echo ' <div class="hidden dropdown form-group">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuReason" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    Выберите причину
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuReason">';
foreach (InquiriesTypes::getHostelReasons() as $i => $hostelReason) {
    echo '<li><a class="dropdown-item-reason" value="' . $i . '">' . $hostelReason . '</a></li>';
}
echo '</ul></div>';

echo CHtml::hiddenField('InquiriesRequests[takePickup]');

echo ' <div class="hidden dropdown form-group">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuTakepickup1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    Выберите способ получения
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuTakepickup">';
foreach (InquiriesTypes::getTakesPickup(1) as $i => $type) {
        echo '<li><a class="dropdown-item-takepickup1" value="' . $i . '">' . $type . '</a></li>';
}
echo '</ul>
</div>';

echo ' <div class="hidden dropdown form-group">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuTakepickup2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    Выберите способ получения
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuTakepickup">';
foreach (InquiriesTypes::getTakesPickup(2) as $i => $type) {
    echo '<li><a class="dropdown-item-takepickup2" value="' . $i . '">' . $type . '</a></li>';
}
echo '</ul>
</div>';


echo ' <button type="submit" class="btn btn-success">Подать заявку</button>';

echo CHtml::endForm();

?>

<?php if ($showTable) { ?>
    <h2>Поданные заявки</h2>
    <?php
    $this->widget('application.widgets.grid.BGridView', array(
        'id' => 'requests-table',
        'beforeAjaxUpdate' => 'js:function() { $("#requests-table").addClass("loading");}',
        'afterAjaxUpdate' => 'js:function() { $("#requests-table").removeClass("loading");
        $(\'[rel="tooltip"]\').tooltip();}',
        'dataProvider' => $requests->searchStudent(),
        'filter' => $requests,
        'columns' => array(
            'type' => array(
                'header' => 'Тип заявки',
                'type' => 'raw',
                'value' => 'InquiriesTypes::getTypeString($data->typeId)',
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
            'created' => array(
                'header' => 'Дата подачи',
                'type' => 'raw',
                'value' => 'date("d.m.Y H:i:s",strtotime($data->createdAt))',
                'htmlOptions' => array('style' => 'text-align:center;'),
            ),
            'status' => array(
                'header' => 'Действия',
                'type' => 'raw',
                'value' => 'InquiriesRequests::getUploadStudent($data->filePath,$data->id)',
                'htmlOptions' => array('style' => 'text-align:center;'),
                'filter' => false
            ),
        ),
    ));
}
?>

<script>
    $(document).ready(function () {
        $('a.dropdown-item').click(function () {
            $('#InquiriesRequests_typeId').val($(this).attr('value'));
            $('#dropdownMenuStartMonth').closest('.form-group').addClass('hidden');
            $('#dropdownMenuFinishMonth').closest('.form-group').addClass('hidden');
            $('#InquiriesRequests_place').addClass('hidden');
            $('#InquiriesRequests_startDate').closest('.form-group').addClass('hidden');
            $('#InquiriesRequests_finishDate').closest('.form-group').addClass('hidden');
            $('#dropdownMenuReason').closest('.form-group').addClass('hidden');

            $('#dropdownMenuTakepickup1').html('Выберите способ получения <span class="caret"></span>');
            $('#dropdownMenuTakepickup2').html('Выберите способ получения <span class="caret"></span>');
            $('#dropdownMenuTakepickup1').closest('.form-group').addClass('hidden');
            $('#dropdownMenuTakepickup2').closest('.form-group').addClass('hidden');
            $('#InquiriesRequests_takePickup').val('');

            if($(this).text() == '<?=InquiriesTypes::INCOME?>'){
                $('#dropdownMenuTakepickup2').closest('.form-group').removeClass('hidden');
            }else{
                $('#dropdownMenuTakepickup1').closest('.form-group').removeClass('hidden');
            }

            if ($(this).text() == '<?=InquiriesTypes::PLACE_OF_STUDY?>') {
                $('#InquiriesRequests_place').removeClass('hidden');
            } else if ($(this).text() == '<?=InquiriesTypes::PFR?>') {
            } else if ($(this).text() == '<?=InquiriesTypes::HOSTEL?>') {
                $('#InquiriesRequests_startDate').closest('.form-group').removeClass('hidden');
                $('#InquiriesRequests_finishDate').closest('.form-group').removeClass('hidden');
                $('#dropdownMenuReason').closest('.form-group').removeClass('hidden');
            } else {
                $('#dropdownMenuStartMonth').closest('.form-group').removeClass('hidden');
                $('#dropdownMenuFinishMonth').closest('.form-group').removeClass('hidden');
            }
            $('#dropdownMenu').html($(this).text() + ' <span class="caret"></span>');
        });
        $('a.dropdown-item-group').click(function () {
            $('#InquiriesRequests_groupNpp').val($(this).attr('value'));
            $('#dropdownMenuGroup').html($(this).text() + ' <span class="caret"></span>');
        });
        $('a.dropdown-item-startMonth').click(function () {
            $('#InquiriesRequests_startMonth').val($(this).attr('value'));
            $('#dropdownMenuStartMonth').html($(this).text() + ' <span class="caret"></span>');
        });
        $('a.dropdown-item-startYear').click(function () {
            $('#InquiriesRequests_startYear').val($(this).attr('value'));
            $('#dropdownMenuStartYear').html($(this).text() + ' <span class="caret"></span>');
        });
        $('a.dropdown-item-finishMonth').click(function () {
            $('#InquiriesRequests_finishMonth').val($(this).attr('value'));
            $('#dropdownMenuFinishMonth').html($(this).text() + ' <span class="caret"></span>');
        });
        $('a.dropdown-item-finishYear').click(function () {
            $('#InquiriesRequests_finishYear').val($(this).attr('value'));
            $('#dropdownMenuFinishYear').html($(this).text() + ' <span class="caret"></span>');
        });
        $('a.dropdown-item-reason').click(function () {
            $('#InquiriesRequests_reason').val($(this).attr('value'));
            $('#dropdownMenuReason').html($(this).text() + ' <span class="caret"></span>');
        });
        $('a.dropdown-item-takepickup1').click(function () {
            $('#InquiriesRequests_takePickup').val($(this).attr('value'));
            $('#dropdownMenuTakepickup1').html($(this).text() + ' <span class="caret"></span>');
        });
        $('a.dropdown-item-takepickup2').click(function () {
            $('#InquiriesRequests_takePickup').val($(this).attr('value'));
            $('#dropdownMenuTakepickup2').html($(this).text() + ' <span class="caret"></span>');
        });
    });
</script>

<style>
    .dropdown {
        display: inline-block;
        vertical-align: middle;
    }

    .form-group {
        margin-bottom: 8px !important;
        vertical-align: middle;
    }

    .btn-success {
        margin-bottom: 8px;
    }
</style>