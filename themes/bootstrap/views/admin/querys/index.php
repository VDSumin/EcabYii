<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 10.09.2020
 * Time: 11:11
 */
/* @var $this QuerysController */

$this->pageTitle = 'SQL-запросы';

$this->breadcrumbs = array(
    'SQL-запросы',
);

?>

<center><h1>SQL-запросы</h1></center>

<div class="row">
    <label for="querys_base" >Пример</label><br/>
    <text>ALTER TABLE tbl_inquiries_requests ADD takePickUp INT(11) NULL</text><br/>
    <text>SHOW TABLES</text>
</div>


<?php echo CHtml::beginForm('', 'post', array("id" => "querys", 'class' => 'form-inline')); ?>

<div class="row">
    <label for="querys_base" >База данных</label><br/>
    <select class="form-control" name="querys[base]" id="querys_base" style="display: inline; width: 200px;">
        <option value="db">db</option>
    </select>
</div>
    <br/>
<div class="row">
    <label for="querys_base" >Запрос</label><br/>
    <textarea class="form-control" rows="7" placeholder="Ваш запрос" style="resize: vertical; background-color: white; width: 100%" name="querys[text]" id="querys_text"></textarea>
</div>
<br/>
<div class="row">
    <?= CHtml::submitButton('Выполнить', array('class' => 'saveButton btn btn-primary', 'name' => 'button', 'style' => 'margin-bottom:20px;')); ?>
</div>


<?php
echo CHtml::endForm();
?>
<hr/>
<?php
if(!empty($data)):
    if (is_array($data)):
?>
<center><h3>Результат</h3></center>
<div style="width: 100%; resize: horizontal; overflow: auto; height: 800px;">
    <table border="1" class="table table-bordered">
<?php
$str = '';
$keys = array_keys($data[0]);
$str = '<tr>';
foreach ($keys as $key) {
    $str .= "<th>".$key."</th>";
}
$str .= "</tr>";
echo $str;

foreach ($data as $one){
    $str = '<tr>';
    foreach ($one as $onekey) {
        $str .= "<td>".$onekey."</td>";
    }
    $str .= "</tr>";
    echo $str;
}
?>
    </table>
</div>
<?php
    else:
        if($data) {
            echo '<center><h3>Выполнен успешно</h3></center>';
        }else{
            echo '<center><h3>Выполнен с ошибкой</h3></center>';
        }
        endif;
    else:
    echo "<center><h3>Пустой ответ</h3></center>";
endif;
?>

