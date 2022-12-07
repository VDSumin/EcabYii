<?php

class MarkController extends Controller
{

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index',
                    'ratingControlWeek', 'controlWeekPrint', 'saveRatingControlWeek', 'audHoursControlWeek',
                    'downLoadWorkFile',
                    'list', 'listPrint', 'draftPrint', 'saveMark', 'saveMarkDip', 'supervisorscomp',
                    'kursTheme', 'saveKursTheme',
                    'ListExaminer', 'deleteExaminer', 'updateExaminer'
                ),
                'users' => array('*'),
                'expression' => 'Controller::checkfnpp()',
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    protected function beforeAction($action)
    {
        if (Yii::app()->user->getFnpp() == null) {
            $this->redirect(array('/site'));
        }
        if (in_array($action->id, ['downLoadWorkFile', 'updateCommentAtFile', 'updateStateAtFile'])) {
            return true;
        }
        if (!isset(Yii::app()->session['ApiKey'])) {
            $key = ApiKeys::model()->findByAttributes(array('fnpp' => Yii::app()->user->getFnpp()));
            if ($key instanceof ApiKeys) {
                Yii::app()->session['ApiKey'] = $key->apikey;
            } else {
                $this->render('emptylist', ['code' => 1002, 'text' => 'Отсутствует API-key']);
                die;
            }
        };
        return true;
    }

    /**
     * Список всех ведомостей
     * @param bool $year
     * @throws CException
     */
    public function actionIndex($year = false)
    {
        $fnpp = Yii::app()->user->getFnpp();
        $yearCW = ApiKeyService::getCurrentYear();

        $data = ApiKeyService::queryApi('fnppList', array("fnpp" => $fnpp), Yii::app()->session['ApiKey']);
        ApiKeyService::checkResponseApi($data, 'fnppList');
        $data = $data['json_data'];
        sort($data);

        $yearList = [];
        foreach ($data as $datum) {
            if (!in_array($datum['year'], $yearList)) {
                $yearList[] = $datum['year'];
            }
        }
        rsort($yearList);

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }

        if ($year) {
            $filter->year = $year;
        } elseif (!empty(Yii::app()->session['YearList'])) {
            $filter->year = Yii::app()->session['YearList'];
            $year = Yii::app()->session['YearList'];
        } else {
            if (in_array($yearCW, $yearList)) {
                $filter->year = $yearCW;
                $year = $yearCW;
            } else {
                $filter->year = isset($yearList[0]) ? $yearList[0] : $yearCW;
                $year = isset($yearList[0]) ? $yearList[0] : $yearCW;
            }

        }
        Yii::app()->session['YearList'] = $year;

        $lists = [];
        //Убрал практики
        /*foreach ($data as $datum) {
            if($datum['typeListInt'] != 9){
                $lists[] = $datum;
            }
        }*/
        foreach ($data as $datum) {
            //if(substr($datum['discipline'], 0, 31) != 'Учебная практика'){
            $lists[] = $datum;
            //}
        }

        $data = $lists;


        $pager = new CPagination();
        $pageSize = 15;
        $pager->pageSize = $pageSize;
        $dataProvider = new CArrayDataProvider(
            $filter->arrayFilter($data),
            array('pagination' => $pager, 'keyField' => 'nrec')
        );

        $this->render('index', [
            'dataProvider' => $dataProvider,
            'filter' => $filter,
            'yearList' => $yearList,
            'year' => $year,
        ]);
    }

    //------------------------------------------------------------------------------------------------
    //работа с ведомостями по КН
    /**
     * Заполнение ведомости по контрольной недели
     * @param $id
     */
    public function actionRatingControlWeek($id)
    {

        $data = ApiKeyService::queryApi('getListStruct', array("nrec" => $id), Yii::app()->session['ApiKey']);
        ApiKeyService::checkResponseApi($data, 'getListStruct');
        $data = $data['json_data'];
        /*Api не отдаёт данные с первого раза*/
        $data = $this->emptyAnswerStudents($data, $id);
        $dataProvider = new CArrayDataProvider(CHtml::value($data, 'student'), array('pagination' => false, 'keyField' => 'markStudNrec'));

        $this->render('ratingControlWeek', [
            'dataProvider' => $dataProvider,
            'info' => $data,
        ]);
    }

    /**
     * Печать ведомости по КН из FR
     * @param $id
     */
    public function actionControlWeekPrint($id)
    {

        header('Content-Type: application/pdf');
        //echo file_get_contents('http://olap.omgtu/cgi/list.pdf?templatefile=D:\report\u_list_kn.fr3&nrec=281474977149757');
        echo file_get_contents('http://olap.omgtu/cgi/list.pdf?templatefile=D:\report\u_list_kn.fr3&nrec=' . $id);
    }

    /**
     * Сохранение рейтинга по КН через ajax
     */
    public function actionSaveRatingControlWeek()
    {
//        var_dump($_POST);die;
        $successFields = [];
        $writeArray = [];
        if (isset($_POST)) {

            $writeArray['nrec'] = $_POST['AudHours_N']['nrecList'];
            $writeArray['audHoursCurr'] = $_POST['AudHours_N']['audHoursCurr'];
            $writeArray['dateOfCurHours'] = CMisc::toGalDate((($_POST['AudHours_N']['dateCW'] != '') ? $_POST['AudHours_N']['dateCW'] : date('Y-m-d')));
            $j = 0;
            foreach ($_POST['wrating'] as $key => $row) {
                $writeArray['student'][$j]['markStudNrec'] = $key;
                $writeArray['student'][$j]['percent'] = (isset($_POST['pHours'][$key]) ? $_POST['pHours'][$key] : "0");
                $writeArray['student'][$j]['totalStudHours'] = (isset($_POST['tHours'][$key]) ? $_POST['tHours'][$key] : "0");
                $writeArray['student'][$j]['rating'] = $row;
                //if($row == 0){continue;}
                $result = ApiKeyService::queryApi('updateFieldRatingHours', $writeArray, Yii::app()->session['ApiKey']);
                $successFields[$key] = $result;
            }
        }
        echo CJSON::encode(array('success' => true, 'successFields' => $successFields));
    }

    /**
     * Получение общих часов по КН
     */
    public function actionAudHoursControlWeek()
    {

        // TODO: было бы не плохо фигачить это через api. Поменять дату начала семестра здесь
        $id = $_POST['idList'];
        $date1 = date('2022-09-01 00:00:00');//<<----поменять дату начала семестра здесь
        $date2 = date('Y-m-d 23:59:59', strtotime($_POST['DateCW']));
        $model = ApiKeyService::queryApi('getListStruct', array("nrec" => $id), Yii::app()->session['ApiKey']);
        if ($model['code'] != 0) {
            $model = $model['json_data'];
        }

        /*Api не отдаёт данные с первого раза*/
        $model = $this->emptyAnswerStudents($model, $id);

        /*надо держать это в сессии*/
        $disciplineNrec = CMisc::_bn($model['disciplineNrec']);
        $groupId = Yii::app()->db2->createCommand()
            ->select('agg.id')
            ->from('attendance_galruz_group agg')
            ->where('agg.name = \'' . $model['studGroup'] . '\'')
            ->order('agg.id DESC')
            ->queryScalar();
        if (empty($groupId) || empty($disciplineNrec) || empty($date1) || empty($date2)) {

            echo CJSON::encode(array('success' => false, 'text' => 0));
        } else {

            $TotalHoursCW = ListClass::getAudHoursCW($groupId, $date1, $date2, $disciplineNrec);
            $tHours = [];
            $pHours = [];

            foreach ($model['student'] as $row) {
                $CountHCW = Yii::app()->db2->createCommand()
                    ->select('SUM(ac.countHours) as sum')
                    ->from('attendance_journal aj')
                    ->leftJoin('attendance_schedule ats', 'aj.scheduleId = ats.id')
                    ->leftJoin('attendance_catalog ac', 'aj.teacherMarkId = ac.id')
                    ->where(array('AND',
                        'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                        'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
                    ))
                    ->andWhere('aj.studentNrec = ' . $row['studPersonNrec'])
                    ->andWhere('ats.disciplineNrec = UNHEX(:discip)', array(':discip' => $disciplineNrec))
                    ->queryScalar();

                $tHours['tHours_' . $row['markStudNrec']] = (int)$CountHCW < $TotalHoursCW ? (int)$CountHCW : $TotalHoursCW;
                $pHours['pHours_' . $row['markStudNrec']] = ($TotalHoursCW > 0) ? (int)round(($tHours['tHours_' . $row['markStudNrec']] / $TotalHoursCW) * 100, 0) : 0;

            }

            echo CJSON::encode(array('success' => true, 'text' => $TotalHoursCW, 'thours' => $tHours, 'phours' => $pHours));
        }
    }

    //------------------------------------------------------------------------------------------------
    //Модальное окно
    /**
     * Получаем список загруженных файлов для модального окна
     */
    public function actionLoadlistfile()
    {
        if (isset($_POST) && is_array($_POST)) {
            $result = ListClass::getListOfFilesByDisAndSemester($_POST['studVal']);
        } else {
            $result = false;
        }
        echo CJSON::encode(array('success' => $result));
    }

    public function actionDownLoadWorkFile($id = null)
    {
        $fnpp = isset($_GET['fnpp']) ? $_GET['fnpp'] : Yii::app()->user->getFnpp();
        $id = isset($_GET['id']) ? $_GET['id'] : $id;
        $query = curl_init();
        curl_setopt($query, CURLOPT_URL, 'https://omgtu.ru/ecab/modules/vkr2/getfup.php');
        curl_setopt($query, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($query, CURLOPT_POST, true);
        curl_setopt($query, CURLOPT_POSTFIELDS, ['id' => $id, 'fnpp' => $fnpp, 'key' => md5('!' . $fnpp . '#' . $id . 'omstuSolt'), 'pdf' => 1]);
        $reply = curl_exec($query);
        curl_close($query);
        $file = Yii::app()->db2->createCommand('SELECT t.name FROM ecab.vkrfiles t WHERE t.id =' . $id)->queryScalar();
        header('Content-Type: application/pdf');
        header("Content-Disposition: filename=\"" . ((!empty($file)) ? $file : "download.pdf") . "\";");
        echo $reply;
    }

    /**
     * Обновление комментария для проверенного файла
     */
    public function actionUpdateCommentAtFile()
    {
        if (isset($_POST) && is_array($_POST)) {
            if (Yii::app()->user->getFnpp() != null) {
                $sql = "UPDATE ecab.vkrfiles t SET t.comment = '" . $_POST['value'] . "', t.whocomment = " . Yii::app()->user->getFnpp() . " WHERE id = " . $_POST['id'];
                $result = Yii::app()->db2->createCommand($sql)->execute();
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        echo CJSON::encode(array('success' => $result));
    }

    /**
     * Обновление статуса файла для моадльного окна
     */
    public function actionUpdateStateAtFile()
    {
        if (isset($_POST) && is_array($_POST)) {
            if (Yii::app()->user->getFnpp() != null) {
                $sql = "UPDATE ecab.vkrfiles t SET t.state = '" . $_POST['state'] . "', t.whostate = " . Yii::app()->user->getFnpp() . " WHERE id = " . $_POST['id'];
                $result = Yii::app()->db2->createCommand($sql)->execute();
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        echo CJSON::encode(array('success' => $result));
    }

    //------------------------------------------------------------------------------------------------
    //Основные ведомости
    /**
     * Заполнение основной аттестационной ведомости
     * @param $id
     */
    public function actionList($id)
    {
        $data = ApiKeyService::queryApi('getListStruct', array("nrec" => $id), Yii::app()->session['ApiKey']);
        ApiKeyService::checkResponseApi($data, 'getListStruct');
        $data = $data['json_data'];
        /*Api не отдаёт данные с первого раза*/
        $data = $this->emptyAnswerStudents($data, $id);


        $list_marks = ApiKeyService::queryApi('getCatalogMarks', array(), Yii::app()->session['ApiKey'], 'GET');
        ApiKeyService::checkResponseApi($list_marks, 'getCatalogMarks');
        $list_marks = $list_marks['json_data'];
        $dip_data = [];
        if (in_array(CHtml::value($data, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))) {
            $dip_data = ApiKeyService::queryApi('getListDipStruct', array("nrec" => $id), Yii::app()->session['ApiKey'], 'GET');
            ApiKeyService::checkResponseApi($dip_data, 'getListDipStruct');
            $data = $dip_data['json_data'];
        }

//
//       var_dump($data); die;
//        $data['dopStatusList'] = 0;
//        $data['status'] = 2;
        foreach ($list_marks as $list_mark) {
            if (in_array($data['typeDiffer'], [1])) {
                if ($list_mark['groupName'] == 'Зачетные оценки') {
                    $marks[$list_mark['nrec']] = $list_mark['nameMark'];
                }
            } else {
                if ($list_mark['groupName'] == 'Экзаменационные оценки') {
                    $marks[$list_mark['nrec']] = $list_mark['nameMark'];
                }
            }
        }

        if ($data['typeList'] == 9) {
            $Practices = ApiKeyService::queryApi('getPracticeList', array("nrec" => $id), Yii::app()->session['ApiKey'], 'GET');
            $Practices = $Practices['json_data'];
            foreach ($data['student'] as &$student) {
                foreach ($Practices as $pract) {
                    if ($student['studPersonNrec'] == $pract['personNrec']) {
                        $student['enterprise'] = $pract['label'];
                        $student['begin'] = $pract['begin'];
                        $student['end'] = $pract['end'];
                        $student['practiceNrec'] = $pract['nrec'];
                    }
                }
            }
        }

        //var_dump($data['student']);
        //var_dump($Practices['0']);die;

        $galId = bin2hex(Yii::app()->user->getGalIdT());
        $dataProvider = new CArrayDataProvider(CHtml::value($data, 'student'), array('pagination' => false, 'keyField' => 'markStudNrec'));

        Yii::app()->session['Lists'] = array($id => $data);
        $this->render('list', [
            'dataProvider' => $dataProvider,
            'info' => $data,
            'marks' => $marks,
            'PN' => $galId,
            'dip_data' => $dip_data,
        ]);
    }

    /**
     * В случае пустого ответа списка студентов от api делаем три повторных запроса, "один дай бог прокатит"
     * @param $data
     * @return mixed
     */
    public function emptyAnswerStudents($data, $id)
    {
        $data_r = $data;
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($data['student'])) {
                break;
            }
            $data_r = ApiKeyService::queryApi('getListStruct', array("nrec" => $id), Yii::app()->session['ApiKey']);
            $data_r = $data_r['json_data'];
        }
        return $data_r;
    }

    /**
     * Печать основной ведомости из FR
     * @param $id
     */
    public function actionListPrint($id)
    {

        header('Content-Type: application/pdf');
        echo file_get_contents('http://olap.omgtu/cgi/list.pdf?templatefile=D:\report\UList_2020Temp.fr3&nrec=' . $id);
    }

    /**
     * Печать черновика ведомости из FR
     * @param $id
     */
    public function actionDraftPrint($id)
    {

        header('Content-Type: application/pdf');
        echo file_get_contents('http://olap.omgtu/cgi/list.pdf?templatefile=D:\report\draft_list.fr3&nrec=' . $id);
    }

    /**
     * Сохранение рейтинга через ajax
     */
    public function actionSaveMark()
    {
//        var_dump($_POST);die;
        $successFields = [];
        $writeArray = [];
        $resultSuccess = true;
        if (isset($_POST)) {
            $id = $_POST['AudHours_N']['nrecList'];
            $sessionList = isset(Yii::app()->session['Lists'][$id]) ? Yii::app()->session['Lists'][$id] : null;
            if (empty($sessionList)) {
                $sessionList = ApiKeyService::queryApi('getListStruct', array("nrec" => $id), Yii::app()->session['ApiKey'])['json_data'];
                /*Api не отдаёт данные с первого раза*/
                $sessionList = $this->emptyAnswerStudents($sessionList, $id);
            }
            $sessionListStudent = ListClass::getStudentForUpdate($sessionList);

            /*Отдельно вносим поля даты ведомости и признака закрытия*/
            $dateList = CMisc::toGalDate((
            isset($_POST['AudHours_N']['dateDoc']) ?
                ($_POST['AudHours_N']['dateDoc'] != '') ? $_POST['AudHours_N']['dateDoc'] : date('d.m.Y') :
                date('d.m.Y')
            ));
            $dopStatusList = intval($_POST['AudHours_N']['dop']);
            if ($sessionList['dateList'] != $dateList || $sessionList['dopStatusList'] != $dopStatusList) {
                $writeArray['nrec'] = $id;
                $writeArray['dateList'] = $dateList;
                $writeArray['dopStatusList'] = $dopStatusList;
                $result = ApiKeyService::queryApi('updateMarkAndRating', $writeArray, Yii::app()->session['ApiKey']);
            }

            $writeArray = [];
            $writeArray['nrec'] = $id;
            $j = 0;
            if (isset($_POST['marks'])) {
                foreach ($_POST['marks'] as $key => $row) {
                    if ($row != '') {
                        $checkupdate = false;
                        $writeArray['student'][$j] = [];
                        if (!in_array($row, ['0x800100000000242E']) or $dopStatusList) {
                            $writeArray['student'][$j]['markStudNrec'] = CMisc::_id($key);
                            if ($row != '') {
                                /*достаём поля из POST*/
                                $ratingsem = ((isset($_POST['ratingsem'][$key])) ?
                                    $_POST['ratingsem'][$key] :
                                    ((isset($_POST['ratingres'][$key])) ?
                                        $_POST['ratingres'][$key] : 0));
                                $ratingatt = ((isset($_POST['ratingatt'][$key])) ?
                                    $_POST['ratingatt'][$key] : 0);
                                if ($ratingatt == -1) {
                                    $ratingatt = 0;
                                }
                                $makrExaminerNrec = mb_strtolower(($_POST['examiners'][$key] != '') ? $_POST['examiners'][$key] : '0x8000000000000000');

                                /*заносим их в запрос на сохранение*/
                                $writeArray['student'][$j]['ratingsem'] = $ratingsem ?:0;
                                $writeArray['student'][$j]['ratingatt'] = $ratingatt;
                                $writeArray['student'][$j]['makrExaminerNrec'] = $makrExaminerNrec;

                                /*проверяем отличаются ли они от того что сейчас находится в сессии*/
                                if ($sessionListStudent[$key]['ratingsem'] != $ratingsem) {
                                    $checkupdate = true;
                                }
                                if ($sessionListStudent[$key]['ratingatt'] != $ratingatt) {
                                    $checkupdate = true;
                                }
                                if (mb_strtolower($sessionListStudent[$key]['makrExaminerNrec']) != $makrExaminerNrec) {
                                    $checkupdate = true;
                                }
                            }
                            /*достаём поля из POST*/
                            $markLinkNumberNrec = mb_strtolower(($row != '') ? CMisc::_id($row) : '0x8000000000000000');
                            $recordBookExist = (isset($_POST['rbExist'][$key])) ? intval($_POST['rbExist'][$key]) : 2;
                            /*заносим их в запрос на сохранение*/
                            $writeArray['student'][$j]['markLinkNumberNrec'] = $markLinkNumberNrec;
                            $writeArray['student'][$j]['recordBookExist'] = $recordBookExist;
                            /*проверяем отличаются ли они от того что сейчас находится в сессии*/
                            if (mb_strtolower($sessionListStudent[$key]['markLinkNumberNrec']) != $markLinkNumberNrec) {
                                $checkupdate = true;
                            }
                            if ($sessionListStudent[$key]['recordBookExist'] != $recordBookExist) {
                                $checkupdate = true;
                            }
                            if ($checkupdate) {
                                $result = ApiKeyService::queryApi('updateMarkAndRating', $writeArray, Yii::app()->session['ApiKey']);
                                $successFields[$key] = $result;
                                /*запись изменений обратно в сессию*/
                                if ($result['code'] == 200) {
                                    if (isset($ratingsem)) {
                                        $sessionList['student'][$sessionListStudent[$key]['id']]['ratingsem'] = $ratingsem;
                                    }
                                    if (isset($ratingatt)) {
                                        $sessionList['student'][$sessionListStudent[$key]['id']]['ratingatt'] = $ratingatt;
                                    }
                                    if (isset($makrExaminerNrec)) {
                                        $sessionList['student'][$sessionListStudent[$key]['id']]['makrExaminerNrec'] = $makrExaminerNrec;
                                    }
                                    $sessionList['student'][$sessionListStudent[$key]['id']]['markLinkNumberNrec'] = $markLinkNumberNrec;
                                    $sessionList['student'][$sessionListStudent[$key]['id']]['recordBookExist'] = $recordBookExist;
                                    Yii::app()->session['Lists'] = array($id => $sessionList);
                                } else {
                                    $resultSuccess = false;
                                }
                            }
                        }
                    }
                }

                if ($resultSuccess && isset($_POST['recordBookNumber'])) {
                    $this->saveRecordBook($_POST['recordBookNumber']);
                }
            }
            if (isset($_POST['comp'])) {
                $this->actionSavePractice($sessionList, $resultSuccess);
            }
        }
        echo CJSON::encode(array('success' => $resultSuccess, 'successFields' => $successFields));
    }

    public function actionSavePractice($list, &$resultSuccess)
    {
        //var_dump($_POST, $list);die;
        $j = 0;
        foreach ($_POST['comp'] as $mark => $item) {
            //var_dump($mark, $item);die;
            $writeArray = [];
            if (isset($item['nrec']) and isset($item['name']) /*and isset($item['datebegin']) and isset($item['dateend'])*/) {
                if ($item['nrec'] != "" and $item['name'] != "") {
                    $writeArray['label'] = $item['name'];
                    $writeArray['begin'] = null;
                    $writeArray['end'] = null;
                    $writeArray['examinerNrec'] = $list['student'][$j]['makrExaminerNrec'];
                    $writeArray['listNrec'] = $list['nrec'];
                    //$writeArray['discipline'] = substr($list['discipline'], 0, 31) == 'Учебная практика' ? 'Учебная практика' : $list['discipline'];
                    $writeArray['discipline'] = "Практика";
                    // в personNrec записываем T$U_MARKS.F$NREC для получения T$PERSONS.F$NREC
                    $writeArray['personNrec'] = $mark;
                    if ($item['datebegin'] != "" and preg_match("/^[0-3][0-9].[0|1][0-9].(19|20)[0-9]{2}/", $item['datebegin'])) {
                        $writeArray['begin'] = $item['datebegin'];
                    }
                    if ($item['dateend'] != "" and preg_match("/^[0-3][0-9].[0|1][0-9].(19|20)[0-9]{2}/", $item['dateend'])) {
                        $writeArray['end'] = $item['dateend'];
                    }
                    $result = ApiKeyService::queryApi('updatePracticeList', $writeArray, Yii::app()->session['ApiKey']);
                    $resultSuccess = $result['code'] == 200;
                }

            }
            $j++;
        }
    }

    private function getEntCat()
    {
        $Enterprise = [];
        /*$hEnterprise = ApiKeyService::queryApi('getEntCat', array("hash"=>1), Yii::app()->session['ApiKey'], 'GET');
        $check = StudyProcessModule::checkEntHashRows($hEnterprise['json_data']);
        if(!$check){
            $model = ApiKeyService::queryApi('getEntCat', array(), Yii::app()->session['ApiKey'], 'GET');
            StudyProcessModule::insertEntRows($model['json_data'], $hEnterprise['json_data']);
            $model = $model['json_data'];
        }
        else{
            $model = EnterpriseList::model()->findAll();
        }*/
        $model = ApiKeyService::queryApi('getEntCat', array(), Yii::app()->session['ApiKey'], 'GET')['json_data'];
        $i = 0;
        /*foreach ($model as $item){
            $Enterprise[$i]['label'] = $item['attributes']['name'];
            $Enterprise[$i]['value'] = $item['attributes']['nrec'];
            $i++;
        }*/
        return $model;
    }

    public function actionSupervisorscomp($term)
    {
        //Получаем список всех предприятий
        if (!isset($Enterprise)) {
            $Enterprise = $this->getEntCat();
        }
        $maxCnt = 20;
        $result = array();
        foreach ($Enterprise as $row) {
            if (mb_strstr(mb_strtolower($row['label'], Yii::app()->charset), mb_strtolower($term, Yii::app()->charset), false, Yii::app()->charset)) {
                $result[] = $row;
                if (--$maxCnt < 0) {
                    break;
                }
            }
        }
        echo CJSON::encode($result);
    }

    /**
     * Функция для сохранения номеров зачетных книжек
     * @param $data
     */
    public function saveRecordBook($data)
    {
        $writeArray = [];
        $j = 0;
        foreach ($data as $key => $row) {
            if (trim($row) != '') {
                $writeArray['student'][$j] = [];
                $writeArray['student'][$j]['studPersonNrec'] = CMisc::_id($key);
                $writeArray['student'][$j]['recordBookNumber'] = $row;
                $j++;
            }
        }
        if ($j) {
            $result = ApiKeyService::queryApi('updateRecordBook', $writeArray, Yii::app()->session['ApiKey'], 'PUT');
        }
    }

    public function actionSaveAtteDate()
    {
        $writeArray = [];
        $resultSuccess = true;
        if (isset($_POST)) {
            $id = $_POST['AudHours_N']['nrecList'];
        }
    }

    /**
     * Сохранение оценок по дипломным работам через ajax
     */
    public function actionSaveMarkDip()
    {
        //var_dump($_POST);die;
        $successFields = [];
        $writeArray = [];
        $resultSuccess = true;
        if (isset($_POST)) {
            $id = $_POST['AudHours_N']['nrecList'];
            $sessionList = isset(Yii::app()->session['Lists'][$id]) ? Yii::app()->session['Lists'][$id] : null;
            if (empty($sessionList)) {
                $sessionList = $data = ApiKeyService::queryApi('getListDipStruct', array("nrec" => $id), Yii::app()->session['ApiKey'], "GET")['json_data'];
            }
            $sessionListStudent = ListClass::getStudentForUpdate($sessionList);

            /*Отдельно вносим поля даты ведомости и признака закрытия*/
            $dateList = CMisc::toGalDate(date('d.m.Y'));
            $dopStatusList = intval($_POST['AudHours_N']['dop']);
            if ($sessionList['dateList'] != $dateList || $sessionList['dopStatusList'] != $dopStatusList) {
                $writeArray['nrec'] = $id;
                $writeArray['dateList'] = $dateList;
                $writeArray['dopStatusList'] = $dopStatusList;
                $result = ApiKeyService::queryApi('updateMarkAndRating', $writeArray, Yii::app()->session['ApiKey']);
            }

            $writeArray = [];
            $writeArray['nrec'] = $id;
            $j = 0;
            foreach ($_POST['marks'] as $key => $row) {
                $checkupdate = false;
                $writeArray['student'][$j] = [];
                if (!in_array($row, ['0x800100000000242E']) or $dopStatusList) {
                    $writeArray['student'][$j]['markStudNrec'] = CMisc::_id($key);

                    /*достаём поля из POST*/
                    $markLinkNumberNrec = mb_strtolower(($row != '') ? CMisc::_id($row) : '0x8000000000000000');
                    /*заносим их в запрос на сохранение*/
                    $writeArray['student'][$j]['markLinkNumberNrec'] = $markLinkNumberNrec;
                    /*проверяем отличаются ли они от того что сейчас находится в сессии*/
                    if (mb_strtolower($sessionListStudent[$key]['markLinkNumberNrec']) != $markLinkNumberNrec) {
                        $checkupdate = true;
                    }

//                    var_dump($writeArray);die;
                    //Внос данных по диплому
                    $numberProto = (isset($_POST['numberProto'][$key])) ? $_POST['numberProto'][$key] : '';
                    $dataProto = (isset($_POST['dataProto'][$key])) ?
                        (($_POST['dataProto'][$key] != '') ? CMisc::toGalDate($_POST['dataProto'][$key]) : 0)
                        : 0;

                    $writeArray['student'][$j]['numberProto'] = $numberProto;
                    $writeArray['student'][$j]['dataProto'] = $dataProto;
                    if ($sessionListStudent[$key]['numberProto'] != $numberProto) {
                        $checkupdate = true;
                    }
                    if ($sessionListStudent[$key]['dataProto'] != $dataProto) {
                        $checkupdate = true;
                    }

                    if ($checkupdate) {
                        $result = ApiKeyService::queryApi('updateDipMark', $writeArray, Yii::app()->session['ApiKey'], "PUT");
                        $successFields[$key] = $result;
                        /*запись изменений обратно в сессию*/
                        if ($result['code'] == 200) {
                            $sessionList['student'][$sessionListStudent[$key]['id']]['markLinkNumberNrec'] = $markLinkNumberNrec;
                            $sessionList['student'][$sessionListStudent[$key]['id']]['numberProto'] = $numberProto;
                            $sessionList['student'][$sessionListStudent[$key]['id']]['dataProto'] = $dataProto;
                            Yii::app()->session['Lists'] = array($id => $sessionList);
                        } else {
                            $resultSuccess = false;
                        }
                    }
                }
            }
        }
        echo CJSON::encode(array('success' => $resultSuccess, 'successFields' => $successFields));
    }

    //------------------------------------------------------------------------------------------------
    //Тема курсовых работ
    public function actionKursTheme($id)
    {

        $data = ApiKeyService::queryApi('getKursTheme', array("nrec" => $id), Yii::app()->session['ApiKey'], 'GET');
        ApiKeyService::checkResponseApi($data, 'getKursTheme');
        $themes = $data['json_data'];
//        var_dump($themes);die;

        $galId = bin2hex(Yii::app()->user->getGalIdT());
        $dataProvider = new CArrayDataProvider(CHtml::value($themes, 'student'), array('pagination' => false, 'keyField' => 'markStudNrec'));

        Yii::app()->session['Themes'] = array($id => $themes);
        $this->render('kursTheme', [
            'dataProvider' => $dataProvider,
            'info' => $themes,
            'PN' => $galId,]);
    }

    /**
     * Сохранение тем курсовых работ (проектов)
     */
    public function actionSaveKursTheme()
    {
//        var_dump($_POST);die;
        $successFields = [];
        $writeArray = [];
        $resultSuccess = true;
        if (isset($_POST)) {
            $id = $_POST['AudHours_N']['nrecList'];
            $sessionTheme = isset(Yii::app()->session['Themes'][$id]) ? Yii::app()->session['Themes'][$id] : null;
            if (empty($sessionTheme)) {
                $sessionTheme = ApiKeyService::queryApi('getKursTheme', array("nrec" => $id), Yii::app()->session['ApiKey'], 'GET')['json_data'];
            }
            $sessionThemeStudent = ListClass::getStudentForUpdate($sessionTheme);

            $writeArray['nrec'] = $id;
            $j = 0;
            foreach ($_POST['kursTheme'] as $key => $row) {
                if (strlen(trim($row)) == 0) {
                    continue;
                }
                $checkupdate = false;
                $writeArray['student'][$j] = [];
                /*постоянные*/
                $writeArray['student'][$j]['markStudNrec'] = CMisc::_id($key);
                $writeArray['student'][$j]['dbDipNrec'] = (isset($_POST['kursThemeNrec'][$key]) ? $_POST['kursThemeNrec'][$key] : '0x8000000000000000');
                /*достаём поля из POST*/
                $kursTheme = ListClass::sanitizeThemes($row);
                $kursThemeTeacherNrec = mb_strtolower(($_POST['examiners'][$key] != '') ? $_POST['examiners'][$key] : '0x8000000000000000');
                /*заносим их в запрос на сохранение*/
                $writeArray['student'][$j]['kursTheme'] = $kursTheme;
                $writeArray['student'][$j]['kursThemeTeacherNrec'] = $kursThemeTeacherNrec;
                /*проверяем отличаются ли они от того что сейчас находится в сессии*/
                if ($sessionThemeStudent[$key]['kursTheme'] != $kursTheme) {
                    $checkupdate = true;
                }
                if (mb_strtolower($sessionThemeStudent[$key]['kursThemeTeacherNrec']) != $kursThemeTeacherNrec) {
                    $checkupdate = true;
                }

//                var_dump(json_encode($writeArray));die;
                if ($checkupdate) {
//                    echo json_encode($writeArray);die;
                    $result = ApiKeyService::queryApi('modifeKursTheme', $writeArray, Yii::app()->session['ApiKey'], 'PUT', true);
                    $successFields[$key] = $result;
                    if ($result['code'] == 200) {
                        $sessionTheme['student'][$sessionThemeStudent[$key]['id']]['kursTheme'] = $kursTheme;
                        $sessionTheme['student'][$sessionThemeStudent[$key]['id']]['kursThemeTeacherNrec'] = $kursThemeTeacherNrec;
                        Yii::app()->session['Themes'] = array($id => $sessionTheme);
                    } else {
                        $resultSuccess = false;
                    }
                }
            }
        }
        echo CJSON::encode(array('success' => $resultSuccess, 'successFields' => $successFields));
    }

    //------------------------------------------------------------------------------------------------
    //Закрепление ответственного за ведомостью
    /**
     * Отображение представления закрепления преподавателя
     * @param $id
     */
    public function actionListExaminer($id)
    {

        $data = ApiKeyService::queryApi('getListStruct', array("nrec" => $id), Yii::app()->session['ApiKey']);
        ApiKeyService::checkResponseApi($data, 'getListStruct');
        $data = $data['json_data'];
        /*Api не отдаёт данные с первого раза*/
        $data = $this->emptyAnswerStudents($data, $id);

        $listExaminer = CHtml::value($data, 'listexaminer');
//        var_dump($data);die;

        $listExaminerChair = ApiKeyService::queryApi('getStuffForList', array("nrec" => $id, 'all' => 0), Yii::app()->session['ApiKey'], 'GET');
        ApiKeyService::checkResponseApi($listExaminerChair, 'getStuffForList');
        $listExaminerChair = $listExaminerChair['json_data'];
        $listExaminerChairNrecs = array_column($listExaminerChair, 'nrec');

        foreach ($listExaminer as $examiner) {
            if (!in_array($examiner['nrecExaminer'], $listExaminerChairNrecs)) {
                $listExaminerChair[] = ['nrec' => $examiner['nrecExaminer'], 'fio' => $examiner['fioExaminer'] . ' (другая кафедра)'];
            }
        }

//        var_dump($listExaminerChair);die;

        $galId = CMisc::_id(bin2hex(Yii::app()->user->getGalIdT()), 'upper');
        $main = $data['examinerNrec'];

        $this->render('listExaminer', [
            'listExaminer' => $listExaminer,
            'listExaminerChair' => $listExaminerChair,
            'info' => $data,
            'mainExaminer' => $main,
            'PN' => $galId]);
    }

    /**
     * Удаление преподавателя
     */
    public function actionDeleteExaminer()
    {
//        var_dump($_POST);die;
        $result = false;
        if (isset($_POST)) {
            $nrec = $_POST['nrec'];
            if ($nrec != '') {
                $successFields = ApiKeyService::queryApi('delreclistexam', $_POST, Yii::app()->session['ApiKey'], 'DELETE');
                $result = ($successFields['code'] == 200) ? true : false;
                $successFields = $successFields['json_data'];
            } else {
                $result = false;
                $successFields = 'Пользователь ещё не внесён';
            }
        }
        echo CJSON::encode(array('success' => $result, 'successFields' => $successFields));
    }

    /**
     * Обновление преподавателя
     */
    public function actionUpdateExaminer()
    {
//        var_dump($_POST);die;
        $resultSuccess = true;
        $writeArray = [];
        $successFields = '';
        if (isset($_POST)) {
            $nrecList = $_POST['listNrec'];
            $mainExam = $_POST['mainExam'];
            $nrec = isset($_POST['nrec']) ? $_POST['nrec'] : false;
            $examiner = isset($_POST['Examiner']) ? (($_POST['Examiner'] != '') ? $_POST['Examiner'] : '0x8000000000000000') : false;
            if ($mainExam != '') {
                if ($nrecList != '') {
                    $writeArray['nrec'] = $nrecList;
                    $writeArray['examinerNrec'] = $mainExam;
                    if ($examiner) {
                        if ($nrec != '') {
                            $writeArray['listexaminer'][0]['nrec'] = $nrec;
                        }
                        $writeArray['listexaminer'][0]['nrecExaminer'] = $examiner;
                    }
                } else {
                    $resultSuccess = false;
                    $successFields = 'Ведомость не определена';
                }
            } else {
                $resultSuccess = false;
                $successFields = 'Выбран не существующий ответственный';
            }
//            var_dump($resultSuccess, $successFields, $writeArray, json_encode($writeArray));die;
            if ($resultSuccess) {
                $result = ApiKeyService::queryApi('updateExaminer', $writeArray, Yii::app()->session['ApiKey'], 'POST');
                $successFields = $result['json_data'];
                if ($result['code'] != 200) {
                    $resultSuccess = false;
                }
                if (!$nrec && $resultSuccess) {
                    $data = ApiKeyService::queryApi('getListStruct', array("nrec" => $nrecList), Yii::app()->session['ApiKey']);
                    $data = $data['json_data']['listexaminer'];
                    foreach ($data as $row) {
                        if ($row['nrecExaminer'] == $examiner) {
                            $nrec = $row['nrec'];
                        }
                    }
                }
            }
        }
        echo CJSON::encode(array('success' => $resultSuccess, 'successFields' => $successFields, 'returnField' => $nrec));
    }

    /**
     * Формирование поля для добавления нового ответственного
     */
    public function actionAddExaminerField()
    {

        $listExaminerChair = ApiKeyService::queryApi('getStuffForList', array("nrec" => $_POST['listNrec'], 'all' => 0), Yii::app()->session['ApiKey'], 'GET')['json_data'];
        $return = '<div class="jumbotron" style="padding-top: 30px;padding-bottom: 30px; margin-bottom: 10px; border: 1px black solid; text-align-last: center;">'
            . CHtml::hiddenField('examinerNrec', '')
            . CHtml::hiddenField('selectedNrec', '')
            . CHtml::dropdownList("examiner", '',
                CHtml::listData($listExaminerChair, "nrec", "fio"),
                array("prompt" => "", "class" => "updateExaminer form-control", "style" => "display: inline-block; width: 85%; text-align-last: center;"))

            . CHtml::link('<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Удалить ответственного" class="glyphicon glyphicon-trash"/>',
                '', array('class' => 'deleteExaminer btn btn-danger',
                    'style' => 'margin-left: 5px; margin-bottom: 5px'))

            . CHtml::link('<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Ответственный преподаватель" class="glyphicon glyphicon-user"/>',
                '', array('class' => 'mainExam btn btn-primary',
                    'style' => 'margin-left: 5px; margin-bottom: 5px; display: none;'))
            . CHtml::link('<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Назначить ответственным" class="glyphicon glyphicon-user"/>',
                '', array('class' => 'Exam btn btn-primary',
                    'style' => 'margin-left: 5px; margin-bottom: 5px; color: #337ab7;'))
            . '</div>';

        echo CJSON::encode(array('success' => $return));
    }

}
