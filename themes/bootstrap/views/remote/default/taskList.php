<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Список выданных работ'
];


?>
<style>
    .force-select-all {
        -webkit-user-select: all; /* Для браузеров на движке Blink и Firefox */
        user-select: all; /* Когда-нибудь будет хватать только этой строки */
    }
</style>

<h1>Список выданных работ</h1>
<?php echo '<b>Дисциплина:</b> ' . uDiscipline::model()->findByPk(hex2bin(CMisc::_bn($discipline)))->name; ?><br/>
<?php echo '<b>Группа:</b> ' . AttendanceGalruzGroup::model()->findByPk($group)->name ?>

<div class="alert alert-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <strong>Внимание!</strong> Уведомления по электронной почте работает не стабильно в связи с высокой нагрузкой
    серверов,
    а так же с большим количеством не актуальных адресов электронной почты студентов.
</div>
<div>
    <?php echo CHtml::link('Выдать задание', ['/remote/default/giveTheTask', 'group' => $group, 'discipline' => $discipline]
                                    , ['class' => 'btn btn-default']); ?>
</div>

<hr/>
<table class="table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;" id="List">
    <tr>
        <th style='align-content: center;padding:10px; width: 5%'>Номер</th>
        <th style='align-content: center; width: 50%'>Комментарий</th>
        <th style='align-content: center; width: 20%'>Файлы</th>
        <th style='align-content: center; width: 15%'>Дата создания</th>
        <th style='align-content: center; width: 10%'>Действия</th>
    </tr>
    <?php
    $j = 1;
    foreach ($list as $row) {
        echo "<tr style='border-bottom: 1px solid darkgray;'>";
        echo "<td style='padding:10px;'>" . ($j++) . "</td>
        <td style='padding:10px;'>" . '<div class="form-control force-select-all" rows="7" style="background-color: white; height: auto" readonly>'
            . RemoteModule::printComment($row['comment']) . '</div>' . "</td>
        <td style='overflow-x: auto;'>" . RemoteModule::listfile($row['id']) . "</td>
        <td>" . $row['create_date'] . "</td>";
        if ($row['author_fnpp'] == Yii::app()->user->getFnpp()) {
            echo "<td>" . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Изменить\' class=\'glyphicon glyphicon-pencil\'>',
                    ['/remote/default/editTask', 'id' => $row['id']]) . ' '
                . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Оповестить по почте\' class=\'glyphicon glyphicon-envelope\'>',
                    ['/remote/default/mailGroup', 'id' => $row['id']], ['onclick' => 'return confirm(\'Оповестить студентов по почте?\');',
                        'style' => (($row['send_mail']) ? 'display: none;' : '')]) . ' '
                . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Удалить\' class=\'glyphicon glyphicon-trash\'>',
                    ['/remote/default/deleteTask', 'id' => $row['id']], ['onclick' => 'return confirm(\'Удалить выбранную запись?\');'])
                . "</td>";
        } else {
            echo "<td  style='overflow-x: auto;'>Автор: " . Fdata::model()->findByPk($row['author_fnpp'])->getFIO() . "</td>";
        }

        echo "</tr>";
    }
    ?>
</table>

