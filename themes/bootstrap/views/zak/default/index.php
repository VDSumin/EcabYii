<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 09.07.2020
 * Time: 11:40
 */
/* @var $this DefaultController */

$this->pageTitle = 'Заявки на закупки';

$this->breadcrumbs = array(
    'Заявки на закупки',
);

?>

<center><h1>Список поданных заявок</h1></center>

<?= CHtml::link('Добавить заявку', array('create'), array('class' => 'btn btn-success')); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'zak-grid',
    'dataProvider' => $model,
    'filter' => $filter,
    'emptyText' => 'Список пуст',
    'columns'=>array(
        'auction' => array(
            'header' => 'Аукцион',
            'type' => 'raw',
            'name' => 'auction',
            'value' => 'ZakAuction::model()->auctionName(CHtml::value($data, "auction"))',
        ),
        'auctionState' => array(
            'header' => 'Статус аукциона',
            'type' => 'raw',
            'name' => 'auctionState',
            'value' => 'ZakAuction::model()->state(CHtml::value($data, "auction"))',
        ),
        'oborud' => array(
            'header' => 'Принтер',
            'type' => 'raw',
            'name' => 'oborud',
            'value' => 'ZakOborud::model()->findByPk(CHtml::value($data, "oborud"))->name',
        ),
        'tovar' => array(
            'header' => 'Картридж',
            'type' => 'raw',
            'name' => 'tovar',
            'value' => 'ZakTovar::model()->findByPk(CHtml::value($data, "tovar"))->name',
        ),
        'kolvo' => array(
            'header' => 'Колич.',
            'type' => 'raw',
            'name' => 'kolvo',
        ),
        'struct' => array(
            'header' => 'Подразделение',
            'type' => 'raw',
            'name' => 'struct',
            'value' => 'StructD_rp::model()->findByPk(CHtml::value($data, "struct"))->name',
        ),
        'finsource' => array(
            'header' => 'Источник финансирования',
            'type' => 'raw',
            'name' => 'finsource',
            'value' => 'ZakClass::finsourse(CHtml::value($data, "finsource"))',
        ),
        'dz' => array(
            'header' => 'Дата заказа',
            'type' => 'raw',
            'name' => 'dz',
        ),
        'invNumber' => array(
            'header' => 'Инвертарный номер',
            'type' => 'raw',
            'name' => 'invNumber',
        ),
        'addres' => array(
            'header' => 'Адрес нахождения',
            'type' => 'raw',
            'name' => 'addres',
        ),
        'fnpp' => array(
            'header' => 'Ответственный',
            'type' => 'raw',
            'name' => 'fnpp',
            'value' => '(CHtml::value($data, "fnpp"))?Fdata::model()->findByPk(CHtml::value($data, "fnpp"))->getFIO():""',
        ),
        array(
            'class'=>'BButtonColumn',
            'template' => '{update} {delete}',
            'buttons' => array(
                'update' => array(
                    'visible' => 'ZakClass::getActionStatus(CHtml::value($data, "auction"))',
                ),
                'delete' => array(
                    'visible' => 'ZakClass::getActionStatus(CHtml::value($data, "auction"))',
                ),
            ),
        ),
    )
));




