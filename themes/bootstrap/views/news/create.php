<?php
/* @var $this NewsController */
/* @var $model News */

$this->breadcrumbs = array(
    'Новости' => array('index'),
    'Добавление',
);

?>

    <h1>Добавление новости</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>