<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:09
 */
/* @var $this OborudController */
/* @var $model ItdepOborudRequest */

$this->pageTitle = 'Редактирование существующей заявки';

$this->breadcrumbs=array(
    'Заявки на закупку оборудования'=>array('index'),
    'Редактирование',
);

?>

    <h1>Редактирование заявки на закупку оборудования</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>