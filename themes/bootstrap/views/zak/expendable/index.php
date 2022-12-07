<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 09.07.2020
 * Time: 11:40
 */
/* @var $this ExpendableController */

$this->pageTitle = 'Заявки на расходные материалы';

$this->breadcrumbs = array(
    'Заявки на расходные материалы',
);

?>

<center><h1>Список поданных заявок на расходные материалы</h1></center>

<?= CHtml::link('Добавить заявку', array('create'), array('class' => 'btn btn-success')); ?>

<div style="position: absolute; left: 20px; right: 20px;">
<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'request-grid',
    'dataProvider' => $model,
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
        array(
            'class'=>'BButtonColumn',
            'template' => '{update} {delete}',
            'buttons' => array(
                'update' => array(
                    'visible' => 'ItdepExpendableAuction::getActionStatus(CHtml::value($data, "auction"))',
                ),
                'delete' => array(
                    'visible' => 'ItdepExpendableAuction::getActionStatus(CHtml::value($data, "auction"))',
                ),
            ),
        ),
    )
));
?>
</div>
<div style="height: 1000px">
</div>




