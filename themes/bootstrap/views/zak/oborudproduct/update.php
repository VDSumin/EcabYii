<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:09
 */

$this->pageTitle = Yii::app()->name . ' - Каталог оборудования';

$this->breadcrumbs = array(
    'Заявки на закупку оборудования' => ['/zak/oborud'],
    'Каталог оборудования' => ['index'],
    'Редактирование'
);

?>

    <h1>Редактирование записи оборудования </h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>