<?php

class ExtramarkController extends Controller
{

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index',
                    'downLoadWorkFile',
                    'list', 'listPrint', 'draftPrint', 'saveMark', 'kursTheme', 'saveKursTheme'
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
        if (!isset(Yii::app()->session['ApiKey'])) {
            $key = ApiKeys::model()->findByAttributes(array('fnpp' => Yii::app()->user->getFnpp()));
            if ($key instanceof ApiKeys) {
                Yii::app()->session['ApiKey'] = $key->apikey;
            } else {
                $this->render('../mark/emptylist', ['code' => 1002, 'text' => 'Отсутствует API-key']);
                die;
            }
        };
        return true;
    }

    /**
     * Список всех направлений
     * @param bool $year
     * @throws CException
     */
    public function actionIndex($status = 1)
    {
        $fnpp = Yii::app()->user->getFnpp();

        $data = ApiKeyService::queryApi('fnppExtraList', array("fnpp" => $fnpp), Yii::app()->session['ApiKey'], 'GET');
        ApiKeyService::checkResponseApi($data, 'fnppExtraList');
        $data = $data['json_data'];
        $newdata = [];
        foreach ($data as $row) {
            $newdata[] = $row;
        }
        sort($data);

        $data = $newdata;

        $yearList = [];
        foreach ($data as $datum) {
            if (!in_array($datum['year'], $yearList)) {
                $yearList[$datum['year']] = $datum['year'];
            }
        }
        krsort($yearList);


        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }

        $filter->status = $status;

//        if ($year) {
//            $filter->year = $year;
//        } elseif (!empty(Yii::app()->session['YearList'])) {
//            $filter->year = Yii::app()->session['YearList'];
//            $year = Yii::app()->session['YearList'];
//        }else {
//            if (in_array($yearCW, $yearList)){
//                $filter->year = $yearCW;
//                $year = $yearCW;
//            } else {
//                $filter->year = isset($yearList[0]) ? $yearList[0] : $yearCW;
//                $year = isset($yearList[0]) ? $yearList[0] : $yearCW;
//            }
//
//        }
//        Yii::app()->session['YearList'] = $year;

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
//            'year' => $year,
            'status' => $status
        ]);
    }

    //------------------------------------------------------------------------------------------------
    //Модальное окно
    /**
     * Получаем список загруженных файлов для модального окна
     */
    public function actionLoadlistfile()
    {
        if (isset($_POST) && is_array($_POST)) {
            $result = ListClass::getListOfFilesByDisAndSemester($_POST['studVal'], true);
        } else {
            $result = false;
        }
        echo CJSON::encode(array('success' => $result));
    }

    public function actionDownLoadWorkFile($id)
    {

        $fnpp = Yii::app()->user->getFnpp();
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
//        var_dump($reply, ['id' => $id, 'fnpp' => $fnpp, 'key' => md5('!'.$fnpp.'#'.$id.'omstuSolt')]);
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

        $data = ApiKeyService::queryApi('getExtraListStruct', array("nrec" => $id), Yii::app()->session['ApiKey'], 'GET');
        ApiKeyService::checkResponseApi($data, 'getExtraListStruct');
        $data = $data['json_data'];
        $list_marks = ApiKeyService::queryApi('getCatalogMarks', array(), Yii::app()->session['ApiKey'], 'GET');
        ApiKeyService::checkResponseApi($list_marks, 'getCatalogMarks');
        $list_marks = $list_marks['json_data'];

        if (empty($data['listexaminer'])) {
            $data['listexaminer'][] = ['nrecExaminer' => $data['examinerNrec'], 'fioExaminer' => $data['examinerFio']];
        }

//        var_dump($data); die;
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

        if ($data['typeList'] % 50 == 9) {
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

        /*отдельно надо будет сделать dip_work*/
        if (in_array(CHtml::value($data, 'typeList'), array(uList::TYPE_DIP_WORK, uList::TYPE_DIP_PROJECT))) {
            echo '<center><h2>В разработке</h2></center>';
        }
        $galId = bin2hex(Yii::app()->user->getGalIdT());

        $dataProvider = new CArrayDataProvider(CHtml::value($data, 'student'), array('pagination' => false, 'keyField' => 'markStudNrec'));

        Yii::app()->session['Lists'] = array($id => $data);
        $this->render('list', [
            'dataProvider' => $dataProvider,
            'info' => $data,
            'marks' => $marks,
            'PN' => $galId,
        ]);
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
     * Сохранение рейтинга по КН через ajax
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
                $sessionList = $data = ApiKeyService::queryApi('getListStruct', array("nrec" => $id), Yii::app()->session['ApiKey'])['json_data'];
            }
            $sessionListStudent = ListClass::getStudentForUpdate($sessionList);

            /*Отдельно вносим поля даты ведомости и признака закрытия*/
            $dateList = CMisc::toGalDate((
            isset($_POST['AudHours_N']['dateDoc'])?
                ($_POST['AudHours_N']['dateDoc'] != '')?$_POST['AudHours_N']['dateDoc']:date('d.m.Y'):
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
            foreach ($_POST['marks'] as $key => $row) {
                $checkupdate = false;
                $writeArray['student'][$j] = [];
                if (!in_array($row, ['0x800100000000242E']) or $dopStatusList) {
                    $writeArray['student'][$j]['markStudNrec'] = CMisc::_id($key);
                    if ($row != '') {
                        /*достаём поля из POST*/
//                        $ratingsem = ((isset($_POST['ratingsem'][$key])) ?
//                            $_POST['ratingsem'][$key] :
//                            ((isset($_POST['ratingres'][$key])) ?
//                                (($_POST['ratingres'][$key] >= 60) ? 60 :
//                                    $_POST['ratingres'][$key]) : 0));
//                        $ratingatt = ((isset($_POST['ratingatt'][$key])) ?
//                            $_POST['ratingatt'][$key] :
//                            ((isset($_POST['ratingres'][$key])) ?
//                                (($_POST['ratingres'][$key] >= 60) ? ($_POST['ratingres'][$key] - 60) :
//                                    0) : 0));
                        $ratingsem = ((isset($_POST['ratingsem'][$key])) ?
                            $_POST['ratingsem'][$key] :
                            ((isset($_POST['ratingres'][$key])) ?
                                $_POST['ratingres'][$key] : 0));
                        $ratingatt = ((isset($_POST['ratingatt'][$key])) ?
                            $_POST['ratingatt'][$key] :
                            0);
                        if ($ratingatt == -1) {
                            $ratingatt = 0;
                        }
                        $makrExaminerNrec = mb_strtolower(($_POST['examiners'][$key] != '') ? $_POST['examiners'][$key] : '0x8000000000000000');

                        /*заносим их в запрос на сохранение*/
                        $writeArray['student'][$j]['ratingsem'] = $ratingsem;
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

//                    var_dump($writeArray);die;
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
            if(isset($_POST['comp'])){
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

    public function actionKursTheme($id)
    {

        $data = ApiKeyService::queryApi('getKursTheme', array("nrec" => $id), Yii::app()->session['ApiKey'], 'GET');
        ApiKeyService::checkResponseApi($data, 'getKursTheme');
        $themes = $data['json_data'];
//        var_dump($themes);die;

//        if(empty($themes['listexaminer'])){
//            $themes['listexaminer'][] = ['nrecExaminer' => $themes['examinerNrec'], 'fioExaminer' => $themes['examinerFio']];
//        }

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

}
