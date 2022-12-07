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
        <?= CHtml::link('По текущюю дату', ['attendance/getstatistics', 'stat' => 1, 'type' => 0]); ?>
    </li>
    <li role="presentation" class="<?= ($stat == '2') ? 'active' : '' ?>">
        <?= CHtml::link('За весь семестр', ['attendance/getstatistics', 'stat' => 2, 'type' => 0]); ?>
    </li>
    <li role="presentation" class="<?= ($stat == '3') ? 'active' : '' ?>">
        <?= CHtml::link('За выбранный период', ['attendance/getstatistics', 'stat' => 3, 'type' => 0]); ?>
    </li>
</ul>