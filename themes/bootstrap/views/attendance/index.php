<?php
/* @var $this AttendanceController */
/* @var $dataProvider CActiveDataProvider */
$this->pageTitle = 'Электронный журнал посещаемости';

?>
<script>
    function FilterGroup() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("Group");
        filter = input.value.toUpperCase();
        table = document.getElementById("ListGroup");
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
</script>
<center><h1>Список доступных групп</h1></center>
<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="ListGroup">
    <tr><th style='align-content: center;padding:10px;'>Факультет</th>
        <th style='align-content: center;'><input type="text" id="Group" onkeyup="FilterGroup()" placeholder="Группа" title="Поиск по названию группы" class="form-control"></th>
        <th style='width: 10%;padding:10px;'>Заполнение</th><th style='width: 10%;padding:10px;'>Просмотр</th><th style='width: 10%;padding:10px;'>Статистика</th></tr>
<?php
foreach ($listgroup as $group){
    echo "<tr style='border-bottom: 1px solid darkgray;'>";
    echo "<td style='padding:10px;'>".$group['department']."</td>
    <td style='padding:10px;'>".$group['groupName']."</td>
    <td style='width: 10%; padding:10px;' align='center'><a href='index.php?r=attendance/getfilling&group=".$group['studGroupId']."'><span rel='tooltip' data-toggle='tooltip' title='Заполнение журнала посещаемости' class='glyphicon glyphicon-pencil'></span></a></td>
    <td style='width: 10%; padding:10px;' align='center'><a href='index.php?r=attendance/getview&group=".$group['studGroupId']."&type=0'><span rel='tooltip' data-toggle='tooltip' title='Просмотр журнала посещаемости' class='glyphicon glyphicon-file'></span></a></td>
    <td style='width: 10%; padding:10px;' align='center'><a href='index.php?r=attendance/getstatistics&group=".$group['studGroupId']."&type=0'><span rel='tooltip' data-toggle='tooltip' title='Статистика по журналу посещаемости' class='glyphicon glyphicon-list-alt'></span></a></td>";
    echo "</tr>";
}
foreach ($listgroupcur as $group){
    echo "<tr style='border-bottom: 1px solid darkgray;'>";
    echo "<td style='padding:10px;'>".$group['department']."</td>
    <td style='padding:10px;'>".$group['groupName']." (Вы куратор у этой группы)</td>
    <td style='width: 10%; padding:10px;' align='center'></td>
    <td style='width: 10%; padding:10px;' align='center'><a href='index.php?r=attendance/getview&group=".$group['id']."&type=1'><span rel='tooltip' data-toggle='tooltip' title='Просмотр журнала посещаемости' class='glyphicon glyphicon-file'></span></a></td>
    <td style='width: 10%; padding:10px;' align='center'><a href='index.php?r=attendance/getstatistics&group=".$group['id']."&type=1'><span rel='tooltip' data-toggle='tooltip' title='Статистика по журналу посещаемости' class='glyphicon glyphicon-list-alt'></span></a></td>";
    echo "</tr>";
}
?>
</table>

