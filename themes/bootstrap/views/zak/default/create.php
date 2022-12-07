<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:08
 */
/* @var $this DefaultController */
/* @var $model ZakZakaz */

$this->breadcrumbs=array(
    'Заявки на закупки'=>array('index'),
    'Создание',
);

?>

    <h1>Создание заявки</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>