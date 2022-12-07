<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 09.07.2020
 * Time: 11:40
 */
/* @var $this OborudController */

$this->pageTitle = 'Заявки на закупку программного обеспечения';

$this->breadcrumbs = array(
    'Заявки на закупку программного обеспечения',
);

?>

<center><h1>Список поданных заявок на закупку программного обеспечения</h1></center>

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
            'header' => 'Место установки ПО',
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
        array(
            'class'=>'BButtonColumn',
            'template' => '{update} {delete}',
            'buttons' => array(
                'update' => array(
                    'visible' => 'ItdepOborudAuction::getActionStatus(CHtml::value($data, "auction"))',
                ),
                'delete' => array(
                    'visible' => 'ItdepOborudAuction::getActionStatus(CHtml::value($data, "auction"))',
                ),
            ),
        ),
    )
));
?>
</div>
<div style="height: 1000px">
</div>




