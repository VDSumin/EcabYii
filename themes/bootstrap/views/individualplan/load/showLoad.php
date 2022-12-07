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
    'Плановая / фактическая нагрузка'
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
            <a href=<?php echo Yii::app()->createUrl('individualplan/load/showLoad')?>>
                <span aria-hidden="true">Отобразить меню</span>
            </a>
        </li>
    </ul>
</nav>
<?php
endif;
?>
<h1>Нагрузка преподавателя <br/> <?= $person ? $person->fam . ' ' . $person->nam . ' ' . $person->otc : 'Не определен'?></h1>
<h3>по кафедре "<?= Catalog::model()->findByPk(Yii::app()->session['chairNrec'])->name; ?>"</h3>
<div>

    <?= CHtml::link('Плановая нагрузка', ['load/showdisplanload',
        ],
        ['class' => 'btn btn-primary disabled', 'style' => 'margin-bottom: 10px']); ?>

    <?= CHtml::link('Фактическая нагрузка', ['load/showactualload',
        ],
        ['class' => 'btn btn-primary', 'style' => 'margin-bottom: 10px']); ?>

    <h4>Сумма плановых часов по выбранному разделу: <b id="summInSection"><?= $totalHours['plan'] ?>(ак) / <?= round(0.75*$totalHours['plan'], 2) ?>(ас)</b></h4>
    <h4>Сумма фактических часов по выбранному разделу: <b id="summInSection"><?= ($totalHours['actual'])?$totalHours['actual']:'0' ?>(ак) / <?= round(0.75*$totalHours['actual'], 2) ?>(ас)</b></h4>
    <table class="table table-striped table-bordered table-hover" style="text-align: center; vertical-align: middle">
        <thead>
        <tr>
            <th rowspan="3">
                Вид нагрузки
            </th>
            <th colspan="6">
                Осень
            </th>
            <th colspan="6">
                Весна
            </th>
        </tr>
        <tr>
            <th colspan="3">
                Плановая нагрузка
            </th>
            <th colspan="3">
                Фактическая нагрузка
            </th>
            <th colspan="3">
                Плановая нагрузка
            </th>
            <th colspan="3">
                Фактическая нагрузка
            </th>
        </tr>
        <tr>
            <th>
                Очная форма
            </th>
            <th >
                Вечерняя форма
            </th>
            <th >
                Заочная форма
            </th>
            <th>
                Очная форма
            </th>
            <th >
                Вечерняя форма
            </th>
            <th >
                Заочная форма
            </th>
            <th>
                Очная форма
            </th>
            <th >
                Вечерняя форма
            </th>
            <th >
                Заочная форма
            </th>
            <th>
                Очная форма
            </th>
            <th >
                Вечерняя форма
            </th>
            <th >
                Заочная форма
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach (WorkloadCatalog::model()->findAll() as $oneTypeOfLoad) : ?>
            <tr>
                <td>
                    <?= $oneTypeOfLoad->nameFull ?>
                </td>
                <?php foreach ([WorkloadPlanActual::SEASON_AUTUMN, WorkloadPlanActual::SEASON_SPRING] as $seasonOfLoad): ?>
                    <?php foreach ([WorkloadPlanActual::TYPE_LOAD_PLAN, WorkloadPlanActual::TYPE_LOAD_ACTUAL] as $kindOfLoad): ?>
                        <?php foreach ([CMisc::INTERNAL, CMisc::EVENING, CMisc::EXTRAMURAL] as $formEdu) : ?>
                            <?php $value = WorkloadPlanActual::getValueOfLoad($person->npp, $oneTypeOfLoad->id, $seasonOfLoad,$kindOfLoad, $formEdu); ?>
                            <td class="<?= ($value > 0) ? (($kindOfLoad == 1)?'bg-info':'bg-success') : ''?>">
                               <?= $value > 0 ? $value : '-';  ?>
                            </td>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td>
                <b>Итог</b>
            </td>
            <?php foreach ([WorkloadPlanActual::SEASON_AUTUMN, WorkloadPlanActual::SEASON_SPRING] as $seasonOfLoad): ?>
                <?php foreach ([WorkloadPlanActual::TYPE_LOAD_PLAN, WorkloadPlanActual::TYPE_LOAD_ACTUAL] as $kindOfLoad): ?>
                    <?php foreach ([CMisc::INTERNAL, CMisc::EVENING, CMisc::EXTRAMURAL] as $formEdu) : ?>
                        <?php $value = WorkloadPlanActual::getSummValueOfLoad($person->npp, $seasonOfLoad,$kindOfLoad, $formEdu); ?>
                        <td class="<?= ($value > 0) ? (($kindOfLoad == 1)?'bg-info':'bg-success') : ''?>">
                            <?= $value > 0 ? $value : '-';  ?>
                        </td>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tr>

        </tbody>
    </table>




</div>
    
  