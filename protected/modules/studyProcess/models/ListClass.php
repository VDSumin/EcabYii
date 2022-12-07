<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 29.03.2020
 * Time: 22:05
 */

class ListClass
{

    public static function getWRating($mass, $nrec, $value, $class, $readonly = false, $disabled = false)
    {

        $return = CHtml::textField($mass . "[" . $nrec . "]", $value, array(
            'disabled' => $disabled,
            'readonly' => $readonly,
            'class' => $class . ' form-control',
            'style' => 'text-align: center; width: 90%; ' . ((in_array($mass, ["pHours", "tHours"])) ? 'cursor: default;' : '')));

        return $return;
    }

    /*Функция для получения поля рейтинга для заполнения в ведомости*/
    public static function getValueRating($model, $status, $marks, $Rmas, $class, $wtype, $kursIsset, $dopStatusList, $listFlag)
    {
        $return = null;
        if ($status == 2 || $listFlag) {
            return CHtml::value($model, $Rmas);
        }
        if ($dopStatusList == 1) {
            if ($dopStatusList == 1 && (CHtml::value($model, "markLinkNumberNrec", '0x8000000000000000') != '0x8000000000000000'
                    || CHtml::value($model, "tolerance", 1) == 0)) {
                return CHtml::value($model, $Rmas);
            }
        }
        $kursMark = CHtml::value($model, "markFromKursList", null);
        if ($wtype != ulist::TYPE_EXAM && $wtype != ulist::TYPE_PRACTICE) {
            if ($Rmas == 'ratingres') {
                $readonly = false;
            } else {
                $readonly = true;
            }

        } else {
            $readonly = (($Rmas == 'ratingatt') || ($Rmas == 'ratingres'));
        }

        $mark = CHtml::value($model, "markLinkNumberNrec", null);
        $wmark = CHtml::value($model, "markNumber");
        $tolerance = CHtml::value($model, "tolerance", 1);
        if ($tolerance == 1 && $mark == '0x800100000000242E') {
            $mark = '0x8000000000000000';
        }

        if ($kursIsset != '' && !isset($marks[$mark])) {
            if (!in_array($kursMark, [5, 4, 3, 0])
                || (!in_array($wtype, [uList::TYPE_KURS_PROJECT, uList::TYPE_KURS_WORK]) && $kursMark == 0)) {
                return '';
            }
        }
//        if (($mark !== null) && isset($marks[$mark]) || ($wmark == -3)) {
        if (($mark !== null) && isset($marks[$mark]) || $tolerance == 0) {
            if (CHtml::value($model, "dopStatusList", null) == 0 && !in_array(CHtml::value($model, "wendres"), array(uMark::TM_TRANSFER, uMark::TM_RECERTIFICATION))
//                && ($wmark != -3)
                && ($tolerance == 1)
            ) {
                if ($wtype != ulist::TYPE_EXAM && $wtype != ulist::TYPE_PRACTICE) {
                    if ($Rmas == 'ratingres') {
                        $readonly = false;
                    } else {
                        $readonly = true;
                    }
                } else {
                    $readonly = ($Rmas == 'ratingres' || ($Rmas == 'ratingatt' && CHtml::value($model, $Rmas) == 0));
                }
                $return = CHtml::textField($Rmas . "[" . CHtml::value($model, "markStudNrec") . "]",
                    ((CHtml::value($model, $Rmas) != 0 || $wmark != 0) ?
                        (($Rmas == 'ratingatt' && $wmark == -1) ? '-1' : CHtml::value($model, $Rmas))
                        : ''),
                    array("style" => "width: 40px; text-align:center;", 'readonly' => $readonly, 'class' => $class));
            } else {
                //$return = ((CHtml::value($model, $Rmas) != 0) ? CHtml::value($model, $Rmas) : '');
                $return = CHtml::textField($Rmas . "[" . CHtml::value($model, "markStudNrec") . "]", ((CHtml::value($model, $Rmas) != 0) ? CHtml::value($model, $Rmas) : ''),
                    array("style" => "width: 40px; text-align:center;", 'readonly' => 'readonly', 'class' => $class));
            }
        } else {
            if (in_array($wtype, [uList::TYPE_KURS_PROJECT, uList::TYPE_KURS_WORK])) {
                if (Chtml::value($model, 'dbDipNrecNrec') == '0x8000000000000000') {
                    return "";
                }
            }

            $return = CHtml::textField($Rmas . "[" . CHtml::value($model, "markStudNrec") . "]", ((CHtml::value($model, $Rmas) != 0) ? CHtml::value($model, $Rmas) : ''),
                array("style" => "width: 40px; text-align:center;", 'readonly' => $readonly, 'class' => $class));
        }
        return $return;
    }

    public static function getLecturerList($model, $ListL, $status, $dopStatusList, $PN, $listFlag)
    {
        $nrec = isset($model['makrExaminerNrec']) ? $model['makrExaminerNrec'] : $model['kursThemeTeacherNrec'];
        $fio = isset($model['makrExaminerNrec']) ? $model['makrExaminerFio'] : $model['kursThemeTeacherFio'];
        if ($status == 2 || $listFlag) {
            return $fio;
        }
        if ($dopStatusList == 1 && (CHtml::value($model, "markLinkNumberNrec", '0x8000000000000000') != '0x8000000000000000'
                || CHtml::value($model, "tolerance", 1) == 0)) {
            return $fio;
        }
        $List = [];
        foreach ($ListL as $row) {
            $List[mb_strtolower($row['nrecExaminer'])] = $row['fioExaminer'];
        }

        return CHtml::dropdownList("examiners[" . CHtml::value($model, "markStudNrec") . "]",
            mb_strtolower((!in_array($nrec, ['0x8000000000000000', '0x0000000000000000'])) ?
                $nrec : $PN),
            $List,
            array("prompt" => "--Выберете преподавателя--",
//                "id" => false,
                "class" => "form-control _table-ulist"));

    }


    public static function getMarkField($model, $marks, $wtype, $dis, $kursIsset, $dopStatusList, $status, $listFlag)
    {
        $return = '';
        $markNrec = CHtml::value($model, "markStudNrec", null);
        $mark = CHtml::value($model, "markLinkNumberNrec", null);
        $wmark = CHtml::value($model, "markNumber");
        $wendres = CHtml::value($model, "markWendres");
        $kursMark = CHtml::value($model, "markFromKursList");
        $tolerance = CHtml::value($model, "tolerance", 1);
        if ($tolerance == 1 && $mark == '0x800100000000242E') {
            $mark = '0x8000000000000000';
        }

        $extraText = ($wtype > 10 && $wtype < 100) ? ' (досрочно)' : '';
        if ($status == 2 || $listFlag) {
            if (isset($marks[$mark])) {
                if ($listFlag) {
                    return '<span rel="tooltip" data-toggle="tooltip" data-placement="top" style= "cursor: default" '
                        . 'title="Оценка из ведомости: ' . CHtml::value($model, "markListNumDoc") . '" >' .
                        $marks[$mark] . $extraText . '</span>';
                } else {
                    return $marks[$mark] . $extraText;
                }
            } else {
                return '';
            }
        }


        if ($kursIsset != '' && !isset($marks[$mark])) {
            if (!in_array($kursMark, [5, 4, 3, 0]) || (!in_array($wtype, [uList::TYPE_KURS_PROJECT, uList::TYPE_KURS_WORK]) && $kursMark == 0)) {
                if ($dopStatusList == 0) {
                    return CHtml::textField("marksName[" . $markNrec . "]", 'Недопущен', array(
                            "style" => "text-align:center; width: 150px;",
                            "class" => "markName form-group form-control _table-ulist",
                            "readonly" => "readonly"
                        )) . CHtml::hiddenField("marks[" . $markNrec . "]", '0x800100000000242E', array(
                            "class" => "markValue",
                            "readonly" => "readonly"
                        ));
                } else {
                    return 'Недопущен';
                }
            }
        }
        if (($mark !== null) && isset($marks[$mark]) && (!in_array($wtype, array(uList::TYPE_DIP_PROJECT, uList::TYPE_DIP_WORK)) && (CMisc::_id($dis) != uDiscipline::DIS_GOS_EXAM))) {
            if (in_array($wendres, array(uMark::TM_RECERTIFICATION, uMark::TM_TRANSFER))) {
                return $marks[$mark] . ' (' . uMark::markStatusLabels($wendres) . ')';
            } elseif ($dopStatusList == 1) {
                if (isset($marks[$mark])) {
                    return $marks[$mark] . $extraText;
                } else {
                    return '';
                }
            } elseif ($tolerance == 1 && $mark == '0x800100000000242E') {
                return CHtml::textField("marksName[" . $markNrec . "]", '', array(
                        "style" => "text-align:center; width: 150px;",
                        "class" => "markName form-group form-control _table-ulist",
                        "readonly" => "readonly"
                    )) . CHtml::hiddenField("marks[" . $markNrec . "]", '', array(
                        "class" => "markValue",
                        "readonly" => "readonly"
                    ));
            } else {
                return CHtml::textField("marksName[" . $markNrec . "]", $marks[$mark], array(
                        "style" => "text-align:center; width: 150px;",
                        "class" => "markName form-group form-control _table-ulist",
                        "readonly" => "readonly"
                    )) . CHtml::hiddenField("marks[" . $markNrec . "]", $mark, array(
                        "class" => "markValue",
                        "readonly" => "readonly"
                    ));
            }
        } elseif ($tolerance == 0) {
            if ($dopStatusList == 0) {
                return CHtml::textField("marksName[" . $markNrec . "]", 'Недопущен', array(
                        "style" => "text-align:center; width: 150px;",
                        "class" => "markName form-group form-control _table-ulist",
                        "readonly" => "readonly"
                    )) . CHtml::hiddenField("marks[" . $markNrec . "]", '0x800100000000242E', array(
                        "class" => "markValue",
                        "readonly" => "readonly"
                    ));
            } else {
                return 'Недопущен';
            }
        } else {
            if (in_array($wtype, [uList::TYPE_KURS_PROJECT, uList::TYPE_KURS_WORK])) {
                if (Chtml::value($model, 'dbDipNrecNrec') == '0x8000000000000000') {
                    return "Нет темы КР (КП)";
                }
            }

            if (in_array($wtype, array(uList::TYPE_DIP_PROJECT, uList::TYPE_DIP_WORK)) ||
                (CMisc::_id($dis) == uDiscipline::DIS_GOS_EXAM)) {
                return CHtml::dropDownList("marks[" . $markNrec . "]", $mark, $marks, array(
                    "empty" => "",
                    "style" => "width: 160px;",
                    "class" => "markValue form-control _table-ulist"
                ));
            } else {
                return CHtml::textField("marksName[" . $markNrec . "]", '', array(
                        "style" => "text-align:center; width: 150px;",
                        "class" => "markName form-group form-control _table-ulist",
                        "readonly" => "readonly"
                    )) . CHtml::hiddenField("marks[" . $markNrec . "]", '', array(
                        "class" => "markValue",
                        "readonly" => "readonly"
                    ));
            }

        }

        return $return;
    }

    /**
     * @param $markNrec // Nrec оценки
     * @param $rbExist // признак зачётки
     * @return string
     */
    public static function showCBrbExist($markNrec, $rbExist, $status, $dopStatusList, $listFlag)
    {

        if ($status == 2 || $dopStatusList == 1 || $listFlag) {
            if ($rbExist == 1) {
                echo '<b class="textCBrb">Да</b>';
            } elseif ($rbExist == 2) {
                echo '<b class="textCBrb">Нет</b>';
            } else {
                echo '<b class="textCBrb">Нет</b>';
            }
        } else {
            $return = '<div class="blockChair toggle btn ' . (($rbExist == 1) ? 'btn-primary' : 'btn-default active') . '" data-toggle="toggle" style="width: 63px; height: 34px;">';
            if (!$rbExist) {
                $return .=
                    '<div class="textCBrb">Нет</div>' .
                    CHtml::checkBox("rbExist[" . $markNrec . "]", false, ["class" => "checkboxCBrb", "style" => "display:none;", "value" => 2]);
            } elseif ($rbExist == 2) {
                $return .=
                    '<div class="textCBrb">Нет</div>' .
                    CHtml::checkBox("rbExist[" . $markNrec . "]", false, ["class" => "checkboxCBrb", "style" => "display:none;", "value" => 2]);
            } elseif ($rbExist == 1) {
                $return .=
                    '<div class="textCBrb">Да</div>' .
                    CHtml::checkBox("rbExist[" . $markNrec . "]", true, ["class" => "checkboxCBrb", "style" => "display:none;", "value" => 1]);
            }
            $return .= '</div>';
            return $return;
        }
    }

    public static function getKursName($model, $status, $dopStatusList)
    {
        $readonly = false;
//        if($status == 2 || $dopStatusList == 1){
//            $readonly = true;
//        }

        $markNrec = CHtml::value($model, 'markStudNrec');
        $kursNrec = CHtml::value($model, 'dbDipNrec');
        $kursName = CHtml::value($model, 'kursTheme');

        $result = CHtml::textArea("kursTheme[" . $markNrec . "]", $kursName,
            array("style" => "width: 100%; resize: vertical;", "class" => "form-group form-control",
                "rows" => "4", 'readonly' => $readonly));
        $result .= CHtml::hiddenField("kursThemeNrec[" . $markNrec . "]", $kursNrec);

        return $result;

    }

    public static function getTotalInfoOfFilesByDisAndSemester($student, $numdoc, $discipline, $semester, $db_dip, $typeList, $extra = false)
    {
        $result = '';
        if ($student) {
            $sql = 'SELECT DISTINCT k.fnpp
        FROM keylinks k        
        LEFT JOIN fdata f on f.npp = k.fnpp
        INNER JOIN skard s on s.fnpp = f.npp         
        WHERE gal_unid = ' . CMisc::_id($student) . '
        ORDER BY f.clink DESC, f.npp ASC';

            $fnpp = Yii::app()->db2->createCommand($sql)->queryColumn();
        } else {
            $fnpp = null;
        }


        if ($fnpp) {
            $md5 = md5($student . '_' . $numdoc);
            Yii::app()->session[$md5] = [
                'studentHex' => $student,
                'studentFnpp' => $fnpp,
                'discipline' => $discipline,
                'semester' => $semester,
                'db_dip' => $db_dip
            ];
            if (in_array($typeList, [3, 4])) {
                if (isset($db_dip)) {
                    $sql = "SELECT t.id, t.state
        FROM ecab.vkrfiles t
        WHERE t.vkrnrec = UNHEX(" . CMisc::str(strtoupper(str_replace('0x', '', $db_dip))) . ")
        ORDER BY field(t.state, 1, 2, 0);
        LIMIT 1
        ";
                    //var_dump($db_dip);die;
                    $files = Yii::app()->db2->createCommand($sql)->queryRow();
                } else {
                    return CHtml::button('Нет темы КР (КП)', ['class' => 'btn btn-warning']);
                }
            } else {
                $sql = "SELECT t.id, t.state
        FROM ecab.vkrfiles t
        WHERE (TRIM(t.type) in ('other', 'work') AND t.fnpp in (" . implode(',', $fnpp) . ") AND t.disc = " . CMisc::str(strtoupper(str_replace('0x', '', $discipline))) . " AND t.semester = " . $semester . ")
        " . (!$extra ? ' and datediff(now(), from_unixtime(unixdate)) < 800 ' : '') . "
        ORDER BY field(t.state, 1, 0, 2);
        LIMIT 1
        ";
                $files = Yii::app()->db2->createCommand($sql)->queryRow();

                $sql = "SELECT t.id, t.state
        FROM ecab.vkrfiles t
        WHERE (TRIM(t.type) in ('other', 'work') AND t.fnpp in (" . implode(',', $fnpp) . ") AND t.disc = " . CMisc::str(strtoupper(str_replace('0x', '', $discipline))) . ") and t.semester is null
        " . (!$extra ? ' and datediff(now(), from_unixtime(unixdate))  < 400 ' : '') . " 
        ORDER BY field(t.state, 1, 0, 2);
        LIMIT 1
        ";
                $filesWOS = Yii::app()->db2->createCommand($sql)->queryRow();

                $files = $files ? $files : $filesWOS;
            }

            if ($files) {
                if ($files['state'] == 1) {
                    $result = CHtml::button('Работа загружена', ['class' => 'btn btn-success modalWin', 'data-toggle' => "modal"]) .
                        CHtml::hiddenField('', $md5, ['class' => 'studVal']);
                } elseif ($files['state'] == 2) {
                    $result = CHtml::button('Работа отклонена', ['class' => 'btn btn-danger modalWin', 'data-toggle' => "modal"]) .
                        CHtml::hiddenField('', $md5, ['class' => 'studVal']);
                } else {
                    $result = CHtml::button('Работа не проверена', ['class' => 'btn btn-info modalWin', 'data-toggle' => "modal"]) .
                        CHtml::hiddenField('', $md5, ['class' => 'studVal']);
                }


            } else {
                $result = CHtml::button('Работа не загружена', ['class' => 'btn btn-warning']);
            }
        } else {
            $result = CHtml::button('Работа не загружена', ['class' => 'btn btn-warning']);
        }

        return $result;
    }

    //функция вывода строки в Учебном процессе загруженные работы кнопкой с датой

    public static function getListOfFilesByDisAndSemester($md5, $extra = false)
    {
        $result = '';
        if (isset(Yii::app()->session[$md5])) {
            $info = Yii::app()->session[$md5];

            if ($info['db_dip'] != '0x8000000000000000' && $info['db_dip']) {
                $sql = "SELECT t.id, t.name, t.state, t.comment, t.size, from_unixtime(t.unixdate,'%d.%m.%Y') dtime
        FROM ecab.vkrfiles t
        WHERE t.vkrnrec = UNHEX(" . CMisc::str(str_replace('0x', '', $info['db_dip'])) . ")  ";
                $files = Yii::app()->db2->createCommand($sql)->queryAll();
            } else {
                $sql = "SELECT t.id, t.name, t.state, t.comment, t.size, from_unixtime(t.unixdate,'%d.%m.%Y') dtime
        FROM ecab.vkrfiles t
        WHERE t.fnpp in( " . implode(', ', $info['studentFnpp']) . ") AND t.disc = " . CMisc::str(str_replace('0x', '', $info['discipline'])) . " AND t.semester = " . $info['semester'] .
                     (!$extra ? ' and datediff(now(), from_unixtime(unixdate)) < 800 ' : '');
                $files = Yii::app()->db2->createCommand($sql)->queryAll();

                $sql = "SELECT t.id, t.name, t.state, t.comment, t.size, from_unixtime(t.unixdate,'%d.%m.%Y') dtime
        FROM ecab.vkrfiles t
        WHERE  t.fnpp in( " . implode(', ', $info['studentFnpp']) . ") AND t.disc = " . CMisc::str(str_replace('0x', '', $info['discipline']))
                     . (!$extra ? ' and datediff(now(), from_unixtime(unixdate)) < 400 ' : '');
                $filesWOS = Yii::app()->db2->createCommand($sql)->queryAll();

                $files = $files ? $files : $filesWOS;

            }

            if ($files) {
                $result .= '<table class="table table-striped table-bordered">
                                   <thead>
                                   <tr><th>Файл</th><th>Операции</th><th>Комментарий (250 символов)</th></tr>
</thead><tbody>
';
                foreach ($files as $one) {
                    $result .= '<tr> ';
                    if ($one['state'] == 1) {
                        $classFont = 'btn-success';
                        $tooltip = "Работа принята";
                    } elseif ($one['state'] == 2) {
                        $classFont = 'btn-danger';
                        $tooltip = "Работа отклонена";
                    } else {
                        $classFont = 'btn-info';
                        $tooltip = "Работа не проверена";
                    }

                    $result .= '<td>';
                    $result .= '<span id="tooltipId" rel="tooltip" title= "' . $tooltip . '" data-toggle="tooltip" >' . '<div class="dtime">'.
                        CHtml::link($one['name'] . ' (' . round($one['size'] / 1024 / 1024, 2) . ' Мбайт) '.$one['dtime'] .')</div>', array('downLoadWorkFile', 'id' => $one['id']), [
                            'class' => ' btn btn-mine ' . $classFont,
                            'style' => 'white-space: normal',
                            'target' => '_blank'
                        ]) . '</span>';
                    $result .= '</td>';
                    $result .= '<td style="text-align: center">';
                    $result .= '<span rel="tooltip" title= "Принять работу" data-toggle="tooltip" >' . CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok',
                            'style' => 'vertical-align: top',
                            'id' => 'stateOfFile'
                        ]) . '</span>';
                    $result .= '<span rel="tooltip" title= "Отклонить работу" data-toggle="tooltip" >' . CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove',
                            'style' => 'vertical-align: top',
                            'id' => 'stateOfFile'
                        ]) . '</span>';
                    $result .= '<span rel="tooltip" title= "Снять статус с работы" data-toggle="tooltip" >' . CHtml::link('', '#', [
                            'class' => 'btn btn-info glyphicon glyphicon-ban-circle',
                            'style' => 'vertical-align: top',
                            'id' => 'stateOfFile'
                        ]) . '</span>';

                    $result .= '</td>';
                    $result .= '<td>';
                    $result .= CHtml::textArea('commentFieldText', $one['comment'], ['class' => 'form-control', 'maxlength' => 254, 'rows' => 3, 'style' => 'resize: vertical;']) .
                        CHtml::hiddenField('commentFieldId', $one['id']);
                    $result .= '</td>';
                    $result .= '</tr>';
                }

            }
            $result .= '</tbody>';
            $result .= '</table>';
        } else {
            $result = 'Файлы не найдены';
        }


        return $result;
    }

    public static function getAudHoursCW($groupId, $date1, $date2, $disciplineNrec)
    {
        //var_dump($groupId, $date1, $date2, $disciplineNrec);
        $Hours[1] = (int)Yii::app()->db2->createCommand()
            ->select('COUNT(ats.id)')
            ->from('attendance_schedule ats')
            ->where(array('AND',
                'ats.studGroupId =' . $groupId,
                'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
                'ats.kindOfWorkId = 1',
            ))
            ->andWhere('ats.disciplineNrec = UNHEX(:discip)', array(':discip' => $disciplineNrec))
            ->queryScalar();//ЛК
        $Hours[2] = (int)Yii::app()->db2->createCommand()
            ->select('COUNT(ats.id)')
            ->from('attendance_schedule ats')
            ->where(array('AND',
                'ats.studGroupId =' . $groupId,
                'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
                'ats.kindOfWorkId = 2',
                'ats.studGroupName like \'%/1\'',
            ))
            ->andWhere('ats.disciplineNrec = UNHEX(:discip)', array(':discip' => $disciplineNrec))
            ->queryScalar();//ЛР
        if ($Hours[2] == 0) {
            $Hours[2] = (int)Yii::app()->db2->createCommand()
                ->select('COUNT(ats.id)')
                ->from('attendance_schedule ats')
                ->where(array('AND',
                    'ats.studGroupId =' . $groupId,
                    'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                    'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
                    'ats.kindOfWorkId = 2',
                    'ats.studGroupName like \'%/2\'',
                ))
                ->andWhere('ats.disciplineNrec = UNHEX(:discip)', array(':discip' => $disciplineNrec))
                ->queryScalar();
            if ($Hours[2] == 0) {
                $Hours[2] = (int)Yii::app()->db2->createCommand()
                    ->select('COUNT(ats.id)')
                    ->from('attendance_schedule ats')
                    ->where(array('AND',
                        'ats.studGroupId =' . $groupId,
                        'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                        'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
                        'ats.kindOfWorkId = 2',
                    ))
                    ->andWhere('ats.disciplineNrec = UNHEX(:discip)', array(':discip' => $disciplineNrec))
                    ->queryScalar();
            }
        }//ЛР
        /*----ПЗ----*/
        $Hours[3] = (int)Yii::app()->db2->createCommand()
            ->select('COUNT(ats.id)')
            ->from('attendance_schedule ats')
            ->where(array('AND',
                'ats.studGroupId =' . $groupId,
                'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
                'ats.kindOfWorkId = 3',
                'ats.studGroupName like \'%/2\'',
            ))
            ->andWhere('ats.disciplineNrec = UNHEX(:discip)', array(':discip' => $disciplineNrec))
            ->queryScalar();//ПЗ
        if ($Hours[3] == 0) {
            $Hours[3] = (int)Yii::app()->db2->createCommand()
                ->select('COUNT(ats.id)')
                ->from('attendance_schedule ats')
                ->where(array('AND',
                    'ats.studGroupId =' . $groupId,
                    'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                    'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
                    'ats.kindOfWorkId = 3',
                    'ats.studGroupName like \'%/1\'',
                ))
                ->andWhere('ats.disciplineNrec = UNHEX(:discip)', array(':discip' => $disciplineNrec))
                ->queryScalar();
            if ($Hours[3] == 0) {
                $Hours[3] = (int)Yii::app()->db2->createCommand()
                    ->select('COUNT(ats.id)')
                    ->from('attendance_schedule ats')
                    ->where(array('AND',
                        'ats.studGroupId =' . $groupId,
                        'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                        'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
                        'ats.kindOfWorkId = 3',
                    ))
                    ->andWhere('ats.disciplineNrec = UNHEX(:discip)', array(':discip' => $disciplineNrec))
                    ->queryScalar();
            }
        }//ПЗ
        $TotalHoursCW = 2 * array_sum($Hours);
        return $TotalHoursCW;
    }

    /**
     * Функция для преобразования массива с последовательными ключами 0,1,2... в массив с ключом nrec для быстрого поиска
     * @param $data
     * @return array
     */
    public static function getStudentForUpdate($data)
    {
        $students = [];
        foreach ($data['student'] as $key => $row) {
            $students[$row['markStudNrec']] = $row;
            $students[$row['markStudNrec']]['id'] = $key;
        }
        return $students;
    }

    public static function getMeMyGhief($dep)
    {
        $dep = bin2hex($dep);
        $galId = CMisc::_id(bin2hex(Yii::app()->user->getGalIdT()), 'upper');
//        var_dump($dep, $galId);
        $return = Yii::app()->db2->createCommand()
            ->select('COUNT(*)')
            ->from('gal_chief gc')
            ->where(array('AND',
                'gc.cdepartment = ' . CMisc::_id($dep),
                'gc.cperson  = ' . CMisc::_id($galId),
                'gc.isChief = 1',
            ))
            ->queryScalar();
        return $return;
    }

    /**
     * Remove from theme title special chars like \n or \t and unify quotes
     * @param string $theme
     * @return string
     */
    public static function sanitizeThemes($theme)
    {
        if (substr_count($theme, '«') > substr_count($theme, '»')) {
            $theme .= '»';
        }
        return trim(str_replace(array('«', '»', "\n", "\r", "\t", "  ", "'"), array('"', '"', ' ', '', '', ' ', '"'), $theme));
    }

    public static function getDipNumberData($model)
    {
        $return = null;
        $numberDip = CHtml::value($model, "eduNrec", null);

        if (is_null($numberDip) === false) {
            if (CHtml::value($model, "numberProto") == 0) {
                $return = CHtml::textField("numberProto[" . CHtml::value($model, "markStudNrec") . "]", CHtml::value($model, "numberProto"),
                    array("style" => "width: 60px; text-align:center;",
                        'class' => 'form-control'
                    ));
            } else {
                $return = CHtml::value($model, "numberProto")
                    . CHtml::hiddenField("numberProto[" . CHtml::value($model, "markStudNrec") . "]", CHtml::value($model, "numberProto"));
            }
        }
        return $return;
    }

    public static function getDipProtoData($model)
    {
        $return = null;
        $dataDip = CHtml::value($model, "eduNrec", null);


        if (is_null($dataDip) === false) {
            if (CHtml::value($model, "dataProto") == 0) {
                $return = CHtml::textField("dataProto[" . CHtml::value($model, "markStudNrec") . "]",
                    CHtml::value($model, "dataProto"),
                    array(
                        "style" => "width: 120px; text-align:center;",
                        'class' => 'dataPicker form-control'
                    ));
            } else {
                $return = CMisc::fromGalDate(CHtml::value($model, "dataProto"))
                    . CHtml::hiddenField("dataProto[" . CHtml::value($model, "markStudNrec") . "]", CMisc::fromGalDate(CHtml::value($model, "dataProto"), 'd.m.Y'));
            }
        }

        return $return;
    }

    /**
     * @param $model // recordBookNumber
     * @return false|mixed|string|null
     */
    public static function getRecordBook($model)
    {
        if (is_null(CHtml::value($model, "recordBookNumber")) == true) {
            return CHtml::textField("recordBookNumber[" . CHtml::value($model, "studPersonNrec") . "]", CHtml::value($model, "recordBookNumber"), array(
                "style" => "width: 80px; text-align:center;",
                "class" => "form-control"
            ));
        } else {
            return CHtml::value($model, "recordBookNumber");
        }

    }

    /**
     * Отображение заполнения ведомости по КН
     * @return bool
     */
    public static function checkCWAccess($year, $semester)
    {
        // TODO: создать запрос в API к графику учебного процесса
        if ($year != ApiKeyService::getCurrentYear()) return false;
        $date = getdate();
        if ($semester == 'осенний' &
            (($date['mon'] == 10 & $date['mday'] > 20)
                || ($date['mon'] == 11 & $date['mday'] < 13))) {
            return true;
        } elseif (($date['mon'] == 3 & $date['mday'] > 0) || ($date['mon'] == 4 & $date['mday'] <= 30)) {
            return true;
        }
        return false;
    }

    public static function getExtraStudentList($data)
    {
        if (count($data) == 0) {
            return '';
        }
        foreach ($data as $item) {
            $fio = explode(' ', $item);
            $result[] = $fio[0] . ' ' . mb_substr($fio[1], 0, 1) . '.';
        }
        $return = CHtml::textArea('stud', implode("\n", $result), array(
            'class' => 'form-control scroll',
            'style' => 'width: 100%; white-space: pre; padding: unset; 
            text-indent: 2px; background-color: #fff; overflow-x: hidden; 
            height: auto; resize: none'));

        return $return;
    }

}