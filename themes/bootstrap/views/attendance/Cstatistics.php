<?php
/* @var $this AttendanceController */
/* @var $dataProvider CActiveDataProvider */
$this->pageTitle = 'Электронный журнал посещаемости';
?>
<center><h1>Статистика посещаемости группы <?php echo $this->getgroupname($group);?></h1></center>
<?= $this->renderPartial('_tabsmenuC'); ?>

<?php
if(in_array($stat,[1,2])) {
    $numbers = 1;
    foreach ($data as $dis) {
        $numbers++;
    }
    ?>
    <table width="825px" align="center">
    <tr>
    <td>
    <div style="position:relative">
        <div style="overflow-x:scroll; overflow-y:visible; width:100%; margin-left:200px; width:625px; ">
            <table class=" table table-striped _table-ulist static table-hover"
                   style="table-layout:fixed; width: 100%;">
                <thead>
                <tr>
                    <th style="position:absolute; left:0; width:200px; vertical-align: bottom; height:100%;">
                        ФИО
                    </th>
                    <?php
                    foreach ($data as $dis) {
                        //var_dump($dis);die;
                        if ($numbers <= 2) {
                            echo '<th width="625 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
                        } else {
                            if ($numbers <= 3) {
                                echo '<th width="312 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
                            } else {
                                if ($numbers <= 4) {
                                    echo '<th width="208 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
                                } else {
                                    echo '<th width="150 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
                                }
                            }
                        }
                        echo ') </br>' . $dis['teacherFio'];
                        echo '</th>';
                    }
                    echo '<th width="150 px" colspan="2" style="border-left: 1px solid darkgray; vertical-align: center;">Общий </br>процент </br>посещаемости</th>';
                    ?>
                </tr>
                <tr>
                    <th></th>
                    <?php
                    foreach ($data as $dis) {
                        echo '<th style="border-left: 1px solid darkgray">Ст.</th>';
                        echo '<th>ППС</th>';
                    }
                    echo '<th style="border-left: 1px solid darkgray">Ст.</th>';
                    echo '<th>ППС</th>';
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                    $i = 0;
                    foreach ($list as $li) {
                        echo '<tr><th style="position:absolute; left:0; width:200px;">' . $li['fio'] . '</th>';
                        $j = 0;
                        foreach ($data as $dat) {
                            echo '<td colspan="1" style="border-right: 1px solid darkgray; padding:10px;" align="center">' . $lists[$i][$j] . '/' . 2 * $dat['Amount'] . '</td>';
                            echo '<td colspan="1" style="border-right: 1px solid darkgray; padding:10px;" align="center">' . $liststeach[$i][$j] . '/' . 2 * $dat['Amount'] . '</td>';
                            $j++;
                        }
                        echo '<td colspan="1" style="border-right: 1px solid darkgray; padding:10px;" align="center">' . 100*round(($proc[1][$i]/$proc[0][$i]),2) . '%</td>';
                        echo '<td colspan="1" style="border-right: 1px solid darkgray; padding:10px;" align="center">' . 100*round(($proc[2][$i]/$proc[0][$i]),2) . '%</td>';
                        echo '</tr>';
                        $i++;
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    </td>
    </tr>
    </table>
    <?php
}else{
    if(isset($_SESSION['DateFrom']) && isset($_SESSION['DateTo'])) {
        echo CHtml::beginForm('', 'post', array("id" => "markStatSave", 'align' => "center", 'data-form-confirm' => "modal__confirm"));
        echo "Показать статистику от ";
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'DateFrom',
            'value' => $_SESSION['DateFrom'],
            'language' => 'ru',
            'options' => array(
                'dateFormat' => "yy-mm-dd",
                'minDate' => '2016-09-01',
                'showAnim' => 'slideDown',
                //'showAnim'=>'bounce',
                'firstDay' => 1,
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;'
            ),
        ));
        echo " до ";
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'DateTo',
            'value' => $_SESSION['DateTo'],
            'language' => 'ru',
            'options' => array(
                'dateFormat' => "yy-mm-dd",
                'minDate' => '2016-09-01',
                'showAnim' => 'slideDown',
                //'showAnim'=>'bounce',
                'firstDay' => 1,
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;'
            ),
        ));
        ?>
        <button type="submit" name="down" class="btn btn-secondary btn-sm" value="Send">Показать статистику</button>
        <?php
        echo CHtml::endForm();
        //-----
        $numbers = 1;
        foreach ($data as $dis) {
            $numbers++;
        }
        ?>
        <table width="825px" align="center">
        <tr>
        <td>
        <div style="position:relative">
            <div style="overflow-x:scroll; overflow-y:visible; width:100%; margin-left:200px; width:625px; ">
                <table class=" table table-striped _table-ulist static table-hover"
                       style="table-layout:fixed; width: 100%;">
                    <thead>
                    <tr>
                        <th style="position:absolute; left:0; width:200px; vertical-align: bottom; height:100%;">
                            ФИО
                        </th>
                        <?php
                        foreach ($data as $dis) {
                            //var_dump($dis);die;
                            if ($numbers <= 2) {
                                echo '<th width="625 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
                            } else {
                                if ($numbers <= 3) {
                                    echo '<th width="312 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
                                } else {
                                    if ($numbers <= 4) {
                                        echo '<th width="208 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
                                    } else {
                                        echo '<th width="150 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
                                    }
                                }
                            }
                            echo ') </br>' . $dis['teacherFio'];
                            echo '</th>';
                        }
                        echo '<th width="150 px" colspan="2" style="border-left: 1px solid darkgray; vertical-align: center;">Общий </br>процент </br>посещаемости</th>';
                        ?>
                    </tr>
                    <tr>
                        <th></th>
                        <?php
                        foreach ($data as $dis) {
                            echo '<th style="border-left: 1px solid darkgray">Ст.</th>';
                            echo '<th>ППС</th>';
                        }
                        echo '<th style="border-left: 1px solid darkgray">Ст.</th>';
                        echo '<th>ППС</th>';
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i = 0;
                        foreach ($list as $li) {
                            echo '<tr><th style="position:absolute; left:0; width:200px;">' . $li['fio'] . '</th>';
                            $j = 0;
                            foreach ($data as $dat) {
                                echo '<td colspan="1" style="border-right: 1px solid darkgray; padding:10px;" align="center">' . $lists[$i][$j] . '/' . 2 * $dat['Amount'] . '</td>';
                                echo '<td colspan="1" style="border-right: 1px solid darkgray; padding:10px;" align="center">' . $liststeach[$i][$j] . '/' . 2 * $dat['Amount'] . '</td>';
                                $j++;
                            }
                            echo '<td colspan="1" style="border-right: 1px solid darkgray; padding:10px;" align="center">' . 100*round(($proc[1][$i]/$proc[0][$i]),2) . '%</td>';
                            echo '<td colspan="1" style="border-right: 1px solid darkgray; padding:10px;" align="center">' . 100*round(($proc[2][$i]/$proc[0][$i]),2) . '%</td>';
                            echo '</tr>';
                            $i++;
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        </td>
        </tr>
        </table>
        <?php
    }else{
        echo CHtml::beginForm('', 'post', array("id" => "markStatSave", 'align' => "center", 'data-form-confirm' => "modal__confirm"));
        echo "Показать статистику от ";
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'DateFrom',
            'language' => 'ru',
            'options' => array(
                'dateFormat' => "yy-mm-dd",
                'minDate' => '2016-09-01',
                'showAnim' => 'slideDown',
                //'showAnim'=>'bounce',
                'firstDay' => 1,
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;'
            ),
        ));
        echo " до ";
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'DateTo',
            'language' => 'ru',
            'options' => array(
                'dateFormat' => "yy-mm-dd",
                'minDate' => '2016-09-01',
                'showAnim' => 'slideDown',
                //'showAnim'=>'bounce',
                'firstDay' => 1,
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;'
            ),
        ));
        ?>
        <button type="submit" name="down" class="btn btn-secondary btn-sm" value="Send">Показать статистику</button>
        <?php
        echo CHtml::endForm();
    }
}
?>