<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа'
];

Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/js/modalStatusFiles.js?2', CClientScript::POS_END);
?>

<script>
    var linkComment = <?php echo CJSON::encode(Yii::app()->createAbsoluteUrl('/studyProcess/mark/updateCommentAtFile'));?>;
    var linkUpdate = <?php echo CJSON::encode(Yii::app()->createAbsoluteUrl('/studyProcess/mark/updateStateAtFile'));?>;
    function FilterGroup() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("Group");
        filter = input.value.toUpperCase();
        table = document.getElementById("List");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    function FilterDiscipline() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("Discipline");
        filter = input.value.toUpperCase();
        table = document.getElementById("List");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    function FilterFormed() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("Formed");
        filter = input.value;
        table = document.getElementById("List");
        tr = table.getElementsByTagName("tr");
        if(filter == ''){
            for (i = 0; i < tr.length; i++) {
                tr[i].style.display = "";
            }
        }else{
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[2];
                if (td) {
                    console.log(td.innerHTML , filter);
                    if (td.innerHTML.toUpperCase() == filter.toUpperCase()) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    }
</script>

<h1>Контактная работа</h1>
Здесь отображаются дисциплины и группы, за которыми вы закреплены как преподаватель в расписании.
<br />

<?php if(ChairClass::getMyStruct(false) != null){ echo '<br/>'.CHtml::link('Загруженные работы кафедры', ['/remote/chair'], ['class' => 'btn btn-default']);} ?>
<?php if(ChairClass::getMyStruct(false) != null){ echo '  '.CHtml::link('Закрепить преподователей за дополнительными заданиями', ['/remote/chair/extraTask'], ['class' => 'btn btn-default']);} ?>
<?php if(DeanClass::getMyStruct(false) != null){ echo '<br/>'.CHtml::link('Загруженные работы для групп факультета', ['/remote/dean'], ['class' => 'btn btn-default']);} ?>
<!--<hr />
<div >РЕКОМЕНДУЕМ подключаться к системе "Мираполис" через "Яндекс.Браузер" или "Opera", чтобы избежать временных технических проблем с трансляцией звука.</div>
<hr />-->
<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
    <tr>
        <th style='align-content: center;padding:10px;'><input type="text" id="Discipline" onkeyup="FilterDiscipline()" placeholder="Дисциплина" title="Поиск по названию дисциплины" class="form-control"></th>
        <th style='align-content: center;'><input type="text" id="Group" onkeyup="FilterGroup()" placeholder="Группа" title="Поиск по названию группы" class="form-control"></th>
        <th style='align-content: center; width: 14%'>
            <select class="form-control" onchange="FilterFormed()" id="Formed" title="Поиск по форме обучения">
                <option value=""></option>
                <option value="Очная">Очная</option>
                <option value="Заочная">Заочная</option>
                <option value="Вечерняя">Вечерняя</option>
            </select>
        </th>
        <th style='align-content: center; width: 10%'>Количество заданий</th>
        <th style='align-content: center; width: 10%'>Действия</th>
    </tr>
    <?php
    foreach ($list as $row){
        echo "<tr style='border-bottom: 1px solid darkgray;'>";
        echo "<td style='padding:10px;'>".$row['discipline']
            .CHtml::hiddenField('disciplineId', $row['disciplineId'])."</td>
        <td style='padding:10px;'>".$row['group']." ".$row['extraText']
            .CHtml::hiddenField('groupId', $row['groupId']). "</td>
        <td style='padding:10px; text-align: center;'>".$row['formed']."</td>
        <td style='padding:10px; text-align: center;'>".$row['count']."</td>
        <td><h4 style='text-align: center;'>".(($row['disciplineId'] != '' and $row['groupId'] != '')?
            CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Выдать задание\' class=\'glyphicon glyphicon-plus\'>',
                ['/remote/default/giveTheTask', 'group' => $row['groupId'], 'discipline' => $row['disciplineId']])." "
            .CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Список выданных заданий\' class=\'glyphicon glyphicon-list\'>',
                ['/remote/default/taskList', 'group' => $row['groupId'], 'discipline' => $row['disciplineId']])." "
            .CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Контакты студентов\' class=\'glyphicon glyphicon-user\'>',
                '',['class'=>'studentContacts'])
            ."<br/> "
            .CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Отчётные работы'.(($row['newWorks'])?' (Есть не проверенные работы)':'').'\' class=\'glyphicon glyphicon-folder-open'
                .(($row['newWorks'])?' blink_field':'').'\'>',
                '',['class'=>'reportFiles', 'style' => (($row['newWorks'])?'color:darkorange':'')])
                :'')
            ."</h4></td>";
        echo "</tr>";
    }
    ?>
</table>

<div class="modal fade" id="myModalContacts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Контакты студентов</h4>
            </div>
            <div id="modalStudentContacts" class="modal-body">

            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.studentContacts', function(e) {
        e.preventDefault();
        var group = $(this).parent().parent().parent().find('#groupId').val();
        var $select = $(this);

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('remote/default/studentContacts'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'group=' + group,
            'success' : function(responce) {
                if (responce.success) {
                    $('#myModalContacts #myModalLabel').html('Контакты студентов');
                    $('#myModalContacts #modalStudentContacts').html(responce.success);
                    $('#myModalContacts').modal('toggle');
                }
            }
        });
    });

    $(document).on('click', '.reportFiles', function(e) {
        e.preventDefault();
        var group = $(this).parent().parent().parent().find('#groupId').val();
        var discipline = $(this).parent().parent().parent().find('#disciplineId').val();
        var semester = $(this).parent().find('#semester').val();
        var $select = $(this);

        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('remote/default/reportFiles'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'group=' + group + '&discipline='+discipline + '&semester='+semester,
            'success' : function(responce) {
                if (responce.success) {
                    $('#myModalContacts #myModalLabel').html('Отчётные работы');
                    $('#myModalContacts #modalStudentContacts').html(responce.success);
                    $('#myModalContacts').modal('show');
                }
            }
        });
    });
    (function blink() {
        $(".blink_field").fadeOut(1500).fadeIn(1500, blink);
    })();
</script>
<style>
    .dtime{
        text-align: center;
        font-size: 12px;
    }
</style>