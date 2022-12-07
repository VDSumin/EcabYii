<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote/read'),
    'Список выданных работ'
];

?>

<style>
    .force-select-all {
        -webkit-user-select: all; /* Для браузеров на движке Blink и Firefox */
        user-select: all; /* Когда-нибудь будет хватать только этой строки */
    }
</style>

<h1>Список работ</h1>
<?php echo '<b>Дисциплина:</b> '.uDiscipline::model()->findByPk(hex2bin(CMisc::_bn($discipline)))->name; ?><br/>
<?php echo (count($group) == 1)? '<b>Группа:</b> '.AttendanceGalruzGroup::model()->findByPk($group)->name: ''; ?>

<div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Внимание!</strong> Выданные задания отсортированы по дате выдаче.
</div>

<hr />
<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
    <tr>
        <th style='align-content: center;padding:10px; width: 5%'>Номер</th>
        <th style='align-content: center; width: 50%'>Комментарий</th>
        <th style='align-content: center; width: 20%'>Файлы</th>
        <th style='align-content: center; width: 10%'>Дата создания</th>
        <th style='align-content: center; width: 15%'>Преподаватель</th>
    </tr>
    <?php
    $j=1;
    foreach ($list as $row){
        echo "<tr style='border-bottom: 1px solid darkgray;'>";
        echo "<td style='padding:10px;'>".($j++)."</td>
        <td style='padding:10px;'>".'<div class="form-control force-select-all" rows="7" placeholder="Комментарий к работе отсутствует" style="background-color: white; height: auto;"readonly>'
            .RemoteModule::printComment($row['comment']).'</div>'."</td>
        <td style='overflow-x: auto;'>".ReadClass::listfile($row['id'])."</td>
        <td>".$row['create_date']."</td>
        <td style='overflow-x: auto;'>".Fdata::model()->findByPk($row['author_fnpp'])->getFIO()."</td>";
        echo "</tr>";
    }
    ?>
</table>
