<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:09
 */
/* @var $this ExpendableController */
/* @var $model ItdepExpendableRequest */

$this->pageTitle = 'Редактирование существующей заявки';

$this->breadcrumbs=array(
    'Заявки на расходные материалы'=>array('index'),
    'Редактирование',
);

?>

    <h1>Редактирование заявки на расходные материалы</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>