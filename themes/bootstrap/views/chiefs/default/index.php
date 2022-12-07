<?php
$this->pageTitle = Yii::app()->name . ' - Управление подразделениями';
$this->breadcrumbs = [
    'Управление подразделениями'
];
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/chiefs/export.js?2', CClientScript::POS_END);
echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>мы готовим файл</small></div>
</div>';

if ($boss) {
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'id' => 'datePicker',
        'name' => 'datePicker',
        'flat' => true,
        'options' => array(
            'dateFormat' => 'yy-mm-dd',
            'changeMonth' => false,
            'changeYear' => false,
            'hideIfNoPrevNext' => true,
            'minDate' => ChiefsModule::getMinDate(),
            'maxDate' => 'today',
            'showOtherMonths' => true,
            'selectOtherMonths' => false,
        ),
        'language' => 'ru',
        'htmlOptions' => array(//            'style' => 'padding: 10%; height: 300px; position: absolute; top: 0; bottom: 0; margin: auto 0;'
        ),
        'value' => date("Y-m-d", strtotime('today'))
    ));
    echo '<button type="button" department="' . 6 . '" period="day" style="margin: 10px 10px 0 0" class="export btn btn-success">Ежедневный отчет по ОмГТУ <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></button>';
    echo '<button type="button" style="margin: 10px 10px 0 0; background-color: darkgreen" class="downloadReport btn btn-success">Скачать отчет по ОмГТУ <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></button>';
    echo '<hr/>';
}

foreach ($departments as $department) {
    echo '<h3>' . $department['department'] . '</h3>';
    echo CHtml::link('Заполнение ежедневного отчета', array('/chiefs/everyday', 'id' => $department['npp']), array('class' => 'btn btn-default', 'style' => 'margin: 10px 10px 0 0'));
    echo '<button type="button" department="' . $department['npp'] . '" period="day" style="margin: 10px 10px 0 0" class="export btn btn-success">Сформировать ежедневный отчет <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></button>';
    echo '<hr/>';
}