<?php
/** @var string $year
 *  @var array $yearList
 */
?>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation" class="<?= ($status == 1) ? 'active' : ''?>">
        <?= CHtml::link('Активные', ['extramark/index']); ?>
    </li>
    <li role="presentation" class="<?= ($status == 2) ? 'active' : ''?>">
        <?= CHtml::link('Закрытые', ['extramark/index', 'status' => 2]); ?>
    </li>
</ul>