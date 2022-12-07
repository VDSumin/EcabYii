<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 08.04.2020
 * Time: 19:11
 */

$this->pageTitle = Yii::app()->name . ' - Аукцион';

$this->breadcrumbs = array(
    'Реестр аукционов' => ['/zak/auctions'],
    'Список аукционов на закупку программного обеспечения'
);

?>
<style>
    .shortPriview {
        display: inline;
    }
</style>
<center><h1>Список аукционов на закупку программного обеспечения</h1></center>

<div style="position: absolute; left: 20px; right: 20px; margin-bottom: 100px;">
<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'zak-cart-grid',
    'dataProvider' => $dataProvider,
    'filter' => $filter,
    'emptyText' => 'Список пуст',
    'columns'=>array(
        'struct' => array(
            'header' => 'Подразделение',
            'type' => 'raw',
            'name' => 'struct',
            'value' => 'StructD_rp::model()->findByPk(CHtml::value($data, "struct"))->name',
        ),
        'softName' => array(
            'header' => 'Наименование ПО',
            'type' => 'raw',
            'name' => 'softName',
        ),
        'versionSW' => array(
            'header' => 'Версия ПО',
            'type' => 'raw',
            'name' => 'versionSW',
        ),
        'editionSW' => array(
            'header' => 'Редакция ПО',
            'type' => 'raw',
            'name' => 'editionSW',
        ),
        'amount' => array(
            'header' => 'Количество',
            'type' => 'raw',
            'name' => 'amount',
        ),
        'kindOfActivity' => array(
            'header' => 'Вид деятельности',
            'type' => 'raw',
            'name' => 'kindOfActivity',
        ),
        'purpose' => array(
            'header' => 'Назначение (основание) приобретения',
            'type' => 'raw',
            'name' => 'purpose',
        ),
        'finsource' => array(
            'header' => 'Планируемый источник финансирования',
            'type' => 'raw',
            'name' => 'finsource',
            'value' => 'ZakClass::finsourse(CHtml::value($data, "finsource"))',
        ),
        'placement' => array(
            'header' => 'Место нахождения обекта',
            'type' => 'raw',
            'name' => 'placement',
        ),
        'responsible' => array(
            'header' => 'Материально ответственное лицо',
            'type' => 'raw',
            'name' => 'responsible',
        ),
        'contacts' => array(
            'header' => 'Контакты',
            'type' => 'raw',
            'name' => 'contacts',
        ),
        'comment' => array(
            'header' => 'Комментарий',
            'type' => 'raw',
            'name' => 'comment',
        ),
        'last_date_time' => array(
            'header' => 'Дата и время создания',
            'type' => 'raw',
            'name' => 'last_date_time',
        ),
        'creater' => array(
            'header' => 'Составитель заявки',
            'type' => 'raw',
            'name' => 'creater',
        ),

    )
));
?>
    <div style="height: 100px">
    </div>
</div>
<?php

echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>Информация обрабатывается</small></div>
</div>';
?>
