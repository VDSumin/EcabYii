<?php
/* @var $this PortfolioController */
/* @var $dataProvider CActiveDataProvider */


//$this->menu=$menu;
$this->pageTitle = 'Электронное портфолио студента';

?>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <center>ОБЩИЕ СВЕДЕНИЯ</center>
                </a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">

                <table border="0" width="100%">
                    <tr><td width="200px" style="padding-left: 5px">ФИО</td><td style="padding-left: 5px"><u><?php echo $info['fio']; ?></u></td>
                        <td width="200px" align="center" rowspan="7" ><?php if($fotocheck){echo '<img src="http://www.omgtu.ru/ecab/persons/photo.php?f='.$npp.'" width="150px">';} ?></td>
                    </tr>
                    <tr><td style="padding-left: 5px">Уровень подготовки</td><td style="padding-left: 5px"><u><?php echo $info['degree']; ?></u></td></tr>
                    <tr><td style="padding-left: 5px">Направление подготовки / специальность</td><td style="padding-left: 5px; vertical-align: bottom;"><u><?php echo $info['post']; ?></u></td></tr>
                    <tr><td style="padding-left: 5px">Профиль / направленность образовательной программы</td><td style="padding-left: 5px; vertical-align: bottom;"><u><?php echo $info['profil']; ?></u></td></tr>
                    <tr><td style="padding-left: 5px">Группа</td><td style="padding-left: 5px"><u><?php echo $info['group']; ?></u></td></tr>
                    <tr><td style="padding-left: 5px">Зачислен(а) в ОмГТУ</td><td style="padding-left: 5px"><u><?php echo CMisc::fromGalDate($info['firstdate']); ?></u></td></tr>
                    <tr><td style="padding-left: 5px">Плановый период обучения</td><td style="padding-left: 5px"><u><?php echo CMisc::fromGalDate($info['termstart'])." - ".CMisc::fromGalDate($info['termend']); ?></u></td></tr>
                </table>
                <br />
                <table border="0" width="100%">
                    <tr><td colspan="2">Контактная информация:</td><td colspan="2"></td></tr>
                    <tr><td width="100px">Телефон(ы):</td><td style="vertical-align: bottom;" width="43%" ><u><?php echo $info['phone']; ?></u></td>
                        <td width="100px">E-mail:</td><td  width="43%"><u><?php echo $info['email']; ?></u></td></tr>
                </table>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <center>1. СВЕДЕНИЯ О ПРЕДШЕСТВУЮЩЕМ ОБРАЗОВАНИИ</center>
                </a>
            </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
            <div class="panel-body">
                <center><b>Основное и профессиональное образование</b></center>
                <table border="1" width="100%"><thead><tr><th><center>Уровень образования</center></th>
                        <th><center>Документ об образовании</center></th>
                        <th><center>Дата выдачи<br /> документа<br /> об<br /> образовании</center></th>
                        <th><center>Наименование образовательной организации</center></th>
                        <th><center>Страна, город</center></th>
                        <th><center>Образовательная программа</center></th></tr></thead>
                    <tbody>
                    <?php
                    foreach ($education as $edu){
                        echo "<tr>";
                        echo "<td>".$edu['level']."</td>";
                        echo "<td>".$edu['name']."</td>";
                        echo "<td><center>".(($edu['diplomdate'] !='0')?CMisc::fromGalDate($edu['diplomdate']):"-")."</center></td>";
                        echo "<td>".$edu['sname']."</td>";
                        echo "<td>".$edu['eduAddr']."</td>";
                        echo "<td><center>".(($edu['spcode'] != "")?($edu['spcode']." - ".$edu['spname']):"-")."</center></td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <?php /*<center><b>Дополнительное образование и профессинальное обучение</b></center>*/?>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingThree">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <center>2. УЧЕБНАЯ ДЕЯТЕЛЬНОСТЬ</center>
                </a>
            </h4>
        </div>
        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
            <div class="panel-body">
                <center><b>Значение показателей успеваемости по образовательной программе</b></center>
                <table border="1" width="100%"><thead><tr><th rowspan="2" width="16%"><center>Показатели</center></th>
                        <th rowspan="2" width="27%"><center>Правила расчёта показателей</center></th>
                        <th rowspan="2" width="12%"><center>Семестр</center></th>
                        <th colspan="3" width="45%"><center>Значение</center></th></tr>
                    <tr><th><center>Средний балл</center></th>
                        <th><center>Средний рейтинг</center></th>
                        <th><center>Посещаемость, %</center></th></tr></thead>
                    <tbody>
                    <?php $first = 0;
                    foreach ($numbersemestr as $semestr){
                        echo "<tr>";
                        if($first == 0){$first++; echo '<td rowspan="'.count($numbersemestr).'"><center>Показатели по результатам сессии</center></td>
                    <td rowspan="'.count($numbersemestr).'"><center>Средний балл – вычисление среднего значения баллов, полученных в результате промежуточной аттестации в сессиях (экзамены, дифференцируемые зачеты, курсовые работы/проекты, практики).
Средний рейтинг – вычисление среднего значения рейтинговых баллов, полученных в результате промежуточной аттестации в сессиях по всем видам контроля.
Посещаемость (%) – абсолютное значение посещаемости всех видов аудиторных занятий в семестре.</center></td>';}
                        echo "<td><center>".$semestr['semester']."</center></td>";
                        if($semestr['average'] != 0) {
                            echo "<td><center>". number_format(round($semestr['average'],2), 2, ',', ' ') ."</center></td>";
                        }else{
                            echo "<td><center>-</center></td>";
                        }
                        if($semestr['raiting'] != '') {
                            echo "<td><center>". number_format(round($semestr['raiting'],2), 2, ',', ' ') ."</center></td>";
                        }else{
                            echo "<td><center>-</center></td>";
                        }
                        if($semestr['procent'] != 0) {
                            echo "<td><center>". (($semestr['procent'] != 0)? number_format($semestr['procent'], 2, ',', ' ').'%':'') ."</center></td>";
                        }else{
                            echo "<td><center>-</center></td>";
                        }
                        echo "</tr>";
                    } ?>
                    </tbody>
                </table>
                <table border="1" width="100%">
                    <tbody>
                    <tr>
                        <td rowspan="2" width="16%"><center>Показатели освоения учебного плана</center></td>
                        <td rowspan="2" width="39%"><center>Процент оценок на текущий момент обучения</center></td>
                        <th><center>отлично</center></th>
                        <th><center>хорошо</center></th>
                        <th><center>удовлетв.</center></th>
                    </tr>
                    <tr>
                        <td><center><?php if(isset($procentUchPlan[5]) and !(array_sum($procentUchPlan) == 0)){echo round(100*$procentUchPlan[5]/array_sum($procentUchPlan),2);}else{echo '0';} ?>%</center></td>
                        <td><center><?php if(isset($procentUchPlan[4]) and !(array_sum($procentUchPlan) == 0)){echo round(100*$procentUchPlan[4]/array_sum($procentUchPlan),2);}else{echo '0';} ?>%</center></td>
                        <td><center><?php if(isset($procentUchPlan[3]) and !(array_sum($procentUchPlan) == 0)){echo round(100*$procentUchPlan[3]/array_sum($procentUchPlan),2);}else{echo '0';} ?>%</center></td>
                    </tr>
                    </tbody>
                </table><br />
                <?php
                if(!empty($krkp)){
                    echo "<center><b>Результаты выполнения курсовых работ (проектов), предусмотренных образовательной программой</b></center>
                    <table border=\"1\" width=\"100%\">
                    <thead>
                    <tr>
                        <th><center>Семестр</center></th>
                        <th><center>Наименование учебной дисциплины</center></th>
                        <th><center>Наименование темы курсовой работы/проекта</center></th>
                        <th><center>Оценка</center></th>
                        <th><center>Комментарии</center></th>
                    </tr>
                    </thead>
                    <tbody>";
                    foreach ($krkp as $item){
                        echo "<tr><td><center>".$item['semester']."</center></td>
                        <td>".$item['name']."</td>
                        <td>".(($item['title'] !='')?
                                ((($item['work'])?("<a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=".$item['work']."&pdf' target='_blank'>".$item['title']."</a>"):($item['title'])).
                                    (($item['worktask'])?("<br /><a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=".$item['worktask']."&pdf' target='_blank'>Задание - ".$item['worktaskfile']."</a>"):"").
                                    (($item['dop1'])?("<br /><a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=".$item['dop1']."&pdf' target='_blank'>Дополнительный файл - ".$item['dop1file']."</a>"):"").
                                    (($item['dop2'])?("<br /><a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=".$item['dop2']."&pdf' target='_blank'>Дополнительный файл - ".$item['dop2file']."</a>"):""))
                                :'<center>-</center>')."</td>
                        <td>".$item['mark']."</td>
                        <td><center> - </center></td>
                        </tr>";
                    }
                    echo "</tbody></table><br />";
                }
                ?>
                <?php if(!empty($practic)): ?>
                    <center><b>Итоги прохождения практик</b></center>
                    <?php
                    foreach ($practic as $oneitem){
                        if($oneitem['nreclist'] != '') {
                            echo "<table border=\"1\" width=\"100%\">";
                            echo "<tr><th width=\"30%\">Наименование практики</th><th>" . $oneitem['discipline'] . "</th></tr>";
                            echo "<tr><th>Семестр, учебный год</th><td>" . $oneitem['semester'] . " семестр, " . $oneitem['yeared'] . " уч.год</td></tr>";
                            if($oneitem['sbegin']!=0){echo "<tr><th>Сроки прохождения</th><td>".(($oneitem['sbegin']!=0)?(" c ".CMisc::fromGalDate($oneitem['sbegin'])." по ".CMisc::fromGalDate($oneitem['send'])):"")."</td></tr>";}
                            if($oneitem['PredprName']!=""){echo "<tr><th>Наименование организации</th><td>" . $oneitem['PredprName'] . "</td></tr>";}
                            if($oneitem['PredprAddr']!=""){echo "<tr><th>Страна, город</th><td>" . $oneitem['PredprAddr'] . "</td></tr>";}
                            if($oneitem['id']!=""){echo "<tr><th>Отчетная работа</th><td>".$oneitem['text']." (<a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=".$oneitem['id']."&pdf' target='_blank'>".$oneitem['name']."</a>)</td></tr>";}
                            if($oneitem['comment']!=""){echo "<tr><th>Отзывы</th><td>".$oneitem['comment']."</td></tr>";}
                            if(false){echo "<tr><th>Отзыв руководителя от предприятия</th><td>-</td></tr>";}
                            if(false){echo "<tr><th>Профессиональные навыки</th><td>-</td></tr>";}
                            echo "<tr><th>Оценка</th><td>" . $oneitem['mark'] . "</td></tr>";
                            echo "</table><br />";
                        }
                    }
                    ?>

                <?php endif; ?>
                <?php if(!empty($otherWork)): ?>
                    <center><b>Отчётные работы по дисциплинам учебного плана</b></center>
                    <table border="1" width="100%">
                        <thead>
                        <tr>
                            <th><center>№<br />п/п</center></th>
                            <th><center>Наименование учебной дисциплины</center></th>
                            <th><center>Наименование работы</center></th>
                            <th><center>Семестр</center></th>
                            <th><center>Комментарии</center></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $privdisc = '';
                        foreach ($otherWork as $key => $oneitem){
                            echo "<tr><td><center>" . ($key + 1) . "</center></td>";
                            echo "<td><center>" . $oneitem['discipline'] . "</center></td>";
                            echo "<td>".$oneitem['text']." (<a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=".$oneitem['id']."&pdf' target='_blank'>".$oneitem['name']."</a>)</td>";
                            echo "<td><center>".$oneitem['semester']."</center></td>";
                            echo "<td>".$oneitem['comment']."</td></tr>";
                        }
                        /*
                        foreach ($otherWork as $key => $oneitem){
                            if($privdisc != $oneitem['discipline']) {
                                echo "<tr><td rowspan='".$oneitem['countdisc']."'><center>" . ($key + 1) . "</center></td>";
                                echo "<td rowspan='".$oneitem['countdisc']."'><center>" . $oneitem['discipline'] . "</center></td>";
                            }
                            $privdisc = $oneitem['discipline'];
                            echo "<td>".$oneitem['text']." (<a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=".$oneitem['id']."&pdf' target='_blank'>".$oneitem['name']."</a>)</td>";
                            echo "<td><center>".$oneitem['semester']."</center></td>";
                            echo "<td>".$oneitem['comment']."</td></tr>";
                        }*/
                        ?>
                        </tbody>
                    </table><br />
                <?php endif; ?>
                <center><b>Результаты прохождения государственной итоговой аттестации, предусмотренной образовательной программой</b></center>
                <?php if(!empty($themeAll)){
                    echo "<hr />";
                    foreach ($themeAll as $theme) {

                        if ($theme['title'] != "") {
                            echo "Выпускная квалификационная работа " . $theme['degree'] . " на тему: \"" . $theme['title'] . "\"." . "<br />";
                        }
                        if ($theme['fio'] != "") {
                            echo "Руководитель: " . $theme['post'] . " " . $theme['fio'] . "<br />";
                        }
                        if ($theme['chair'] != "") {
                            echo "Выпускающая кафедра: " . $theme['chair'] . "<br />";
                        }
                        if ($theme['mark'] != "") {
                            echo "Оценка: " . $theme['mark'] . "<br />";
                        }
                        if ($theme['date'] != "0000-00-00") {
                            echo "Дата защиты: " . date("d.m.Y г.", strtotime($theme['date'])) . "<br />";
                        }
                        if ($theme['worktit'] != "") {
                            echo "Файл работы: <a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=" . $theme['worktit'] . "&pdf' target='_blank'>" . $theme['worktitname'] . "</a>" . "<br />";
                        }
                        if ($theme['work'] != "") {
                            echo "Файл работы для проверки на плагиат: <a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=" . $theme['work'] . "&pdf' target='_blank'>" . $theme['workname'] . "</a>" . "<br />";
                        }
                        if ($theme['review'] != "") {
                            echo "Файл отзыва руководителя: <a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=" . $theme['review'] . "&pdf' target='_blank'>" . $theme['reviewname'] . "</a>" . "<br />";
                        }
                        if ($theme['rev'] != "") {
                            echo "Файл рецензии: <a href='http://omgtu.ru/ecab/modules/vkr2/file.php?id=" . $theme['rev'] . "&pdf' target='_blank'>" . $theme['revname'] . "</a>" . "<br />";
                        }
                        echo "<hr />";
                    }
                }else{
                    echo "Выпускная квалификационная работа: Отсутствует";
                } ?>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingFour">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    <center>3. НАУЧНО-ИССЛЕДОВАТЕЛЬСКАЯ РАБОТА</center>
                </a>
            </h4>
        </div>
        <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
            <div class="panel-body">
                <?php if(!empty($nauka)): ?>
                    <table style="width: 100%;" class="table table-bordered">
                        <tr><td><center>Год заявления</center></td><td><center>Показатель</center></td><td><center>Название</center></td>
                            <td><center>Критерий</center></td><td><center>Авторы (участие)</center></td></tr>
                        <?php
                        foreach ($nauka as $row){
                            echo "<tr>";
                            echo "<td><center>".$row['year']."</center></td>";
                            echo "<td>".$row['sort']."</td>";
                            echo "<td>".(($row['link'])?("<a href='".$row['link']."'>".$row['description']."</a>"):$row['description'])
                                ."<div style='font-size: 60%; color: gray;'>".Date('d.m.Y H:i', strtotime($row['dc']))." №".$row['npp']."</div>"."</td>";
                            echo "<td>".$row['req']."</td>";
                            echo "<td>".PortfolioController::getapp($row['npp']).PortfolioController::getcoau($row['npp'])."</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                <?php else:
                    echo "<center>Информация о научно-исследовательской деятельности отсутствует</center>";
                endif; ?>
                <center><a href="https://omgtu.ru/ecab/uchet.php" target="_blank">Переход на нучные достижения</a></center>
            </div>
        </div>
    </div>
    <?php
    if($info['degree'] == 'Аспирант'){
        echo '<div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingFive">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    <center>4. ДОКУМЕНТЫ АСПИРАНТА</center>
                </a>
            </h4>
        </div>
        <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
            <div class="panel-body">';?>
        <?php if(!empty($aspirant)): ?>
            <table style="width: 100%;" class="table table-bordered">
                <tr><td><center>Учебный год</center></td><td><center>Тип</center></td><td><center>Ссылка</center></td><td><center>Статус</center></td></tr>
                <?php
                foreach ($aspirant as $row){
                    echo "<tr>";
                    echo "<td class='".(($row['checkasp']==1)?'success':'')."'><center>".PortfolioController::yearandyear($row['yy'])."</center></td>";//надо уточнить
                    echo "<td class='".(($row['checkasp']==1)?'success':'')."'>".$row['Name']."</td>";
                    echo "<td class='".(($row['checkasp']==1)?'success':'')."'><center>"."<a href='https://omgtu.ru/ecab/modules/pf/getrepfile.php?id=" . $row['id'] . "' target='_blank'>" . "Скачать" . "</a>"."</center></td>";
                    echo "<td class='".(($row['checkasp']==1)?'success':'')."'><center>".(($row['checkasp']==1)?'Подтверждено':'-')."</center></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        <?php else:
            echo "<center>Информация аспиранта отсутствует</center>";
        endif; ?>
        <center><a href="https://omgtu.ru/ecab/pf.php" target="_blank">Страница аспиранта</a></center>
        <?php
        echo '</div>
        </div>
    </div>';

    }
    ?>

<?php /*
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingFour">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    <center>3. НАУЧНО-ИССЛЕДОВАТЕЛЬСКАЯ ДЕЯТЕЛЬНОСТЬ</center>
                </a>
            </h4>
        </div>
        <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
            <div class="panel-body">
                <center><b>Гранты</b></center>
                <center><b>Награды, полученные на конкурсах за лучшую НИР, на выставках и т.п.</b></center>
                <center><b>Участие в научно-практических конференциях</b></center>
                <center><b>Научные публикации</b></center>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingFive">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    <center>4. ДОПОЛНИТЕЛЬНЫЕ ЛИЧНЫЕ ДОСТИЖЕНИЯ</center>
                </a>
            </h4>
        </div>
        <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
            <div class="panel-body">

                <center><b>Участие в спортивных мероприятиях университета</b></center>
                <center><b>Спортивные достижения</b></center>
                <center><b>Участие в общественной жизни университета</b></center>
                <center><b>Опыт работы</b></center>
                <center><b>Дополнительные сведения</b></center>
            </div>
        </div>
    </div>*/?>
</div>
