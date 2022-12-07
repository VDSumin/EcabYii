<?php
/* @var $this DefaultController */
/* @var $row Fdata  */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Список кафедры'
];

?>

<script>
    function FilterDebt() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("Debt");
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
</script>

<h1>Список кафедры</h1>
Здесь отображаются сотрудники вашей кафедры.
<br />
<?php if(ChairClass::getMyStruct() != null){ echo '<br/>'.CHtml::link('Назначить ответственных', ['/remote/chair/acting'], ['class' => 'btn btn-default']);} ?>
<hr />
<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
    <tr>
        <th>ФИО</th>
        <th style='align-content: center; width: 20%'>Выложено заданий / из</th>
        <th style='align-content: center; width: 10%'><input type="text" id="Debt" onkeyup="FilterDebt()" placeholder="Долг" title="Поиск по наличию долгов" class="form-control"></th>
        <th style='align-content: center; width: 10%'>Действия</th>
    </tr>
    <?php

    foreach ($list as $row){
        $taskData = ChairClass::getPersonTask($row->npp, $chair);
        if($taskData['all'] == 0){continue;}
        echo "<tr style='border-bottom: 1px solid darkgray;'>";
//        echo "<td style='padding:10px;'>". (($actingList[$row->npp] instanceof RemoteRights)?"<b>". $row->getFIO() . "</b>" : $row->getFIO()) ."</td>";
        echo "<td style='padding:10px;'>".$row->getFIO()."</td>";
        echo "<td style='text-align: center; padding:10px;'>".$taskData['success']." / ". $taskData['all'] ."</td>";
        echo "<td style='text-align: center; padding:10px;'>".(($taskData['success'] == $taskData['all'])?'Нет':'<b>Да</b>') ."</td>";
        echo "<td style='text-align: center; padding:10px;'>"
            .CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Просмотр\' class=\'glyphicon glyphicon-search\'>',
                ['/remote/chair/PersonList', 'id' => $row->npp])." ";
        if(false) { //ChairClass::getMyStruct() != null
            if ($actingList[$row->npp] instanceof RemoteRights) {
                echo CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Убрать права исполняющего\' class=\'glyphicon glyphicon-minus-sign\' style="color: red">',
                    ['/remote/chair/acting_chiefPerson', 'fnpp' => $row->npp]);
            } else {
                echo CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Назначить исполняющим\' class=\'glyphicon glyphicon-plus-sign\'>',
                    ['/remote/chair/acting_chiefPerson', 'fnpp' => $row->npp]);
            }
        }
        echo "</td>";
        echo "</tr>";
    }
    ?>
</table>
