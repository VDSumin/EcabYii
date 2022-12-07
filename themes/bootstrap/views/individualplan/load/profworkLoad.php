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
$this->breadcrumbs=array(
    'Индивидуальный план' => ['/individualplan'],
    'Структура плана' => ['default/struct', 'chair' => $chair],
    'Профориентационная работа и довузовская подготовка'
);?>

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
            <a href=<?php echo Yii::app()->createUrl('individualplan/default/struct', ['chair' => $chair])?>>
                <span aria-hidden="true">&larr;</span> Назад
            </a>
        </li>
        <li class="previous">
            <a href=<?php echo Yii::app()->createUrl('individualplan/load/profworkLoad')?>>
                <span aria-hidden="true">Отобразить меню</span>
            </a>
        </li>
    </ul>
</nav>
<?php
endif;
?>
<div class="alert alert-warning" role="alert" align="center"><b>Вся нагрузка заполняется в астрономических часах.</b></div>
<h1>Нагрузка преподавателя по профориентационной и довузовская подготовке <br /> <?= $person ? $person->fam . ' ' . $person->nam . ' ' . $person->otc : 'Не определен'?></h1>
<h3>по кафедре "<?= Catalog::model()->findByPk(Yii::app()->session['chairNrec'])->name; ?>"</h3>
<h4>Сумма заполненных часов по выбранному разделу: <b id="summInSection"><?= LoadController::summInSection($idFromCatalog) ?></b></h4>
<div>
    <? if(count($listWork)>0): ?>
        <table class="table table-striped table-bordered table-hover" style="text-align: center; vertical-align: middle">
            <thead>
            <tr><th>№</th><th width="40%">Вид работы</th><th>Нормы времени в часах</th><th>Отчетность</th><th width="10%">Плановый показатель, час.</th><th width="10%">Подтверждено, час.</th></tr>
            </thead>
            <tbody>
            <?php
            foreach ($listWork as $k => $row) {
                echo '<tr><td>' . ($k + 1) . '</td>'
                    .'<td>' . $row['name'] . '</td>'
                    .'<td>' . $row['timeNorms'] . '</td>'
                    .'<td>' . $row['ReportingForm'] . '</td>'
                    .'<td><div class="form-group ' . (($row['status'] == 1 && $row['hours'] == $row['correctHours']) ? "has-success" :
                        (($row['status'] == 2 || ($row['status'] == 1 && $row['hours'] != $row['correctHours'])) ? "has-error" : "")) . '">
                    <input type="hidden" class="id" value="' . $row['id'] . '">
                    <input ' . (($row['isBlock']) ? 'disabled="disabled"' : '') . ' style="text-align: center;" type="text" class="js-update-hoursplan form-control" value="' . $row['hours'] . '">'
                    .(($row['status'] == 1 && $row['hours'] == $row['correctHours']) ? '' : //'<label class="control-label">Подтверждено</label>'
                        (($row['status'] == 2 || ($row['status'] == 1 && $row['hours'] != $row['correctHours'])) ? '<label class="control-label">Отклонено</label>' : ''))
                    .'</div></td>'
                    .'<td>'.$row['correctHours'].(($row['status'])?'<br /><b style="color: #3c763d">Подтверждено</b>':'').'</td></tr>';
            }
            ?>
            </tbody>
        </table>
    <? else:
        echo '<center><h3>Отсутствует доступная для заполнения нагрузка за выбранный учебный год.</h3></center>';
    endif; ?>
</div>

<script>
    $(document).on('change', '.js-update-hoursplan', function() {
        var $select = $(this);
        var parent = $(this);
        var value = $select.val();
        var id = $select.parent().find('.id').val();
        var fnpp = <?= Yii::app()->session['fnpp']; ?>;
        var year = <?= Yii::app()->session['yearEdu']; ?>;
        var chair = <?= Yii::app()->session['chairNpp']; ?>;
        var elemsummInSection = document.getElementById('summInSection');

        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/load/addhoursplan'); ?>",
            type: "post",
            dataType: "json",
            data: 'value='+value+'&id='+id+'&fnpp='+fnpp+'&year='+year+'&chair='+chair+'&idCatalog='+<?= $idFromCatalog ?>,
            'success' : function(responce) {
                if (responce.success) {
                    var text = 'Ваш общий показатель часов <b>'+responce.currentload['load']+'</b> из <b>'+responce.currentload['needload']+'</b>,' +
                        ' процент внесенного показателя '+(100*responce.currentload['load']/responce.currentload['needload']).toFixed(2)+'%';
                    var typecolor = ((100*responce.currentload['load']/responce.currentload['needload']) > 102)?'danger':'info';
                    $.notify({
                        title: "<center><strong><h4>Показатель внесен</h4></strong></center>",
                        message: "<center>"+text+"</center>"
                    }, {
                        type: typecolor ,
                        delay: 5000,
                        placement: {
                            from: 'bottom',
                            align: 'center'
                        },
                        offset: {
                            y: 80
                        }
                    });
                    parent.css({background:'#00FF00'});
                    parent.css({opacity: 0.8});
                    setTimeout(function(){
                        parent.css({background :'#fff'});
                        parent.css({opacity: 1.0});
                    }, 1000);
                    elemsummInSection.textContent = responce.valueT;
                    elemsummInSection.parentElement.style.backgroundColor = '#00FF00';
                    elemsummInSection.parentElement.style.opacity = 0.8;
                    setTimeout(function(){
                        elemsummInSection.parentElement.style.backgroundColor = '#fff';
                        elemsummInSection.parentElement.style.opacity = 1.0;
                    }, 1000);
                } else {
                    $select.css({background:'#FF0000'});
                    $select.css({opacity: 0.8});
                    setTimeout(function(){
                        $select.val($select.data('prev'));
                        $select.css({background :'#fff'});
                        $select.css({opacity: 1.0});
                    }, 1000);
                }
            }
        });
    });
</script>