<?php
/** @var string $archive
 */
if(!isset($_SESSION)){
    session_start();
}
$stat = $_SESSION['stat'];
?>
<ul class="nav nav-tabs nav-justified">
    <li role="presentation" class="<?= ($stat == '1') ? 'active' : '' ?>">
        <?= CHtml::link('По текущюю дату', ['journal/getstatistics', 'stat' => 1]); ?>
    </li>
    <li role="presentation" class="<?= ($stat == '2') ? 'active' : '' ?>">
        <?= CHtml::link('За весь семестр', ['journal/getstatistics', 'stat' => 2]); ?>
    </li>
    <li role="presentation" class="<?= ($stat == '3') ? 'active' : '' ?>">
        <?= CHtml::link('За выбранный период', ['journal/getstatistics', 'stat' => 3]); ?>
    </li>
</ul>