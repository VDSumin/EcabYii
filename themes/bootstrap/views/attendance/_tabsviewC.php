<?php
/** @var string $archive
 */
$mon = date('d.m',strtotime($dates[0]));
$tue = date('d.m',strtotime($dates[1]));
$wen = date('d.m',strtotime($dates[2]));
$thu = date('d.m',strtotime($dates[3]));
$fri = date('d.m',strtotime($dates[4]));
$sat = date('d.m',strtotime($dates[5]));
$sun = date('d.m',strtotime($dates[6]));
$active = [];
foreach ($activedates as $date){
    $active[] = date('w', strtotime($date['date']));
}
//var_dump($active);die;
?>


<ul class="nav nav-tabs nav-justified">
    <li role="presentation" class="<?= ($day == '7') ? 'active' : '' ?>">
        <?= CHtml::link('Вся </br> неделя', ['attendance/getview', 'day' => 7, 'type' => 1]); ?>
    </li>
    <li role="presentation" class="<?= ($day == '1') ? 'active' : '' ?>">
        <?php if(in_array(1,$active)) {
            echo CHtml::link('Понедельник <br/>'.$mon, ['attendance/getview', 'day' => 1, 'date' => $dates[0], 'type' => 1], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Понедельник <br/>'.$mon, ['attendance/getview', 'day' => 1, 'date' => $dates[0], 'type' => 1], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '2') ? 'active' : ''?>">
        <?php if(in_array(2,$active)) {
            echo CHtml::link('Вторник <br/>'.$tue, ['attendance/getview', 'day' => 2, 'date' => $dates[1], 'type' => 1], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Вторник <br/>'.$tue, ['attendance/getview', 'day' => 2, 'date' => $dates[1], 'type' => 1], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '3') ? 'active' : ''?>">
        <?php if(in_array(3,$active)) {
            echo CHtml::link('Среда <br/>'.$wen, ['attendance/getview', 'day' => 3, 'date' => $dates[2], 'type' => 1], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Среда <br/>'.$wen, ['attendance/getview', 'day' => 3, 'date' => $dates[2], 'type' => 1], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '4') ? 'active' : ''?>">
        <?php if(in_array(4,$active)) {
            echo CHtml::link('Четверг <br/>'.$thu, ['attendance/getview', 'day' => 4, 'date' => $dates[3], 'type' => 1], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Четверг <br/>'.$thu, ['attendance/getview', 'day' => 4, 'date' => $dates[3], 'type' => 1], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '5') ? 'active' : ''?>">
        <?php if(in_array(5,$active)) {
            echo CHtml::link('Пятница <br/>'.$fri, ['attendance/getview', 'day' => 5, 'date' => $dates[4], 'type' => 1], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Пятница <br/>'.$fri, ['attendance/getview', 'day' => 5, 'date' => $dates[4], 'type' => 1], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '6') ? 'active' : ''?>">
        <?php if(in_array(6,$active)) {
            echo CHtml::link('Суббота <br/>'.$sat, ['attendance/getview', 'day' => 6, 'date' => $dates[5], 'type' => 1], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Суббота <br/>'.$sat, ['attendance/getview', 'day' => 6, 'date' => $dates[5], 'type' => 1], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
</ul>