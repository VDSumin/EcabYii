<?php
/* @var $this SiteController */

// $this->pageTitle= ($type == RUserIdentity::TYPE_STUDENT) ? 'Студенческий портал - ' : 'Портал преподавателя - ' .Yii::app()->name;
$this->pageTitle = 'Учебный портал - ' . Yii::app()->name;
?>
<!---
<center><h1>Добро пожаловать на <i>"Учебный портал"</i></h1></center>
--->
<!--<div class="jumbotron">
    <?php if (Yii::app()->user->isGuest): ?>
        <p><a class="btn btn-primary btn-lg" href="http://omgtu.ru/ecab" role="button">Зайти</a></p>
    <?php endif; ?>
</div>-->

<div class="jumbotron">
    <center><h1>Новости</h1></center>
<?php $this->widget('zii.widgets.CListView', array(
    'dataProvider' => $newsProvider,
    'itemView' => '_news',
    'emptyText' => '',
    'htmlOptions' => array('style' => 'overflow-y: scroll; max-height: 50vh;'),
    'template' => '{items}'
)); ?>
</div>

<?php
if(Yii::app()->user->getPerStatus()){
    $listAttendance = AttendanceSchedule::getMyAttendanceTeacher(Yii::app()->user->getFnpp());
}else{
    $listAttendance = AttendanceSchedule::getMyAttendanceStudent(Yii::app()->user->getFnpp());
}

?>

<?php
if($listAttendance):
?>

<div class="jumbotron">
    <center><h1><b>Расписание на 2 дня</b></h1><h3>(в данном разделе информация обновляется раз в сутки (ночью))</h3></center>
    <div style="overflow-y: scroll; max-height: 40vh;" id="yw0" class="list-view">
        <div class="items">
            <div class="news">
    <?php
    $currentDate = '';
    foreach ($listAttendance as $one){
        if($currentDate != $one['date']){
            $currentDate = $one['date'];
            echo '<h2>'. date('d.m.Y', strtotime($one['date'])).'</h2><hr/>';
        }
        if($one['discipline'] == ''){
            echo "Расписание отсутствует!<hr />";
            continue;
        }
        echo "<div>
        <div style='display: inline; background-color: #eee;padding: 4px;'>".date('H:i', strtotime($one['stime']))." - ".date('H:i', strtotime($one['etime']))."</div>
        <div style='display: inline; '><b>".$one['discipline']."</b></div>
                <br/>
        <div style='display: inline; margin-left: 10%;'> <span class=\"glyphicon glyphicon-tags\" aria-hidden=\"true\" /> </div>
        <div style='display: inline;'>".$one['kind']."</div>
                <br/>
        <div style='display: inline; margin-left: 10%;'> <span class=\"glyphicon glyphicon-map-marker\" aria-hidden=\"true\" /> </div>
        <div style='display: inline;'>".$one['auditorium']."</div>
                <br/>
        <div style='display: inline; margin-left: 10%;'> <span class=\"glyphicon glyphicon-education\" aria-hidden=\"true\" /> </div>
        <div style='display: inline;'>".$one['studGroupName']."</div>
                        <br/>
        <div style='display: inline; margin-left: 10%;'> <span class=\"glyphicon glyphicon-user\" aria-hidden=\"true\" /> </div>
        <div style='display: inline;'>".$one['teacherFio']."</div>
        </div>
        <hr />";

    }
    ?>
            </div>
        </div>
    </div>
</div>

<?php
endif;
?>

<div class="jumbotron">
    <?= $this->renderPartial('_table'); ?>
</div>
