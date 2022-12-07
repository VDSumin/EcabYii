<?php

class ExportController extends Controller
{

    public $layout = '//layouts/column1';

    private function checkAccess($id, $date)
    {
        $fnpp = Yii::app()->user->getFnpp();
        if (!$fnpp || is_null($id)) {
            return false;
        }
        if (
            !(in_array($id, MonitorAccess::getStructuresByFnpp($fnpp)))
        ) {
            if ((!(Yii::app()->db2->createCommand()
                        ->select('*')
                        ->from('wkardc_rp w')
                        ->where('w.struct = ' . $id . ' AND w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL) AND w.fnpp = ' . $fnpp)
                        ->queryRow()
                    ) || ($date !== date('Y-m-d'))) && !in_array($fnpp, Controller::ADMIN_FNPP)) {
                return false;
            }
        }
        Yii::app()->session['reportDate'] = $date;
        return true;
    }

    public function actionDownloadReport($date)
    {
        $path = 'protected/data/uploads/temp/';
        $file = 'Ежедневный отчет по ОмГТУ ' . $date.'.xlsx';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename = ' . $file);
        header('Cache-Control: max-age=0');
        $filename = $path . $file;
        echo file_get_contents($filename);
    }

    public function actionDay($id, $date)
    {
        Yii::app()->session['reportDate'] = $date;
        self::getDay($id);
        /*if ($this->checkAccess($id, $date)) {
            self::getDay($id);
        } else {
            echo 'Нет доступа';
            return '';
        }*/
    }

    public function actionUpdateRetired()
    {
        if (in_array(Yii::app()->user->getFnpp(), Controller::ADMIN_FNPP)) {
            set_time_limit(0);
            $sql = Yii::app()->db2->createCommand()
                ->selectDistinct('f.npp')
                ->from('wkardc_rp w')
                ->join('fdata f', 'f.npp=w.fnpp')
                ->join('struct_d_rp s', 's.npp = w.struct AND s.prudal = 0')
                ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                ->andWhere('f.fam NOT LIKE \'%Test%\'')
                ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) >= 55')
                ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) <= 65')
                ->queryAll();
            $i = 1;
            foreach ($sql as $npp) {
                if (FilterEverydayForm::checkRetired($npp['npp'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_category
                        SET category = ' . ChiefReportsWeek::CATEGORY_RETIRED . '
                        WHERE fnpp = ' . $npp['npp'])->query();
                    echo $i . ')Updated ' . Fdata::model()->findByPk($npp['npp'])->getFIO() . '<br/>';
                    $i++;
                }
            }
        }
    }

    public static function checkForDuplicates()
    {
        Yii::app()->db->createCommand('DROP TEMPORARY TABLE IF EXISTS `t_temp`;
                CREATE TEMPORARY TABLE `t_temp`
                as (
                SELECT max(id) as id
                FROM `tbl_chief_reports_day`
                GROUP BY fnpp, createdAt
                );
                
                DELETE from `tbl_chief_reports_day`
                WHERE `tbl_chief_reports_day`.id not in (
                SELECT id FROM t_temp
                );')->query();
        Yii::app()->db->createCommand('DROP TEMPORARY TABLE IF EXISTS `t_temp`;
                CREATE TEMPORARY TABLE `t_temp`
                as (
                SELECT max(id) as id
                FROM `tbl_chief_reports_week`
                GROUP BY fnpp, createdAt
                );
                
                DELETE from `tbl_chief_reports_week`
                WHERE `tbl_chief_reports_week`.id not in (
                SELECT id FROM t_temp
                );')->query();
        Yii::app()->db->createCommand('DROP TEMPORARY TABLE IF EXISTS `t_temp`;
                CREATE TEMPORARY TABLE `t_temp`
                as (
                SELECT max(id) as id
                FROM `tbl_chief_reports_category`
                GROUP BY fnpp
                );
                
                DELETE from `tbl_chief_reports_category`
                WHERE `tbl_chief_reports_category`.id not in (
                SELECT id FROM t_temp
                );')->query();
        Yii::app()->db->createCommand('DROP TEMPORARY TABLE IF EXISTS `t_temp`;
                CREATE TEMPORARY TABLE `t_temp`
                as (
                SELECT max(id) as id
                FROM `tbl_chief_reports_covid`
                GROUP BY fnpp
                );
                
                DELETE from `tbl_chief_reports_covid`
                WHERE `tbl_chief_reports_covid`.id not in (
                SELECT id FROM t_temp
                );')->query();
    }

    private static function getDay($id)
    {
        function setCellsBold($activeSheet, $arr)
        {
            foreach ($arr as $element) {
                $activeSheet->getStyleByColumnAndRow($element[0], $element[1])->getFont()->setBold(true);
            }
        }

        function setCellItalic($activeSheet, $column, $row)
        {
            $activeSheet->getStyleByColumnAndRow($column, $row)->getFont()->setItalic(true);
        }

        function setColumnsWidth($activeSheet, $arr)
        {
            foreach ($arr as $element) {
                $activeSheet->getColumnDimension($element[0])->setWidth($element[1]);
            }
        }

        function setCellsLeft($activeSheet, $arr)
        {
            foreach ($arr as $element) {
                $activeSheet->getStyleByColumnAndRow($element[0], $element[1])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            }
        }


        function writeOnePerson($activeSheet, $row, $person, $sql)
        {
            $statuses = array(
                'Находится на территории региона',
                'Находится за территорией региона',
                'Находится за пределами РФ',
                'Информация о местонахождении неизвестна',
                'Посещал(а) другие страны в течение 30 календарных дней до даты предоставления сведений',
                'Сведения не указаны',
            );
            setCellsLeft($activeSheet, array(
                [0, $row],
                [1, $row],
                [2, $row],
                [7, $row],
                [12, $row],
                [13, $row],
            ));
            $activeSheet->setCellValueByColumnAndRow(0, $row, mb_strtoupper(mb_substr($person['fam'], 0, 1)) . mb_substr(mb_strtolower($person['fam']), 1, mb_strlen($person['fam']) - 1) . " " . ucfirst($person['nam']) . " " . ucfirst($person['otc']));
            $activeSheet->setCellValueByColumnAndRow(1, $row, mb_strtoupper(mb_substr($person['dolgnost'], 0, 1)) . mb_substr($person['dolgnost'], 1, mb_strlen($person['dolgnost']) - 1));
            $activeSheet->setCellValueByColumnAndRow(2, $row, $person['name']);
            $activeSheet->setCellValueByColumnAndRow(3, $row, $person['sovm']);
            $activeSheet->setCellValueByColumnAndRow(4, $row, $person['category']);
            $activeSheet->setCellValueByColumnAndRow(5, $row, $person['age']);

            if (isset($sql['category'])) {
                if ((int)$person['age'] >= 65) {
                    $activeSheet->setCellValueByColumnAndRow(6, $row, ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_OLD));
                } else {
                    $activeSheet->setCellValueByColumnAndRow(6, $row, ChiefReportsWeek::getCategory($sql['category']));
                }
            } else {
                $activeSheet->setCellValueByColumnAndRow(6, $row, 'Сведения не указаны');
            }

            if (isset($sql['format'])) {
                if (in_array($sql['reasonId'], [1, 2])) {
                    $activeSheet->setCellValueByColumnAndRow(9, $row, ChiefReportsWeek::getFormat(4));
                } else {
                    $activeSheet->setCellValueByColumnAndRow(9, $row, ChiefReportsWeek::getFormat($sql['format']));
                }
                $activeSheet->setCellValueByColumnAndRow(10, $row, ChiefReportsWeek::getReasonId($sql['reasonId']));
            } else {
                $activeSheet->setCellValueByColumnAndRow(9, $row, 'Сведения не указаны');
                $activeSheet->setCellValueByColumnAndRow(10, $row, 'Сведения не указаны');
            }

            if (isset($sql['status'])) {
                if ((int)$sql['status'] === 2) {
                    $activeSheet->setCellValueByColumnAndRow(11, $row, $statuses[2]);
                    $activeSheet->setCellValueByColumnAndRow(12, $row, $sql['country']);
                } elseif ((int)$sql['wasAbroad'] === 1) {
                    $activeSheet->setCellValueByColumnAndRow(11, $row, $statuses[4]);
                    $activeSheet->setCellValueByColumnAndRow(12, $row, $sql['country2']);
                } else {
                    $activeSheet->setCellValueByColumnAndRow(11, $row, $statuses[$sql['status']]);
                }
                $activeSheet->setCellValueByColumnAndRow(13, $row, $sql['additional']);
            } else {
                $activeSheet->setCellValueByColumnAndRow(11, $row, $statuses[5]);
            }

            $CovidStatus = array(
                'Сведения не указаны',
                'переболел COVID-19',
                'привился однократно (Сделана первая прививка)',
                'привился двукратно (Сделана вторая прививка)',
                'имеет официальный мед.отвод',
                'подал заявку на прививку через портал госуслуг',
                'подал заявку на прививку в службу охраны труда',
                'отказался делать прививку',
                'отстранен от работы',
                'Иное',
                'Ревакцинация'
            );

            if (isset($sql['covidStatus'])) {
                if ($sql['covidStatus'] == 0) {
                    $activeSheet->setCellValueByColumnAndRow(7, $row, $CovidStatus[0]);
                } else {
                    $date = in_array(date("d.m.Y", strtotime($sql['date'])), ['01.01.1970', '01.01.1000']) || is_null($sql['date']) ? '' : date("d.m.Y", strtotime($sql['date']));
                    $activeSheet->setCellValueByColumnAndRow(7, $row, $CovidStatus[$sql['covidStatus']]);
                    $activeSheet->setCellValueByColumnAndRow(8, $row, $date);
                }
            }
        }

        function writeSummary($activeSheet, $id)
        {
            setCellsLeft($activeSheet, array(
                [0, 2],
                [0, 3],
                [0, 4],
                [0, 5],
                [0, 6],
                [0, 7],
                [0, 8],
                [0, 9],
                [0, 10],
                [0, 11],
                [0, 12],
                [0, 13],
                [0, 14],
                [0, 15],
                [0, 16],
                [0, 17],
                [0, 18],
                [0, 19],
                [0, 20],
                [0, 21],
                [0, 22],
                [0, 23],
                [0, 24], [0, 25], [0, 26], [0, 27], [0, 28], [0, 29], [0, 30], [0, 31], [0, 32], [0, 33], [0, 34], [0, 35]
            ));

            $row = 2;
            if ($id == 6) {
                $bigSql = Yii::app()->db2->createCommand()
                    ->select('f.npp, w.vpo1cat')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
//                    ->group('f.npp, w.vpo1cat, w.struct')
                    ->queryAll();
            } else {
                $bigSql = Yii::app()->db2->createCommand()
                    ->select('f.npp, w.vpo1cat')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
//                    ->group('f.npp, w.vpo1cat, w.struct')
                    ->queryAll();
            }
            $all = count($bigSql);
            $arr = array();
            foreach ($bigSql as $item) {
                array_push($arr, $item['npp']);
            }
            $npps = implode(', ', $arr);
            if ($id == 6) $activeSheet->setCellValueByColumnAndRow(0, $row, 'Общее число сотрудников (ВСЕГО СОТРУДНИКОВ:ОМР+внешние сов-ли+внутрение совместители) Далее - всего');
            else $activeSheet->setCellValueByColumnAndRow(0, $row, 'Общее число сотрудников');
            $activeSheet->setCellValueByColumnAndRow(1, $row, $all);
            $sql = Yii::app()->db->createCommand()
                ->selectDistinct('format, fnpp')
                ->from('tbl_chief_reports_week')
                ->where('fnpp IN (' . $npps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            $formats = array(
                0 => 0,
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
            );
            if ($sql) foreach ($sql as $item) {
                foreach ($bigSql as $value) {
                    if ($value['npp'] == $item['fnpp']) {
                        $formats[(int)$item['format']] += 1;
                    }
                }
            }
            foreach ($formats as $i => $format) {
                $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
            }
            $sql = Yii::app()->db->createCommand()
                ->selectDistinct('reasonId, fnpp')
                ->from('tbl_chief_reports_week')
                ->where('fnpp IN (' . $npps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            if ($sql) {
                $reasons = array(
                    -1 => 0,
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                );
                foreach ($sql as $item) {
                    foreach ($bigSql as $value) {
                        if ($value['npp'] == $item['fnpp']) {
                            $reasons[(int)$item['reasonId']] += 1;
                        }
                    }
                }
                $reasonsString = '';
                foreach ($reasons as $i => $reason) {
                    if ($reason > 0) {
                        $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                    }
                }
                if (strlen($reasonsString) > 1) {
                    $reasonsString = substr($reasonsString, 0, -2);
                }
                $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
            }


            $category = 0;
            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'В том числе:');


            if ($id == 6) {
                $row++;
                $smallSql = Yii::app()->db2->createCommand()
                    ->select('f.npp, w.vpo1cat')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн" OR w.sovm = "ВнеСовм"')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
                $allSmall = count($smallSql);
                $arr = array();
                foreach ($smallSql as $item) {
                    array_push($arr, $item['npp']);
                }
                $nppsSmall = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Общее число сотрудников (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allSmall);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $nppsSmall . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $nppsSmall . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }

                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Руководитель организации');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 1);
            }


            $row++;
            $category++;
            if ($id == 6) $activeSheet->setCellValueByColumnAndRow(0, $row, 'Люди, достигшие пенсионного возраста всего');
            else $activeSheet->setCellValueByColumnAndRow(0, $row, 'Люди, достигшие пенсионного возраста');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_category')
                ->where('fnpp IN (' . $npps . ')
                AND category = ' . $category)
                ->queryAll()
            ) {
                $allNew = 0;
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($bigSql as $value) {
                        if ($value['npp'] == $item['fnpp']) {
                            $formats[(int)$item['format']] += 1;
                            $allNew += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
            }

            if ($id == 6) {
                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Люди, достигшие пенсионного возраста (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                if ($newNpps = Yii::app()->db->createCommand()
                    ->selectDistinct('fnpp')
                    ->from('tbl_chief_reports_category')
                    ->where('fnpp IN (' . $nppsSmall . ')
                AND category = ' . $category)
                    ->queryAll()
                ) {
                    $allNew = 0;
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['fnpp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $formats[(int)$item['format']] += 1;
                                $allNew += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            -1 => 0,
                            0 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                }
            }


            $row++;
            $category++;
            if ($id == 6) $activeSheet->setCellValueByColumnAndRow(0, $row, 'Беременные женщины всего');
            else $activeSheet->setCellValueByColumnAndRow(0, $row, 'Беременные женщины');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_category')
                ->where('fnpp IN (' . $npps . ')
                AND category = ' . $category)
                ->queryAll()
            ) {
                $allNew = 0;
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($bigSql as $value) {
                        if ($value['npp'] == $item['fnpp']) {
                            $formats[(int)$item['format']] += 1;
                            $allNew += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
            }

            if ($id == 6) {
                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Беременные женщины (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                if ($newNpps = Yii::app()->db->createCommand()
                    ->selectDistinct('fnpp')
                    ->from('tbl_chief_reports_category')
                    ->where('fnpp IN (' . $nppsSmall . ')
                AND category = ' . $category)
                    ->queryAll()
                ) {
                    $allNew = 0;
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['fnpp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $formats[(int)$item['format']] += 1;
                                $allNew += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                }
            }


            $row++;
            $category++;
            if ($id == 6) $activeSheet->setCellValueByColumnAndRow(0, $row, 'Женщины, имеющие детей в возрасте до 14 лет всего');
            else $activeSheet->setCellValueByColumnAndRow(0, $row, 'Женщины, имеющие детей в возрасте до 14 лет');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_category')
                ->where('fnpp IN (' . $npps . ')
                AND category = ' . $category)
                ->queryAll()
            ) {
                $allNew = 0;
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($bigSql as $value) {
                        if ($value['npp'] == $item['fnpp']) {
                            $formats[(int)$item['format']] += 1;
                            $allNew += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
            }

            if ($id == 6) {
                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Женщины, имеющие детей в возрасте до 14 лет (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                if ($newNpps = Yii::app()->db->createCommand()
                    ->selectDistinct('fnpp')
                    ->from('tbl_chief_reports_category')
                    ->where('fnpp IN (' . $nppsSmall . ')
                AND category = ' . $category)
                    ->queryAll()
                ) {
                    $allNew = 0;
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['fnpp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $formats[(int)$item['format']] += 1;
                                $allNew += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                }
            }


            $row++;
            $category++;
            if ($id == 6) $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники в возрасте старше 65 лет всего');
            else $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники в возрасте старше 65 лет');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_category')
                ->where('fnpp IN (' . $npps . ')
                AND category = ' . $category)
                ->queryAll()
            ) {
                $allNew = 0;
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($bigSql as $value) {
                        if ($value['npp'] == $item['fnpp']) {
                            $formats[(int)$item['format']] += 1;
                            $allNew += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
            }

            if ($id == 6) {
                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники в возрасте старше 65 лет (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                if ($newNpps = Yii::app()->db->createCommand()
                    ->selectDistinct('fnpp')
                    ->from('tbl_chief_reports_category')
                    ->where('fnpp IN (' . $nppsSmall . ')
                AND category = ' . $category)
                    ->queryAll()
                ) {
                    $allNew = 0;
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['fnpp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $formats[(int)$item['format']] += 1;
                                $allNew += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                }
            }


            $row++;
            $category++;
            if ($id == 6) $activeSheet->setCellValueByColumnAndRow(0, $row, 'Имеющие заболевания всего');
            else $activeSheet->setCellValueByColumnAndRow(0, $row, 'Имеющие заболевания');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_category')
                ->where('fnpp IN (' . $npps . ')
                AND category = ' . $category)
                ->queryAll()
            ) {
                $allNew = 0;
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($bigSql as $value) {
                        if ($value['npp'] == $item['fnpp']) {
                            $formats[(int)$item['format']] += 1;
                            $allNew += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
            }

            if ($id == 6) {
                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Имеющие заболевания (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                if ($newNpps = Yii::app()->db->createCommand()
                    ->selectDistinct('fnpp')
                    ->from('tbl_chief_reports_category')
                    ->where('fnpp IN (' . $nppsSmall . ')
                AND category = ' . $category)
                    ->queryAll()
                ) {
                    $allNew = 0;
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['fnpp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $formats[(int)$item['format']] += 1;
                                $allNew += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                }
            }


            $row++;
            if ($id == 6) $activeSheet->setCellValueByColumnAndRow(0, $row, 'Численность ППС всего');
            else $activeSheet->setCellValueByColumnAndRow(0, $row, 'Численность ППС');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('w.vpo1cat LIKE \'ППС\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('w.vpo1cat LIKE \'ППС\'')
                    ->queryAll();
            }
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['npp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($bigSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat'] == 'ППС') {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp'] and $value['vpo1cat'] == 'ППС') {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }

            if ($id == 6) {
                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Численность ППС (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('w.vpo1cat LIKE \'ППС\'')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн" OR w.sovm = "ВнеСовм"')
                    ->queryAll();
                if ($newNpps) {
                    $allNew = count($newNpps);
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['npp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp'] and $value['vpo1cat'] == 'ППС') {
                                $formats[(int)$item['format']] += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp'] and $value['vpo1cat'] == 'ППС') {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                }
            }


            $row++;
            if ($id == 6) $activeSheet->setCellValueByColumnAndRow(0, $row, 'Численность другие категории всего');
            else $activeSheet->setCellValueByColumnAndRow(0, $row, 'Численность другие категории');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('w.vpo1cat NOT LIKE \'ППС\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('w.vpo1cat NOT LIKE \'ППС\'')
                    ->queryAll();
            }
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['npp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($bigSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat'] != 'ППС') {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp'] and $value['vpo1cat'] != 'ППС') {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }

            if ($id == 6) {
                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Численность другие категории (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('w.vpo1cat NOT LIKE \'ППС\'')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн" OR w.sovm = "ВнеСовм"')
                    ->queryAll();
                if ($newNpps) {
                    $allNew = count($newNpps);
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['npp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp'] and $value['vpo1cat'] != 'ППС') {
                                $formats[(int)$item['format']] += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp'] and $value['vpo1cat'] != 'ППС') {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                }


                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Мужчины 60,5 - 61 всего');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('f.pol = 1')
                    ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) >= 60.5')
                    ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) <= 61')
                    ->queryAll();
                if ($newNpps) {
                    $allNew = count($newNpps);
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['npp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                                $formats[(int)$item['format']] += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($bigSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                }
                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Мужчины 60,5 - 61 (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('f.pol = 1')
                    ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) >= 60.5')
                    ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) <= 61')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн" OR w.sovm = "ВнеСовм"')
                    ->queryAll();
                if ($newNpps) {
                    $allNew = count($newNpps);
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['npp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                                $formats[(int)$item['format']] += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                }


                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Женщины 55,5 - 56 всего');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('f.pol = 2')
                    ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) >= 55.5')
                    ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) <= 56')
                    ->queryAll();
                if ($newNpps) {
                    $allNew = count($newNpps);
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['npp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($bigSql as $value) {
                            if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                                $formats[(int)$item['format']] += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($bigSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                }

                $row++;
                $activeSheet->setCellValueByColumnAndRow(0, $row, 'Женщины 55,5 - 56 (ОМР+внешние сов-ли)');
                $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
                $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('f.pol = 2')
                    ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) >= 55.5')
                    ->andWhere('ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) <= 56')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн" OR w.sovm = "ВнеСовм"')
                    ->queryAll();
                if ($newNpps) {
                    $allNew = count($newNpps);
                    $arr = array();
                    foreach ($newNpps as $npp) {
                        array_push($arr, $npp['npp']);
                    }
                    $newNpps = implode(', ', $arr);
                    $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('format, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    $formats = array(
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                    );
                    if ($sql) foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                                $formats[(int)$item['format']] += 1;
                            }
                        }
                    }
                    foreach ($formats as $i => $format) {
                        $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                    }
                    $sql = Yii::app()->db->createCommand()
                        ->selectDistinct('reasonId, fnpp')
                        ->from('tbl_chief_reports_week')
                        ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                        ->queryAll();
                    if ($sql) {
                        $reasons = array(
                            0 => 0, -1 => 0,
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        );
                        foreach ($sql as $item) {
                            foreach ($smallSql as $value) {
                                if ($value['npp'] == $item['fnpp']) {
                                    $reasons[(int)$item['reasonId']] += 1;
                                }
                            }
                        }
                        $reasonsString = '';
                        foreach ($reasons as $i => $reason) {
                            if ($reason > 0) {
                                $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                            }
                        }
                        if (strlen($reasonsString) > 1) {
                            $reasonsString = substr($reasonsString, 0, -2);
                        }
                        $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                    }
                }
            }
            $smallSql = Yii::app()->db2->createCommand()
                ->select('f.npp, w.vpo1cat')
                ->from('wkardc_rp w')
                ->join('fdata f', 'f.npp = w.fnpp')
                ->join('struct_d_rp s', 's.npp = w.struct')
                ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн" OR w.sovm = "ВнеСовм"')
                ->andWhere('f.fam NOT LIKE \'%Test%\'')
                ->queryAll();
            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Численность (ОМР)');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн"')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн"')
                    ->queryAll();
            }
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['npp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }

            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Численность (Внешние совместители)');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('w.sovm = "ВнеСовм"')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('w.sovm = "ВнеСовм"')
                    ->queryAll();
            }
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['npp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }

            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники младше 60 лет (ОМР)');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн"')
                    ->andWhere('DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) < 60')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн"')
                    ->andWhere('DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) < 60')
                    ->queryAll();
            }
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['npp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }

            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники старше 65 лет (ОМР)');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн"')
                    ->andWhere('DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) >= 65')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->andWhere('TRIM(w.sovm) = \' \' OR w.sovm = "Осн"')
                    ->andWhere('DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) >=65')
                    ->queryAll();
            }
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['npp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }

            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники, имеющие официальный мед.отвод');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            }
            $arr = array();
            foreach ($newNpps as $npp) {
                array_push($arr, $npp['npp']);
            }
            $newNpps = implode(', ', $arr);
            $newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_covid')
                ->where('covidStatus=4 AND fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }

            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники, переболевшие COVID-19');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            }
            $arr = array();
            foreach ($newNpps as $npp) {
                array_push($arr, $npp['npp']);
            }
            $newNpps = implode(', ', $arr);
            $newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_covid')
                ->where('covidStatus=1 AND fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }
            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники, сделавшие первую прививку');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            }
            $arr = array();
            foreach ($newNpps as $npp) {
                array_push($arr, $npp['npp']);
            }
            $newNpps = implode(', ', $arr);
            $newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_covid')
                ->where('covidStatus=2 AND fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }
            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники, сделавшие вторую прививку');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            }
            $arr = array();
            foreach ($newNpps as $npp) {
                array_push($arr, $npp['npp']);
            }
            $newNpps = implode(', ', $arr);
            $newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_covid')
                ->where('covidStatus=3 AND fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }
            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники, подавшие заявку на прививку через портал госуслуг');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            }
            $arr = array();
            foreach ($newNpps as $npp) {
                array_push($arr, $npp['npp']);
            }
            $newNpps = implode(', ', $arr);
            $newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_covid')
                ->where('covidStatus=5 AND fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }
            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники, подавшие заявку на прививку через службу охраны труда');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            }
            $arr = array();
            foreach ($newNpps as $npp) {
                array_push($arr, $npp['npp']);
            }
            $newNpps = implode(', ', $arr);
            $newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_covid')
                ->where('covidStatus=6 AND fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }
            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Работники, отказавшиеся делать прививку');
            $activeSheet->setCellValueByColumnAndRow(1, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(2, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(3, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(4, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(5, $row, 0);
            $activeSheet->setCellValueByColumnAndRow(6, $row, 0);
            if ($id == 6) {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp = w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            } else {
                $newNpps = Yii::app()->db2->createCommand()
                    ->select('f.npp')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->join('struct_d_rp s', 's.npp = w.struct')
                    ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ')')
                    ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->andWhere('f.fam NOT LIKE \'%Test%\'')
                    ->queryAll();
            }
            $arr = array();
            foreach ($newNpps as $npp) {
                array_push($arr, $npp['npp']);
            }
            $newNpps = implode(', ', $arr);
            $newNpps = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_covid')
                ->where('covidStatus=7 AND fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll();
            if ($newNpps) {
                $allNew = count($newNpps);
                $arr = array();
                foreach ($newNpps as $npp) {
                    array_push($arr, $npp['fnpp']);
                }
                $newNpps = implode(', ', $arr);
                $activeSheet->setCellValueByColumnAndRow(1, $row, $allNew);
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('format, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                $formats = array(
                    0 => 0,
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                );
                if ($sql) foreach ($sql as $item) {
                    foreach ($smallSql as $value) {
                        if ($value['npp'] == $item['fnpp'] and $value['vpo1cat']) {
                            $formats[(int)$item['format']] += 1;
                        }
                    }
                }
                foreach ($formats as $i => $format) {
                    $activeSheet->setCellValueByColumnAndRow($i + 2, $row, $format);
                }
                $sql = Yii::app()->db->createCommand()
                    ->selectDistinct('reasonId, fnpp')
                    ->from('tbl_chief_reports_week')
                    ->where('fnpp IN (' . $newNpps . ')
                AND format = 4
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                    ->queryAll();
                if ($sql) {
                    $reasons = array(
                        -1 => 0,
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    );
                    foreach ($sql as $item) {
                        foreach ($smallSql as $value) {
                            if ($value['npp'] == $item['fnpp']) {
                                $reasons[(int)$item['reasonId']] += 1;
                            }
                        }
                    }
                    $reasonsString = '';
                    foreach ($reasons as $i => $reason) {
                        if ($reason > 0) {
                            $reasonsString .= (($i == 0) ? 'Причина не указана' : ChiefReportsWeek::getReasonId($i)) . '(' . $reason . '), ';
                        }
                    }
                    if (strlen($reasonsString) > 1) {
                        $reasonsString = substr($reasonsString, 0, -2);
                    }
                    $activeSheet->setCellValueByColumnAndRow(6, $row, $reasonsString);
                }
            }


            $row++;
            $activeSheet->setCellValueByColumnAndRow(0, $row, 'Сведения не указаны');
            if ($count = Yii::app()->db->createCommand()
                ->selectDistinct('fnpp')
                ->from('tbl_chief_reports_covid')
                ->where('fnpp IN (' . $npps . ') AND covidStatus = 0
                AND confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
                ->queryAll()
            ) {
                $counter = 0;
                foreach ($bigSql as $item) {
                    foreach ($count as $value) {
                        if ($item['npp'] == $value['fnpp']) {
                            $counter += 1;
                        }
                    }
                }
                $activeSheet->setCellValueByColumnAndRow(1, $row, $all - $counter);
            } else {
                $activeSheet->setCellValueByColumnAndRow(1, $row, $all);
            }
        }


        ////////////////////////////////////////////////////////////////////////////////////

//        checkForDuplicates();

        require_once Yii::getPathOfAlias('application.extensions.phpexcel.Classes.PHPExcel') . '.php';
        set_time_limit(0);

        $obj = new PHPExcel();
        $obj->getDefaultStyle()->getFont()->applyFromArray(array(
            'name' => 'Times New Roman',
            'size' => 12,
            'bold' => false,
            'italic' => false
        ));
        $obj->getDefaultStyle()->getAlignment()->applyFromArray(['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => true,
            'shrinkToFit' => false]);

        $sheetNumber = 0;
        $obj->createSheet($sheetNumber);
        $obj->setActiveSheetIndex($sheetNumber);
        $activeSheet = $obj->getActiveSheet();
        $activeSheet->setTitle('Список');
        setColumnsWidth($activeSheet, array(
            ['A', 35],
            ['B', 35],
            ['C', 35],
            ['D', 15],
            ['E', 15],
            ['F', 15],
            ['G', 20],
            ['H', 25],
            ['I', 15],
            ['J', 20],
            ['K', 25],
            ['L', 35],
            ['M', 35],
            ['N', 35]
        ));
        $row = 1;
        $activeSheet->setCellValueByColumnAndRow(0, $row, 'ФИО');
        $activeSheet->setCellValueByColumnAndRow(1, $row, 'Должность');
        $activeSheet->setCellValueByColumnAndRow(2, $row, 'Наименование подразделения');
        $activeSheet->setCellValueByColumnAndRow(3, $row, 'Условия работы');
        $activeSheet->setCellValueByColumnAndRow(4, $row, 'Категория персонала');
        $activeSheet->setCellValueByColumnAndRow(5, $row, 'Возраст');
        $activeSheet->setCellValueByColumnAndRow(6, $row, 'Категория');
        $activeSheet->setCellValueByColumnAndRow(7, $row, 'Статус по прививке');
        $activeSheet->setCellValueByColumnAndRow(8, $row, 'Дата по прививке');
        $activeSheet->setCellValueByColumnAndRow(9, $row, 'Формат работы');
        $activeSheet->setCellValueByColumnAndRow(10, $row, 'Причина');
        $activeSheet->setCellValueByColumnAndRow(11, $row, 'Статус');
        $activeSheet->setCellValueByColumnAndRow(12, $row, 'Страна');
        $activeSheet->setCellValueByColumnAndRow(13, $row, 'Примечание');


        setCellsBold($activeSheet, array(
            [0, $row],
            [1, $row],
            [2, $row],
            [3, $row],
            [4, $row],
            [5, $row],
            [6, $row],
            [7, $row],
            [8, $row],
            [9, $row],
            [10, $row],
            [11, $row],
            [12, $row],
            [13, $row]
        ));

        if ($id == 6) {
            $sql = Yii::app()->db2->createCommand()
                ->select('w.dolgnost,
                f.npp,
                f.fam,
                f.nam,
                f.otc,
                w.vpo1cat category, 
                struct_getpath1_rp(w.struct) name,
                Case 
                    WHEN TRIM(w.sovm) = \' \' THEN "Осн" 
                    ELSE w.sovm 
                END sovm,
                Case 
                    when (f.pol = 1 
                    AND DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) >= 60
                    AND ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) <= 61)
                    OR
                    (f.pol = 2 
                    AND DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) >= 55
                    AND ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1) <= 56)
                    THEN CONVERT(ROUND(DATEDIFF(NOW(),f.rogd) / 365.2425 , 1),CHAR)
                    ELSE CONVERT(DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')),CHAR)
                END age')
                ->from('wkardc_rp w')
                ->join('fdata f', 'f.npp=w.fnpp')
                ->join('struct_d_rp s', 's.npp = w.struct')
                ->where('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                ->andWhere('f.fam NOT LIKE \'%Test%\'')
                ->order('s.l, f.fam')
//                ->group('f.npp, w.vpo1cat, w.struct')
                ->queryAll();
        } else {
            $sql = Yii::app()->db2->createCommand()
                ->select('w.dolgnost,
                    f.npp,
                    f.fam,
                    f.nam,
                    f.otc,
                    s.name, 
                    w.vpo1cat as category, 
                    Case 
                        WHEN TRIM(w.sovm) = \' \' THEN "Осн" 
                        ELSE w.sovm 
                    END as sovm,
                    DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) AS age'
                )
                ->from('wkardc_rp w')
                ->join('fdata f', 'f.npp=w.fnpp')
                ->join('struct_d_rp s', 's.npp = w.struct AND s.prudal = 0')
                ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($id) . ') AND w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                ->andWhere('f.fam NOT LIKE \'%Test%\'')
                ->order('s.l, f.fam')
//                ->group('f.npp, w.vpo1cat, w.struct')
                ->queryAll();
        }
        $arr = array();
        foreach ($sql as $npp) {
            array_push($arr, $npp['npp']);
        }
        $npps = implode(', ', $arr);
        $localData = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_category t1')
            ->leftJoin('tbl_chief_reports_day t2', 't1.fnpp = t2.fnpp AND t2.confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
            ->leftJoin('tbl_chief_reports_week t3', 't1.fnpp = t3.fnpp AND t3.confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
            ->leftJoin('tbl_chief_reports_covid t4', 't1.fnpp = t4.fnpp AND t4.confirmedAt = \'' . Yii::app()->session['reportDate'] . '\'')
            ->where('t1.fnpp IN (' . $npps . ')')
            ->queryAll();
        foreach ($sql as $person) {
            $localPerson = array();
            foreach ($localData as $item) {
                if ($item['fnpp'] == $person['npp']) {
                    $localPerson = $item;
                    break;
                }
            }
            $row++;
            //var_dump($localPerson, $person, $row);
            writeOnePerson($activeSheet, $row, $person, $localPerson);
        }

        $sheetNumber++;
        $obj->createSheet($sheetNumber);
        $obj->setActiveSheetIndex($sheetNumber);
        $activeSheet = $obj->getActiveSheet();
        $activeSheet->setTitle('Итог');
        setColumnsWidth($activeSheet, array(
            ['A', 45],
            ['B', 35],
            ['C', 35],
            ['D', 35],
            ['E', 35],
            ['F', 35],
            ['G', 35],
        ));

        $department = Yii::app()->db2->createCommand()
            ->select('name')
            ->from('struct_d_rp')
            ->where('npp = ' . $id)
            ->queryScalar();
        $activeSheet->setCellValueByColumnAndRow(0, 1, $department);
        $activeSheet->setCellValueByColumnAndRow(1, 1, 'Всего');
        $activeSheet->setCellValueByColumnAndRow(2, 1, 'Работают удаленно');
        $activeSheet->setCellValueByColumnAndRow(3, 1, 'Частично посещают здание организации');
        $activeSheet->setCellValueByColumnAndRow(4, 1, 'Работают в здании организации');
        $activeSheet->setCellValueByColumnAndRow(5, 1, 'Находятся на карантине или самоизоляции');
        $activeSheet->setCellValueByColumnAndRow(6, 1, 'Иные причины отсутствия на рабочем месте');
        setCellsBold($activeSheet, array(
            [0, 1],
            [1, 1],
            [2, 1],
            [3, 1],
            [4, 1],
            [5, 1],
            [6, 1],
            [0, 2],
        ));
        setCellItalic($activeSheet, 0, 3);
        writeSummary($activeSheet, $id);

        $sheetNumber++;
        $obj->removeSheetByIndex($sheetNumber);
        $obj->setActiveSheetIndex(0);


        $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
        $path = 'protected/data/uploads/temp/';
        if (!file_exists($path)) {
            mkdir($path);
        }

        if ($id == 6) {
            $file = 'Ежедневный отчет по ОмГТУ ' . Yii::app()->session['reportDate'];
        } else {
            $file = 'tmpExcelDay' . rand(1, 999);
        }
        $filename = $path . $file . '.xlsx';
        $objWriter->save($filename);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename = ' . $file. '.xlsx');
        header('Cache-Control: max-age=0');
        echo file_get_contents($filename);
        if ($id != 6) {
            unlink($filename);
        }
    }
}
