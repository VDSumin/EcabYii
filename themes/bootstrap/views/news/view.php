<?php
/* @var $this NewsController */
/* @var $model News */
if (NewsController::checkAccess()) {
    $this->breadcrumbs = array(
        'Новости' => array('index'),
        $model->id,
    );
}
?>

<div class="jumbotron">
<h1><?= $model->title ?></h1>

<?= $model->content ?>
</div>