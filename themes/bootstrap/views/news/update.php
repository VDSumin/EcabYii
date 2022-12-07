<?php
/* @var $this NewsController */
/* @var $model News */

$this->breadcrumbs = array(
    'Новости' => array('index'),
    ' '.$model->id => array('view', 'id' => $model->id),
    'Редактирование',
);

?>

    <h1>Редактирование новости №<?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>