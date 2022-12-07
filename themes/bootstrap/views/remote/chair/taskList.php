<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Список кафедры' => array('/remote/chair'),
    Fdata::model()->findByPk($person)->getFIO() => array('/remote/chair/PersonList', 'id' => $person),
    'Список выданных работ'
];

?>

<h1>Список выданных работ</h1>
<?php echo '<b>Дисциплина:</b> '.uDiscipline::model()->findByPk(hex2bin(CMisc::_bn($discipline)))->name; ?><br/>
<?php echo (count($group) == 1)? '<b>Группа:</b> '.AttendanceGalruzGroup::model()->findByPk($group)->name: ''; ?>

<div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Внимание!</strong> Выданные задания отсортированы по дате выдаче.
</div>

<hr />
<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
    <tr>
        <th style='align-content: center;padding:10px; width: 60px'>Номер</th>
        <th style='align-content: center;'>Комментарий</th>
        <th style='align-content: center; width: 200px'>Файлы</th>
        <th style='align-content: center; width: 150px'>Дата создания</th>
        <th style='align-content: center; width: 150px'>Преподаватель</th>
        <th style='align-content: center; width: 80px'>Действия</th>
    </tr>
    <?php
    $j=1;
    foreach ($list as $row){
        echo "<tr style='border-bottom: 1px solid darkgray;'>";
        echo "<td style='padding:10px;'>".($j++)."</td>
        <td style='padding:10px;'>".'<textarea class="form-control" rows="7" placeholder="Комментарий к работе отсутствует" style="resize: vertical; background-color: white;" readonly>'
            .$row['comment'].'</textarea>'."</td>
        <td style='overflow-x: auto;'>".RemoteModule::listfile($row['id'])."</td>
        <td>".$row['create_date']."</td>
        <td style='overflow-x: auto;'>".Fdata::model()->findByPk($row['author_fnpp'])->getFIO()."</td>";
        echo "<td>" . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Изменить\' class=\'glyphicon glyphicon-pencil\'>',
                ['editTask', 'fnpp' => $person, 'id' => $row['id']]) . ' '
            . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Оповестить по почте\' class=\'glyphicon glyphicon-envelope\'>',
                ['mailGroup', 'fnpp' => $person, 'id' => $row['id']], ['onclick' => 'return confirm(\'Оповестить студентов по почте?\');',
                    'style' => (($row['send_mail']) ? 'display: none;' : '')]) . ' '
            . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Удалить\' class=\'glyphicon glyphicon-trash\'>',
                ['deleteTask', 'fnpp' => $person, 'id' => $row['id']], ['onclick' => 'return confirm(\'Удалить выбранную запись?\');'])
            . "</td>";
        echo "</tr>";
    }
    ?>
</table>
