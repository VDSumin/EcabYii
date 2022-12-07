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
$this->pageTitle=Yii::app()->name .' - '. $titlepage;
$this->breadcrumbs=$breadcrumbs;?>

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
            <a href=<?php echo Yii::app()->createUrl('individualplan/load/educmethLoad')?>>
                <span aria-hidden="true">Отобразить меню</span>
            </a>
        </li>
    </ul>
</nav>
<?php
endif;
?>
<div class="alert alert-warning" role="alert" align="center"><b>Вся нагрузка заполняется в астрономических часах.</b></div>
<h1><?= $titlepage ?> <br /> <?= $person ? $person->fam . ' ' . $person->nam . ' ' . $person->otc : 'Не определен'?></h1>
<h3>по кафедре "<?= Catalog::model()->findByPk(Yii::app()->session['chairNrec'])->name; ?>"</h3>
<h4>Сумма заполненных часов по выбранному разделу: План - <b id="summInSection1"><?= LoadController::summInSection($idFromCatalog, 1) ?></b>,
    Факт - <b id="summInSection2"><?= LoadController::summInSection($idFromCatalog, 2) ?></b></h4>
<div>
<? if(count($listWork)>0): ?>
    <table class="table table-striped table-bordered table-hover" style="text-align: center; vertical-align: middle">
        <thead>
        <tr><th width="3%">№</th><th width="40%">Вид работы</th>
            <!--<th>Нормы времени в часах</th> -->
            <!--<th>Отчетность</th> -->
            <th width="10%">Плановый показатель, час.</th>
            <th width="10%">Подтвержденный плановый показатель, час.</th>
            <th width="10%">Фактический показатель, час.</th>
            <th width="10%">Подтвержденный фактический показатель, час.</th>
            <th width="10%">Выбрать подтверждающие документы</th></tr>

        </thead>
        <tbody>
        <?php
            foreach ($listWork as $k => $row) {
                $titletext = (($row['timeNorms'] != "")?"Нормы времени в часах: ".$row['timeNorms'].";":"").(($row['ReportingForm'] != "")?" Отчетность: ".$row['ReportingForm'].";":"");
                $titletext = str_replace("<br />", "", $titletext);
                echo '<tr><td>' . ($k + 1) . '</td>'
                    .'<td><span rel="tooltip" title="'.$titletext.'" data-toggle="tooltip" >' . $row['name'] . '</span></td>'
                    //.'<td>' . $row['timeNorms'] . '</td>'
                    //.'<td>' . $row['ReportingForm'] . '</td>'
                    .'<td><div class="form-group ' . (($row['status'] == 1 && $row['hours'] == $row['correctHours']) ? "has-success" :
                        (($row['status'] == 2 || ($row['status'] == 1 && $row['hours'] != $row['correctHours'])) ? "has-error" : "")) . '">
                    <input type="hidden" class="id" value="' . $row['id'] . '"><input type="hidden" class="kind" value="1">
                    <input ' . (($row['isBlock'] || !$CanWriteArray['openplan']) ? 'disabled="disabled"' : '') . ' style="text-align: center;" type="text" class="js-update-hoursplan form-control" value="' . $row['hours'] . '">'
                    .(($row['status'] == 1 && $row['hours'] == $row['correctHours']) ? '' : //'<label class="control-label">Подтверждено</label>'
                        (($row['status'] == 2 || ($row['status'] == 1 && $row['hours'] != $row['correctHours'])) ? '<label class="control-label">Отклонено</label>' : ''))
                    .'</div></td>'
                    .'<td>'.$row['correctHours'].(($row['status'])?'<br /><b style="color: #3c763d">Подтверждено</b>':'').'</td>'

                    .'<td><div class="form-group ' . (($row['fstatus'] == 1 && $row['fhours'] == $row['fcorrectHours']) ? "has-success" :
                        (($row['fstatus'] == 2 || ($row['fstatus'] == 1 && $row['fhours'] != $row['fcorrectHours'])) ? "has-error" : "")) . '">
                    <input type="hidden" class="id" value="' . $row['id'] . '"><input type="hidden" class="kind" value="2">
                    <input ' . (($row['fisBlock'] || !$CanWriteArray['openfact']) ? 'disabled="disabled"' : '') . ' style="text-align: center;" type="text" class="js-update-hoursplan form-control" value="' . $row['fhours'] . '">'
                    .(($row['fstatus'] == 1 && $row['fhours'] == $row['fcorrectHours']) ? '' :
                        (($row['fstatus'] == 2 || ($row['fstatus'] == 1 && $row['fhours'] != $row['fcorrectHours'])) ? '<label class="control-label">Отклонено</label>' : ''))
                    .'</div></td>'

                    .'<td>'.$row['fcorrectHours'].(($row['fstatus'])?'<br /><b style="color: #3c763d">Подтверждено</b>':'').'</td>'
                    .'<td style="vertical-align: middle;"><button style="'.(($row['fhours']>0 || $row['fcorrectHours']>0)?'':'visibility: hidden;').'" class="btn btn-primary modalWin" data-toggle="modal">
                    <span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Выбрать подтверждающие документы" class="glyphicon glyphicon-th-large"></button>'
                    .'<input type="hidden" class="confirm_type" value="'.$row['cconfirm'].'">'
                    .'</td>'
                    .'</tr>';
            }
        ?>
        </tbody>
    </table>
<? else:
    echo '<center><h3>Отсутствует доступная для заполнения нагрузка за выбранный учебный год.</h3></center>';
 endif; ?>
</div>

<div class="modal fade" id="Confirm" tabindex="-1" role="dialog" >
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" id="modalContentFromJs">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('change', '.js-update-hoursplan', function() {
        var $select = $(this);
        var parent = $(this);
        var value = $select.val();
        var id = $select.parent().find('.id').val();
        var kind = $select.parent().find('.kind').val();
        var fnpp = <?= Yii::app()->session['fnpp']; ?>;
        var year = <?= Yii::app()->session['yearEdu']; ?>;
        var chair = <?= Yii::app()->session['chairNpp']; ?>;
        var elemsummInSection = document.getElementById('summInSection'+kind);

        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/load/addhoursplan'); ?>",
            type: "post",
            dataType: "json",
            data: 'value='+value+'&id='+id+'&kind='+kind+'&fnpp='+fnpp+'&year='+year+'&chair='+chair+'&idCatalog='+<?= $idFromCatalog ?>,
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
                    //if(kind == 2 && value != '' && value != '0'){$select.parent().parent().parent().find('.modalWin').prop("disabled", false);}
                    if(kind == 2 && value != '' && value != '0'){$select.parent().parent().parent().find('.modalWin').css({visibility: 'visible'});}
                    if(kind == 2 && (value == '' || value == '0')){$select.parent().parent().parent().find('.modalWin').css({visibility: 'hidden'});}
                    //if(kind == 2 && (value == '' || value == '0')){$select.parent().parent().parent().find('.modalWin').prop("disabled", true);}
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

    $(document).on('click', '.modalWin', function(e) {
        e.preventDefault();
        $('#modalContentFromJs').html('');
        $('#myModalLabel').html('');
        var $select = $(this);
        var $url = '';
        btn = $select;
        var id = $select.parent().parent().find('.id').val();
        var kind = $select.parent().parent().find('.kind').val();
        var confirm_id = $select.parent().find('.confirm_type').val();
        var fnpp = <?= Yii::app()->session['fnpp']; ?>;
        var year = <?= Yii::app()->session['yearEdu']; ?>;
        var chair = <?= Yii::app()->session['chairNpp']; ?>;
        var $url = "<?= Yii::app()->createAbsoluteUrl('individualplan/load/getFormConfirm'); ?>";
        $select.prop("disabled", true);

        $.ajax({
            url: $url,
            type: 'post',
            dataType: 'json',
            data: 'id='+id+'&kind='+kind+'&fnpp='+fnpp+'&year='+year+'&chair='+chair+'&confirm='+confirm_id+'&idCatalog='+<?= $idFromCatalog ?>,
            success : function(responce) {
                if (responce.success) {
                    $('#modalContentFromJs').html(responce.success);
                    $('#myModalLabel').html(responce.title);
                    $('#Confirm').modal('show');
                    $select.prop("disabled", false);
                } else {
                    $select.addClass('btn-danger');
                    setTimeout(function(){
                        $select.removeClass('btn-danger');
                        $select.css({opacity: 1});
                    }, 2000);
                    $select.prop("disabled", false);
                }
            },
            error:function() {
                $.notify({
                    // options
                    icon: 'glyphicon glyphicon-warning-sign',
                    title: 'Ошибка!',
                    message: 'При загрузке данных произошла ошибка. Попробуйте позже!',
                    newest_on_top: true
                },{
                    // settings
                    type: 'danger',
                    icon_type: 'class',
                    delay: 2000
                });
                $select.addClass('btn-danger');
                setTimeout(function(){
                    $select.removeClass('btn-danger');
                    $select.css({opacity: 1});
                }, 2000);
                $select.prop("disabled", false);
            }
        });

    });


    $(document).on('change', '.text_comments', function() {
        var $select = $(this);
        var val = $select.val();
        var main_id_confirm = document.getElementById('main_id_confirm').value;

        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/load/addmodalrate'); ?>",
            type: 'post',
            dataType: 'json',
            data: 'id_conf='+main_id_confirm+'&val='+val,
            success : function(responce) {
                if (responce.success) {
                    $select.addClass('btn-success');
                    setTimeout(function(){
                        $select.removeClass('btn-success');
                        $select.css({opacity: 1});
                    }, 2000);
                    $select.prop("disabled", false);
                } else {
                    $select.addClass('btn-danger');
                    setTimeout(function(){
                        $select.removeClass('btn-danger');
                        $select.css({opacity: 1});
                    }, 2000);
                    $select.prop("disabled", false);
                }
            },
            error:function() {
                $select.addClass('btn-danger');
                setTimeout(function(){
                    $select.removeClass('btn-danger');
                    $select.css({opacity: 1});
                }, 2000);
                $select.prop("disabled", false);
            }
        });

    });


    $(document).on('click', '.add_modal_text', function() {
        var $select = $(this);
        var field = document.getElementsByClassName('modal_div_text');
        field[0].style.display = 'unset';
        $select.prop("disabled", true);
        $select.css({background: 'lightgray'});

    });

    $(document).on('change', '.change_text', function() {
        var $select = $(this);
        var val = $select.val();
        var main_id_confirm = document.getElementById('main_id_confirm').value;
        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/load/addmodaltext'); ?>",
            type: 'post',
            dataType: 'json',
            data: 'id_conf='+main_id_confirm+'&val='+val,
            success : function(responce) {
                if (responce.success) {
                    $select.addClass('btn-success');
                    setTimeout(function(){
                        $select.removeClass('btn-success');
                        $select.css({opacity: 1});
                    }, 2000);
                    $select.prop("disabled", false);
                } else {
                    $select.addClass('btn-danger');
                    setTimeout(function(){
                        $select.removeClass('btn-danger');
                        $select.css({opacity: 1});
                    }, 2000);
                    $select.prop("disabled", false);
                }
            },
            error:function() {
                $select.addClass('btn-danger');
                setTimeout(function(){
                    $select.removeClass('btn-danger');
                    $select.css({opacity: 1});
                }, 2000);
                $select.prop("disabled", false);
            }
        });
    });

    $(document).on('click', '.add_modal_links', function() {
        var $select = $(this);
        var field = document.getElementsByClassName('modal_div_links');
        field[0].style.display = 'unset';
        var main_id_confirm = document.getElementById('main_id_confirm').value;
        var newf = $('.new_links_elem');
        newf.append('<div class="input-group add-on">' +
            '<input type="hidden" class="id_link">' +
            '<input type="hidden" class="id_confirm" value="'+main_id_confirm+'">' +
            '<input type="text" class="form form-control text_link" placeholder="добавьте ссылку" style="padding: 3px 12px; margin: 3px 0px; height: auto;">' +
            '<div class="input-group-btn" data-dismiss="alert">' +
            '<button class="btn btn-default del_text_link" style="padding: 3px 12px; margin: 3px 0px;">x</button>' +
            '</div>' +
            '</div>');
    });

    $(document).on('change', '.text_link', function() {
        var $select = $(this);
        var id = $select.parent().find('.id_link');
        var id_val = $select.parent().find('.id_link').val();
        var val = $select.val();
        var id_conf = $select.parent().find('.id_confirm').val();
        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/load/addmodallink'); ?>",
            type: 'post',
            dataType: 'json',
            data: 'id='+id_val+'&val='+val+'&id_conf='+id_conf,
            success : function(responce) {
                if (responce.success) {
                    $select.addClass('btn-success');
                    setTimeout(function(){
                        $select.removeClass('btn-success');
                        $select.css({opacity: 1});
                    }, 2000);
                    $select.prop("disabled", false);
                    id.val(responce.id_val);
                } else {
                    $select.addClass('btn-danger');
                    setTimeout(function(){
                        $select.removeClass('btn-danger');
                        $select.css({opacity: 1});
                    }, 2000);
                    $select.prop("disabled", false);
                }
            },
            error:function() {
                $select.addClass('btn-danger');
                setTimeout(function(){
                    $select.removeClass('btn-danger');
                    $select.css({opacity: 1});
                }, 2000);
                $select.prop("disabled", false);
            }
        });
    });

    $(document).on('click', '.del_text_link', function() {
        var $select = $(this);
        var id = $select.parent().parent().find('.id_link').val();
        var item = $select.parent().parent();
        if(id == null){
            item.remove();
        }else{
            $.ajax({
                url: "<?= Yii::app()->createAbsoluteUrl('individualplan/load/delmodallink'); ?>",
                type: 'post',
                dataType: 'json',
                data: 'id='+id,
                success : function(responce) {
                    if (responce.success) {
                        $select.addClass('btn-success');
                        setTimeout(function(){
                            $select.removeClass('btn-success');
                            $select.css({opacity: 1});
                        }, 2000);
                        item.remove();
                    } else {
                        $select.addClass('btn-danger');
                        setTimeout(function(){
                            $select.removeClass('btn-danger');
                            $select.css({opacity: 1});
                        }, 2000);
                        $select.prop("disabled", false);
                    }
                },
            });
        }
    });


    $(document).on('click', '.add_modal_files', function() {
        var $select = $(this);
        var field = document.getElementsByClassName('modal_div_files');
        field[0].style.display = 'unset';
        var main_id_confirm = document.getElementById('main_id_confirm').value;
        var newf = $('.new_file_elem');
        newf.append('<tr><td><input type="text" class="form form-control file_name"></td>' +
            '<td class="preview_file"><center>(Максимальный размер 10Мб)</center></td>' +
            '<td>' +
            '<center>' +
            '<input class="inputFile" type="file" style="display: none;" name="new_file">' +
            '<button class="btn btn-default add_modal_confirm_files">' +
            '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Добавить файл" class="glyphicon glyphicon-plus">' +
            '</button> ' +
            '<button class="btn btn-danger delete_modal_field" >' +
            '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Удалить" class="glyphicon glyphicon-remove">' +
            '</button>' +
            '</center>' +
            '</td></tr>');


    });

    $(document).on('click', '.add_modal_confirm_files', function() {
        var name = $(this).parent().parent().parent().find('.file_name');
        var name_val = $(this).parent().parent().parent().find('.file_name').val();
        if(name_val.length < 2) {
            name.addClass('btn-warning');
            setTimeout(function(){
                name.removeClass('btn-warning');
            }, 1000);
        }else{
            var input = $(this).parent().find('.inputFile').click();
        }
    });

    $(document).on('change', '.inputFile', function() {
        var $select = $(this);
        var name_val = $(this).parent().parent().parent().find('.file_name').val();
        var main_id_confirm = document.getElementById('main_id_confirm').value;
        var pr_file = $(this).parent().parent().parent().find('.preview_file');
        var row = $(this).parent().parent().parent();

        var fd = new FormData();
        fd.append('file', this.files[0]);
        fd.append('fieldname', name_val);
        fd.append('cconfirm', main_id_confirm);
        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/load/addmodalfile'); ?>",
            type: 'post',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: fd,
            /*xhr: function(){
                var xhr = $.ajaxSettings.xhr();
                xhr.upload.addEventListener('progress', function(evt){
                    if(evt.lengthComputable) { // если известно количество байт
                        var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
                        pr_file.html('<div></div>');
                        pr_file.children('div').stop();
                        pr_file.children('div').html(percentComplete + '%');
                        pr_file.children('div').animate({width: percentComplete+'%'}, 1000);
                        if(percentComplete==100) pr_file.children('div').html('<i>обрабатываем файл...</i>');
                    }
                }, false);
                return xhr;
            },*/
            success : function(responce) {
                if (responce.success) {
                    $select.addClass('btn-success');
                    setTimeout(function(){
                        $select.removeClass('btn-success');
                        $select.css({opacity: 1});
                    }, 2000);
                    //row.remove();
                    row.children('td').remove();
                    row.append('<td>'+ responce.file_info['name'] +'</td>' +
                        '<td class="preview_file"><center>'+ responce.file_info['size'] +'</center></td>' +
                        '<td>' +
                        '<center>' +
                        '<input type="hidden" class="id_file" value="'+responce.file_info['id']+'">' +
                        '<a target="_blank" href="' + '<?= Yii::app()->createAbsoluteUrl('/individualplan/load/viewfile'); ?>' + '&id='+responce.file_info['id']+'&confirm='+main_id_confirm+'" >' +
                        '<button class="btn btn-info open_modal_file">' +
                        '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Файл" class="glyphicon glyphicon-file">' +
                        '</button></a> ' +
                        '<button class="btn btn-danger delete_modal_file">' +
                        '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Удалить" class="glyphicon glyphicon-remove">' +
                        '</button>' +
                        '</center>' +
                        '</td>')
                } else {
                    $select.parent().find('.add_modal_confirm_files').addClass('btn-danger');
                    pr_file.html('<center>'+responce.main_err+'</center>');
                }
            },
        });

    });


    $(document).on('click', '.delete_modal_file', function() {
        var $select = $(this);
        var id = $select.parent().find('.id_file').val();
        var main_id_confirm = document.getElementById('main_id_confirm').value;
        var row = $(this).parent().parent().parent();

        $.ajax({
            url: "<?= Yii::app()->createAbsoluteUrl('individualplan/load/deletemodalfile'); ?>",
            type: 'post',
            dataType: 'json',
            data: 'id='+id+'&cconfirm='+main_id_confirm,

            success : function(responce) {
                if (responce.success) {
                    $select.addClass('btn-success');
                    setTimeout(function(){
                        $select.removeClass('btn-success');
                        $select.css({opacity: 1});
                    }, 2000);
                    row.remove();

                }
            },
        });

    });


    $(document).on('click', '.delete_modal_field', function() {
        var $select = $(this);
        var row = $(this).parent().parent().parent();
        row.remove();
    });



</script>