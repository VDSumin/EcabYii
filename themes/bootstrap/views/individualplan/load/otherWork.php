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
    'Прочие (перемены)'
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
            <a href=<?php echo Yii::app()->createUrl('individualplan/load/otherWork')?>>
                <span aria-hidden="true">Отобразить меню</span>
            </a>
        </li>
    </ul>
</nav>
<?php
endif;
?>
<div class="alert alert-warning" role="alert" align="center"><b>Вся нагрузка заполняется в астрономических часах.</b></div>
<h1>Прочая нагрузка преподавателя <br /> <?= $person ? $person->fam . ' ' . $person->nam . ' ' . $person->otc : 'Не определен'?></h1>
<h3>по кафедре "<?= Catalog::model()->findByPk(Yii::app()->session['chairNrec'])->name; ?>"</h3>
<div>
<table class="table table-striped table-bordered table-hover" style="text-align: center; vertical-align: middle">
    <thead>
    <tr><th width="40%">Вид работы</th><th>Нормы времени в часах</th><th>Ставка</th><th>Количество часов</th></tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $rate['kind']?></td>
        <td><?= $rate['norm']?></td>
        <td><?= $rate['stavka']?></td>
        <td><?= $rate['peremen']." ч."?></td>
    </tr>
    </tbody>
</table>
</div>
