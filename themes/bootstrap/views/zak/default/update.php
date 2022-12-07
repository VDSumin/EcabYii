<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:09
 */
/* @var $this DefaultController */
/* @var $model ZakZakaz */

$this->breadcrumbs=array(
    'Заявки на закупки'=>array('index'),
    'Редактирование',
);

?>

    <h1>Редактирование заявки</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>