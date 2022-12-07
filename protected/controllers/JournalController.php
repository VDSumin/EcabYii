<?php

class JournalController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view', 'statistics', 'getview', 'getindex', 'getstatistics', 'printXls2year'),
                'users' => array('*'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function actionView($id = null)
    {
        $galId = Yii::app()->user->getGalId();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if (Yii::app()->user->getPerStatus()) {
            return $this->redirect(['attendance/index']);
        } else {
            $pps = false;
        }
        if (!isset($_SESSION)) {
            session_start();
        }
        $galIdMassive = Yii::app()->user->getGalIdMass();
        if (isset($id)) {
            $prov = 0;
            foreach ($galIdMassive as $galunid) {
                if ($galunid['Galid'] == $id) {
                    $prov = 1;
                }
            }
            if ($prov == 1) {
                $galId = $id;
                $_SESSION['ID'] = $id;
            }
        } else {
            if (isset($_SESSION['ID'])) {
                $galId = $_SESSION['ID'];
            } else {
                $_SESSION['ID'] = $galId;
            }
        }
        //----------делается запрос даты из окошечка
        $result = $this->_Date(); //запрос даты из окна
        if ($result != '') {
            $_SESSION['date'] = $result;
            $_SESSION['day'] = date('w', strtotime($result));
        }
        if (isset($_SESSION['date'])) {
            $date = $_SESSION['date'];
        } else {
            $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $date = date('Y-m-d', $toDate);
        }
        if (isset($_SESSION['day'])) {
            $day = $_SESSION['day'];
        } else {
            if (isset($date)) {
                $day = date('w', strtotime($date));
            } else {
                $day = date('w');
            }
        }
        $allday = 0;
        if ($day == 7) {
            $allday = 1;
        }
        //---------определяем дату
        if (!isset($date)) {
            if (isset($_SESSION['date'])) {
                $date = $_SESSION['date'];
            } else {
                $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $date = date('Y-m-d', $toDate);
            }
        } else {
            $_SESSION['date'] = $date;
        }
        if (!isset($day)) {
            if (isset($_SESSION['date'])) {
                $day = $_SESSION['day'];
            } else {
                $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $day = date('w', $toDate);
            }
        } else {
            $_SESSION['day'] = $day;
        }
        if (isset($date)) {
            $day = date('w', strtotime($date));
        }
        if ($day == 0) {
            $toDate = date('Y-m-d', strtotime($date . '-7 day'));
        } else {
            $toDate = date('Y-m-d', strtotime($date . '-' . $day . ' day'));
        }
        $i = 0;//определяем даты текущей недели
        while ($i < 7) {//даты с понедельника по воскресения
            $i++;
            $dates[] = date('Y-m-d', strtotime($toDate . '+' . $i . ' day'));
        }
        $date1 = date('Y-m-d', strtotime($toDate . '+1 day'));
        $date2 = date('Y-m-d', strtotime($toDate . '+7 day'));
        if ($allday) {
            $day = 7;
        }
        if ($day == 7) {//показывает дисциплины за неделю
            $discipline = $this->statdis($date1, $date2, $galId);
        } else {//показывает дисциплины за определенную дату
            $discipline = $this->statdisdata($date, $galId);
        }
        $activedates = $this->dateactive($date1, $date2, $galId);
        $steward = $this->steward($galId);
        if ($steward) {
            $list = $this->getlist($galId);
        } else {
            $list = 0;
        }
        $galIdMassiv = Yii::app()->user->getGalIdMass();
        if (count($galIdMassiv) > 1) {
            if ($steward) {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => 'Заполнение', 'url' => array('index')),
                    array('label' => '<center>Выберите группу</center>', 'encodeLabel' => false, 'itemOptions' => array('role' => 'separator', 'class' => 'divider'),),
                );
                foreach ($galIdMassiv as $galid) {
                    if ($galid['Galid'] == $_SESSION['ID']) {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('view', 'id' => $galid['Galid']),
                            'itemOptions' => array('class' => 'btn-default disabled'), 'linkOptions' => array('style' => 'pointer-events: none; color: #999; cursor: default;'));
                    } else {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('view', 'id' => $galid['Galid']), 'itemOptions' => array('class' => 'btn-default'));
                    }
                }
            } else {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => '<center>Выберите группу</center>', 'encodeLabel' => false, 'itemOptions' => array('role' => 'separator', 'class' => 'divider'),),
                );
                foreach ($galIdMassiv as $galid) {
                    if ($galid['Galid'] == $_SESSION['ID']) {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('view', 'id' => $galid['Galid']),
                            'itemOptions' => array('class' => 'btn-default disabled'), 'linkOptions' => array('style' => 'pointer-events: none; color: #999; cursor: default;'));
                    } else {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('view', 'id' => $galid['Galid']), 'itemOptions' => array('class' => 'btn-default'));
                    }
                }
            }
        } else {
            if ($steward) {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => 'Заполнение', 'url' => array('index')),
                );
            } else {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                );
            }
        }
//        var_dump($toDate,$date1,$date2,$day,$dates,$activedates);die;
//        var_dump($activedates);die;
        $name = $this->myname($galId);
        $fnpp = $this->myfnpp($galId);
        $result = Yii::app()->db2->createCommand()
            ->select('s.fnpp')
            ->from('gal_u_student gus')
            ->join('skard s', 'gus.nrec = s.gal_srec')
            ->where('gus.cpersons=:galid', array(':galid' => $galId))
            ->queryAll();
        $fnpps = [];
        foreach ($result as $item) {
            $fnpps[] = $item['fnpp'];
        }
        //var_dump($activedates);die;
        $this->render('view', array(
            'day' => $day,
            'date' => $date,
            'dates' => $dates,
            'activedates' => $activedates,
            'discipline' => $discipline,
            'steward' => $steward,
            'list' => $list,
            'name' => $name,
            'fnpp' => $fnpps,
            'menu' => $menu,
        ));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function actionStatistics($id = null)
    {
        set_time_limit(0);
        $galId = Yii::app()->user->getGalId();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if (Yii::app()->user->getPerStatus()) {
            return $this->redirect(['attendance/index']);
        } else {
            $pps = false;
        }
        if (!isset($_SESSION)) {
            session_start();
        }
        $galIdMassive = Yii::app()->user->getGalIdMass();
        if (isset($id)) {
            $prov = 0;
            foreach ($galIdMassive as $galunid) {
                if ($galunid['Galid'] == $id) {
                    $prov = 1;
                }
            }
            if ($prov == 1) {
                $galId = $id;
                $_SESSION['ID'] = $id;
            }
        } else {
            if (isset($_SESSION['ID'])) {
                $galId = $_SESSION['ID'];
            } else {
                $_SESSION['ID'] = $galId;
            }
        }
        if (!isset($_SESSION['stat'])) {
            $stat = 3;
            $_SESSION['stat'] = 3;
        } else {
            $stat = $_SESSION['stat'];
        }
        if ($stat == 3 and $this->_StatDate()) {
            $date1 = date('Y-m-d 00:00:00', strtotime($_SESSION['DateFrom']));
            $date2 = date('Y-m-d 23:59:59', strtotime($_SESSION['DateTo']));
        } else {
            $date1 = date('2022-09-01 00:00:00');
            $date2 = date('2022-09-01 23:59:59');
        }
        if ($stat == 1) {
            unset($_SESSION['DateFrom']);
            unset($_SESSION['DateTo']);
            $date1 = date('2022-09-01 00:00:00');
            $date2 = date('Y-m-d 23:59:59');
        }
        // TODO: Поменять даты для стастики журнала посещаемости
        if ($stat == 2) {
            unset($_SESSION['DateFrom']);
            unset($_SESSION['DateTo']);
            $date1 = date('2022-09-01 00:00:00');
            $date2 = date('2023-01-31 23:59:59');
        }


        $discipline = $this->statdis($date1, $date2, $galId);
        $shet = 0;
        $i = 0;
        $data[1]['disciplineNrec'] = '';
        $data[1]['discipline'] = '';
        $data[1]['kindOfWorkId'] = '';
        $data[1]['Kind'] = '';
        $data[1]['teacherFio'] = '';
        $data[1]['teacherFnpp'] = '';
        $data[1]['Amount'] = 0;
        $data[1]['studGroupName'] = '';
        foreach ($discipline as $dis) {
            $test = 1;
            foreach ($data as $dat) {
                if ($dis['disciplineNrec'] == $dat['disciplineNrec']) {
                    if ($dis['teacherFnpp'] == $dat['teacherFnpp']) {
                        if ($dis['kindOfWorkId'] == $dat['kindOfWorkId']) {
                            $test = 0;
                        } elseif ($dis['kindOfWorkId'] == '2' and $dis['studGroupName'] != $dat['studGroupName']) {
                            $test = 1;
                        }
                    }
                }
            }
            if ($test) {//если похожих записей не было найдено, то создаем новую запись
                $i++;
                $data[$i]['discipline'] = $dis['discipline'];
                $data[$i]['disciplineNrec'] = $dis['disciplineNrec'];
                $data[$i]['kindOfWorkId'] = $dis['kindOfWorkId'];
                $data[$i]['Kind'] = $dis['Kind'];
                $data[$i]['teacherFio'] = $dis['teacherFio'];
                $data[$i]['teacherFnpp'] = $dis['teacherFnpp'];
                $data[$i]['Amount'] = 0;
                $data[$i]['studGroupName'] = $dis['studGroupName'];
                foreach ($discipline as $dis4et) {
                    if (($data[$i]['disciplineNrec'] == $dis4et['disciplineNrec'])
                        and ($data[$i]['teacherFnpp'] == $dis4et['teacherFnpp'])
                        and ($data[$i]['kindOfWorkId'] == $dis4et['kindOfWorkId'])
                        and ($data[$i]['studGroupName'] == $dis4et['studGroupName'])) {
                        $data[$i]['Amount']++;
                    }
                }
            } else {
                continue;
            }
        }
        sort($data);

        $steward = $this->steward($galId);
        if ($steward) {
            $list = $this->getlist($galId);
        } else {
            $list = $this->getalonelist($galId);
        }
        $i = 0;
        foreach ($list as $li) {
            $proc[0][$i] = 0;
            foreach ($data as $dat) {
                if ((($dat['kindOfWorkId'] == 2) and (substr($dat['studGroupName'], -2) == '/1')) or ($dat['kindOfWorkId'] != 2)) {
                    $proc[0][$i] += (2 * $dat['Amount']);
                }
            }
            $i++;
        }
        $i = 0;
        foreach ($list as $li) {
            $j = 0;
            foreach ($data as $dat) {
                $lists[$i][$j] = 0;
                $j++;
            }
            $i++;
        }
        $i = 0;
        foreach ($list as $li) {
            $proc[1][$i] = 0;
            foreach ($discipline as $dis) {
                $test = 0;
                if (in_array($this->mark($li['fnpp'], $dis['id']), [1, 4, 6])) {
                    $test = 1;
                }
                $j = 0;
                foreach ($data as $dat) {
                    if ($dis['disciplineNrec'] == $dat['disciplineNrec']
                        and $dis['teacherFnpp'] == $dat['teacherFnpp']
                        and $dis['kindOfWorkId'] == $dat['kindOfWorkId']
                        and $dis['studGroupName'] == $dat['studGroupName']) {
                        if ($test) {
                            $lists[$i][$j] = $lists[$i][$j] + 2;
                            $proc[1][$i] += 2;
                        }
                    }
                    $j++;
                }
            }
            $i++;
        }
        $i = 0;
        foreach ($list as $li) {
            $j = 0;
            foreach ($data as $dat) {
                $liststeach[$i][$j] = 0;
                $j++;
            }
            $i++;
        }
        $i = 0;
        foreach ($list as $li) {
            $proc[2][$i] = 0;
            foreach ($discipline as $dis) {
                $test = 0;
                if (in_array($this->markteach($li['fnpp'], $dis['id']), [1, 4, 6])) {
                    $test = 1;
                }
                $j = 0;
                foreach ($data as $dat) {
                    if ($dis['disciplineNrec'] == $dat['disciplineNrec']
                        and $dis['teacherFnpp'] == $dat['teacherFnpp']
                        and $dis['kindOfWorkId'] == $dat['kindOfWorkId']
                        and $dis['studGroupName'] == $dat['studGroupName']) {
                        if ($test) {
                            $liststeach[$i][$j] = $liststeach[$i][$j] + 2;
                            $proc[2][$i] += 2;
                        }
                    }
                    $j++;
                }
            }
            $i++;
        }
        $i = 0;
        foreach ($list as $li) {
            if ($proc[1][$i] > $proc[0][$i]) {
                $proc[1][$i] = $proc[0][$i];
            }
            if ($proc[2][$i] > $proc[0][$i]) {
                $proc[2][$i] = $proc[0][$i];
            }
            $i++;
        }
        //var_dump($data, $proc, $proc[1][1]/$proc[0][1]);die;
        $galIdMassiv = Yii::app()->user->getGalIdMass();
        if (count($galIdMassiv) > 1) {
            if ($steward) {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => 'Заполнение', 'url' => array('index')),
                    array('label' => '<center>Выберите группу</center>', 'encodeLabel' => false, 'itemOptions' => array('role' => 'separator', 'class' => 'divider'),),
                );
                foreach ($galIdMassiv as $galid) {
                    if ($galid['Galid'] == $_SESSION['ID']) {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('statistics', 'id' => $galid['Galid']),
                            'itemOptions' => array('class' => 'btn-default disabled'), 'linkOptions' => array('style' => 'pointer-events: none; color: #999; cursor: default;'));
                    } else {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('statistics', 'id' => $galid['Galid']), 'itemOptions' => array('class' => 'btn-default'));
                    }
                }
            } else {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => '<center>Выберите группу</center>', 'encodeLabel' => false, 'itemOptions' => array('role' => 'separator', 'class' => 'divider'),),
                );
                foreach ($galIdMassiv as $galid) {
                    if ($galid['Galid'] == $_SESSION['ID']) {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('statistics', 'id' => $galid['Galid']),
                            'itemOptions' => array('class' => 'btn-default disabled'), 'linkOptions' => array('style' => 'pointer-events: none; color: #999; cursor: default;'));
                    } else {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('statistics', 'id' => $galid['Galid']), 'itemOptions' => array('class' => 'btn-default'));
                    }
                }
            }
        } else {
            if ($steward) {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => 'Заполнение', 'url' => array('index')),
                );
            } else {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                );
            }
        }
        $this->render('statistics', array(
            'steward' => $steward,
            'discipline' => $discipline,
            'stat' => $stat,
            'data' => $data,
            'list' => $list,
            'lists' => $lists,
            'liststeach' => $liststeach,
            'proc' => $proc,
            'menu' => $menu,
        ));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function actionIndex($id = null)
    {
        $galId = Yii::app()->user->getGalId();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if (Yii::app()->user->getPerStatus()) {
            return $this->redirect(['attendance/index']);
        } else {
            $pps = false;
        }
        if (!isset($_SESSION)) {
            session_start();
        }
        $galIdMassive = Yii::app()->user->getGalIdMass();
        if (isset($id)) {
            $prov = 0;
            foreach ($galIdMassive as $galunid) {
                if ($galunid['Galid'] == $id) {
                    $prov = 1;
                }
            }
            if ($prov == 1) {
                $galId = $id;
                $_SESSION['ID'] = $id;
            }
        } else {
            if (isset($_SESSION['ID'])) {
                $galId = $_SESSION['ID'];
            } else {
                $_SESSION['ID'] = $galId;
            }
        }
        $result = $this->_Date(); //запрос даты из окна
        if ($result != '') {
            $_SESSION['vdate'] = $result;
            $_SESSION['vday'] = date('w', strtotime($result));
        }
        if (isset($_SESSION['vdate'])) {
            $date = $_SESSION['vdate'];
        }
        if (isset($_SESSION['vday'])) {
            $day = $_SESSION['vday'];
        }
        if (!isset($date)) {//настраеваем сегодняшнюю дату, если дата была не определенна
            $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $date = date('Y-m-d', $toDate);
            $day = date('w');
        }
        if (isset($date)) {
            $day = date('w', strtotime($date));
        }
        if ($day == 0) {
            $toDate = date('Y-m-d', strtotime($date . '-7 day'));
        } else {
            $toDate = date('Y-m-d', strtotime($date . '-' . $day . ' day'));
        }
        $i = 0;//определяем даты текущей недели
        while ($i < 7) {
            $i++;
            $dates[] = date('Y-m-d', strtotime($toDate . '+' . $i . ' day'));
        }
        $date1 = date('Y-m-d', strtotime($toDate . '+1 day'));
        $date2 = date('Y-m-d', strtotime($toDate . '+7 day'));
        $discipline = $this->getdiscipline($date, $galId);
        $activedates = $this->dateactive($date1, $date2, $galId);
        $list = $this->getlist($galId);
        $steward = $this->steward($galId);
        if (!$steward) {
            $this->redirect(['journal/view']);
        }
        $fnpp = $this->myfnpp($galId);
        $result = 0;
        $result = $this->_Save();//в случае изменения сохраняем отметки
//        var_dump($toDate,$date1,$date2,$day,$dates,$activedates);die;
        $galIdMassiv = Yii::app()->user->getGalIdMass();
        if (count($galIdMassiv) > 1) {
            if ($steward) {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => 'Заполнение', 'url' => array('index')),
                    array('label' => '<center>Выберите группу</center>', 'encodeLabel' => false, 'itemOptions' => array('role' => 'separator', 'class' => 'divider'),),
                );
                foreach ($galIdMassiv as $galid) {
                    if ($galid['Galid'] == $_SESSION['ID']) {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('index', 'id' => $galid['Galid']),
                            'itemOptions' => array('class' => 'btn-default disabled'), 'linkOptions' => array('style' => 'pointer-events: none; color: #999; cursor: default;'));
                    } else {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('index', 'id' => $galid['Galid']), 'itemOptions' => array('class' => 'btn-default'));
                    }
                }
            } else {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => '<center>Выберите группу</center>', 'encodeLabel' => false, 'itemOptions' => array('role' => 'separator', 'class' => 'divider'),),
                );
                foreach ($galIdMassiv as $galid) {
                    if ($galid['Galid'] == $_SESSION['ID']) {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('index', 'id' => $galid['Galid']),
                            'itemOptions' => array('class' => 'btn-default disabled'), 'linkOptions' => array('style' => 'pointer-events: none; color: #999; cursor: default;'));
                    } else {
                        $menu[] = array('label' => $this->getgroupname($galid['Galid']), 'url' => array('index', 'id' => $galid['Galid']), 'itemOptions' => array('class' => 'btn-default'));
                    }
                }
            }
        } else {
            if ($steward) {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                    array('label' => 'Заполнение', 'url' => array('index')),
                );
            } else {
                $menu = array(
                    array('label' => 'Журнал', 'url' => array('view')),
                    array('label' => 'Статистика', 'url' => array('statistics')),
                );
            }
        }
        $this->render('index', array(
            'stud' => $galId,
            'day' => $day,
            'discipline' => $discipline,
            'list' => $list,
            'result' => $result,
            'date' => $date,
            'dates' => $dates,
            'activedates' => $activedates,
            'steward' => $steward,
            'fnpp' => $fnpp,
            'menu' => $menu,
        ));
    }

    public function actionGetView($day = null, $date = null)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($date)) {
            $_SESSION['date'] = $date;
        }
        if (isset($day)) {
            $_SESSION['day'] = $day;
        }
        $this->redirect(['journal/view']);
    }

    //
    public function actionGetStatistics($stat = null)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($stat)) {
            $_SESSION['stat'] = $stat;
        }
        $this->redirect(['journal/statistics']);
    }

    public function actionGetIndex($day = null, $date = null)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($day)) {
            $_SESSION['vday'] = $day;
        }
        if (isset($date)) {
            $_SESSION['vdate'] = $date;
        }
        if ($_SESSION['vday'] == 7) {
            $date = date('Y-m-d');
            $day = date('w');
            $_SESSION['vdate'] = $date;
            $_SESSION['vday'] = $day;
        }
        $this->redirect(['journal/index']);
    }

    //------------------------------------------------------------------------------------------------------------------
    public function _Save()
    {
        $result = null;
        //var_dump($_POST);die;
        if (Yii::app()->request->isPostRequest && isset($_POST['listname'])) {
            foreach ($_POST['listname'] as $id => $mark) {
                $result = Yii::app()->db2->createCommand()
                    ->select('aj.TeacherWasHere')
                    ->from('attendance_journal aj')
                    ->where('aj.id = :id', array(':id' => $id))
                    ->queryScalar();
                if ($result) {
                    Yii::app()->db2->createCommand()->update('attendance_journal', array(
                        'stwpMarkId' => $mark,
                    ), 'id = :id', array(':id' => $id,));
                } else {
                    Yii::app()->db2->createCommand()->update('attendance_journal', array(
                        'stwpMarkId' => $mark,
                        'teacherMarkId' => $mark,
                    ), 'id = :id', array(':id' => $id,));
                }
            }
            $result = 1;
        }
        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------
    public function _Date()
    {
        $result = null;
        if (Yii::app()->request->isPostRequest && isset($_POST['publishDate'])) {
            if ($_POST['down'] == 'Send') {
                if (strtotime($_POST['publishDate'])) {
                    $result = $_POST['publishDate'];
                }
            } elseif ($_POST['down'] == 'Previous') {
                if (strtotime($_POST['publishDate'])) {
                    $result = date('Y-m-d', strtotime($_POST['publishDate'] . '-7day'));
                }
            } elseif ($_POST['down'] == 'Next') {
                if (strtotime($_POST['publishDate'])) {
                    $result = date('Y-m-d', strtotime($_POST['publishDate'] . '+7day'));
                }
            }
        }
        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------
    public function _StatDate()
    {
        $result = null;
        if (Yii::app()->request->isPostRequest && isset($_POST['DateFrom']) && isset($_POST['DateTo'])) {
            if (!isset($_SESSION)) {
                session_start();
            }
            if (strtotime($_POST['DateFrom'])) {
                $_SESSION['DateFrom'] = $_POST['DateFrom'];
            }
            if (strtotime($_POST['DateTo'])) {
                $_SESSION['DateTo'] = $_POST['DateTo'];
            }
            if (isset($_SESSION['DateFrom']) && isset($_SESSION['DateTo'])) {
                $result = true;
            } else {
                $result = false;
            }
        }
        return $result;
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Journal the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Journal::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Journal $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'journal-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    //определяет группу студента (1)+
    public function getgroupname($galId)
    {
        $result = Yii::app()->db2->createCommand()
            ->select('gus.sdepartment')
            ->from('gal_u_student gus')
            ->where('gus.cpersons=:galid', array(':galid' => $galId))
            ->queryScalar();
        return $result;
    }

    //определяет группу студента id (1.1)+
    public function getgroupid($galId)
    {
        $result = Yii::app()->db2->createCommand()
            ->select('agg.id')
            ->from('gal_u_student gus')
            ->join('attendance_galruz_group agg', 'agg.gal_nrec = gus.cstgr')
            ->where('gus.cpersons = :galid', array(':galid' => $galId))
            ->queryScalar();
        //$fnpp = $this->myfnpp($galId);var_dump($result,$galId,$fnpp);die;
        return $result;
    }

    //определяет дисциплины для студета по дате (2)-
    public function getdiscipline($date, $galId)
    {
        $group = $this->getgroupid($galId);
        if (!$group) {
            return false;
        }
        $result = Yii::app()->db2->createCommand()
            ->select('ats.id,
                      ats.discipline,
                      ats.teacherFio, 
                      ak.name AS \'Kind\', 
                      ats.studGroupName, 
                      atw.name AS \'Type\', 
                      ats.typeOfWorkId,
                      ats.dateTimeStartOfClasses, 
                      DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\'')
            ->from('attendance_schedule ats')
            ->join('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
            ->join('attendance_typeofwork atw', 'atw.id = ats.typeOfWorkId')
            ->where(array('AND',
                'ats.studGroupId = ' . $group,
                array('like', 'ats.dateTimeStartOfClasses', '%' . $date . '%')))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();
        //var_dump($group, $result);die;
        return $result;
    }

    //определяет список своих студентов для старосты (3)+
    public function getlist($galId)
    {
        $group = $this->getgroupid($galId);
        $result = Yii::app()->db2->createCommand()
            ->selectDistinct('s.fnpp, gus.cpersons, gus.fio')
            ->from('gal_u_student gus')
            ->join('attendance_galruz_group agg', 'agg.gal_nrec = gus.cstgr')
            ->join('skard s', 's.npp = (SELECT s1.npp FROM skard s1 INNER JOIN fdata f ON f.npp = s1.fnpp WHERE gus.nrec = s1.gal_srec ORDER BY f.clink DESC, f.npp LIMIT 1 )')
            ->where(array('AND',
                'agg.id = ' . ($group ?: 0),
                'gus.warch = 0'))
            ->order('gus.fio')
            //->text;        echo $result;die;
            ->queryAll();

        return $result;
    }

    //определяет список в котором только один студент(3.1)+
    public function getalonelist($galId)
    {
        $result = Yii::app()->db2->createCommand()
            ->select('s.fnpp, gus.cpersons, gus.fio')
            ->from('gal_u_student gus')
            ->join('skard s', 'gus.nrec = s.gal_srec')
            ->where('gus.cpersons=:galid', array(':galid' => $galId))
            ->queryAll();

        return $result;
    }

    //получаем из журналу оценку (4)+
    public function mark($fnpp, $schid)
    {
        $fnpp_str = is_array($fnpp) ? 'in(' . implode(', ', $fnpp) . ')' : '=' . $fnpp;
        $result = Yii::app()->db2->createCommand()
            ->select('aj.stwpMarkId')
            ->from('attendance_journal aj')
            ->where(array('AND',
                'aj.studentFnpp ' . $fnpp_str,
                'aj.scheduleId=' . $schid))
            ->queryScalar();

        return $result;
    }

    //получаем из журналу оценку (5)+
    public function markteach($fnpp, $schid)
    {
        $fnpp_str = is_array($fnpp) ? 'in(' . implode(', ', $fnpp) . ')' : '=' . $fnpp;
        $result = Yii::app()->db2->createCommand()
            ->select('aj.teacherMarkId')
            ->from('attendance_journal aj')
            ->where(array('AND',
                'aj.studentFnpp ' . $fnpp_str,
                'aj.scheduleId=' . $schid))
            ->queryScalar();

        return $result;
    }

    //получаем из журналу оценку (6)+
    public function markid($fnpp, $schid)
    {
        $fnpp_str = is_array($fnpp) ? 'in(' . implode(', ', $fnpp) . ')' : '=' . $fnpp;
        $result = Yii::app()->db2->createCommand()
            ->select('aj.id')
            ->from('attendance_journal aj')
            ->where(array('AND',
                'aj.studentFnpp ' . $fnpp_str,
                'aj.scheduleId=' . $schid))
            ->queryScalar();

        return $result;
    }

    //является ли студент старостой (7)+
    public function steward($galId)
    {
        $result = Yii::app()->db2->createCommand()
            ->select('(CASE WHEN agg.stwpRec = gus.cpersons THEN 1 ELSE 0 END)')
            ->from('gal_u_student gus')
            ->join('attendance_galruz_group agg', 'agg.gal_nrec = gus.cstgr')
            ->where('gus.cpersons=:galid', array(':galid' => $galId))
            ->queryScalar();
        return $result;
    }

    //определяет дисциплины для статистике по дате (8)---
    public function statdis($date1, $date2, $galId)
    {
        $group = $this->getgroupid($galId);
        $result = Yii::app()->db2->createCommand()
            ->select('ats.studGroupId, ats.id, ats.discipline, ats.disciplineNrec, ats.kindOfWorkId, ats.teacherFio, ats.teacherFnpp, 
                    ak.name AS \'Kind\', ats.studGroupName, atw.name AS \'Type\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%d.%m.%Y\') AS \'dat\'')
            ->from('attendance_schedule ats')
            ->join('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
            ->join('attendance_typeofwork atw', 'atw.id = ats.typeOfWorkId')
            ->where(array('AND',
                'ats.studGroupId=' . ($group ?: 0),
                'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
            ))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();

        return $result;
    }

    //определяет дисциплины для статистике по дате (9)---
    public function statdisdata($date, $galId)
    {
        $group = $this->getgroupid($galId);
        $result = Yii::app()->db2->createCommand()
            ->select('ats.studGroupId, ats.id, ats.discipline, ats.teacherFio, ak.name AS \'Kind\', ats.studGroupName, atw.name AS \'Type\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\', DATE_FORMAT(ats.dateTimeStartOfClasses, \'%d.%m.%Y\') AS \'dat\'')
            ->from('attendance_schedule ats')
            ->join('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
            ->join('attendance_typeofwork atw', 'atw.id = ats.typeOfWorkId')
            ->where(array('AND',
                'ats.studGroupId = ' . ($group ?: 0),
                array('like', 'ats.dateTimeStartOfClasses', '%' . $date . '%'),))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();

        return $result;
    }

    //определяем имя студента  +
    public function myname($galId)
    {
        $result = Yii::app()->db2->createCommand()
            ->select('gus.fio')
            ->from('gal_u_student gus')
            ->where('gus.cpersons=:galid', array(':galid' => $galId))
            ->queryScalar();

        return $result;
    }

    //определяет fnpp студента  +
    public function myfnpp($galId)
    {
        $result = Yii::app()->db2->createCommand()
            ->select('s.fnpp')
            ->from('gal_u_student gus')
            ->join('skard s', 'gus.nrec = s.gal_srec')
            ->where('gus.cpersons=:galid', array(':galid' => $galId))
            ->queryScalar();

        return $result;
    }

    //определяем ссылку cperson на студента  +
    public function cpersons($fnpp)
    {
        $result = Yii::app()->db2->createCommand()
            ->select('gus.cpersons')
            ->from('gal_u_student gus')
            ->join('skard s', 'gus.nrec = s.gal_srec')
            ->where('s.fnpp=:fnpp', array(':fnpp' => $fnpp))
            ->queryScalar();

        return $result;
    }

    //определяем есть ли запись в журнале  +
    public function logjournal($fnpp, $schid)
    {
        $result = Yii::app()->db2->createCommand()
            ->select('count(*)')
            ->from('attendance_journal aj')
            ->where(array('AND',
                'aj.studentFnpp = ' . $fnpp,
                'aj.scheduleId = ' . $schid
            ))
            ->queryScalar();
        $result = (int)($result);
        //var_dump($result);die;
        return $result;
    }

    //добавляем в журнал строки  +
    public function insertjournal($fnpp, $cpersons, $schid)
    {
        Yii::app()->db2->createCommand()
            ->insert('attendance_journal', array(
                'studentFnpp' => $fnpp,
                'scheduleId' => $schid,
                'studentNrec' => $cpersons
            ));
    }

    //определяет есть ли расписание на текущем дне
    public function dateactive($date1, $date2, $galId)
    {
        $group = $this->getgroupid($galId);
        $result = Yii::app()->db2->createCommand()
            ->selectDistinct('CONVERT(ats.dateTimeStartOfClasses, date) as date')
            ->from('attendance_schedule ats')
            ->where(array('AND',
                'ats.studGroupId=' . ($group ?: 0),
                'ats.dateTimeStartOfClasses > \'' . $date1 . '\'',
                'ats.dateTimeStartOfClasses < \'' . $date2 . '\'',
            ))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();
        //var_dump($result);die;
        return $result;
    }
    /*
        public function actionPrintXls2year($id = 1820)
        {
            set_time_limit(0);
            $sql = Yii::app()->db2->createCommand('SELECT ats.id, ats.discipline, ats.teacherFio, ak.name AS \'Kind\',
          ats.studGroupName, atw.name AS \'Type\', ats.typeOfWorkId, ats.dateTimeStartOfClasses,
          DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\'
          FROM `attendance_schedule` `ats`
          JOIN `attendance_kindofwork` `ak` ON ak.id = ats.kindOfWorkId
          JOIN `attendance_typeofwork` `atw` ON atw.id = ats.typeOfWorkId
          WHERE (ats.studGroupId = '.$id.')
          AND `ats`.`dateTimeStartOfClasses` >= \'2018-09-01\'
          AND `ats`.`dateTimeStartOfClasses` <= \'2020-01-16\'
          ORDER BY `ats`.`dateTimeStartOfClasses` -- LIMIT 10
          ')
                ->queryAll();
            $list = Yii::app()->db2->createCommand()
                ->selectDistinct('s.fnpp, gus.cpersons, gus.fio')
                ->from('gal_u_student gus')
                ->join('attendance_galruz_group agg', 'agg.gal_nrec = gus.cstgr')
                ->join('skard s', 's.npp = (SELECT s1.npp FROM skard s1 INNER JOIN fdata f ON f.npp = s1.fnpp WHERE gus.nrec = s1.gal_srec ORDER BY f.clink DESC, f.npp LIMIT 1 )')
                ->where(array('AND',
                    'agg.id = '.$id,
                    'gus.warch = 0'))
                ->order('gus.fio')
                ->queryAll();
            //var_dump($sql);
            echo '<table border="1">';
            echo '<tr>';
            echo '<td></td>';
            foreach ($sql as $s){
                echo '<td>'.$s['discipline'].'<br/>'.$s['dateTimeStartOfClasses'].'</td>';
            }
            echo '</tr>';
            foreach ($list as $stud){
                echo '<tr>';
                echo '<td>'.$stud['fio'].'</td>';

                foreach ($sql as $s){
                    echo '<td style="'.(($this->markteach($stud['fnpp'], $s['id']) == 1)?'background-color: green':'').'">'.$this->markCatalog( $this->markteach($stud['fnpp'], $s['id']) ).'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }

        public function markCatalog($id) {
            if($id == ''){$result = 'нет данных';}else {
                $result = Yii::app()->db2->createCommand()
                    ->select('aj.nameShort')
                    ->from('attendance_catalog aj')
                    ->where('aj.id = ' . $id)
                    ->queryScalar();
            }

            return $result;
        }*/

}
