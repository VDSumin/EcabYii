<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:08
 */
/* @var $this AccessController */
/* @var $model MonitorAccess */

$this->breadcrumbs=array(
    'Monitoring Access'=>array('index'),
    'Создание',
);
?>

    <h1>Создание</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>