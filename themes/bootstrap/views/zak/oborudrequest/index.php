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
    'Список заявок на закупку оборудования'
);

?>
<style>
    .shortPriview {
        display: inline;
    }
</style>
<center><h1>Список заявок на закупку оборудования</h1></center>

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
        'coborud' => array(
            'header' => 'Оборудование',
            'type' => 'raw',
            'name' => 'coborud',
            'value' => 'ItdepOborudProduct::model()->findByPk(CHtml::value($data, "coborud"))->name',
        ),
        'kindOfActivity' => array(
            'header' => 'Вид деятельности',
            'type' => 'raw',
            'name' => 'kindOfActivity',
        ),
        'purposeOfEquipment' => array(
            'header' => 'Назначение оборудования',
            'type' => 'raw',
            'name' => 'purposeOfEquipment',
            'value' => 'ZakModule::getShortPriviewText(CHtml::value($data, "purposeOfEquipment"))',
        ),
        'composition' => array(
            'header' => 'Характеристики',
            'type' => 'raw',
            'name' => 'composition',
            'value' => 'ZakModule::getShortPriviewText(CHtml::value($data, "composition"))',
        ),
        'amount' => array(
            'header' => 'Количество',
            'type' => 'raw',
            'name' => 'amount',
        ),
        'finsource' => array(
            'header' => 'Планируемый источник финансирования',
            'type' => 'raw',
            'name' => 'finsource',
            'value' => 'ZakClass::finsourse(CHtml::value($data, "finsource"))',
        ),
        'replacement' => array(
            'header' => 'Признак замены или нового',
            'type' => 'raw',
            'name' => 'replacement',
        ),
        'useOfExisting' => array(
            'header' => 'Дальнейшее использование имеющегося оборудования',
            'type' => 'raw',
            'name' => 'useOfExisting',
        ),
        'placement' => array(
            'header' => 'Место нахождения обекта',
            'type' => 'raw',
            'name' => 'placement',
        ),
        'finResponsible' => array(
            'header' => 'Материально ответственный',
            'type' => 'raw',
            'name' => 'finResponsible',
        ),
        'reqResponsible' => array(
            'header' => 'Ответственный за заявку',
            'type' => 'raw',
            'name' => 'reqResponsible',
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
        'dateAndTime' => array(
            'header' => 'Дата и время',
            'type' => 'raw',
            'name' => 'dateAndTime',
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
