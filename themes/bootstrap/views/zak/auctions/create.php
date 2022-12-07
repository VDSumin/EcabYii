<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:08
 */

$this->pageTitle = Yii::app()->name . ' - Список аукционов';

$this->breadcrumbs = array(
    'Заявки на закупку оборудования' => ['/zak/oborud'],
    'Список аукционов' => ['index'],
    'Создание'
);
?>

    <h1>Создание новой записи аукциона</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>