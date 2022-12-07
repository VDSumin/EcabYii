<?php
/* @var $info array */
/* @var $marks array */

//var_dump($info, $marks);die;
function pr($data)
{
    echo '<pre>', var_export($data, true), '</pre>';
}

function getDisciplines($data = null)
{
    $disciplines = array();
    $type = '';

    if ($data) {
        foreach ($data as $item) {
            if (in_array($item['typeOfWork'], ['Зачёт', 'П/аттестация (н/диф.)', 'Контрольные работы'])) {
                $type = 'ladder';
            }
            if (in_array($item['typeOfWork'], ['Дифференцированный зачёт'])) {
                $type = 'difladder';
            }
            if (in_array($item['typeOfWork'], ['Экзамен', 'Кандидатский экзамен', 'П/аттестация (диф.)', 'Государственный экзамен', 'Комплексный экзамен'])) {
                $type = 'exam';
            }
            if (in_array($item['typeOfWork'], ['Курсовая работа', 'Курсовой проект', 'П/аттестация (КП/КР)'])) {
                $type = 'krp';
            }
            if (in_array($item['typeOfWork'], ['Практика', 'Практика СПО', 'Защита практики часть 1', 'Защита практики часть 2', 'Учебная практика (вуз)',
                'П/аттестация (практика)', 'Распределенная практика на предприятии', 'Защита практики', 'Научно-исследовательская работа', 'Распределенная практика ввузе'])) {
                $type = 'practic';
            }
            if (in_array($item['typeOfWork'], ['Защита бакалаврской работы', 'Защита выпускной квалификационной работы СПО', 'Защита магистерской работы',
                'Защита дипломного проекта', 'Защита дипломной работы', 'Представление научного доклада об основных результатах подготовленной научно-квалификационной работы',
                'Защита выпускной квалификационной работы (шт)', 'Государственный экзамен', 'Итоговая аттестация офицеров запаса',
                'Итоговая аттестация (квалификационные испытания) сержантов запаса'])) {
                $type = 'defend';
            }
            $disciplines[$item['semester']]['toleran'] = $item['Toleran'];
            $disciplines[$item['semester']][$type][] = $item;
        }
    }

    return $disciplines;
}

$disciplines = getDisciplines($marks);
ksort($disciplines);

function colorMark($mark)
{
    $class = '';

    if ($mark == 'Отлично' or $mark == 'Зачтено') {
        $class = 'success';
    } elseif ($mark == 'Хорошо') {
        $class = 'info';
    } elseif ($mark == 'Удовлетворительно') {
        $class = 'warning';
    } elseif ($mark == 'Неудовлетворительно' or $mark == 'Незачет' or $mark == 'Неявка' or $mark == 'Незачтено') {
        $class = 'danger';
    }

    return $class;
}

$cs = Yii::app()->clientScript;
$baseUrl = Yii::app()->theme->getBaseUrl();
/* @var $cs CClientScript */
$cs->registerCssFile($baseUrl . '/css/studbook.css');
$cs->registerScriptFile($baseUrl . '/js/studbook/main.js');
$this->pageTitle = 'Зачетная книжка';
?>
<br/><br/>
<div class="row row-offcanvas row-offcanvas-right">
    <div class="col-xs-12 col-sm-9">
        <p class="pull-right visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Навигация</button>
        </p>
        <div class="jumbotron bs-example-bg-classes">
            <h1>
                <?php echo $info['fio']; ?>
            </h1>

            <p>
                <b>Номер книжки:</b> <?php echo $info['recordBook']; ?><br>
                <b>Специальность:</b> <?php echo $info['spec']; ?><br>
                <b>Группа:</b> <?php echo $info['studGroup']; ?><br>
                <b>Форма обучения:</b> <?php echo $info['formEdu']; ?><br>
                <?php
                if ($info['promAuth'] != 'логин: , пароль: ' and $info['faculS'] != 'ИЗО') {
                    echo '<b>Реквизиты в СДО "Прометей":</b> ' . $info['promAuth'] . '<br>';
                }
                if (!empty($info['libTicket'])) {
                    echo '<b>Читательский билет:</b> № <i>' . $info['libTicket'] . '</i>';
                    echo '  для доступа в <a target="_blank" href="https://yadi.sk/i/zmZSd30tN6OtTw">ЭБС "Арбуз"</a></br>';
                }
                echo CHtml::link('Доступ к ресурсам библиотеки',
                    'https://www.omgtu.ru/ecab/%D0%AD%D0%BB%D0%B5%D0%BA%D1%82%D1%80%D0%BE%D0%BD%D0%BD%D1%8B%D0%B5%20%D1%80%D0%B5%D1%81%D1%83%D1%80%D1%81%D1%8B%20%D0%B1%D0%B8%D0%B1%D0%BB%D0%B8%D0%BE%D1%82%D0%B5%D0%BA%D0%B8.pdf',
                    array('target' => '_blank')); ?>
            </p>
        </div>

        <div class="tab-content">
            <?php if ($disciplines) : ?>
                <?php foreach ($disciplines as $term => $discipline) : ?>
                    <div role="tabpanel" class="row tab-pane" id="semestr<?php echo $term; ?>">
                        <?php if (isset($discipline['exam'])) : $i = 0; ?>
                            <div class="col-xs-12">
                                <h3>Экзамены:</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название предмета</th>
                                            <th>Кол. часов</th>
                                            <th>Рейтинг по КН</th>
                                            <th>Рейтинг</th>
                                            <th>Оценка</th>
                                            <th>Дата сдачи</th>
                                            <th>Преподаватель</th>
                                            <th>В дип</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach ($discipline['exam'] as $examDiscipline) : $i++; ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td class="<?php echo colorMark($examDiscipline['mark']); ?>"><?php echo $examDiscipline['discipline'] .
                                                        (($examDiscipline['typeOfWork'] == 'Комплексный экзамен') ? ' (Компл. Экз.)' :
                                                            (($examDiscipline['typeOfWork'] == 'Кандидатский экзамен') ? ' (Канд. Экз.)' : '')); ?></td>
                                                <td class="<?php echo colorMark($examDiscipline['mark']); ?>"><?php echo $examDiscipline['hoursOfPlan'] ?></td>
                                                <td class="<?php echo colorMark($examDiscipline['mark']); ?>"><?php echo (!empty($examDiscipline['rcw'])) ? ($examDiscipline['rcw']) : '-'; ?></td>
                                                <td class="<?php echo colorMark($examDiscipline['mark']); ?>"><?php echo $examDiscipline['r']; ?></td>
                                                <td class="<?php echo colorMark($examDiscipline['mark']); ?>">
                                                    <?php echo !empty($examDiscipline['mark']) ? $examDiscipline['mark'] : '-'; ?>
                                                </td>
                                                <td class="<?php echo colorMark($examDiscipline['mark']); ?>">
                                                    <?php if (!empty($examDiscipline['mark'])) {
                                                        echo $examDiscipline['markDate'];
                                                    } else {
                                                        echo '-';
                                                    } ?>
                                                </td>
                                                <td class="<?php echo colorMark($examDiscipline['mark']); ?>">
                                                    <?php echo $examDiscipline['examiner']; ?>
                                                </td>
                                                <td class="<?php echo colorMark($examDiscipline['mark']); ?>">
                                                    <?php echo(($examDiscipline['listInDiplom'] == 1) ? 'Да' : (($examDiscipline['listInDiplom'] == 0) ? 'Нет' : '')); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($discipline['difladder'])) : $i = 0; ?>
                            <div class="col-xs-12">
                                <h3>Дифференцированный зачет:</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название предмета</th>
                                            <th>Кол. часов</th>
                                            <th>Рейтинг по КН</th>
                                            <th>Рейтинг</th>
                                            <th>Оценка</th>
                                            <th>Дата сдачи</th>
                                            <th>Преподаватель</th>
                                            <th>В дип</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach ($discipline['difladder'] as $ladderDiscipline) : $i++; ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>"><?php echo $ladderDiscipline['discipline']; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>"><?php echo $ladderDiscipline['hoursOfPlan']; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>"><?php echo (!empty($ladderDiscipline['rcw'])) ? ($ladderDiscipline['rcw']) : '-'; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>"><?php echo $ladderDiscipline['r']; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>">
                                                    <?php echo !empty($ladderDiscipline['mark']) ? $ladderDiscipline['mark'] : '-'; ?>
                                                </td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>">
                                                    <?php if (!empty($ladderDiscipline['mark'])) {
                                                        echo $ladderDiscipline['markDate'];
                                                    } else {
                                                        echo '-';
                                                    } ?>
                                                </td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>">
                                                    <?php echo $ladderDiscipline['examiner']; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>">
                                                    <?php echo(($ladderDiscipline['listInDiplom'] == 1) ? 'Да' : (($ladderDiscipline['listInDiplom'] == 0) ? 'Нет' : '')); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($discipline['ladder'])) : $i = 0; ?>
                            <div class="col-xs-12">
                                <h3>Зачёты:</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название предмета</th>
                                            <th>Кол. часов</th>
                                            <th>Рейтинг по КН</th>
                                            <th>Рейтинг</th>
                                            <th>Оценка</th>
                                            <th>Дата сдачи</th>
                                            <th>Преподаватель</th>
                                            <th>В дип</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach ($discipline['ladder'] as $ladderDiscipline) : $i++; ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>"><?php echo $ladderDiscipline['discipline']; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>"><?php echo $ladderDiscipline['hoursOfPlan']; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>"><?php echo (!empty($ladderDiscipline['rcw'])) ? ($ladderDiscipline['rcw']) : '-'; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>"><?php echo $ladderDiscipline['r']; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>">
                                                    <?php echo !empty($ladderDiscipline['mark']) ? $ladderDiscipline['mark'] : '-'; ?>
                                                </td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>">
                                                    <?php if (!empty($ladderDiscipline['mark'])) {
                                                        echo $ladderDiscipline['markDate'];
                                                    } else {
                                                        echo '-';
                                                    } ?>
                                                </td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>">
                                                    <?php echo $ladderDiscipline['examiner']; ?></td>
                                                <td class="<?php echo colorMark($ladderDiscipline['mark']); ?>">
                                                    <?php echo(($ladderDiscipline['listInDiplom'] == 1) ? 'Да' : (($ladderDiscipline['listInDiplom'] == 0) ? 'Нет' : '')); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($discipline['krp'])) : $i = 0; ?>
                            <div class="col-xs-12">
                                <h3>Курсовые работы:</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название предмета</th>
                                            <th>Кол. часов</th>
                                            <th>Тип работ</th>
                                            <th>Рейтинг</th>
                                            <th>Оценка</th>
                                            <th>Дата сдачи</th>
                                            <th>Преподаватель</th>
                                            <th>В дип</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach ($discipline['krp'] as $krpDiscipline) : $i++; ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td class="<?php echo colorMark($krpDiscipline['mark']); ?>"><?php echo $krpDiscipline['discipline']; ?></td>
                                                <td class="<?php echo colorMark($krpDiscipline['mark']); ?>"><?php echo $krpDiscipline['hoursOfPlan']; ?></td>
                                                <td class="<?php echo colorMark($krpDiscipline['mark']); ?>"><?= ($krpDiscipline['typeOfWork'] == 'Курсовая работа') ? 'КР' : ($krpDiscipline['typeOfWork'] == 'Курсовой проект') ? 'КП' : $krpDiscipline['hoursOfPlan']; ?></td>
                                                <td class="<?php echo colorMark($krpDiscipline['mark']); ?>"><?php echo $krpDiscipline['r']; ?></td>
                                                <td class="<?php echo colorMark($krpDiscipline['mark']); ?>">
                                                    <?php echo !empty($krpDiscipline['mark']) ? $krpDiscipline['mark'] : '-'; ?>
                                                </td>
                                                <td class="<?php echo colorMark($krpDiscipline['mark']); ?>">
                                                    <?php if (!empty($krpDiscipline['mark'])) {
                                                        echo $krpDiscipline['markDate'];
                                                    } else {
                                                        echo '-';
                                                    } ?>
                                                </td>
                                                <td class="<?php echo colorMark($krpDiscipline['mark']); ?>">
                                                    <?php echo $krpDiscipline['examiner']; ?>
                                                </td>
                                                <td class="<?php echo colorMark($krpDiscipline['mark']); ?>">
                                                    <?php echo(($krpDiscipline['listInDiplom'] == 1) ? 'Да' : (($krpDiscipline['listInDiplom'] == 0) ? 'Нет' : '')); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($discipline['practic'])) : $i = 0; ?>
                            <div class="col-xs-12">
                                <h3>Практики:</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название практики</th>
                                            <th>Кол. часов</th>
                                            <th>Оценка</th>
                                            <th>Дата сдачи</th>
                                            <th>Преподаватель</th>
                                            <th>В дип</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach ($discipline['practic'] as $practicDiscipline) : $i++; ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td class="<?php echo colorMark($practicDiscipline['mark']); ?>"><?php echo $practicDiscipline['discipline']; ?></td>
                                                <td class="<?php echo colorMark($practicDiscipline['mark']); ?>"><?php echo !empty($practicDiscipline['hoursOfPlan']) ? $practicDiscipline['hoursOfPlan'] : '-'; ?></td>
                                                <td class="<?php echo colorMark($practicDiscipline['mark']); ?>">
                                                    <?php echo !empty($practicDiscipline['mark']) ? $practicDiscipline['mark'] : '-'; ?>
                                                </td>
                                                <td class="<?php echo colorMark($practicDiscipline['mark']); ?>">
                                                    <?php if (!empty($practicDiscipline['mark'])) {
                                                        echo $practicDiscipline['markDate'];
                                                    } else {
                                                        echo '-';
                                                    } ?>
                                                </td>
                                                <td class="<?php echo colorMark($practicDiscipline['mark']); ?>">
                                                    <?php echo $practicDiscipline['examiner']; ?>
                                                </td>
                                                <td class="<?php echo colorMark($practicDiscipline['mark']); ?>">
                                                    <?php echo(($practicDiscipline['listInDiplom'] == 1) ? 'Да' : (($practicDiscipline['listInDiplom'] == 0) ? 'Нет' : '')); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($discipline['gos'])) : $i = 0; ?>
                            <div class="col-xs-12">
                                <h3>Государственный экзамен:</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название</th>
                                            <th>Кол. часов</th>
                                            <th>Оценка</th>
                                            <th>Дата сдачи</th>
                                            <th>Преподаватель</th>
                                            <th>В дип</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach ($discipline['gos'] as $gosDiscipline) : $i++; ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>"><?php echo $gosDiscipline['discipline']; ?></td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>"><?php echo $gosDiscipline['hoursOfPlan']; ?></td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>">
                                                    <?php echo !empty($gosDiscipline['mark']) ? $gosDiscipline['mark'] : '-'; ?>
                                                </td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>">
                                                    <?php if (!empty($gosDiscipline['mark'])) {
                                                        echo $gosDiscipline['markDate'];
                                                    } else {
                                                        echo '-';
                                                    } ?>
                                                </td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>">
                                                    <?php echo $gosDiscipline['examiner']; ?>
                                                </td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>">
                                                    <?php echo(($gosDiscipline['listInDiplom'] == 1) ? 'Да' : (($gosDiscipline['listInDiplom'] == 0) ? 'Нет' : '')); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($discipline['defend'])) : $i = 0; ?>
                            <div class="col-xs-12">
                                <h3>Государственный экзамен:</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Название</th>
                                            <th>Кол. часов</th>
                                            <th>Оценка</th>
                                            <th>Дата сдачи</th>
                                            <th>Преподаватель</th>
                                            <th>В дип</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach ($discipline['defend'] as $gosDiscipline) : $i++; ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>"><?php echo $gosDiscipline['discipline']; ?></td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>"><?php echo $gosDiscipline['hoursOfPlan']; ?></td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>">
                                                    <?php echo !empty($gosDiscipline['mark']) ? $gosDiscipline['mark'] : '-'; ?>
                                                </td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>">
                                                    <?php if (!empty($gosDiscipline['mark'])) {
                                                        echo $gosDiscipline['markDate'];
                                                    } else {
                                                        echo '-';
                                                    } ?>
                                                </td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>">
                                                    <?php echo $gosDiscipline['examiner']; ?>
                                                </td>
                                                <td class="<?php echo colorMark($gosDiscipline['mark']); ?>">
                                                    <?php echo(($gosDiscipline['listInDiplom'] == 1) ? 'Да' : (($gosDiscipline['listInDiplom'] == 0) ? 'Нет' : '')); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div><!--/row-->
    </div><!--/.col-xs-12.col-sm-9-->

    <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
        <?php if ($disciplines) : ?>
            <ul class="list-group semestrs" role="tablist" id="myTab">
                <?php foreach ($disciplines as $term => $discipline) : ?>
                    <li class="list-group-inner">
                        <a class="list-group-item" href="#semestr<?php echo $term; ?>"
                           aria-controls="<?php echo $term; ?>" role="tab" data-toggle="tab"
                           style="background-color:<?= ($discipline['toleran']) ? '#dff0d8 !important' : '#f2dede !important' ?> ">
                            <?= ($discipline['toleran']) ?
                                '<i class="glyphicon glyphicon-ok"></i> Семестр ' . $term . ' (допуск есть)' :
                                '<i class="glyphicon glyphicon-remove"></i> Семестр ' . $term . ' (нет допуска)';
                            ?>

                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div><!--/.sidebar-offcanvas-->
</div><!--/row-->
