<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 28.03.2020
 * Time: 20:09
 */
/* @var $this MarkController */

$this->pageTitle = Yii::app()->name . ' - Ведомости';
$this->breadcrumbs = [
    'Ведомости'
];
?>

Здесь отображены закрепленные за Вами ведомости.<br/><br/>

<?= $this->renderPartial('_tabs', ['yearList' => $yearList, 'year' => $year]); ?>

<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'tableMarks',
    'dataProvider' => $dataProvider,
    'filter' => $filter,
    'beforeAjaxUpdate' => 'js:function() { enableLoading(); } ',
    'afterAjaxUpdate' => 'js:function() { disableLoading(); } ',
    'columns' => array(
        'semester' => array(
            'name' => 'semester',
            'header' => 'Семестр',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
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
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'filter' => CHtml::activeDropDownList($filter, 'typeList', array(
                'Зачёт' => 'Зачёт',
                'Дифференцированный зачёт' => 'Дифференцированный зачёт',
                'Экзамен' => 'Экзамен',
                'Курсовая работа' => 'Курсовая работа',
                'Курсовой проект' => 'Курсовой проект',
                'Дипломная работа' => 'Дипломная работа',
                'Дипломный проект' => 'Дипломный проект',
                'Защита практики' => 'Защита практики',
            ), array(
                'id' => false,
                'prompt' => '',
                'class' => 'form-control'
            )),
            'value' => 'CHtml::value($data, "typeList")',
        ),
        'status' => array(
            'header' => 'Статус',
            'htmlOptions' => array('style' => 'width: 35px; text-align: center;'),
            'name' => 'status',
            'type' => 'raw',
            'filter' => CHtml::activeDropDownList($filter, 'status', array(
                uList::STATUS_ACTIVE => 'В работе',
                uList::STATUS_CLOSE => 'Закрытая'
            ), array(
                'id' => false,
                'prompt' => '',
                'class' => 'form-control'
            )),
            'value' => 'CHtml::value($data, "status") == uList::STATUS_ACTIVE ?
                            CHtml::tag("span", array(
                                                "rel"=>"tooltip",
                                                "data-toggle"=>"tooltip",
                                                "data-placement"=>"top",
                                                "title"=>"В работе",
                                                "class"=>"glyphicon glyphicon-ok-circle"
                            )) :
                            CHtml::tag("span", array(
                                                "rel"=>"tooltip",
                                                "data-toggle"=>"tooltip",
                                                "data-placement"=>"top",
                                                "title"=>"Закрытая",
                                                "class"=>"glyphicon glyphicon-ban-circle"
                            ))',
        ),
        'numdoc' => array(
            'name' => 'numdoc',
            'header' => 'Номер ведомости',
            'htmlOptions' => array('style' => 'text-align: center; width: 115px'),
            'type' => 'raw',
            'value' => 'CHtml::value($data, "numdoc").CHtml::tag("br").CHtml::tag("b", array(), (" [".CHtml::value($data, "markCount")."/".CHtml::value($data, "studentCount")." оценок]"))',
            'cssClassExpression' => '(CHtml::value($data, "markCount") == CHtml::value($data, "studentCount")) ? "success" :
                                ((CHtml::value($data, "markCount") < (CHtml::value($data, "studentCount")/2)) ? "danger" : "warning")'
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
        'examiner' => array(
            'header' => 'Ответственный преподаватель',
            'name' => 'examiner',
            'type' => 'raw',
        ),
        array(
            'class' => 'BButtonColumn',
            'htmlOptions' => array('style' => 'text-align:center'),
            'header' => 'Аттестация',
            'template' => '{user} {list} {theme} {print} {printDraft} ',
            'buttons' => array(
                'user' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Список экзаменаторов" class="glyphicon glyphicon-user"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/mark/listExaminer", array("id" => CHtml::value($data, "nrec")))',
//                    'visible' => 'in_array(Yii::app()->user->getFnpp(), [\'70\', \'1556\', \'39788\', \'879\', \'1098\'])',
                    'visible' => 'ListClass::getMeMyGhief(Catalog::model()->findByAttributes(array(\'longname\'=>CHtml::value($data, "listChair")))->nrec)',
                ),
                'list' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Заполнение аттестационной ведомости" class="glyphicon glyphicon-briefcase"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/mark/list", array("id" => CHtml::value($data, "nrec")))',
                ),
                'theme' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Внесение тем курсовых работ(проектов)" class="glyphicon glyphicon-pencil"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/mark/kursTheme", array("id" => CHtml::value($data, "nrec")))',
                    'visible' => 'in_array(CHtml::value($data, "typeList"), array("Курсовая работа","Курсовой проект"))',
                ),
                'print' => array(
                    'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Печать аттестационной ведомости" class="glyphicon glyphicon-print"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/mark/listPrint", array("id" => CHtml::value($data, "nrecint64")))',
                    'visible' => 'CHtml::value($data, "dopStatusList") == 1 || CHtml::value($data, "status") == 2',
                    'options' => array('target' => '_blank'),
                ),
                'printDraft' => array(
                    'label' => '<span style="color: green" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Печать черновика аттестационной ведомости" class="glyphicon glyphicon-print"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/mark/draftPrint", array("id" => CHtml::value($data, "nrecint64")))',
                    'visible' => 'CHtml::value($data, "dopStatusList") == 0 && CHtml::value($data, "status") != 2',
                    'options' => array('target' => '_blank'),
                ),

            )
        ),
        array(
            'class' => 'BButtonColumn',
            'htmlOptions' => array('style' => 'text-align:center'),
            'header' => 'КН',
            'template' => '{stats} {statsP}',
            'buttons' => array(
                'stats' => array(
                    'label' => '<span style="color:red" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Контрольная неделя" class="glyphicon glyphicon-stats"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/mark/ratingControlWeek", array("id" => CHtml::value($data, "nrec")))',
                    'visible' => 'in_array(CHtml::value($data, "formEdu"), array(0,2)) && in_array(CHtml::value($data, "typeListInt"), array(1,2)) 
                    && listClass::checkCWAccess(CHtml::value($data, "year"), CHtml::value($data, "semester"))',
                ),
                'statsP' => array(
                    'label' => '<span style="color:red" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Печать ведомости по контрольной недели" class="glyphicon glyphicon-print"/>',
                    'url' => 'Yii::app()->createUrl("studyProcess/mark/controlWeekPrint", array("id" => CHtml::value($data, "nrecint64")))',
                    'visible' => 'in_array(CHtml::value($data, "formEdu"), array(0,2)) && in_array(CHtml::value($data, "typeListInt"), array(1,2))',
                    'options' => array('target' => '_blank'),
                ),
            ),
        ),
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

