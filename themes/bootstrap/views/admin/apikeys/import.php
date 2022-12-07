<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 18.04.2020
 * Time: 0:09
 */

$this->pageTitle = 'Ключи для API';

$this->breadcrumbs = array(
    'Ключи для API' => array('index'),
    'Результат импорта'
);

?>


<center><h1>Импорт Api-Keys</h1></center>
<div class="jumbotron">
<?php
echo '<table class="items table table-striped _table-ulist">';
echo '<tr><th>№</th><th>ФИО</th><th>Статус</th><th>Тип внесения</th><th>Номер внесения</th> </tr>';
echo '<tbody>';
$k = 1;
foreach ($result as $key => $row){
    echo '<tr >';
    echo '<td>'.$k++.'</td>';
    echo '<td>'.$key.'</td>';
    echo '<td>'.(($row['success'])?'Успешно':'Неудачно').'</td>';
    echo '<td>'.(($row['type'] == 3)?'Добавление':'Обновление').'</td>';
    echo '<td>'.$row['type'].'</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>
</div>
