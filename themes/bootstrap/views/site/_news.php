<?php
/* @var $this NewsController */
/* @var $data News */
?>

<div class="news">
    <h2><?= $data->title; ?></h2>
    <text class="gray"><?= date('d.m.Y', strtotime($data->createdAt)); ?></text>
    <hr/>
    <?= $data->annonce; ?>
    <?php if (strlen($data->content) > strlen($data->annonce)): ?>
        <?= CHtml::link('Читать далее', array('/news/view', 'id' => $data->id)); ?>
    <?php endif; ?>
    <hr/>
</div>
