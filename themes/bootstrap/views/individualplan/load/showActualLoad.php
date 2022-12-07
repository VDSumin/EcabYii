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
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/chartjs/utils.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/chartjs/Chart.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/chartjs/Chart.PieceLabel.js', CClientScript::POS_BEGIN);


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
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
    .mytable {
        width: 100%;
        table-layout: fixed;
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
            <a href=<?php echo Yii::app()->createUrl('individualplan/load/showactualload')?>>
                <span aria-hidden="true">Отобразить меню</span>
            </a>
        </li>
    </ul>
</nav>
<?php
endif;
?>
<h2>Фактическая нагрузка (на основе расписания)</h2>

<?= $this->renderPartial('_tabs', ['activeTab' => 'total']); ?>

<div class="table-responsive">
    <table class="table mytable">
        <tbody>
        <tr>
<?php $i = 1; $total = 1; foreach ($discipline as $keyDis => $oneDis) : ?>
            <script>

                var config = {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: <?= '[' . implode(', ', array_values($oneDis['kindOfWork'])) . ']'; ?>,
                            backgroundColor: [
                                window.chartColors.red,
                                window.chartColors.orange,
                                window.chartColors.yellow,
                                window.chartColors.green,
                                window.chartColors.blue,
                                window.chartColors.purple,
                            ],
                            label: 'Dataset 1'
                        }],
                        labels: <?= '[\'' . implode('\', \'', array_keys($oneDis['kindOfWork'])) . '\']'; ?>,
                    },
                    options: {
                        pieceLabel: {
                            render: function (args) {
                                return args.value + ' ч.';
                            },
                        },
                        responsive: true,
                        legend: {
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: <?= '\''.trim($keyDis) . '\''; ?>,
                            fontSize: 14,
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                };

            </script>
                <?php if ($i == 2) : $i=1; ?>

                    <td >
                        <div id="canvas-holder<?= $total; ?>" style="width:100%">
                            <canvas id="chart-area<?= $total; ?>" />
                        </div>

                        <script>
                                var ctx = document.getElementById("chart-area<?= $total; ?>").getContext("2d");
                                window.myDoughnut<?= $total; ?> = new Chart(ctx, config);
                        </script>
                    </td>
        </tr> <tr>
                <?php else : ?>
                    <td >
                       <div id="canvas-holder<?= $total; ?>" style="width:100%">
                            <canvas id="chart-area<?= $total; ?>" />
                        </div>

                        <script>
                                var ctx = document.getElementById("chart-area<?= $total; ?>").getContext("2d");
                                window.myDoughnut<?= $total; ?> = new Chart(ctx, config);

                        </script>
                    </td>
                <?php $i++; endif; ?>

<?php $total++; endforeach; ?>
        </tr>
        </tbody>
    </table>
</div>
    
  