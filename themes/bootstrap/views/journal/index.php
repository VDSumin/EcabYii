<?php
/* @var $this JournalController */
/* @var $dataProvider CActiveDataProvider */


$this->menu=$menu;
$this->pageTitle = 'Электронный журнал посещаемости';

?>
<head>
    <style>
        .switch {
            position: relative;display: inline-block;width: 40px;height: 22px;
        }
        .switch input {display:none;}
        .slider {
            position: absolute;cursor: pointer;top: 0;left: 0;right: 0;
            bottom: 0;background-color: #ccc;-webkit-transition: .4s;transition: .4s;
        }
        .slider:before {
            position: absolute;content: "";height: 18px;width: 18px;
            left: 2px;bottom: 2px;background-color: white;-webkit-transition: .4s;
            transition: .4s;
        }
        input:checked + .slider {
            background-color: #0d47a1;
        }
        input:focus + .slider {
            box-shadow: 0 0 1px #0d47a1;
        }
        input:checked + .slider:before {
            -webkit-transform: translateX(18px);
            -ms-transform: translateX(18px);
            transform: translateX(18px);
        }
        /* Rounded sliders */
        .slider.round {
            border-radius: 22px;
        }
        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    <script type="text/javascript" language="javascript">
        $('html').keydown(function(eventObject){
            if (event.keyCode == 192) {
                var contDL = document.getElementsByName('droplistmark');
                var contBM = document.getElementsByName('buttonmark');
                if(document.getElementById('chbox').checked){
                    document.getElementById('chbox').checked = false;
                    document.getElementById('droplist').style.display='none';
                    for(var i=0;i<contDL.length;i++){
                        contDL[i].style.display='';
                    }
                    for(var i=0;i<contBM.length;i++){
                        contBM[i].style.display='none';
                    }
                }else{
                    document.getElementById('chbox').checked = true;
                    document.getElementById('droplist').style.display='';
                    for(var i=0;i<contDL.length;i++){
                        contDL[i].style.display='none';
                    }
                    for(var i=0;i<contBM.length;i++){
                        contBM[i].style.display='';
                    }
                }
            }
            if(document.getElementById('droplist').style.display == '') {
                if (event.keyCode == 49) {
                    document.getElementById('droplist').value = 1;
                }
                if (event.keyCode == 50) {
                    document.getElementById('droplist').value = 3;
                }
                if (event.keyCode == 51) {
                    document.getElementById('droplist').value = 2;
                }
                if (event.keyCode == 52) {
                    document.getElementById('droplist').value = 5;
                }
                if (event.keyCode == 53) {
                    document.getElementById('droplist').value = 4;
                }
                if (event.keyCode == 54) {
                    document.getElementById('droplist').value = 6;
                }
            }
        });
    </script>
</head>
<h1>Заполнение журнала посещаемости</h1>
<div align="right" style="margin: auto;" class="form-inline">Быстрое заполнение
        <select id="droplist" class="form-control" style="width: 120px; display: none;">
            <option value="1">Явка</option>
            <option value="3">Н/я н/у</option>
            <option value="2">Н/я ув.</option>
            <option value="5">Не сост.</option>
            <option value="4">Др/подгр.</option>
            <option value="6">Дист.</option>
        </select>
        <label class="switch">
            <input id="chbox" type="checkbox" onclick="
            var contDL = document.getElementsByName('droplistmark');
            var contBM = document.getElementsByName('buttonmark');
            if(document.getElementById('droplist').style.display=='none'){
                document.getElementById('droplist').style.display='';
                for(var i=0;i<contDL.length;i++){
                    contDL[i].style.display='none';
                }
                for(var i=0;i<contBM.length;i++){
                    contBM[i].style.display='';
                }
            }else{
                document.getElementById('droplist').style.display='none';
                for(var i=0;i<contDL.length;i++){
                    contDL[i].style.display='';
                }
                for(var i=0;i<contBM.length;i++){
                    contBM[i].style.display='none';
                }
            }">
            <div class="slider round"></div>
        </label>
        <span rel='tooltip' data-toggle='tooltip' title='Информация' class='glyphicon glyphicon-info-sign' style="color:black"
              onclick="alert('Функция позволяет производить быстрое заполнение Электронного журнала посещаемости. ' +
               'При нажатии кнопки \'Быстрое заполнение\' Вам откроется меню с возможностью выбора необходимой отметки. \n' +
                'Так же переход в эту функцию осуществляется через клавишу \'ё\' или \'~\'. ' +
                'Выбор отметки для заполнения журнала посещаемости  может осуществляться кнопками от \'1\' до \'5\', соответствует выподающему списку. \n' +
                 'После этого необходимо отметить студентов. Не забудте сохранить. ')" type="button"></span>
    </div>
<?php
echo CHtml::beginForm('','post',array("id" => "markStatSave", 'align' => "center", 'data-form-confirm' => "modal__confirm"));
?>
<button type="submit" name="down" class="btn btn-info btn-sm" value="Previous"><<Предыдущая неделя</button>
<?php
if(date('w')){$showanim = 'slideDown';}else{$showanim = 'bounce';}
$this->widget('zii.widgets.jui.CJuiDatePicker',array(
    'name'=>'publishDate',
    'value' => $date,
    'language'=>'ru',
    'options'=>array(
        'dateFormat' => "yy-mm-dd",
        'minDate' => '2016-09-01',
        'showAnim'=>$showanim,
        'firstDay' => 1,
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
));
echo" ";
?>
<button type="submit" name="down" class="btn btn-primary btn-sm" value="Send">Перейти на дату</button>
<button type="submit" name="down" class="btn btn-info btn-sm" value="Next">Следующая неделя>></button>
<?php
echo CHtml::endForm();
?>
<?= $this->renderPartial('_tabs', ['dates' => $dates, 'day' => $day, 'activedates' => $activedates]);?>

<?php
    echo CHtml::beginForm('','post',array("id" => "markStatSave", 'data-form-confirm' => "modal__confirm"));
if($date<=date('Y-m-d')) {//что такое 0
if ($discipline) {
$numbers = 1;
foreach ($discipline as $dis) {
    $numbers++;
}
$width= 100.0/$numbers;
?>
<div style="position:relative">
    <div style="overflow-x:scroll; overflow-y:visible; width:100%; margin-left:200px; width:625px; ">
        <table class=" table table-striped _table-ulist static table-hover" style="table-layout:fixed; width: 100%;">
            <thead>
            <tr>
                <th style="position:absolute; left:0; width:200px; vertical-align: bottom; height:100%;">
                    ФИО
                </th>
                <?php
                foreach ($discipline as $dis) {
                    if($numbers == 2){
                        echo '<th width="625 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Type'];
                    }else{
                        if($numbers == 3){
                            echo '<th width="312 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Type'];
                        }else{
                            echo '<th width="250 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Type'];
                        }
                    }
                    $i = 0;
                    while ($i < 10) {
                        $i++;
                        if (strstr($dis['studGroupName'], '/' . $i)) {
                            echo '-' . $i;
                            break;
                        }
                    }
                    echo ')' . '<br/>' . $dis['Kind'] . '<br/>' . '(' . $dis['teacherFio'] . ')' . '<br/>' . $dis['time'] . '</th>';
                }
                ?>
            </tr>
            <tr>
                <th></th>
                <?php
                foreach ($discipline as $dis) {
                    echo '<th style="border-left: 1px solid darkgray">Ст.</th>';
                    echo '<th>ППС</th>';
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($steward) {
                foreach ($list as $li) {
                        echo '<tr><th style="position:absolute; left:0; width:200px;">' . $li['fio'] . '</th>';
                    $i = 0;
                    foreach ($discipline as $dis) {
                        if (in_array($this->mark($li['fnpp'], $dis['id']), [1, 6])) {
                            echo '<td class="success" align="center" style="border-right: 1px solid darkgray; padding:10px;">';
                            $classbtn = 'btn btn-success';
                        } else {
                            if (in_array($this->mark($li['fnpp'], $dis['id']), [2, 4, 5])) {
                                echo '<td class="info" align="center" style="border-right: 1px solid darkgray; padding:10px;">';
                                $classbtn = 'btn btn-info';
                            } else {
                                echo '<td class="danger" align="center" style="border-right: 1px solid darkgray; padding:10px;">';
                                $classbtn = 'btn btn-danger';
                            }
                        }
                        if(!($this->logjournal($li['fnpp'], $dis['id']))) {
                            $this->insertjournal($li['fnpp'], $li['cpersons'], $dis['id']);
                        }
                        if($dis['Type'] == 'Подгруппа' or in_array($dis['discipline'],['Профессиональный иностранный язык','Иностранный язык в профессиональной сфере',
                                'Деловой иностранный язык','Иностранный язык в сфере профессиональной коммуникации','Иностранный язык'])){
                            $droplist=array('3' => 'Н/я н/у','1' => 'Явка','6' => 'Дист.','2' => 'Н/я ув.','5' => 'Не сост.','4' => 'Др/подгр.');
                        }else{
                            $droplist=array('3' => 'Н/я н/у','1' => 'Явка','6' => 'Дист.','2' => 'Н/я ув.','5' => 'Не сост.');
                        }
                        $markid = $this->markid($li['fnpp'], $dis['id']);
                        $markName = $droplist[$this->mark($li['fnpp'], $dis['id'])];
                        echo "<div name='droplistmark'>";
                        echo CHtml::dropDownList('listname['. $markid .']', $this->mark($li['fnpp'], $dis['id']),
                                $droplist,
                                ['class' => 'form-control',
                                'onchange' => 'var Mark = [\'Null\',\'Явка\',\'Н/я ув.\',\'Н/я н/у\',\'Др/подгр.\',\'Не сост.\',\'Дист.\'];
                                document.getElementById(\'\'+'.$markid.').value = Mark[this.value];
                                if(this.value == \'1\'){ document.getElementById(\'\'+'.$markid.').className = \'btn btn-success\';}
                                if(this.value == \'2\'){ document.getElementById(\'\'+'.$markid.').className = \'btn btn-info\';}
                                if(this.value == \'3\'){ document.getElementById(\'\'+'.$markid.').className = \'btn btn-danger\';}
                                if(this.value == \'4\'){ document.getElementById(\'\'+'.$markid.').className = \'btn btn-info\';}
                                if(this.value == \'5\'){ document.getElementById(\'\'+'.$markid.').className = \'btn btn-info\';}
                                if(this.value == \'6\'){ document.getElementById(\'\'+'.$markid.').className = \'btn btn-success\';}']);
                        echo "</div><div name='buttonmark' style='display: none;'><input type='button' class='$classbtn'
                                style='width: 100px;' id='$markid' value='$markName' 
                                onclick=\"
                                var Mark = ['Null','Явка','Н/я ув.','Н/я н/у','Др/подгр.','Не сост.','Дист.'];
                                if(document.getElementById('droplist').value == '1'){ $(this).removeClass(); $(this).addClass('btn btn-success');}
                                if(document.getElementById('droplist').value == '6'){ $(this).removeClass(); $(this).addClass('btn btn-success');}
                                if(document.getElementById('droplist').value == '2'){ $(this).removeClass(); $(this).addClass('btn btn-info');}
                                if(document.getElementById('droplist').value == '3'){ $(this).removeClass(); $(this).addClass('btn btn-danger');}
                                if(document.getElementById('droplist').value == '5'){ $(this).removeClass(); $(this).addClass('btn btn-info');}
                                if(document.getElementById('droplist').value == '4'){
                                    if(document.getElementById('listname_'+this.id).options[4]){
                                    $(this).removeClass(); $(this).addClass('btn btn-info');
                                    this.value = Mark[document.getElementById('droplist').value];
                                    document.getElementById('listname_'+this.id).value = document.getElementById('droplist').value;
                                    }
                                }else{
                                this.value = Mark[document.getElementById('droplist').value];
                                document.getElementById('listname_'+this.id).value = document.getElementById('droplist').value;
                                }\"></div></td>";
                        if(in_array($this->markteach($li['fnpp'], $dis['id']), [1])){
                            echo '<td class="success" align="center" style="border-right: 1px solid darkgray; padding:10px;" >Явка</td>';
                        }else{
                            if(in_array($this->markteach($li['fnpp'], $dis['id']), [2])) {
                                echo '<td class="info" align="center" style="border-right: 1px solid darkgray; padding:10px;">Н/я ув.</td>';
                            }else{
                                if(in_array($this->markteach($li['fnpp'], $dis['id']), [5])) {
                                    echo '<td class="info" align="center" style="border-right: 1px solid darkgray; padding:10px;">Не сост.</td>';
                                }else {
                                    if(in_array($this->markteach($li['fnpp'], $dis['id']), [4])) {
                                        echo '<td class="info" align="center" style="border-right: 1px solid darkgray; padding:10px;">Др/подгр.</td>';
                                    }else {
                                        if(in_array($this->markteach($li['fnpp'], $dis['id']), [6])) {
                                            echo '<td class="success" align="center" style="border-right: 1px solid darkgray; padding:10px;" >Дист.</td>';
                                        }else {
                                            echo '<td class="danger" align="center" style="border-right: 1px solid darkgray; padding:10px;">Н/я н/у</td>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
    <?php
    if ($steward) {
        ?>
        <div align="right">
            <?php
            if($result){
                echo "<button type='submit' class='btn btn-success' id='sendForm'>Сохранить</button>";
            }else {
                echo "<button type='submit' class='btn btn-primary' id='sendForm'>Сохранить</button>";
            }
            ?>
        </div>
        <?php
    }
    echo CHtml::endForm();
} else {
    echo '<center>Расписание отсутствует</center>';
}
}else{
    echo '<center>День ещё не наступил</center>';
}
?>
<br />
<table class=" table table-striped _table-ulist static table-hover" border="1">
    <tr><th colspan="2" style="border-top: 1px solid black">Используемые сокращения</th> </tr>
    <tr><th>Н/я н/у</th><td>Студент отсутствовал, по не уважительной причине</td></tr>
    <tr><th>Явка</th><td>Студент присутствал на паре очно</td></tr>
    <tr><th>Дист.</th><td>Студент присутствал на паре дистанционно</td></tr>
    <tr><th>Н/я ув.</th><td>Студент отсутствовал, по уважительной причине</td></tr>
    <tr><th>Не сост.</th><td>Пара не состоялась</td></tr>
    <tr><th>Др/подгр.</th><td>Студент находится в другой подгруппе</td></tr>
    <tr><th style="border-top: 1px solid black">Ст.</th><td style="border-top: 1px solid black">Отметка о посещаемости старостой</td></tr>
    <tr><th>ППС</th><td>Отметка о посещаемости преподавателем</td></tr>
</table>
