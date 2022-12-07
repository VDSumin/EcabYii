<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:08
 */
/* @var $this ExpendableController */
/* @var $model ItdepExpendableRequest */

$this->pageTitle = 'Создание новой заявки';

$this->breadcrumbs=array(
    'Заявки на расходные материалы'=>array('index'),
    'Создание',
);

?>

    <h1>Создание заявки на расходные материалы</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>