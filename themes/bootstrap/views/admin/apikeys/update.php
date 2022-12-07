<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 09.04.2020
 * Time: 0:09
 */
/* @var $this ApiKeysController */
/* @var $model ApiKeys */

$this->breadcrumbs=array(
    'Api Keys'=>array('index'),
    'Редактирование',
);

?>

    <h1>Редактирование <b><?php echo $model->glogin; ?></b></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>