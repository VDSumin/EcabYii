<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:08
 */
/* @var $this SoftwareController */
/* @var $model ItdepSoftwareRequest */

$this->pageTitle = 'Создание новой заявки';

$this->breadcrumbs=array(
    'Заявки на закупку программного обеспечения'=>array('index'),
    'Создание',
);

?>

    <h1>Создание заявки на закупку программного обеспечения</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>