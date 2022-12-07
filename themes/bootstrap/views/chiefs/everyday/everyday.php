<?php

$this->pageTitle = Yii::app()->name . ' - Управление подразделениями';
$this->breadcrumbs = array(
    'Управление подразделениями' => array('/chiefs'),
    'Ежедневный отчет'
);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/chiefs/everyday.js?4', CClientScript::POS_END);

echo '<h3>' . $departmentName . '</h3>';

echo ($status) ?
    '<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        Информация о сотрудниках подразделения подтверждена, спасибо!
    </div>' :
    '<div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        Подтвердите информацию о сотрудниках вашего подразделения!
    </div>';
echo '<form class="form-inline"><div class="form-group">
    <label for="ChiefReportsDay_createdAt">Скопировать данные от </label> ';
$model = new ChiefReportsDay();
$model->createdAt = date('d.m.Y', strtotime('last friday'));
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'model' => $model,
    'attribute' => 'createdAt',
    'language' => 'ru',
    'options' => array(
        'dateFormat' => 'dd.mm.yy',
        'maxDate' => 'today',
    ),
    'htmlOptions' => array(
        'class' => 'form-control',
        'placeholder' => 'Выберите дату'
    ),
));
echo '</div> ';
echo CHtml::tag("a", array(
    'id' => 'copy_modal',
    "class" => "btn btn-primary",
    "rel" => "tooltip",
    "data-toggle" => "tooltip",
    "data-placement" => "top",
    "title" => "Заменить текущие значения",
), 'Скопировать');
echo '</form>';
$this->widget('application.widgets.grid.BGridView', array(
    'id' => 'everyday-grid',
    'dataProvider' => $filter->filterCurrent($id),
    'filter' => $filter,
    'beforeAjaxUpdate' => 'js:function() { $("#everyday-grid").addClass("loading");}',
    'afterAjaxUpdate' => 'js:function() { $("#everyday-grid").removeClass("loading");}',
    'emptyText' => 'Сотрудники не найдены',
    'columns' => array(
        'dolgnost' => array(
            'name' => 'dolgnost',
            'header' => 'Должность',
            'value' => 'mb_strtoupper(mb_substr($data["dolgnost"],0,1)).mb_substr($data["dolgnost"],1,mb_strlen($data["dolgnost"])-1)',
        ),
//        'sovm' => array(
//            'name' => 'sovm',
//            'header' => 'Условия работы',
//        ),
//        'category' => array(
//            'name' => 'category',
//            'header' => 'Категория',
//        ),
        'fio' => array(
            'name' => 'fio',
            'header' => 'ФИО',
            'value' => 'ucfirst($data["fam"])." ".ucfirst($data["nam"])." ".ucfirst($data["otc"])',
            'htmlOptions' => array('style' => ''),
        ),
        'age' => array(
            'name' => 'age',
            'header' => 'Возраст',
            'filter' => false,
            'value' => '$data["age"]',
            'htmlOptions' => array('style' => 'text-align:center'),
        ),
        'categrory' => array(
            'name' => 'category',
            'header' => 'Категория',
            'type' => 'raw',
            'filter' => false,
            'value' => 'FilterEverydayForm::getDropdownCategory($data["npp"])',
            'htmlOptions' => array('style' => 'text-align:center'),
        ),
        'covidStatus' => array(
            'name' => 'covidStatus',
            'header' => 'COVID-19',
            'type' => 'raw',
            'value' => 'FilterEverydayForm::getDropdownCovidStatus($data["npp"])',
            'filter' => false,
            'htmlOptions' => array('style' => 'text-align:center'),
        ),
        'date' => array(
            'name' => 'date',
            'header' => 'Дата прививки / справки',
            'type' => 'raw',
            'filter' => false,
            'htmlOptions' => array('style' => 'width: 40px'),
            'value' => 'FilterEverydayForm::getCovidDate($data["npp"])',
        ),
        'format' => array(
            'name' => 'Формат работы',
            'type' => 'raw',
            'filter' => false,
            'value' => 'FilterEverydayForm::getDropdownFormat($data["npp"])',
            'htmlOptions' => array('style' => 'text-align:center'),
        ),
        'reasonId' => array(
            'name' => 'Причина',
            'type' => 'raw',
            'filter' => false,
            'value' => 'FilterEverydayForm::getDropdownReasonId($data["npp"])',
        ),
        'place' => array(
            'name' => 'place',
            'header' => 'Текущее местоположение',
            'type' => 'raw',
            'filter' => false,
            'value' => 'FilterEverydayForm::getDropdownStatus($data["npp"])',
            'htmlOptions' => array('style' => 'text-align:center'),
        ),
        'country' => array(
            'name' => 'country',
            'header' => 'Страна',
            'type' => 'raw',
            'filter' => false,
            'value' => 'FilterEverydayForm::getCountry($data["npp"])',
        ),
        'wasAbroad' => array(
            'name' => 'wasAbroad',
            'header' => 'За границей в последние 30 дней',
            'type' => 'raw',
            'filter' => false,
            'value' => 'FilterEverydayForm::getBoolButtons($data["npp"])',
            'htmlOptions' => array('style' => 'text-align:center'),
        ),
        'country2' => array(
            'name' => 'country2',
            'header' => 'Страна',
            'type' => 'raw',
            'filter' => false,
            'value' => 'FilterEverydayForm::getCountry2($data["npp"])',
        ),
        'additional' => array(
            'name' => 'additional',
            'header' => 'Примечание (Отпуск, командировка и т.д.)',
            'type' => 'raw',
            'filter' => false,
            'value' => 'FilterEverydayForm::getAdditional($data["npp"])',
        ),
    ),
));
echo CHtml::link('Подтвердить', null, array('class' => 'btn btn-success', 'onclick' => 'Confirm(' . $id . ')', 'style' => 'margin-bottom:20px;'));
?>
<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small id="modal_type">мы сохраняем
            информацию</small></div>
</div>

<div class="modal fade" id="modal_window" tabindex="-1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal_label">Подтвердите действие</h4>
            </div>
            <div class="modal-body">
                <p>Внимание! Все данные текущего дня будут замены на данные от <b id="modal_date"></b></p>
                <button id="modal_confirm" type="button" class="btn btn-primary">Подтвердить</button>
            </div>
        </div>
    </div>
</div>

<script>
    /*window.onload = function(){
        var fnpp = this.attr('id');

        $.ajax({
            type: "POST",
            url: window.location.href,
            async: false,
            data: $(this),
            success: function(data, fnpp)
            {
                alert(data + fnpp);
            }
        });
        return false; // avoid to execute the actual submit of the form.
    };*/
    jQuery(function ($) {
        $.datepicker.setDefaults($.datepicker.regional["ru"]);
        $(".datepicker").datepicker();
        $('[data-toggle="tooltip"]').tooltip();
        $('.date-field').datepicker("option", "onClose", function () {
                console.log($(this).attr('name'));
                console.log($(this).val());
                var fnpp = $(this).attr('name');
                var date = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: window.location.href,
                    data: {fnpp, date},
                });
            }
        );

        /*$('#modal_confirm').click(function () {
            $('#modal_window').modal('toggle');
            var $preloader = $('#preloader');
            $preloader.removeClass('hidden');
            var copy = $(this).attr('value');
            $.ajax({
                type: 'POST',
                url: window.location.href,
                async: false,
                data: {copy},
                success: function (msg) {
                    if (parseFloat(msg)) {
                        $preloader.addClass('hidden');
                    } else {
                        location = window.location.href
                    }
                }
            });
        });*/
    });
</script>

<style>
    .container {
        width: 100%;
    }

    #everyday-grid {
        overflow-x: auto;
    }

    .form-group.date-field {
        margin-bottom: 0px;
        height: 30px;
        padding: 0px;
    }
</style>
