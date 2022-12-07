<?php
/* @var $this DefaultController */
$TimeF=1;
$TimeT=0;
$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа'
];

?>

<script>
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
</script>

<?php
if (ReadController::checkAccessPastTask(Yii::app()->user->getFnpp())){
    echo '<div class="tab">
    <button class="tablinks btn btn-primary" id="btn-Now" onclick="openSchedule(event, \'Now\')">Текущее расписание</button>
    <button class="tablinks btn btn-default" id="btn-Recent" onclick="openSchedule(event, \'Recent\')">Предыдущее расписание</button>
</div>';
}
?>
<h1>Контактная работа</h1>
<div id="Now" class="tabcontent">
Здесь отображаются дисциплины из вашего раписания.
<br />

<!--<hr />
<div >РЕКОМЕНДУЕМ подключаться к системе "Мираполис" через "Яндекс.Браузер" или "Opera", чтобы избежать временных технических проблем с трансляцией звука.</div>
<hr />-->

<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
    <tr>
        <th style='align-content: center;padding:10px;'><input type="text" id="Discipline" onkeyup="FilterDiscipline()" placeholder="Дисциплина" title="Поиск по названию дисциплины" class="form-control"></th>
        <th style='align-content: center; width: 30%'>Преподаватели</th>
        <th style='align-content: center; width: 10%'>Количество заданий</th>
        <th style='align-content: center; width: 10%'>Задание</th>
    </tr>
    <?php
    foreach ($list as $row){
        echo "<tr style='border-bottom: 1px solid darkgray;'>";
        echo "<td style='padding:10px;'>".$row['discipline'].((in_array($row['disciplineId'],
                ['8001000000001D17','8001000000001802']))?" <br/><b>(Для получения заданий свяжитесь со своим командиром взвода)</b>":"")."</td>
        <td>".$row['examiners']."</td>
        <td style='padding:10px; text-align: center;'>".$row['count']."</td>
        <td style='padding:10px;'><h4 style='text-align: center;'>".(($row['disciplineId'] != '')?
                CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Показать задания\' class=\'glyphicon glyphicon-search\'>',
                ['/remote/read/taskList', 'discipline' => $row['disciplineId'],'time'=>$TimeT]):'')
            ."</h4></td>";
        echo "</tr>";
    }
    ?>
</table>
</div>

<div id="Recent" class="tabcontent" style=" display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;">
    Здесь отображаются дисциплины из вашего прошлого раписания.
    <br />
    <table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
        <tr>
            <th style='align-content: center;padding:10px;'><input type="text" id="Discipline" onkeyup="FilterDiscipline()" placeholder="Дисциплина" title="Поиск по названию дисциплины" class="form-control"></th>
            <th style='align-content: center; width: 30%'>Преподаватели</th>
            <th style='align-content: center; width: 10%'>Количество заданий</th>
            <th style='align-content: center; width: 10%'>Задание</th>
        </tr>
        <?php
        foreach ($list2 as $row){
            echo "<tr style='border-bottom: 1px solid darkgray;'>";
            echo "<td style='padding:10px;'>".$row['discipline']."</td>
        <td>".$row['examiners']."</td>
        <td style='padding:10px; text-align: center;'>".$row['count']."</td>
        <td style='padding:10px;'><h4 style='text-align: center;'>".(($row['disciplineId'] != '')?
                    CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Показать задания\' class=\'glyphicon glyphicon-search\'>',
                        ['/remote/read/taskList', 'discipline' => $row['disciplineId'],'time'=>$TimeF]):'')
                ."</h4></td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<script>
function openSchedule(evt, TimeSchedule) {
    // Объявить все переменные
    var i, tabcontent, tablinks;

    // Получить все элементы с помощью class="tabcontent" и спрятать их
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    // Получить все элементы с помощью class="tablinks" и заменить их классы
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" btn-primary", " btn-default");
    }

    // Показать текущую вкладку и добавить "btn-primary" класс для кнопки, которая открыла вкладку
    document.getElementById("btn-"+TimeSchedule).classList.add("btn-primary");
    document.getElementById(TimeSchedule).style.display = "block";
}
</script>