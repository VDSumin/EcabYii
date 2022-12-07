<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:09
 */
/* @var $this AccessController */
/* @var $model MonitorAccess */

$this->breadcrumbs=array(
    'Monitoring Access'=>array('index'),
    'Редактирование',
);

?>

    <h1>Редактирование</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>