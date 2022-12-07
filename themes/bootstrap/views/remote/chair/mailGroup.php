<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 18.03.2020
 * Time: 12:34
 */
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Контактная работа';
$this->breadcrumbs = [
    'Контактная работа' => array('/remote'),
    'Список кафедры' => array('/remote/chair'),
    Fdata::model()->findByPk($person)->getFIO() => array('/remote/chair/PersonList', 'id' => $person),
    'Оповещение по почте'
];

?>

<?php
echo '<div class="jumbotron"><center><h2>Сообщения по дисциплине "'.$discipline->name.'" <br /> разосланы группе: '. $group->name.'</h2></center></div>';
?>

<?php
if(count($good_email) > 0) {

    echo '<div class="jumbotron">';
    echo '<b>Успешная отправка:</b> <br />';
    foreach ($good_email as $item) {
        echo $item['fio'] .' - '. $item['email'].'<br/>';
    }
    echo '</div>';
}
?>

<?php
if(count($bad_email) > 0) {

    echo '<div class="jumbotron">';
    echo '<b>Ошибка в отправке:</b> <br />';
    foreach ($bad_email as $item) {
        echo $item['fio'] .' - '. $item['email'].'<br/>';
    }
    echo '</div>';
}
?>
