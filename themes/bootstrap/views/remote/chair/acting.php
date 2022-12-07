<?php
/* @var $this DefaultController */
/* @var $row Fdata  */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Список кафедры' => array('/remote/chair'),
    'Ответственные на кафедре'
];

?>

<h1>Ответственные на кафедре</h1>
Здесь отображаются сотрудники вашей кафедры.
<hr />
<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
    <tr>
        <th>ФИО</th>
        <th style='align-content: center; width: 10%'>Исполняющий обязанности</th>
    </tr>
    <?php

    foreach ($list as $row){
        $taskData = ChairClass::getPersonTask($row->npp, $chair);
        echo "<tr style='border-bottom: 1px solid darkgray;'>";
        echo "<td style='padding:10px;'>".$row->getFIO()."</td>";
        echo "<td style='text-align: center; padding:10px;'>";
        if(ChairClass::getMyStruct() != null) {
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
