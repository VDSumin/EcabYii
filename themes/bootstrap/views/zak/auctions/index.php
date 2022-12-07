<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 08.04.2020
 * Time: 19:11
 */

$this->pageTitle = Yii::app()->name . ' - Список аукционов';

$this->breadcrumbs = array(
    'Реестр аукционов'
);
?>

<center><h1>Список аукционов на закупку оборудования</h1></center>

<?= CHtml::link('Добавить запись', array('createO'), array('class' => 'btn btn-success')); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'oborud',
    'dataProvider' => $modelO->search(),
    'filter' => $modelO,
    'emptyText' => 'Список пуст',
    'afterAjaxUpdate' => 'js:function() {
        $(".js-cart-change").chosen({disable_search_threshold: 10});
    }',
    'columns'=>array(
        'id',
        'date',
        'name',
        'info',
        'status' => array(
            'header' => 'Статус',
            'type' => 'raw',
            'name' => 'status',
            'filter' => CHtml::activeDropDownList($modelO, 'status', ['0' => 'Черновик', '1' =>  'В работе', '2' => 'Завершено'],
                array(
                    'id' => false,
                    'prompt' => '',
                    'class' => 'form-control'
                )),
            'value' => 'ItdepOborudAuction::model()->state(CHtml::value($data, "id"))',
        ),
        array(
            'header' => 'Количество записей у события',
            'type' => 'raw',
            'value' => 'ItdepOborudAuction::model()->countRecords(CHtml::value($data, "id"))',
        ),
        array(
            'header' => 'Действие',
            'type' => 'raw',
            'value' => 'ItdepOborudAuction::model()->bigButton(CHtml::value($data, "id"))."<br/><br/>".
            ItdepOborudAuction::model()->printButton(CHtml::value($data, "id"))',
        ),
        array(
            'class'=>'BButtonColumn',
            'template' => '{update}',
            'buttons' => array(
                'update' => array(
                    'url' => 'Yii::app()->controller->createUrl("/zak/auctions/updateO", array("id" => CHtml::value($data, "id") ))',
                ),
            )
        ),
    )
));

?>

<hr />
<center><h1>Список аукционов на закупку расходных материалов</h1></center>

<?= CHtml::link('Добавить запись', array('createE'), array('class' => 'btn btn-success')); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'expendable',
    'dataProvider' => $modelE->search(),
    'filter' => $modelE,
    'emptyText' => 'Список пуст',
    'afterAjaxUpdate' => 'js:function() {
        $(".js-cart-change").chosen({disable_search_threshold: 10});
    }',
    'columns'=>array(
        'id',
        'date',
        'name',
        'info',
        'status' => array(
            'header' => 'Статус',
            'type' => 'raw',
            'name' => 'status',
            'filter' => CHtml::activeDropDownList($modelE, 'status', ['0' => 'Черновик', '1' =>  'В работе', '2' => 'Завершено'],
                array(
                    'id' => false,
                    'prompt' => '',
                    'class' => 'form-control'
                )),
            'value' => 'ItdepExpendableAuction::model()->state(CHtml::value($data, "id"))',
        ),
        array(
            'header' => 'Количество записей у события',
            'type' => 'raw',
            'value' => 'ItdepExpendableAuction::model()->countRecords(CHtml::value($data, "id"))',
        ),
        array(
            'header' => 'Действие',
            'type' => 'raw',
            'value' => 'ItdepExpendableAuction::model()->bigButton(CHtml::value($data, "id"))."<br/><br/>".
            ItdepExpendableAuction::model()->printButton(CHtml::value($data, "id"))."<br/><br/>".
            ItdepExpendableAuction::model()->printBid(CHtml::value($data, "id"))',
        ),
        array(
            'class'=>'BButtonColumn',
            'template' => '{update}',
            'buttons' => array(
                'update' => array(
                    'url' => 'Yii::app()->controller->createUrl("/zak/auctions/updateE", array("id" => CHtml::value($data, "id") ))',
                ),
            )
        ),
    )
));

?>

<hr />
<center><h1>Список аукционов на закупку программного обеспечения</h1></center>

<?= CHtml::link('Добавить запись', array('createS'), array('class' => 'btn btn-success')); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'software',
    'dataProvider' => $modelS->search(),
    'filter' => $modelS,
    'emptyText' => 'Список пуст',
    'afterAjaxUpdate' => 'js:function() {
        $(".js-cart-change").chosen({disable_search_threshold: 10});
    }',
    'columns'=>array(
        'id',
        'date',
        'name',
        'info',
        'status' => array(
            'header' => 'Статус',
            'type' => 'raw',
            'name' => 'status',
            'filter' => CHtml::activeDropDownList($modelS, 'status', ['0' => 'Черновик', '1' =>  'В работе', '2' => 'Завершено'],
                array(
                    'id' => false,
                    'prompt' => '',
                    'class' => 'form-control'
                )),
            'value' => 'ItdepSoftwareAuction::model()->state(CHtml::value($data, "id"))',
        ),
        array(
            'header' => 'Количество записей у события',
            'type' => 'raw',
            'value' => 'ItdepSoftwareAuction::model()->countRecords(CHtml::value($data, "id"))',
        ),
        array(
            'header' => 'Действие',
            'type' => 'raw',
            'value' => 'ItdepSoftwareAuction::model()->bigButton(CHtml::value($data, "id"))."<br/><br/>".
            ItdepSoftwareAuction::model()->printButton(CHtml::value($data, "id"))',
        ),
        array(
            'class'=>'BButtonColumn',
            'template' => '{update}',
            'buttons' => array(
                'update' => array(
                    'url' => 'Yii::app()->controller->createUrl("/zak/auctions/updateS", array("id" => CHtml::value($data, "id") ))',
                ),
            )
        ),
    )
));

?>
