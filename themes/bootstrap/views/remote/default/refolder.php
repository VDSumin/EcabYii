<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 25.05.2020
 * Time: 17:53
 */

$this->pageTitle = Yii::app()->name . ' - Переконфигурирование директорий';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Переконфигурирование директорий'
];

?>

<h1>Текущие директории</h1>

<table border="1" width="100%">
<?php
foreach ($success as $key => $row){
    echo '<tr>';
    echo '<td>'.$key.'</td>';
    echo '<td>'.$row.'</td>';
    echo '</tr>';
}
?>
</table>

