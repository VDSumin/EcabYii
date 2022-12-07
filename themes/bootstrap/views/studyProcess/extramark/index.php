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
<style>
    .container {width:1304px}
    .scroll::-webkit-scrollbar{
        width: 5px;
        background-color: #ddd;
    }
    .scroll::-webkit-scrollbar-thumb {
        background: slategray;
        border-radius: 10px;
    }
</style>

Здесь отображены закрепленные за Вами направления.<br/><br/>

<?= $this->renderPartial('_tabs', ['status' => $status]); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'tableMarks',
    'dataProvider' => $dataProvider,
    'filter' => $filter,
    'beforeAjaxUpdate' => 'js:function() { enableLoading(); } ',
    'afterAjaxUpdate' => 'js:function() { disableLoading(); } ',
    'columns' => array(
        'year' => array(
            'name' => 'year',
            'header' => 'Уч. год',
            'htmlOptions' => array('style' => 'text-align: center; width: 35px; padding: unset;'),
            'type' => 'raw',
            'filter' => CHtml::activeDropDownList($filter, 'year',
                $yearList,
                array(
                    'id' => false,
                    'prompt' => '',
                    'class' => 'form-control'
                )),
            'value' => 'CHtml::value($data, "year")',
        ),
        'semester' => array(
            'name' => 'semester',
            'header' => 'Семестр',
            'htmlOptions' => array('style' => 'text-align: center; width: 40px'),
            'type' => 'raw',
            'filter' => CHtml::activeDropDownList($filter, 'semester', array(
                'осенний' => 'осенний',
                'весенний' => 'весенний',
            ), array(
                'id' => false,
                'prompt' => '',
                'class' => 'form-control'
            )),
            'value' => 'CHtml::value($data, "semester")',
        ),
        'typeList' => array(
            'name' => 'typeList',
            'header' => 'Вид',
            'htmlOptions' => array('style' => 'text-align: center; width: 100px'),
            'type' => 'raw',
            'filter' => CHtml::activeDropDownList($filter, 'typeList', array(
                'Зачёт' => 'Зачёт',
                'Дифференцированный зачёт' => 'Дифференцированный зачёт',
                'Экзамен' => 'Экзамен',
                'Курсовая работа' => 'Курсовая работа',
                'Курсовой проект' => 'Курсовой проект',
                'Дипломная работа' => 'Дипломная работа',
                'Дипломный проект' => 'Дипломный проект',
                'Практика' => 'Практика',
                'Защита практики' => 'Защита практики'
            ), array(
                'id' => false,
                'prompt' => '',
                'class' => 'form-control'
            )),
            'value' => 'CHtml::value($data, "typeList")',
        ),
//        'status' => array(
//            'header' => 'Статус',
//            'htmlOptions' => array('style' => 'width: 35px; text-align: center;'),
//            'name' => 'status',
//            'type' => 'raw',
//            'filter' => CHtml::activeDropDownList($filter, 'status', array(
//                uList::STATUS_ACTIVE => 'В работе',
//                uList::STATUS_CLOSE => 'Закрытая'
//            ), array(
//                'id' => false,
//                'prompt' => '',
//                'class' => 'form-control'
//            )),
//            'value' => 'CHtml::value($data, "status") == uList::STATUS_ACTIVE ?
//                            CHtml::tag("span", array(
//                                                "rel"=>"tooltip",
//                                                "data-toggle"=>"tooltip",
//                                                "data-placement"=>"top",
//                                                "title"=>"В работе",
//                                                "class"=>"glyphicon glyphicon-ok-circle"
//                            )) :
//                            CHtml::tag("span", array(
//                                                "rel"=>"tooltip",
//                                                "data-toggle"=>"tooltip",
//                                                "data-placement"=>"top",
//                                                "title"=>"Закрытая",
//                                                "class"=>"glyphicon glyphicon-ban-circle"
//                            ))',
//        ),

        'numdoc' => array(
            'name' => 'numdoc',
            'header' => 'Номер направления',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "numdoc").CHtml::tag("br").CHtml::tag("b", array(), (" [".CHtml::value($data, "markCount")."/".CHtml::value($data, "studentCount")." оценок]"))',
            'cssClassExpression' => '(CHtml::value($data, "markCount") == CHtml::value($data, "studentCount")) ? "success" :
                                ((CHtml::value($data, "markCount") < (CHtml::value($data, "studentCount")/2)) ? "danger" : "warning")'
        ),
        'studGroup' => array(
            'header' => 'Группа',
            'name' => 'studGroup',
            'htmlOptions' => array('style' => 'text-align: center; padding: unset')
        ),
        'listChair' => array(
            'header' => 'Кафедра ведомости',
            'name' => 'listChair',
            'htmlOptions' => array('style' => 'text-align: center; padding: unset')
        ),
        'listFacult' => array(
            'header' => 'Факультет',
            'name' => 'listFacult',
            'htmlOptions' => array('style' => 'text-align: center; padding: unset')
        ),
        'discipline' => array(
            'header' => 'Дисциплина',
            'name' => 'discipline',
        ),
        'examiner' => array(
            'header' => 'Ответственный преподаватель',
            'name' => 'examiner',
            'type' => 'raw',
        ),
        array(
            'class' => 'BButtonColumn',
            'htmlOptions' => array('style' => 'text-align:center; padding:unset'),
            'header' => 'Аттест.',
            'template' => '{list} {theme} {print}',
            'buttons' => array(
                'list' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Заполнение направления" class="glyphicon glyphicon-briefcase"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/extramark/list", array("id" => CHtml::value($data, "nrec")))',
                ),
                'theme' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Внесение тем курсовых работ(проектов)" class="glyphicon glyphicon-pencil"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/extramark/kursTheme", array("id" => CHtml::value($data, "nrec")))',
                    'visible' => 'in_array(CHtml::value($data, "typeList"), array("Курсовая работа","Курсовой проект"))',
                ),
                'print' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Печать заполненого направления" class="glyphicon glyphicon-print"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/extramark/listPrint", array("id" => CHtml::value($data, "nrecint64")))',
                    'options' => array('target' => '_blank'),
                ),

            )
        ),
        'students' => array(
            'header' => 'Студенты',
            'name' => 'student',
            'type' => 'raw',
            'value' => 'ListClass::getExtraStudentList(CHtml::value($data, "student"))',
            'htmlOptions' => array('style' => 'width: 10%; padding: 1px'),

            'filter' => false,

        )
    ),

));

echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>Применяются фильтры</small></div>
</div>';
?>

<script>
    function enableLoading() {
        var $preloader = $('#preloader');
        $preloader.removeClass('hidden');
    }

    function disableLoading() {
        var $preloader = $('#preloader');
        $preloader.addClass('hidden');
    }
</script>

