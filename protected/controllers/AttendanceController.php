<?php

class AttendanceController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
				'actions'=>array('index', 'view', 'Cview', 'statistics', 'Cstatistics', 'filling', 'getview', 'getfilling','getstatistics'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * -----------------------------------------------------------------------------------------------------------------
	 */
	public function actionIndex()
	{
        $galId = Yii::app()->user->getGalIdT();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if(Yii::app()->user->getPerStatus()){
            $pps = true;
        }else{
            return $this->redirect(['journal/index']);
        }
        if(!isset($_SESSION)){
            session_start();
        }
        $listgroup = $this->listgroup($galId);
        $listgroupcur = $this->curator($galId);
        $i=0;
        foreach ($listgroup as $groupId){
            $listgroup[$i]['groupName'] = $this->groupname($groupId['studGroupId']);
            $listgroup[$i]['department'] = $this->depname($groupId['studGroupId']);
            $i++;
        }
        $i=0;
        foreach ($listgroupcur as $groupId){
            $listgroupcur[$i]['groupName'] = $this->groupname($groupId['id']);
            $listgroupcur[$i]['department'] = $this->depname($groupId['id']);
            $i++;
        }
        asort($listgroup);
        asort($listgroupcur);
        $fnpp = $this->myfnpp($galId);
        //var_dump($listgroup,$listgroupcur,bin2hex($galId),$fnpp);die;
        $this->layout='//layouts/column1';
        $this->render('index',array(
            'stud' => $galId,
            'listgroup' => $listgroup,
            'listgroupcur' => $listgroupcur,
        ));
	}

    public function actionGetFilling($group = null, $day = null, $date = null)
    {
        if(!isset($_SESSION)){
            session_start();
        }
        if(isset($group)) {
            $_SESSION['group'] = $group;
        }
        if(isset($day)) {
            $_SESSION['vday'] = $day;
        }
        if(isset($date)) {
            $_SESSION['vdate'] = $date;
        }
        if($_SESSION['vday'] == 7){
            $date = date('Y-m-d');
            $day = date('w');
            $_SESSION['vdate'] = $date;
            $_SESSION['vday'] = $day;
        }
        $this->redirect(['attendance/filling']);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function actionFilling()
    {
        $galId = Yii::app()->user->getGalIdT();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if(Yii::app()->user->getPerStatus()){
            $pps = true;
        }else{
            return $this->redirect(['journal/index']);
        }
        if(!isset($_SESSION)){
            session_start();
        }
        $result = $this->_Date(); //запрос даты из окна
        if($result != ''){
            $_SESSION['vdate'] = $result;
            $_SESSION['vday'] = date('w', strtotime($result));
        }
        if(isset($_SESSION['vdate'])) {
            $date = $_SESSION['vdate'];
        }
        if(isset($_SESSION['vday'])) {
            $day = $_SESSION['vday'];
        }
        if(!isset($date)) {//настраеваем сегодняшнюю дату, если дата была не определенна
            $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $date = date('Y-m-d', $toDate);
            $day = date('w');
        }
        if($day == 0){
            $toDate = date('Y-m-d', strtotime($date . '-7 day'));
        }else {
            $toDate = date('Y-m-d', strtotime($date . '-' . $day . ' day'));
        }
        $i = 0;//определяем даты текущей недели
        while ($i < 7) {
            $i++;
            $dates[] = date('Y-m-d', strtotime($toDate.'+'.$i.' day'));
        }
        $date1 = date('Y-m-d', strtotime($toDate.'+1 day'));
        $date2 = date('Y-m-d', strtotime($toDate.'+7 day'));
        if(isset($_SESSION['group'])){
            $group = $_SESSION['group'];
        }else{
            $this->render('index');
        }
        $fnpp = $this->myfnpp($galId);
        $list = $this->getlist($group);
        $discipline = $this->getdiscipline($date, $group, $fnpp);
        $activedates = $this->dateactive($date1, $date2, $group, $fnpp);
        $result = $this->_Save();//в случае изменения сохраняем отметки
        //var_dump($fnpp,$list,$discipline,$group);die;
        //var_dump($toDate,$date1,$date2,$day);die;
        $this->layout='//layouts/column1';
        $this->render('filling',array(
            'stud' => $galId,
            'group' => $group,
            'day' => $day,
            'discipline' => $discipline,
            'list' => $list,
            'result' => $result,
            'date' => $date,
            'dates' => $dates,
            'activedates' => $activedates,
            'fnpp' => $fnpp,
        ));
    }

    public function actionGetView($group = null, $type = null, $day = null, $date = null)
    {
        if(!isset($_SESSION)){
            session_start();
        }
        if(isset($group)) {
            $_SESSION['group'] = $group;
        }
        if(isset($type)) {
            $_SESSION['type'] = $type;
        }
        if(isset($date)) {
            $_SESSION['date'] = $date;
        }
        if(isset($day)) {
            $_SESSION['day'] = $day;
        }
        if($type == 0) {
            $this->redirect(['attendance/view']);
        }elseif($type == 1){
            $this->redirect(['attendance/Cview']);
        }
    }

    public function actionView()
    {
        $galId = Yii::app()->user->getGalIdT();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if(Yii::app()->user->getPerStatus()){
            $pps = true;
        }else{
            return $this->redirect(['journal/view']);
        }
        if(!isset($_SESSION)){
            session_start();
        }
        $result = $this->_Date(); //запрос даты из окна
        if($result != ''){
            $_SESSION['date'] = $result;
            $_SESSION['day'] = date('w', strtotime($result));
        }
        if(isset($_SESSION['date'])) {
            $date = $_SESSION['date'];
        }else{
            $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $date = date('Y-m-d', $toDate);
        }
        if(isset($_SESSION['day'])) {
            $day = $_SESSION['day'];
        }else{
            if(isset($date)){
                $day = date('w', strtotime($date));
            }else{
                $day = date('w');
            }
        }
        $allday = 0;
        if($day == 7){
            $allday = 1;
        }
        //---------определяем дату
        if(!isset($date)){
            if (isset($_SESSION['date'])) {
                $date = $_SESSION['date'];
            } else {
                $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $date = date('Y-m-d', $toDate);
            }
        }else{
            $_SESSION['date'] = $date;
        }
        if(!isset($day)){
            if (isset($_SESSION['date'])) {
                $day = $_SESSION['day'];
            } else {
                $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $day = date('w', $toDate);
            }
        }else{
            $_SESSION['day'] = $day;
        }
        if(isset($date)){
            $day = date('w', strtotime($date));
        }
        if($day == 0){
            $toDate = date('Y-m-d', strtotime($date . '-7 day'));
        }else {
            $toDate = date('Y-m-d', strtotime($date . '-' . $day . ' day'));
        }
        $i = 0;//определяем даты текущей недели
        while ($i < 7) {//даты с понедельника по воскресения
            $i++;
            $dates[] = date('Y-m-d', strtotime($toDate.'+'.$i.' day'));
        }
        $date1 = date('Y-m-d', strtotime($toDate.'+1 day'));
        $date2 = date('Y-m-d', strtotime($toDate.'+7 day'));
        if(isset($_SESSION['group'])){
            $group = $_SESSION['group'];
        }else{
            $this->render('index');
        }
        if($allday){
            $day = 7;
        }
        $fnpp = $this->myfnpp($galId);
        if($day ==7) {//показывает дисциплины за неделю
            $discipline = $this->statdis($date1, $date2, $group, $fnpp);
        }else{//показывает дисциплины за определенную дату
            $discipline = $this->statdisdata($date, $group, $fnpp);
        }
        $activedates = $this->dateactive($date1, $date2, $group, $fnpp);
        $list = $this->getlist($group);
        //var_dump($toDate,$date1,$date2,$day);die;
        $this->layout='//layouts/column1';
        $this->render('view',array(
            'day'=>$day,
            'date' => $date,
            'dates' => $dates,
            'activedates' => $activedates,
            'group' => $group,
            'discipline' => $discipline,
            'list' => $list,
            'fnpp' => $fnpp,
        ));
    }

    public function actionCView()
    {
        $galId = Yii::app()->user->getGalIdT();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if(Yii::app()->user->getPerStatus()){
            $pps = true;
        }else{
            return $this->redirect(['journal/view']);
        }
        if(!isset($_SESSION)){
            session_start();
        }
        $result = $this->_Date(); //запрос даты из окна
        if($result != ''){
            $_SESSION['date'] = $result;
            $_SESSION['day'] = date('w', strtotime($result));
        }
        if(isset($_SESSION['date'])) {
            $date = $_SESSION['date'];
        }else{
            $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $date = date('Y-m-d', $toDate);
        }
        if(isset($_SESSION['day'])) {
            $day = $_SESSION['day'];
        }else{
            if(isset($date)){
                $day = date('w', strtotime($date));
            }else{
                $day = date('w');
            }
        }
        $allday = 0;
        if($day == 7){
            $allday = 1;
        }
        //---------определяем дату
        if(!isset($date)){
            if (isset($_SESSION['date'])) {
                $date = $_SESSION['date'];
            } else {
                $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $date = date('Y-m-d', $toDate);
            }
        }else{
            $_SESSION['date'] = $date;
        }
        if(!isset($day)){
            if (isset($_SESSION['date'])) {
                $day = $_SESSION['day'];
            } else {
                $toDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $day = date('w', $toDate);
            }
        }else{
            $_SESSION['day'] = $day;
        }
        if(isset($date)){
            $day = date('w', strtotime($date));
        }
        if($day == 0){
            $toDate = date('Y-m-d', strtotime($date . '-7 day'));
        }else {
            $toDate = date('Y-m-d', strtotime($date . '-' . $day . ' day'));
        }
        $i = 0;//определяем даты текущей недели
        while ($i < 7) {//даты с понедельника по воскресения
            $i++;
            $dates[] = date('Y-m-d', strtotime($toDate.'+'.$i.' day'));
        }
        $date1 = date('Y-m-d', strtotime($toDate.'+1 day'));
        $date2 = date('Y-m-d', strtotime($toDate.'+7 day'));
        if(isset($_SESSION['group'])){
            $group = $_SESSION['group'];
        }else{
            $this->render('index');
        }
        if($allday){
            $day = 7;
        }
        $fnpp = $this->myfnpp($galId);
        if($day ==7) {//показывает дисциплины за неделю
            $discipline = $this->Cstatdis($date1, $date2, $group);
        }else{//показывает дисциплины за определенную дату
            $discipline = $this->Cstatdisdata($date, $group);
        }
        $activedates = $this->dateactiveС($date1, $date2, $group);
        $list = $this->getlist($group);
        //var_dump($toDate,$date1,$date2,$day);die;
        $this->layout='//layouts/column1';
        $this->render('Cview',array(
            'day'=>$day,
            'date' => $date,
            'dates' => $dates,
            'activedates' => $activedates,
            'group' => $group,
            'discipline' => $discipline,
            'list' => $list,
            'fnpp' => $fnpp,
        ));
    }

    public function actionGetStatistics($group = null, $type = null, $stat = null)
    {
        if(!isset($_SESSION)){
            session_start();
        }
        if(isset($stat)) {
            $_SESSION['stat'] = $stat;
        }
        if(isset($group)) {
            $_SESSION['group'] = $group;
        }
        if(isset($type)) {
            $_SESSION['type'] = $type;
        }
        if($type == 0) {
            $this->redirect(['attendance/statistics']);
        }elseif($type == 1){
            $this->redirect(['attendance/Cstatistics']);
        }
    }

    public function actionStatistics()
    {
        $galId = Yii::app()->user->getGalIdT();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if(Yii::app()->user->getPerStatus()){
            $pps = true;
        }else{
            return $this->redirect(['journal/statistics']);
        }
        if(!isset($_SESSION)){
            session_start();
        }
        if(!isset($_SESSION['stat'])){
            $stat = 3;
            $_SESSION['stat'] = 3;
        }else{
            $stat = $_SESSION['stat'];
        }
        if(isset($_SESSION['group'])){
            $group = $_SESSION['group'];
        }else{
            $this->render('index');
        }
        if($stat == 3 and $this->_StatDate()){
            $date1 = date('Y-m-d 00:00:00', strtotime($_SESSION['DateFrom']));
            $date2 = date('Y-m-d 23:59:59', strtotime($_SESSION['DateTo']));
        }else{
            $date1 = date('2022-09-01 00:00:00');
            $date2 = date('2023-09-01 23:59:59');
        }
        if($stat == 1){
            unset($_SESSION['DateFrom']);
            unset($_SESSION['DateTo']);
            $date1 = date('2022-09-01 00:00:00');
            $date2 = date('Y-m-d 23:59:59');
        }
        if($stat == 2){
            unset($_SESSION['DateFrom']);
            unset($_SESSION['DateTo']);
            $date1 = date('2022-09-01 00:00:00');
            $date2 = date('2023-01-31 23:59:59');
        }
        $fnpp = $this->myfnpp($galId);
        $discipline = $this->statdis($date1, $date2, $group, $fnpp);
        $i=0;
        $data[1]['disciplineNrec'] = '';
        $data[1]['discipline'] = '';
        $data[1]['kindOfWorkId'] = '';
        $data[1]['Kind'] = '';
        $data[1]['teacherFio'] = '';
        $data[1]['teacherFnpp'] = '';
        $data[1]['Amount'] = 0;
        $data[1]['studGroupName'] = '';
        foreach($discipline as $dis){
            $test = 1;
            foreach ($data as $dat){
                if($dis['disciplineNrec'] == $dat['disciplineNrec']) {
                    if($dis['teacherFnpp'] == $dat['teacherFnpp']) {
                        if($dis['studGroupName'] == $dat['studGroupName']) {
                            if ($dis['kindOfWorkId'] == $dat['kindOfWorkId']) {
                                $test = 0;
                            } elseif ($dis['kindOfWorkId'] == '2' and $dis['studGroupName'] != $dat['studGroupName']) {
                                $test = 1;
                            }
                        }
                    }
                }
            }
            if($test){//если похожих записей не было найдено, то создаем новую запись
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
                    if(($data[$i]['disciplineNrec'] == $dis4et['disciplineNrec'])
                        and ($data[$i]['teacherFnpp'] == $dis4et['teacherFnpp'])
                        and ($data[$i]['kindOfWorkId'] == $dis4et['kindOfWorkId'])
                        and ($data[$i]['studGroupName'] == $dis4et['studGroupName'])) {
                        $data[$i]['Amount']++;
                    }
                }
            }else{continue;}
        }
        //var_dump($data);die;
        sort($data);
        $list = $this->getlist($group);
        $i=0;
        foreach ($list as $li) {
            $proc[0][$i] = 0;
            foreach ($data as $dat) {
                if ((($dat['kindOfWorkId'] == 2) and (substr($dat['studGroupName'], -2) == '/1')) or ($dat['kindOfWorkId'] != 2)) {
                    $proc[0][$i] += (2 * $dat['Amount']);
                }
            }
            $i++;
        }
        $i=0;
        foreach ($list as $li){
            $j=0;
            foreach ($data as $dat){
                $lists[$i][$j] = 0;
                $j++;
            }
            $i++;
        }
        $i=0;
        foreach ($list as $li){
            $proc[1][$i] = 0;
            foreach ($discipline as $dis){
                $test = 0;
                if( in_array($this->mark($li['fnpp'],$dis['id']), [1,6])){
                    $test = 1;
                }
                $j=0;
                foreach ($data as $dat){
                    if($dis['disciplineNrec'] == $dat['disciplineNrec']
                        and $dis['teacherFnpp'] == $dat['teacherFnpp']
                        and $dis['kindOfWorkId'] == $dat['kindOfWorkId']
                        and $dis['studGroupName'] == $dat['studGroupName']) {
                        if ($test) {
                            $lists[$i][$j] = $lists[$i][$j]+2;
                            $proc[1][$i] += 2;
                        }
                    }$j++;
                }
            }$i++;
        }
        $i=0;
        foreach ($list as $li){
            $j=0;
            foreach ($data as $dat){
                $liststeach[$i][$j] = 0;
                $j++;
            }$i++;
        }
        $i=0;
        foreach ($list as $li){
            $proc[2][$i] = 0;
            foreach ($discipline as $dis){
                $test = 0;
                if( in_array($this->markteach($li['fnpp'],$dis['id']), [1,6])){
                    $test = 1;
                }
                $j=0;
                foreach ($data as $dat){
                    if($dis['disciplineNrec'] == $dat['disciplineNrec']
                        and $dis['teacherFnpp'] == $dat['teacherFnpp']
                        and $dis['kindOfWorkId'] == $dat['kindOfWorkId']
                        and $dis['studGroupName'] == $dat['studGroupName']) {
                        if ($test) {
                            $liststeach[$i][$j] = $liststeach[$i][$j]+2;
                            $proc[2][$i] += 2;
                        }
                    }$j++;
                }
            }$i++;
        }
        $i=0;
        foreach ($list as $li) {
            if ($proc[1][$i] > $proc[0][$i]) {
                $proc[1][$i] = $proc[0][$i];
            }
            if ($proc[2][$i] > $proc[0][$i]) {
                $proc[2][$i] = $proc[0][$i];
            }
            $i++;
        }
        //var_dump($data, $proc);die;
        $this->layout='//layouts/column1';
        $this->render('statistics',array(
            'discipline' => $discipline,
            'stat' => $stat,
            'group' => $group,
            'data' => $data,
            'list' => $list,
            'lists' => $lists,
            'liststeach' => $liststeach,
            'proc' => $proc,
        ));
    }

    public function actionCStatistics()
    {
        $galId = Yii::app()->user->getGalIdT();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if(Yii::app()->user->getPerStatus()){
            $pps = true;
        }else{
            return $this->redirect(['journal/statistics']);
        }
        if(!isset($_SESSION)){
            session_start();
        }
        if(!isset($_SESSION['stat'])){
            $stat = 3;
            $_SESSION['stat'] = 3;
        }else{
            $stat = $_SESSION['stat'];
        }
        if(isset($_SESSION['group'])){
            $group = $_SESSION['group'];
        }else{
            $this->render('index');
        }
        if($stat == 3 and $this->_StatDate()){
            $date1 = date('Y-m-d 00:00:00', strtotime($_SESSION['DateFrom']));
            $date2 = date('Y-m-d 23:59:59', strtotime($_SESSION['DateTo']));
        }else{
            $date1 = date('2020-09-01 00:00:00');
            $date2 = date('2020-09-01 23:59:59');
        }
        if($stat == 1){
            unset($_SESSION['DateFrom']);
            unset($_SESSION['DateTo']);
            $date1 = date('2020-09-01 00:00:00');
            $date2 = date('Y-m-d 23:59:59');
        }
        if($stat == 2){
            unset($_SESSION['DateFrom']);
            unset($_SESSION['DateTo']);
            $date1 = date('2020-09-01 00:00:00');
            $date2 = date('2021-01-31 23:59:59');
        }
        $fnpp = $this->myfnpp($galId);
        $discipline = $this->Cstatdis($date1, $date2, $group);
        $i=0;
        $data[1]['disciplineNrec'] = '';
        $data[1]['discipline'] = '';
        $data[1]['kindOfWorkId'] = '';
        $data[1]['Kind'] = '';
        $data[1]['teacherFio'] = '';
        $data[1]['teacherFnpp'] = '';
        $data[1]['Amount'] = 0;
        foreach($discipline as $dis){
            $test = 1;
            foreach ($data as $dat){
                if($dis['disciplineNrec'] == $dat['disciplineNrec']) {
                    if($dis['teacherFnpp'] == $dat['teacherFnpp']) {
                        if($dis['kindOfWorkId'] == $dat['kindOfWorkId']) {
                            $test = 0;
                        }
                    }
                }
            }
            if($test){//если похожих записей не было найдено, то создаем новую запись
                $i++;
                $data[$i]['discipline'] = $dis['discipline'];
                $data[$i]['disciplineNrec'] = $dis['disciplineNrec'];
                $data[$i]['kindOfWorkId'] = $dis['kindOfWorkId'];
                $data[$i]['Kind'] = $dis['Kind'];
                $data[$i]['teacherFio'] = $dis['teacherFio'];
                $data[$i]['teacherFnpp'] = $dis['teacherFnpp'];
                $data[$i]['Amount'] = 0;
                foreach ($discipline as $dis4et) {
                    if(($data[$i]['disciplineNrec'] == $dis4et['disciplineNrec'])
                        and ($data[$i]['teacherFnpp'] == $dis4et['teacherFnpp'])
                        and ($data[$i]['kindOfWorkId'] == $dis4et['kindOfWorkId'])) {
                        $data[$i]['Amount']++;
                    }
                }
            }else{continue;}
        }
        //var_dump($data);die;
        sort($data);
        $list = $this->getlist($group);
        $i=0;
        foreach ($list as $li) {
            $proc[0][$i] = 0;
            foreach ($data as $dat) {
                if ((($dat['kindOfWorkId'] == 2) and (substr($dat['studGroupName'], -2) == '/1')) or ($dat['kindOfWorkId'] != 2)) {
                    $proc[0][$i] += (2 * $dat['Amount']);
                }
            }
            $i++;
        }
        $i=0;
        foreach ($list as $li){
            $j=0;
            foreach ($data as $dat){
                $lists[$i][$j] = 0;
                $j++;
            }
            $i++;
        }
        $i=0;
        foreach ($list as $li){
            $proc[1][$i] = 0;
            foreach ($discipline as $dis){
                $test = 0;
                if( in_array($this->mark($li['fnpp'],$dis['id']), [1,6])){
                    $test = 1;
                }
                $j=0;
                foreach ($data as $dat){
                    if($dis['disciplineNrec'] == $dat['disciplineNrec']
                        and $dis['teacherFnpp'] == $dat['teacherFnpp']
                        and $dis['kindOfWorkId'] == $dat['kindOfWorkId']) {
                        if ($test) {
                            $lists[$i][$j] = $lists[$i][$j]+2;
                            $proc[1][$i] += 2;
                        }
                    }
                    $j++;
                }
            }
            $i++;
        }
        $i=0;
        foreach ($list as $li){
            $j=0;
            foreach ($data as $dat){
                $liststeach[$i][$j] = 0;
                $j++;
            }
            $i++;
        }
        $i=0;
        foreach ($list as $li){
            $proc[2][$i] = 0;
            foreach ($discipline as $dis){
                $test = 0;
                if( in_array($this->markteach($li['fnpp'],$dis['id']), [1,6])){
                    $test = 1;
                }
                $j=0;
                foreach ($data as $dat){
                    if($dis['disciplineNrec'] == $dat['disciplineNrec']
                        and $dis['teacherFnpp'] == $dat['teacherFnpp']
                        and $dis['kindOfWorkId'] == $dat['kindOfWorkId']) {
                        if ($test) {
                            $liststeach[$i][$j] = $liststeach[$i][$j]+2;
                            $proc[2][$i] += 2;
                        }
                    }
                    $j++;
                }
            }
            $i++;
        }
        $i=0;
        foreach ($list as $li) {
            if ($proc[1][$i] > $proc[0][$i]) {
                $proc[1][$i] = $proc[0][$i];
            }
            if ($proc[2][$i] > $proc[0][$i]) {
                $proc[2][$i] = $proc[0][$i];
            }
            $i++;
        }
        //var_dump($data, $proc);die;
        $this->layout='//layouts/column1';
        $this->render('Cstatistics',array(
            'discipline' => $discipline,
            'stat' => $stat,
            'group' => $group,
            'data' => $data,
            'list' => $list,
            'lists' => $lists,
            'liststeach' => $liststeach,
            'proc' => $proc,
        ));
    }

    //------------------------------------------------------------------------------------------------------------------
    public function _Save()
    {
        $result = 0;
        if (Yii::app()->request->isPostRequest && isset($_POST['listname'])) {
            foreach ($_POST['listname'] as $id => $mark) {
                Yii::app()->db2->createCommand()->update('attendance_journal', array(
                    'teacherMarkId' => $mark,
                    'TeacherWasHere' => 1,
                ), 'id = :id', array(':id' => $id,));
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
            if($_POST['down'] == 'Send') {
                if (strtotime($_POST['publishDate'])) {
                    $result = $_POST['publishDate'];
                }
            }elseif ($_POST['down'] == 'Previous'){
                if (strtotime($_POST['publishDate'])) {
                    $result = date('Y-m-d', strtotime($_POST['publishDate'].'-7day'));
                }
            }elseif ($_POST['down'] == 'Next'){
                if (strtotime($_POST['publishDate'])) {
                    $result = date('Y-m-d', strtotime($_POST['publishDate'].'+7day'));
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
            if(!isset($_SESSION)){
                session_start();
            }
            if(strtotime($_POST['DateFrom'])) {
                $_SESSION['DateFrom'] = $_POST['DateFrom'];
            }
            if(strtotime($_POST['DateTo'])) {
                $_SESSION['DateTo'] = $_POST['DateTo'];
            }
            if(isset($_SESSION['DateFrom']) && isset($_SESSION['DateTo'])) {
                $result = true;
            }else{
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
		$model=Journal::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Journal $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='journal-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    //определяет группу студента (1)+
    public function getgroupname($group) {
        $result = Yii::app()->db2->createCommand()
            ->select('agg.name')
            ->from('attendance_galruz_group agg')
            ->where('agg.id=:id',array(':id' => $group))
            ->queryScalar();
        return $result;
    }

    //определяет группу студента id (1.1)+
    public function getgroupid($galId) {
        $result = Yii::app()->db2->createCommand()
            ->select('agg.id')
            ->from('gal_u_student gus')
            ->join('attendance_galruz_group agg', 'agg.gal_nrec = gus.cstgr')
            ->where('gus.cpersons = :galid',array(':galid' => $galId))
            ->queryScalar();
        //var_dump($result,$galId);die;
        return $result;
    }

    //определяет дисциплины для студета по дате (2)-
    public function getdiscipline($date, $group,$fnpp) {
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
                'ats.studGroupId = '.$group,
                'ats.teacherFnpp = '.$fnpp,
                array('like', 'ats.dateTimeStartOfClasses', '%' . $date . '%')))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();
            //var_dump($group, $result);die;
        return $result;
    }

    //определяет список студентов по группе(3)+
    public function getlist($group) {
        $result = Yii::app()->db2->createCommand()
            ->selectDistinct('s.fnpp, gus.cpersons, gus.fio')
            ->from('gal_u_student gus')
            ->join('attendance_galruz_group agg', 'agg.gal_nrec = gus.cstgr')
            ->join('skard s', 's.npp = (SELECT s1.npp FROM skard s1 INNER JOIN fdata f ON f.npp = s1.fnpp WHERE gus.nrec = s1.gal_srec ORDER BY f.clink DESC, f.npp LIMIT 1 )')
            ->where(array('AND',
                'agg.id = '.$group,
                'gus.warch = 0'))
            ->order('gus.fio')
            ->queryAll();

        return $result;
    }

//    //определяет список своих студентов для старосты (3.1)+
//    public function getalonelist($galId) {
//        $result = Yii::app()->db2->createCommand()
//            ->select('s.fnpp, gus.cpersons, gus.fio')
//            ->from('gal_u_student gus')
//            ->join('skard s', 'gus.nrec = s.gal_srec')
//            ->where('gus.cpersons=:galid',array(':galid' => $galId))
//            ->queryAll();
//
//        return $result;
//    }

    //получаем из журналу оценку (4)+
    public function mark($fnpp,$schid) {
        $result = Yii::app()->db2->createCommand()
            ->select('aj.stwpMarkId')
            ->from('attendance_journal aj')
            ->where(array('AND',
                'aj.studentFnpp='.$fnpp,
                'aj.scheduleId='.$schid))
            ->queryScalar();

        return $result;
    }

    //получаем из журналу оценку (5)+
    public function markteach($fnpp,$schid, $toFill = false) {
        if($toFill){
            //
        }else{
            //стандартный исход
            $result = Yii::app()->db2->createCommand()
                ->select('aj.teacherMarkId')
                ->from('attendance_journal aj')
                ->where(array('AND',
                    'aj.studentFnpp='.$fnpp,
                    'aj.scheduleId='.$schid))
                ->queryScalar();
            return $result;
        }

    }

    //получаем из журналу оценку (6)+
    public function markid($fnpp,$schid) {
        $result = Yii::app()->db2->createCommand()
            ->select('aj.id')
            ->from('attendance_journal aj')
            ->where(array('AND',
                'aj.studentFnpp='.$fnpp,
                'aj.scheduleId='.$schid))
            ->queryScalar();

        return $result;
    }

//    //является ли студент старостой (7)+
//    public function steward($galId) {
//        $result = Yii::app()->db2->createCommand()
//            ->select('(CASE WHEN agg.stwpRec = gus.cpersons THEN 1 ELSE 0 END)')
//            ->from('gal_u_student gus')
//            ->join('attendance_galruz_group agg', 'agg.gal_nrec = gus.cstgr')
//            ->where('gus.cpersons=:galid',array(':galid' => $galId))
//            ->queryScalar();
//        return $result;
//    }

    //определяет дисциплины для статистике по дате (8)---
    public function statdis($date1, $date2, $group, $fnpp) {
        $result = Yii::app()->db2->createCommand()
            ->select('ats.studGroupId, ats.id, ats.discipline, ats.disciplineNrec, ats.kindOfWorkId, ats.teacherFio, ats.teacherFnpp, 
                    ak.name AS \'Kind\', ats.studGroupName, atw.name AS \'Type\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%d.%m.%Y\') AS \'dat\'')
            ->from('attendance_schedule ats')
            ->join('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
            ->join('attendance_typeofwork atw', 'atw.id = ats.typeOfWorkId')
            ->where(array('AND',
                'ats.studGroupId='.$group,
                'ats.teacherFnpp = '.$fnpp,
                'ats.dateTimeStartOfClasses > \''.$date1.'\'',
                'ats.dateTimeStartOfClasses < \''.$date2.'\'',
            ))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();

        return $result;
    }

    //определяет дисциплины для статистике по дате (8.1)---
    public function Cstatdis($date1, $date2, $group) {
        $result = Yii::app()->db2->createCommand()
            ->select('ats.studGroupId, ats.id, ats.discipline, ats.disciplineNrec, ats.kindOfWorkId, ats.teacherFio, ats.teacherFnpp, 
                    ak.name AS \'Kind\', ats.studGroupName, atw.name AS \'Type\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%d.%m.%Y\') AS \'dat\'')
            ->from('attendance_schedule ats')
            ->join('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
            ->join('attendance_typeofwork atw', 'atw.id = ats.typeOfWorkId')
            ->where(array('AND',
                'ats.studGroupId='.$group,
                'ats.dateTimeStartOfClasses > \''.$date1.'\'',
                'ats.dateTimeStartOfClasses < \''.$date2.'\'',
            ))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();

        return $result;
    }

    //определяет дисциплины для статистике по дате (9)---
    public function statdisdata($date, $group, $fnpp) {
        $result = Yii::app()->db2->createCommand()
            ->select('ats.studGroupId, ats.id, ats.discipline, ats.teacherFio, ak.name AS \'Kind\', ats.studGroupName, atw.name AS \'Type\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\', DATE_FORMAT(ats.dateTimeStartOfClasses, \'%d.%m.%Y\') AS \'dat\'')
            ->from('attendance_schedule ats')
            ->join('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
            ->join('attendance_typeofwork atw', 'atw.id = ats.typeOfWorkId')
            ->where(array('AND',
                'ats.studGroupId = '.$group,
                'ats.teacherFnpp = '.$fnpp,
                array('like', 'ats.dateTimeStartOfClasses', '%' . $date . '%'),))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();

        return $result;
    }

    //определяет дисциплины для статистике по дате (9.1)---
    public function Cstatdisdata($date, $group) {
        $result = Yii::app()->db2->createCommand()
            ->select('ats.studGroupId, ats.id, ats.discipline, ats.teacherFio, ak.name AS \'Kind\', ats.studGroupName, atw.name AS \'Type\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\', DATE_FORMAT(ats.dateTimeStartOfClasses, \'%d.%m.%Y\') AS \'dat\'')
            ->from('attendance_schedule ats')
            ->join('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
            ->join('attendance_typeofwork atw', 'atw.id = ats.typeOfWorkId')
            ->where(array('AND',
                'ats.studGroupId = '.$group,
                array('like', 'ats.dateTimeStartOfClasses', '%' . $date . '%'),))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();

        return $result;
    }

    //определяем имя студента  +
    public function myname($galId) {
        $result = Yii::app()->db2->createCommand()
            ->select('gus.fio')
            ->from('gal_u_student gus')
            ->where('gus.cpersons=:galid',array(':galid' => $galId))
            ->queryScalar();

        return $result;
    }

    //определяет fnpp студента  +
    public function myfnpp($galId) {
        $result = Yii::app()->db2->createCommand()
            ->select('k.fnpp')
            ->from('keylinks k')
            ->join('fdata f','k.fnpp = f.npp')
            ->where('k.gal_unid=:galid',array(':galid' => $galId))
            ->order('f.clink DESC')
            ->queryScalar();

        return $result;
    }

    //определяем ссылку cperson на студента  +
    public function cpersons($fnpp) {
        $result = Yii::app()->db2->createCommand()
            ->select('k.gal_unid')
            ->from('keylinks k')
            ->where('k.fnpp=:fnpp',array(':fnpp' => $fnpp))
            ->queryScalar();

        return $result;
    }

    //определяем есть ли запись в журнале  +
    public function logjournal($fnpp, $schid) {
        $result = Yii::app()->db2->createCommand()
            ->select('count(*)')
            ->from('attendance_journal aj')
            ->where(array('AND',
                'aj.studentFnpp = ' .$fnpp,
                'aj.scheduleId = ' .$schid
                ))
            ->queryScalar();
        $result = (int)($result);
        //var_dump($result);die;
        return $result;
    }

    //добавляем в журнал строки  +
    public function insertjournal($fnpp, $cpersons, $schid) {
        Yii::app()->db2->createCommand()
                ->insert('attendance_journal',array(
                    'studentFnpp' => $fnpp,
                    'scheduleId' => $schid,
                    'studentNrec' => $cpersons
                ));
    }

    //получаем список групп для преподавателя---------------------------------------------------------------------------
    public function listgroup($galId) {
        if($galId){
            $fnpp = Yii::app()->db2->createCommand()
                ->select('k.fnpp')
                ->from('keylinks k')
                ->join('fdata f','k.fnpp=f.npp')
                ->where('k.gal_unid = :galid',array(':galid' => $galId))
                ->order('f.clink DESC')
                ->queryScalar();
        }else {
            $fnpp = Yii::app()->user->getFnpp();
        }

        $result = Yii::app()->db2->createCommand()
            ->selectDistinct('ats.studGroupId')
            ->from('attendance_schedule ats')
            ->join('attendance_galruz_group agg', 'ats.studGroupId = agg.id')
            ->where('ats.teacherFnpp = :fnpp',array(':fnpp' => $fnpp))
            ->andWhere(array('in','agg.wformed',array(0,2)))
            ->andWhere('ats.dateTimeStartOfClasses > \''.date('2022-01-01').'\'') /* начало семестра */
            ->queryAll();
        //var_dump($galId,$fnpp,$result);die;
        return $result;
    }
    //получаем имя группы по id-----------------------------------------------------------------------------------------
    public function groupname($groupId) {
        $result = Yii::app()->db2->createCommand()
            ->select('agg.name')
            ->from('attendance_galruz_group agg')
            ->where('agg.id ='.$groupId)
            ->queryScalar();
        //var_dump($result);die;
        return $result;
    }
    //получаем название кафедры по id-----------------------------------------------------------------------------------
    public function depname($groupId) {
        $result = Yii::app()->db2->createCommand()
            ->select('gc.name')
            ->from('attendance_galruz_group agg')
            ->join('gal_catalogs gc', 'gc.nrec = agg.cfaculty')
            ->where('agg.id ='.$groupId)
            ->queryScalar();
        //var_dump($result);die;
        return $result;
    }

    //Список групп куратора
    public function curator($galId) {
        $fnpp = Yii::app()->db2->createCommand()
            ->select('k.fnpp')
            ->from('keylinks k')
            ->join('fdata f','k.fnpp=f.npp')
            ->where('k.gal_unid = :galid',array(':galid' => $galId))
            ->order('f.clink DESC')
            ->queryScalar();
        $galunids = Yii::app()->db2->createCommand()
            ->selectDistinct ('HEX(k.gal_unid) as unid')
            ->from('keylinks k')
            ->where('(k.gal_unid IS NOT NULL) and k.fnpp = :fnpp',array(':fnpp' => $fnpp))
            ->queryAll();
        $arrayunid = [];
        foreach ($galunids as $unid){
            if($unid['unid'] != '') {
                $arrayunid[] = '0x'.$unid['unid'];
            }
        }
        //var_dump($fnpp);die;
        $result = Yii::app()->db2->createCommand()
            ->selectDistinct('agg.id')
            ->from('attendance_galruz_group agg')
            ->where('agg.curpRec in (' . implode(",",$arrayunid).')')
            ->andWhere(array('in','agg.wformed',array(0,2)))
            ->queryAll();
        //var_dump($result);die;
        return $result;
    }

    //определяет есть ли расписание на текущем дне
    public function dateactive($date1, $date2,  $group, $fnpp) {
        $result = Yii::app()->db2->createCommand()
            ->selectDistinct('CONVERT(ats.dateTimeStartOfClasses, date) as date')
            ->from('attendance_schedule ats')
            ->where(array('AND',
                'ats.studGroupId = '.$group,
                'ats.teacherFnpp = '.$fnpp,
                'ats.dateTimeStartOfClasses > \''.$date1.'\'',
                'ats.dateTimeStartOfClasses < \''.$date2.'\'',
            ))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();
        //var_dump($result);die;
        return $result;
    }

    //определяет есть ли расписание на текущем дне куратор
    public function dateactiveС($date1, $date2,  $group) {
        $result = Yii::app()->db2->createCommand()
            ->selectDistinct('CONVERT(ats.dateTimeStartOfClasses, date) as date')
            ->from('attendance_schedule ats')
            ->where(array('AND',
                'ats.studGroupId = '.$group,
                'ats.dateTimeStartOfClasses > \''.$date1.'\'',
                'ats.dateTimeStartOfClasses < \''.$date2.'\'',
            ))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();
        //var_dump($result);die;
        return $result;
    }
}
