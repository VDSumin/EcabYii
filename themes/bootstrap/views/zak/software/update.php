<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:09
 */
/* @var $this SoftwareController */
/* @var $model ItdepSoftwareRequest */

$this->pageTitle = 'Редактирование существующей заявки';

$this->breadcrumbs=array(
    'Заявки на закупку программного обеспечения'=>array('index'),
    'Редактирование',
);

?>

    <h1>Редактирование заявки на закупку программного обеспечения</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>