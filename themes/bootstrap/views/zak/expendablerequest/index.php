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
    'Список аукционов на закупку расходных материалов'
);

?>
<style>
    .shortPriview {
        display: inline;
    }
</style>
<center><h1>Список аукционов на закупку расходных материалов</h1></center>

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
        'device' => array(
            'header' => 'Устройство печати',
            'type' => 'raw',
            'name' => 'device',
            'value' => 'ItdepExpendableDevice::model()->findByPk(CHtml::value($data, "device"))->name',
        ),
        'invertNumber' => array(
            'header' => 'Инвертарный номер',
            'type' => 'raw',
            'name' => 'invertNumber',
        ),
        'typeCart' => array(
            'header' => 'Тип картриджа',
            'type' => 'raw',
            'name' => 'typeCart',
            'value' => 'ItdepExpendableTypecart::model()->findByPk(CHtml::value($data, "typeCart"))->name',
        ),
        'amount' => array(
            'header' => 'Количество',
            'type' => 'raw',
            'name' => 'amount',
        ),
        'placement' => array(
            'header' => 'Место нахождения обекта',
            'type' => 'raw',
            'name' => 'placement',
        ),
        'responsible' => array(
            'header' => 'Ответственное лицо',
            'type' => 'raw',
            'name' => 'responsible',
        ),
        'phone' => array(
            'header' => 'Телефонный номер',
            'type' => 'raw',
            'name' => 'phone',
        ),
        'email' => array(
            'header' => 'Почтовый адрес',
            'type' => 'raw',
            'name' => 'email',
        ),
        'comment' => array(
            'header' => 'Комментарий',
            'type' => 'raw',
            'name' => 'comment',
        ),
        'last_date_time' => array(
            'header' => 'Дата и время',
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
