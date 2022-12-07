<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Список кафедры' => array('/remote/chair'),
    Fdata::model()->findByPk($person)->getFIO()
];

?>

<script>
    function FilterGroup() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("Group");
        filter = input.value.toUpperCase();
        table = document.getElementById("List");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[2];
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
                td = tr[i].getElementsByTagName("td")[3];
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

<h1>Дисциплины преподавателя</h1>
Здесь отображаются дисциплины из раписания сотрудника: <?= Fdata::model()->findByPk($person)->getFIO() ?>.
<hr />
<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
    <tr>
        <th style='align-content: center;padding:10px;'><input type="text" id="Discipline" onkeyup="FilterDiscipline()" placeholder="Дисциплина" title="Поиск по названию дисциплины" class="form-control"></th>
        <th style='align-content: center; width: 30%'>Преподаватели</th>
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
        <th style='align-content: center; width: 10%'>Задание</th>
    </tr>
    <?php
    foreach ($list as $row){
        echo "<tr style='border-bottom: 1px solid darkgray;'>";
        echo "<td style='padding:10px;'>".$row['discipline']."</td>
        <td>".$row['examiners']."</td>
        <td style='padding:10px;'>".$row['group']."</td>
        <td style='padding:10px; text-align: center;'>".$row['formed']."</td>
        <td style='padding:10px; text-align: center;'>".$row['count']."</td>
        <td style='padding:10px;'><h4 style='text-align: center;'>".(($row['disciplineId'] != '' and $row['groupId'] != '')?
            CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Выдать задание\' class=\'glyphicon glyphicon-plus\'>',
                ['giveTheTask', 'fnpp' => $person, 'group' => $row['groupId'], 'discipline' => $row['disciplineId']])." "
            .CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Просмотреть выданные задания\' class=\'glyphicon glyphicon-list\'>',
                ['taskList', 'fnpp' => $person, 'group' => $row['groupId'], 'discipline' => $row['disciplineId']]):'')
            ."</h4></td>";
        echo "</tr>";
    }
    ?>
</table>
