<?php
/** @var string $year
 *  @var array $yearList
 */
?>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation" class="<?= (isset($activeTab) && ('total' == $activeTab)) ? 'active' : ''?>">
        <?= CHtml::link('Сводная информация', ['load/showactualload']); ?>
    </li>
    <li role="presentation" class="<?= (isset($activeTab) && ('dis' == $activeTab)) ? 'active' : ''?>">
        <?= CHtml::link('Дисциплины', ['load/showdisactualload']); ?>
    </li>
</ul>