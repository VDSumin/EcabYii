<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 28.03.2020
 * Time: 20:09
 */
/* @var $this MarkController */

$this->pageTitle = Yii::app()->name . ' - Направления';
$this->breadcrumbs = [
    'Направления'
];
?>

Здесь отображены закрепленные за Вами направления.<br /><br />

<h1>Активные направления</h1>
<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'tableExtraMarks',
    'dataProvider' => $dataProvider,
    'columns'=>array(
        'year' => array(
            'name' => 'numdoc',
            'header' => 'Учебный год',
            'htmlOptions' => array('style' => 'text-align: center; width: 35px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "year")',
        ),
        'semester' => array(
            'name' => 'semester',
            'header' => 'Семестр',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "semester")',
        ),
        'typeList' => array(
            'name' => 'typeList',
            'header' => 'Вид',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "typeList")',
        ),
        'status' => array(
            'name' => 'status',
            'header' => 'Статус',
            'type' => 'raw',
        ),
        'dateList' => array(
            'name' => 'dateList',
            'header' => 'Дата сдачи',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CMisc::fromGalDate(CHtml::value($data, "dateList"))',
        ),
        'numDoc' => array(
            'name' => 'numDoc',
            'header' => 'Номер направления',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "numDoc")',
            'visible' => false,
        ),
        'studGroup' => array(
            'header' => 'Группа',
            'name' => 'studGroup',
            'htmlOptions' => array('style' => 'text-align: center')
        ),
        'listChair' => array(
            'header' => 'Кафедра ведомости',
            'name' => 'listChair',
            'htmlOptions' => array('style' => 'text-align: center')
        ),
        'listFacult' => array(
            'header' => 'Факультет группы',
            'name' => 'listFacult',
            'htmlOptions' => array('style' => 'text-align: center')
        ),
        'discipline' => array(
            'header' => 'Дисциплина',
            'name' => 'discipline',
        ),
        'examinerFio' => array(
            'header' => 'Ответственный преподаватель',
            'name' => 'examinerFio',
            'type' => 'raw',
            'value' => '(CHtml::value($data, "examinerFio") != "")?CHtml::value($data, "examinerFio"):CHtml::value($data, "lecturerFio")',
        ),
    ),

));

 ?>
<hr />

<h1>Закрытые направления</h1>
<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'tableExtraMarksPas',
    'dataProvider' => $dataProviderPas,
    'columns'=>array(
        'year' => array(
            'name' => 'numdoc',
            'header' => 'Учебный год',
            'htmlOptions' => array('style' => 'text-align: center; width: 35px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "year")',
        ),
        'semester' => array(
            'name' => 'semester',
            'header' => 'Семестр',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "semester")',
        ),
        'typeList' => array(
            'name' => 'typeList',
            'header' => 'Вид',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "typeList")',
        ),
        'status' => array(
            'name' => 'status',
            'header' => 'Статус',
            'type' => 'raw',
        ),
        'dateList' => array(
            'name' => 'dateList',
            'header' => 'Дата сдачи',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CMisc::fromGalDate(CHtml::value($data, "dateList"))',
        ),
        'numDoc' => array(
            'name' => 'numDoc',
            'header' => 'Номер направления',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "numDoc")',
            'visible' => false,
        ),
        'studGroup' => array(
            'header' => 'Группа',
            'name' => 'studGroup',
            'htmlOptions' => array('style' => 'text-align: center')
        ),
        'listChair' => array(
            'header' => 'Кафедра ведомости',
            'name' => 'listChair',
            'htmlOptions' => array('style' => 'text-align: center')
        ),
        'listFacult' => array(
            'header' => 'Факультет группы',
            'name' => 'listFacult',
            'htmlOptions' => array('style' => 'text-align: center')
        ),
        'discipline' => array(
            'header' => 'Дисциплина',
            'name' => 'discipline',
        ),
        'examinerFio' => array(
            'header' => 'Ответственный преподаватель',
            'name' => 'examinerFio',
            'type' => 'raw',
            'value' => '(CHtml::value($data, "examinerFio") != "")?CHtml::value($data, "examinerFio"):CHtml::value($data, "lecturerFio")',
        ),
    ),

));

?>
<hr />
