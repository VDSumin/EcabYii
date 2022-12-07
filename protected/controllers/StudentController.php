<?php

/**
 * Description of StudentController
 *
 * @author user
 */
class StudentController extends Controller {

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('nolink'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('index', 'tolerance','choiseBook'),
                'users' => array('@'),
                //'roles' => array(WebUser::ROLE_STUDENT),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Выбор зачетных книжек из списка
     * @return mixed
     */
    public function actionchoiseBook() {
        $info = Yii::app()->user->getStudentCards(1);
        if (null === $info) {
            return $this->redirect(['student/nolink']);
        }
        return $this->render('choiseBook', array('infos' => $info));
    }

    /**
     * Зачетная книжка
     * @param null $id
     * @return mixed
     */
    public function actionIndex($id=null) {
        $galIdMassive = Yii::app()->user->getStudentCards(1);//Получаем все карточки студента
        if(empty($id)) {
            $galId = Yii::app()->user->getStudentCards();//Получаем только текущие карточки студента
            if (count($galId)) {//Если их больше 0, берем первую попавшуюся
                $galId = $galId[0]['nrec'];
            } else {
                $galId = Yii::app()->user->getStudentCards(1)[0]['nrec'];
            }
        }else{
            $galId = CMisc::_id(bin2hex($id));
        }
        foreach ($galIdMassive as $item) {
            if( mb_strtolower($item['nrec']) == mb_strtolower($galId)){
                $info = $item;
                break;
            }
        }

        $marks = $this->listMarks($info['nrecint64']);
        if (empty($galId) && $galId == '') {
            return $this->redirect(['student/nolink']);
        }

        if (empty($info) || empty($marks)) {
            return $this->redirect(['student/nolink']);
        }

        return $this->render('bookVirtual', array('info' => $info, 'marks' => $marks));
    }

    public function actionTolerance() {
        $galId = Yii::app()->user->getGalId();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }

        $info = Yii::app()->db2->createCommand()
            ->select('tuc.yeared, tuc.dateapp % 256 day, tuc.dateapp /256 % 256 month, tut.wsemester, tut.wresultes')
            ->from(uStudent::model()->tableName() . ' tus')
            ->leftJoin(Person::model()->tableName() . ' tp', 'tp.nrec=tus.cpersons')
            ->leftJoin('gal_u_tolerancesession tut', 'tut.cstudent = tp.nrec')
            ->leftJoin(uCurriculum::model()->tableName() . ' tuc', 'tuc.nrec = tut.cplan')
            ->leftJoin(Catalog::model()->tableName() . ' tc', 'tc.nrec=tuc.cspeciality')
            ->where('tp.nrec=:id', [':id' => $galId])
            ->group('tut.wsemester')
            ->queryAll();
        //var_dump($info);die;
        return $this->render('tolerance', array('info' => $info));
    }

    public function actionNolink() {
        $this->render('nolink');
    }

    public function listMarks($id) {
        if(!isset(Yii::app()->session['ApiKey'])) {
            Yii::app()->session['ApiKey'] = 'c80d479b-4468-4065-a0a5-75e254b22a64';//mobile
        }
        $data = ApiKeyService::queryApi('getRecordBook', array("nrec" => $id), Yii::app()->session['ApiKey'], 'GET');
        if($data['code'] == 0){
            return [];
        }
        return $data['json_data'];
    }


}
