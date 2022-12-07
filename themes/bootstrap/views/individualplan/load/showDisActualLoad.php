<?php
/**
 * @var $person Fdata
 *
 */
$cs = Yii::app()->getClientScript();
/* @var $cs CClientScript */
$cs->registerScriptFile($cs->getCoreScriptUrl() . '/jui/js/jquery-ui.min.js');
$cs->registerCssFile($cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css');
$cs->registerScriptFile($cs->getCoreScriptUrl() . '/jui/js/jquery-ui-i18n.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/jquery.caret.js', CClientScript::POS_END);

Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/tooltip.js', CClientScript::POS_END);

?>
<?php
$this->pageTitle=Yii::app()->name . ' - Нагрузка преподавателя';
$this->breadcrumbs=[
    'Индивидуальный план' => ['/individualplan'],
    'Структура плана' => ['default/struct', 'chair' => Yii::app()->session['chairNpp']],
    'Плановая / фактическая нагрузка' => ['load/showLoad'],
    'Дисциплины фактической нагрузки'
];?>

<style>
    th {
        text-align: center;
    }
</style>
<?php
if(!$menu):
?>
<nav>
    <ul class="pager">
        <li class="previous">
            <a href=<?php echo Yii::app()->createUrl('individualplan/load/showLoad')?>>
                <span aria-hidden="true">&larr;</span> Назад
            </a>
        </li>
        <li class="previous">
            <a href=<?php echo Yii::app()->createUrl('individualplan/load/showdisactualload')?>>
                <span aria-hidden="true">Отобразить меню</span>
            </a>
        </li>
    </ul>
</nav>
<?php
endif;
?>
<h2>Фактическая нагрузка (на основе расписания)</h2>

<?= $this->renderPartial('_tabs', ['activeTab' => 'dis']); ?>


<?php $this->widget('application.widgets.grid.BGridView', array(
    'id'=>'attendance-schedule-grid',
    'beforeAjaxUpdate' => 'js:function() { $("#attendance-schedule-grid").addClass("loading");}',
    'afterAjaxUpdate' => 'js:function() { $("#attendance-schedule-grid").removeClass("loading");}',
    'dataProvider'=>$model->searchDis(),
    'filter'=>$model,
    'columns'=>array(
        'dateTimeStartOfClasses' => [
            'name' => 'dateTimeStartOfClasses',
            'type' => 'raw',
            'value' => 'date(\'d.m.Y H:i:s\', strtotime($data->dateTimeStartOfClasses));'
        ],
        'dateTimeEndOfClasses' => [
            'name' => 'dateTimeEndOfClasses',
            'type' => 'raw',
            'value' => 'date(\'d.m.Y H:i:s\', strtotime($data->dateTimeEndOfClasses));'
        ],
        'discipline' =>  [
            'name' => 'discipline',
            'filter' => CHtml::activeDropDownList(
                $model,
                'discipline',
                CHtml::listData(AttendanceSchedule::getDisActualByPersonAndYear(), 'discipline', 'discipline'), [
                'id' => false,
                'empty' => 'Дисциплина',
                'class' => 'form-control'
            ]),
            'type' => 'raw',
            'value' => '$data->discipline'
        ],
        'kindOfWorkId' => [
            'name' => 'kindOfWorkId',
            'header' => 'Вид занятия',
            'filter' => CHtml::activeDropDownList(
                $model,
                'kindOfWorkId',
                CHtml::listData(AttendanceKindofwork::model()->findAll(), 'id', 'name'), [
                'id' => false,
                'empty' => 'Вид занятия',
                'class' => 'form-control'
            ]),
            'type' => 'raw',
            'value' => '$data->kindOfWork->name'
        ],
        'studGroupId' => [
            'name' => 'studGroupId',
            'header' => 'Группа',
            'filter' => CHtml::activeDropDownList(
                $model,
                'studGroupId',
                CHtml::listData(AttendanceSchedule::getGroupActualByPersonAndYear(), 'studGroupId', 'studGroupName'), [
                'id' => false,
                'empty' => 'Группа',
                'class' => 'form-control'
            ]),
            'type' => 'raw',
            'value' => '$data->studGroupName'
        ],
        'auditorium' => [
            'name' => 'auditorium',
            'header' => 'Аудитория',
            'type' => 'raw',
            'value' => '$data->auditorium'
        ],
        'formEdu' => [
            'name' => 'formEdu',
            'filter' => CHtml::activeDropDownList(
                $model,
                'formEdu',
                CMisc::getListOfEduLabel(), [
                'id' => false,
                'empty' => 'Ф.О.',
                'class' => 'form-control'
            ]),
            'header' => 'Форма обучения',
            'type' => 'raw',
            'value' => 'CMisc::getFromEduLabel($data->studGroup->wformed)'
        ],
        'semesterStartDate' => [
            'header' => 'Дата начала семестра',
            'name' => 'semesterStartDate',
            'type' => 'raw',
            'value' => 'date(\'d.m.Y\', strtotime($data->semesterStartDate))'
        ],
    ),
)); ?>
    
  