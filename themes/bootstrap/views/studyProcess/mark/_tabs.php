<?php
/** @var string $year
 *  @var array $yearList
 */
?>
<ul class="nav nav-tabs nav-justified">
    <?php if ($year) :
        foreach ($yearList as $value) : ?>

        <li role="presentation" class="<?= ($value == $year) ? 'active' : ''?>">
            <?= CHtml::link($value, ['mark/index', 'year' => $value]); ?>
        </li>

    <?php endforeach;
    else :
            foreach ($yearList as $key => $value) : ?>

                <li role="presentation" class="<?= ($key == 0) ? 'active' : ''?>">
                    <?= CHtml::link($value, ['mark/index', 'year' => $value]); ?>
                </li>
    <?php endforeach;
    endif ?>
</ul>