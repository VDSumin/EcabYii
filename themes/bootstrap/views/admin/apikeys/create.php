<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:08
 */
/* @var $this ApiKeysController */
/* @var $model ApiKeys */

$this->breadcrumbs=array(
    'Api Keys'=>array('index'),
    'Создание',
);
?>

    <h1>Создание ApiKeys</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>