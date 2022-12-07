<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:09
 */

$this->pageTitle = Yii::app()->name . ' - Список аукционов';

$this->breadcrumbs = array(
    'Заявки на закупку оборудования' => ['/zak/oborud'],
    'Список аукционов' => ['index'],
    'Редактирование'
);
?>

    <h1>Редактирование записи аукциона</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>