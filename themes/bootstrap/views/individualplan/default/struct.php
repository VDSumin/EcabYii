<?php
/* @var $this DefaultController
 * @var $chair StructD_rp
 */

Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerScriptFile(Yii::app()->clientScript->getCoreScriptUrl(). '/jquery.cookie.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->getBaseUrl(true) . '/css/individualplan.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/chartjs/utils.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/chartjs/Chart.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/chartjs/Chart.PieceLabel.js', CClientScript::POS_BEGIN);

$this->pageTitle = Yii::app()->name . ' - Индивидуальный план';
$this->breadcrumbs = [
    'Индивидуальный план' => ['/individualplan'],
    'Структура плана'
];
?>
<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }

</style>


<h1>Структура плана за <?= $year . " - " . ($year+1)?> учебный год</h1>
<h3><?= ($chair instanceof StructD_rp) ? $chair->name : ""?></h3>
<form class="form-inline">
    <div class="form-group">
        <label class="control-label">Ставка по кафедре </label>
        <input type="text" <?= (!empty($rate['rate'])?'disabled="disabled"':'') ?> class="js-update-rate form-control" value="<?= $rate['rate'] ?>" style="text-align: center" placeholder="<?= $rate['stavka'] ?>">
        <input type="button" value="Данные по ставке" class="btn btn-default" onclick="modalShow()">
        <a href= <?= Yii::app()->createUrl("individualplan/default/LoadPdfOfPlanFromFR"); ?> target="_blank">
            <span style="color:red" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Печать индвидуального плана" class="btn btn-default glyphicon glyphicon-print"/>
        </a>
    </div>
</form>
<?php
//На данный момент Вы можете просмотреть свою учебную нагрузку в разделе <b>"Учебная нагрузка"</b>
?>
<br />
<br />
<div id="canvas-holder" style="width:90%">
    <canvas id="chart-area" />
</div>
<div class="alert alert-warning" role="alert" align="center"><b>Вся нагрузка заполняется в астрономических часах.</b></div>
<table class="table table-bordered">
    <tr><th colspan="11" style="background: aliceblue;"><center>Планируемые сводные данные, заполненные преподавателем</center></th></tr>
    <tr><th>Ставка</th><th>Учебная нагрузка</th><th>Учебно-метод. работа</th><th>Орг.-метод. работа</th>
        <th>Научно-исслед. работа</th><th>Учебно-восп. работа</th><th>Профор. работа и довузовская подготовка</th>
        <th>Перемены</th><th>Всего часов</th><th>Подтв. часов</th><th>Норма часов</th>
    </tr>
    <tr><th colspan="11"><center style="background: aliceblue;">Плановая нагрузка</center></th></tr>
    <tr style="text-align:center"><td><?= $generalTable['stavka'] ?></td><td><?= round($generalTable['educload'], 2) ?>(ак) <br /> <?= round(0.75*$generalTable['educload'],2) ?>(ас)</td>
        <td><?= $generalTable['educmethload'] ?></td><td><?= $generalTable['orgmethload'] ?></td>
        <td><?= $generalTable['reseachload'] ?></td><td><?= $generalTable['educationalload'] ?></td><td><?= $generalTable['profworkload'] ?></td>
        <td><?= ($generalTable['peremen']) ?></td><td><?= round($generalTable['sumH']+(0.75*$generalTable['educload'])+($generalTable['peremen']), 2) ?></td>
        <td><?= round($generalTable['sumSuccesH']+(0.75*$generalTable['educload'])+($generalTable['peremen']), 2) ?></td>
        <td><?= $generalTable['stavka']*1440 ?></td></tr>
    <tr><th colspan="11"><center style="background: aliceblue;">Фактическая нагрузка</center></th></tr>
    <tr style="text-align:center"><td><?= $generalTable1['stavka'] ?></td><td><?= round($generalTable1['educload'], 2) ?>(ак) <br /> <?= round(0.75*$generalTable1['educload'],2) ?>(ас)</td>
        <td><?= $generalTable1['educmethload'] ?></td><td><?= $generalTable1['orgmethload'] ?></td>
        <td><?= $generalTable1['reseachload'] ?></td><td><?= $generalTable1['educationalload'] ?></td><td><?= $generalTable1['profworkload'] ?></td>
        <td><?= ($generalTable1['peremen']) ?></td><td><?= round($generalTable1['sumH']+(0.75*$generalTable1['educload'])+($generalTable1['peremen']), 2) ?></td>
        <td><?= round($generalTable1['sumSuccesH']+(0.75*$generalTable1['educload'])+($generalTable1['peremen']), 2) ?></td>
        <td><?= $generalTable1['stavka']*1440 ?></td></tr>
</table>

<table class="table table-bordered">
    <tr><th colspan="11" style="background: aliceblue;"><center>Cводные данные по утвержденным часам</center></th></tr>
    <tr><th>Ставка</th><th>Учебная нагрузка</th><th>Учебно-метод. работа</th><th>Орг.-метод. работа</th>
        <th>Научно-исслед. работа</th><th>Учебно-восп. работа</th><th>Профор. работа и довузовская подготовка</th>
        <th>Перемены</th><th>Всего часов</th><th>Подтв. часов</th><th>Норма часов</th>
    </tr>
    <tr><th colspan="11"><center style="background: aliceblue;">Плановая нагрузка</center></th></tr>
    <tr style="text-align:center"><td><?= $generalTable2['stavka'] ?></td><td><?= round($generalTable2['educload'], 2) ?>(ак) <br /> <?= round(0.75*$generalTable2['educload'],2) ?>(ас)</td>
        <td><?= $generalTable2['educmethload'] ?></td><td><?= $generalTable2['orgmethload'] ?></td>
        <td><?= $generalTable2['reseachload'] ?></td><td><?= $generalTable2['educationalload'] ?></td><td><?= $generalTable2['profworkload'] ?></td>
        <td><?= ($generalTable2['peremen']) ?></td><td><?= round($generalTable2['sumH']+(0.75*$generalTable2['educload'])+($generalTable2['peremen']), 2) ?></td>
        <td><?= round($generalTable2['sumSuccesH']+(0.75*$generalTable2['educload'])+($generalTable2['peremen']), 2) ?></td>
        <td><?= $generalTable2['stavka']*1440 ?></td></tr>
    <tr><th colspan="11"><center style="background: aliceblue;">Фактическая нагрузка</center></th></tr>
    <tr style="text-align:center"><td><?= $generalTable3['stavka'] ?></td><td><?= round($generalTable3['educload'], 2) ?>(ак) <br /> <?= round(0.75*$generalTable3['educload'],2) ?>(ас)</td>
        <td><?= $generalTable3['educmethload'] ?></td><td><?= $generalTable3['orgmethload'] ?></td>
        <td><?= $generalTable3['reseachload'] ?></td><td><?= $generalTable3['educationalload'] ?></td><td><?= $generalTable3['profworkload'] ?></td>
        <td><?= ($generalTable3['peremen']) ?></td><td><?= round($generalTable3['sumH']+(0.75*$generalTable3['educload'])+($generalTable3['peremen']), 2) ?></td>
        <td><?= round($generalTable3['sumSuccesH']+(0.75*$generalTable3['educload'])+($generalTable3['peremen']), 2) ?></td>
        <td><?= $generalTable3['stavka']*1440 ?></td></tr>
</table>

<div class="modal fade" id="Rate" tabindex="-1" role="dialog" >
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" id="modalContentFromJs">
                ...
            </div>
        </div>
    </div>
</div>

<script>
    var config = {
        type: 'bar',
        data: {
            datasets: [{
                borderColor: window.chartColors.red,
                borderWidth: 1,
                data: [
                    <?= round($generalTable['sumSuccesH']+(0.75*$generalTable['educload'])+($generalTable['stavka']*60), 2) ?
                    round($generalTable['sumSuccesH']+(0.75*$generalTable['educload'])+($generalTable['stavka']*60), 2) : 0; ?>,
                ],
                backgroundColor: [
                    window.chartColors.red,
                ],
                label: 'Общая плановая нагрузка за год'
            },
                {
                    borderColor: window.chartColors.red,
                    borderWidth: 1,
                    data: [
                        <?= $totalLoad['actual'] ? $totalLoad['actual'] : 0; ?>
                    ],
                    backgroundColor: [
                        window.chartColors.green
                    ],
                    label: 'Общая фактическая нагрузка за год'
                }],
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
                text: 'Общая информация о нагрузке',
                fontSize: 14,
            },
            animation: {
                animateScale: true,
                animateRotate: true
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    };

    var ctx = document.getElementById("chart-area").getContext("2d");
    window.myDoughnut = new Chart(ctx, config);
    $(document).on('change', '.js-update-rate', function() {
        var $select = $(this);
        var parent = $(this);
        var value = $select.val();
        var fnpp = <?= Yii::app()->session['fnpp']; ?>;
        var year = <?= Yii::app()->session['yearEdu']; ?>;
        var chair = <?= Yii::app()->session['chairNpp']; ?>;
        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/default/addRate'); ?>",
            type: "post",
            dataType: "json",
            data: 'value='+value+'&fnpp='+fnpp+'&year='+year+'&chair='+chair,
            'success' : function(responce) {
                if (responce.success) {
                    parent.css({background:'#00FF00'});
                    parent.css({opacity: 0.8});
                    setTimeout(function(){
                        parent.css({background :'#f5f5f5'});
                        parent.css({opacity: 1.0});
                    }, 1000);
                } else {
                    $select.css({background:'#FF0000'});
                    $select.css({opacity: 0.8});
                    setTimeout(function(){
                        $select.val($select.data('prev'));
                        $select.css({background :'#f5f5f5'});
                        $select.css({opacity: 1.0});
                    }, 1000);
                }
            }
        });
    });

    $(window).load(function (){ if(<?= (empty($rate['rate'])?1:0) ?> == 1){modalFirstShow()}else{
        if (<?= (!empty($rate['rate'])?$rate['rate']:0) ?> != <?= $rate['stavka'] ?>) {modalShow();}
    }
    checkRate();
    });

    $("#Rate").on("hidden.bs.modal", function () {
        checkRate()
    });

    function  checkRate(){
        var fnpp = <?= Yii::app()->session['fnpp']; ?>;
        var year = <?= Yii::app()->session['yearEdu']; ?>;
        var chair = <?= Yii::app()->session['chairNpp']; ?>;
        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/default/checkRate'); ?>",
            type: 'post',
            dataType: 'json',
            data: 'fnpp='+fnpp+'&year='+year+'&chair='+chair,
            success : function(responce) {
                if (responce.success) {
                    $('.bs-docs-sidebar').css({display:'unset'});
                    $('.col-md-12').addClass('col-md-9');
                    $('.col-md-9').removeClass('col-md-12');
                }else{
                    $('.bs-docs-sidebar').css({display:'none'});
                    $('.col-md-9').addClass('col-md-12');
                    $('.col-md-12').removeClass('col-md-9');
                }
            },
            error:function() {
                $.notify({
                    // options
                    icon: 'glyphicon glyphicon-warning-sign',
                    title: 'Ошибка!',
                    message: 'При загрузке данных произошла ошибка.',
                    newest_on_top: true
                },{
                    type: 'danger',
                    icon_type: 'class',
                    delay: 2000
                });
            }
        });
    }

    function modalShow() {
            var fnpp = <?= Yii::app()->session['fnpp']; ?>;
            var year = <?= Yii::app()->session['yearEdu']; ?>;
            var chair = <?= Yii::app()->session['chairNpp']; ?>;
            $('#modalContentFromJs').html('');
            $('#myModalLabel').html('');
            var $url = "<?= Yii::app()->createAbsoluteUrl('individualplan/default/getFormRate'); ?>";

            $.ajax({
                url: $url,
                type: 'post',
                dataType: 'json',
                data: 'fnpp='+fnpp+'&year='+year+'&chair='+chair,
                success : function(responce) {
                    if (responce.success) {
                        $('#modalContentFromJs').html(responce.success);
                        $('#myModalLabel').html('Сведения о назначениях и перемещениях за отчётный период');
                        $('#Rate').modal('show');
                        /*{backdrop: 'static', keyboard: false}*/
                    }
                },
                error:function() {
                    $.notify({
                        // options
                        icon: 'glyphicon glyphicon-warning-sign',
                        title: 'Ошибка!',
                        message: 'При загрузке данных произошла ошибка.',
                        newest_on_top: true
                    },{
                        type: 'danger',
                        icon_type: 'class',
                        delay: 2000
                    });
                }
            });
    }

    function modalFirstShow() {
        var fnpp = <?= Yii::app()->session['fnpp']; ?>;
        var year = <?= Yii::app()->session['yearEdu']; ?>;
        var chair = <?= Yii::app()->session['chairNpp']; ?>;
        $('#modalContentFromJs').html('');
        $('#myModalLabel').html('');
        var $url = "<?= Yii::app()->createAbsoluteUrl('individualplan/default/getFormFirstRate'); ?>";

        $.ajax({
            url: $url,
            type: 'post',
            dataType: 'json',
            data: 'fnpp='+fnpp+'&year='+year+'&chair='+chair,
            success : function(responce) {
                if (responce.success) {
                    $('#modalContentFromJs').html(responce.success);
                    $('#myModalLabel').html('Сведения о назначениях и перемещениях за отчётный период');
                    $('#Rate').modal({backdrop: 'static', keyboard: false});
                }
            },
            error:function() {
                $.notify({
                    // options
                    icon: 'glyphicon glyphicon-warning-sign',
                    title: 'Ошибка!',
                    message: 'При загрузке данных произошла ошибка.',
                    newest_on_top: true
                },{
                    type: 'danger',
                    icon_type: 'class',
                    delay: 2000
                });
            }
        });
    }

    $(document).on('click', '.js-modal-addRow', function() {
        var elements = document.getElementsByClassName('modal-hidden-row');
        if(elements.length > 0){
            var input = elements[0];
            input.removeAttribute('hidden');
            input.classList.remove('modal-hidden-row');
        }else{
            $.notify({
                icon: 'glyphicon glyphicon-warning-sign',
                title: 'Ошибка!',
                message: 'Вы пытаетесь добавить слишком много полей',
                newest_on_top: true
            },{
                type: 'danger',
                icon_type: 'class',
                delay: 2000,
                placement: {
                    from: 'bottom',
                    align: 'center'
                },
                offset: {
                    y: 80
                }
            });
        }
    });

    $(document).on('click', '.js-modal-save', function() {
        var $select = $(this);
        var parent = $(this).parent().parent().parent();
        var rate = parent.find('.rate').val();
        var date = parent.find('.date').val();
        var dateend = parent.find('.dateend').val();
        var typerate1 = parent.find('.rowtypeRate1').prop('checked');
        var typerate2 = parent.find('.rowtypeRate2').prop('checked');
        var fnpp = <?= Yii::app()->session['fnpp']; ?>;
        var year = <?= Yii::app()->session['yearEdu']; ?>;
        var chair = <?= Yii::app()->session['chairNpp']; ?>;
        var typerate=1;
        if(typerate1){typerate=1;}
        if(typerate2){typerate=2;}
        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/default/addmodalrate'); ?>",
            type: "post",
            dataType: "json",
            data: 'fnpp='+fnpp+'&year='+year+'&chair='+chair+'&rate='+rate+'&date='+date+'&dateend='+dateend+'&typerate='+typerate,
            'success' : function(responce) {
                if (responce.success) {
                    $select.removeClass('js-modal-save');
                    $select.prop("disabled", true);
                    parent.css({background:'#00FF00'});
                    parent.css({opacity: 0.8});
                    setTimeout(function(){
                        parent.css({background :'#fff'});
                        parent.css({opacity: 1.0});
                    }, 1000);
                    $('#Rate').modal('hide');
                }
            }
        });
    });


    $(document).on('click', '.js-modal-update', function() {
        var $select = $(this);
        var parent = $(this).parent().parent().parent();
        var rate = parent.find('.rate').val();
        var date = parent.find('.date').val();
        var dateend = parent.find('.dateend').val();
        var id = parent.find('.id').val();
        var typerate1 = parent.find('.rowtypeRate1').prop('checked');
        var typerate2 = parent.find('.rowtypeRate2').prop('checked');
        var fnpp = <?= Yii::app()->session['fnpp']; ?>;
        var year = <?= Yii::app()->session['yearEdu']; ?>;
        var chair = <?= Yii::app()->session['chairNpp']; ?>;
        $select.prop("disabled", true);
        var typerate=1;
        if(typerate1){typerate=1;}
        if(typerate2){typerate=2;}
        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/default/updatemodalrate'); ?>",
            type: "post",
            dataType: "json",
            data: 'fnpp='+fnpp+'&year='+year+'&chair='+chair+'&id='+id+'&rate='+rate+'&date='+date+'&dateend='+dateend+'&typerate='+typerate,
            'success' : function(responce) {
                if (responce.success) {
                    parent.css({background:'#00FF00'});
                    parent.css({opacity: 0.8});
                    setTimeout(function(){
                        parent.css({background :'#fff'});
                        parent.css({opacity: 1.0});
                    }, 1000);
                    $select.prop("disabled", false);
                }
            }
        });

    });

</script>


