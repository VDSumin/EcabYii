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
    <li role="presentation" class="<?= ($day == '1') ? 'active' : '' ?>">
        <?php if(in_array(1,$active)) {
            echo CHtml::link('Понедельник <br/>'.$mon, ['journal/getindex', 'day' => 1, 'date' => $dates[0]], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Понедельник <br/>'.$mon, ['journal/getindex', 'day' => 1, 'date' => $dates[0]], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '2') ? 'active' : ''?>">
        <?php if(in_array(2,$active)) {
            echo CHtml::link('Вторник <br/>'.$tue, ['journal/getindex', 'day' => 2, 'date' => $dates[1]], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Вторник <br/>'.$tue, ['journal/getindex', 'day' => 2, 'date' => $dates[1]], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '3') ? 'active' : ''?>">
        <?php if(in_array(3,$active)) {
            echo CHtml::link('Среда <br/>'.$wen, ['journal/getindex', 'day' => 3, 'date' => $dates[2]], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Среда <br/>'.$wen, ['journal/getindex', 'day' => 3, 'date' => $dates[2]], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '4') ? 'active' : ''?>">
        <?php if(in_array(4,$active)) {
            echo CHtml::link('Четверг <br/>'.$thu, ['journal/getindex', 'day' => 4, 'date' => $dates[3]], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Четверг <br/>'.$thu, ['journal/getindex', 'day' => 4, 'date' => $dates[3]], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '5') ? 'active' : ''?>">
        <?php if(in_array(5,$active)) {
            echo CHtml::link('Пятница <br/>'.$fri, ['journal/getindex', 'day' => 5, 'date' => $dates[4]], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Пятница <br/>'.$fri, ['journal/getindex', 'day' => 5, 'date' => $dates[4]], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
    <li role="presentation" class="<?= ($day == '6') ? 'active' : ''?>">
        <?php if(in_array(6,$active)) {
            echo CHtml::link('Суббота <br/>'.$sat, ['journal/getindex', 'day' => 6, 'date' => $dates[5]], ['style' => 'font-weight: bold;']);
        }else{
            echo CHtml::link('Суббота <br/>'.$sat, ['journal/getindex', 'day' => 6, 'date' => $dates[5]], ['style' => 'pointer-events: none; cursor: default; color: #999;']);
        }?>
    </li>
</ul>