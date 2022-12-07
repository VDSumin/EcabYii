<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 19.04.2020
 * Time: 11:24
 */

$this->pageTitle = Yii::app()->name . ' - Закрепление ответственных';
$this->breadcrumbs = [
    'Ведомости'=>array('/studyProcess/mark'),
    'Закрепление ответственных за ведомостью '.CHtml::value($info, 'numDoc')
];
?>

<p class="text-header" style="text-align: center; font-size: 12pt;">
    ЗАКРЕПЛЕНИЕ ОТВЕТСТВЕННЫХ ЗА ВЕДОМОСТЬЮ №<strong><ins><?php echo CHtml::value($info, 'numDoc'); ?></ins></strong><br/>
    ФАКУЛЬТЕТ (ИНСТИТУТ): <strong><ins><?php echo CHtml::value($info, 'listFacult'); ?></ins></strong>,
    Группа <strong><ins><?php echo CHtml::value($info, 'studGroup'); ?></ins></strong><br/>
    ДИСЦИПЛИНА <ins><strong><?php echo CHtml::value($info, 'discipline'); ?></strong></ins><br/>
</p>
<?php echo CHtml::hiddenField('AudHours_N[nrecList]', CHtml::value($info, 'nrec')); ?>
<br/>

<div id="existExaminer">
<?php
foreach ($listExaminer as $examiner){
    echo '<div class="jumbotron" style="padding-top: 30px;padding-bottom: 30px; margin-bottom: 10px; border: 1px black solid; text-align-last: center;">'
        .CHtml::hiddenField('examinerNrec', $examiner['nrec'])
        .CHtml::hiddenField('selectedNrec', $examiner['nrecExaminer'])
        .CHtml::dropdownList("examiner", $examiner['nrecExaminer'],
        CHtml::listData($listExaminerChair, "nrec", "fio"),
        array("prompt" => "", "class" => "updateExaminer form-control", "style" => "display: inline-block; width: 85%; text-align-last: center;"))

        .CHtml::link('<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Удалить ответственного" class="glyphicon glyphicon-trash"/>',
            '', array('class' => 'deleteExaminer btn btn-danger',
                'style' => 'margin-left: 5px; margin-bottom: 5px'))

        .CHtml::link('<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Ответственный преподаватель" class="glyphicon glyphicon-user"/>',
            '', array('class' => 'mainExam btn btn-primary',
                'style' => 'margin-left: 5px; margin-bottom: 5px;'.(($mainExaminer != $examiner['nrecExaminer'])?' display: none;':'')))
        .CHtml::link('<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Назначить ответственным" class="glyphicon glyphicon-user"/>',
            '', array('class' => 'Exam btn btn-primary',
                'style' => 'margin-left: 5px; margin-bottom: 5px; color: #337ab7;'.(($mainExaminer == $examiner['nrecExaminer'])?' display: none;':'')))
            
        .'</div>';
}
?>
</div>

<div class="add-examiner jumbotron" style="margin: 0px; cursor: pointer; background-color: #bfd4bf; border: 1px black solid;
padding-top: 20px; padding-bottom: 20px;">
    <center><h1 style="margin-bottom: 0px; "><span style="color: gray;" class="glyphicon glyphicon-plus"></span></h1></center>
</div>


<script>
    $(document).ready(function() {
        $(".updateExaminer").each(function () {
            $(this).data("value", $(this).val());
        });
    });

    $(document).on('click', '.deleteExaminer', function () {
        var selected = $(this);
        var nrec = selected.parent().find('#examinerNrec');
        var value = selected.parent().find('#examiner');

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('studyProcess/mark/deleteExaminer'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'nrec=' + nrec.val(),
            'success' : function(responce) {
                if (responce.success) {
                    notifyMessageField('Успешно',responce.successFields,'success')
                    selected.parent().css({background: 'red'});
                    selected.parent().css({opacity: 0.5});
                    setTimeout(function(){
                        selected.parent().remove();
                    }, 1000);
                }else{
                    notifyMessageField('Ошибка',responce.successFields,'danger')
                }
            }
        });
    });
    
    $(document).on('change', '.updateExaminer', function () {
        var selected = $(this);
        var nrec = selected.parent().find('#examinerNrec');
        var selectedNrec = selected.parent().find('#selectedNrec');
        var value = selected.parent().find('#examiner');
        var listNRec = document.getElementById('AudHours_N_nrecList');
        var mainExam = serchMainExaminer();

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('studyProcess/mark/updateExaminer'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'listNrec=' + listNRec.value + '&mainExam=' + mainExam.value + '&nrec=' + nrec.val() + '&Examiner=' + value.val(),
            'success' : function(responce) {
                if (responce.success) {
                    notifyMessageField('Успешно',responce.successFields,'success');
                    selected.data("value", selected.val());
                    nrec.val(responce.returnField);
                    selectedNrec.val(selected.val());
                }else{
                    notifyMessageField('Ошибка',responce.successFields,'danger');
                    selected.val(selected.data("value"))
                }
            }
        });
    });

    $(document).on('click', '.Exam', function () {
        var selected = $(this);
        var mainExam = selected.parent().find('#examiner');
        var listNRec = document.getElementById('AudHours_N_nrecList');

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('studyProcess/mark/updateExaminer'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'listNrec=' + listNRec.value + '&mainExam=' + mainExam.val(),
            'success' : function(responce) {
                if (responce.success) {
                    $('#content .mainExam').css({display: 'none'});
                    $('#content .Exam').css({display: ''});
                    selected.css({display: 'none'});
                    selected.parent().find('.mainExam').css({display: ''});
                    notifyMessageField('Успешно',responce.successFields,'success')
                }else{
                    notifyMessageField('Ошибка',responce.successFields,'danger')
                }
            }
        });
    });

    function serchMainExaminer() {
        var mainExaminers = document.getElementsByClassName('mainExam');
        for (i = 0; i < mainExaminers.length; i++) {
            if(mainExaminers[i].style.display != 'none'){
                return mainExaminers[i].parentElement.children.item(2);
            }
        }
        var Examiners = document.getElementsByClassName('Exam');
        Examiners[0].parentElement.children.item(4).style.display = '';
        Examiners[0].parentElement.children.item(5).style.display = 'none';
        return Examiners[0].parentElement.children.item(2);
    }

    $(document).on('click', '.add-examiner', function () {
        var mainArea = document.getElementById('existExaminer');
        var listNRec = document.getElementById('AudHours_N_nrecList');

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('studyProcess/mark/addExaminerField'); ?>",
            'type': 'post',
            'data': 'listNrec=' + listNRec.value,
            'dataType': 'json',
            'success' : function(responce) {
                if (responce.success) {
                    document.getElementById('existExaminer').innerHTML += responce.success;
                    refreshSelected();
                }
            }
        });
    });

    function refreshSelected() {
        $(".updateExaminer").each(function () {
             $(this).val($(this).parent().find('#selectedNrec').val());
        });
    }

    function notifyMessageField($title, $message, $color) {
        $.notify({
            title: "<center><strong><h4>" + $title + "</h4></strong></center>",
            message: "<center>" + $message + "</center>",
        }, {
            type: $color,
            delay: 5000,
            placement: {
                from: 'bottom',
                align: 'center'
            },
            offset: {
                y: 80
            }
        });
    }

</script>



