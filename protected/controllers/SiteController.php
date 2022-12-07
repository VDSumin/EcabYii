<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex($code = null, $type=null) {
        if (!empty($code)) {
            if($type>10){
                $identity = new RUserIdentity($code, 1);
            }else{
                $identity = new RUserIdentity($code, $type);
            }

            if ($identity->authenticate() && Yii::app()->user->login($identity)) {
                if (Yii::app()->user->checkAccess(WebUser::ROLE_ADMIN)) {
                    return Yii::app()->user->logout(); //Нельзя из вне зайти под админом
                }else{
                    if($type == '11') {
                        return $this->redirect(array('/zak/oborud'));
                    }elseif ($type == '12'){
                        return $this->redirect(array('/zak/expendable'));
                    }elseif ($type == '13'){
                        return $this->redirect(array('/zak/software'));
                    }elseif ($type == '14'){
                        return $this->redirect(array('/chiefs'));
                    }else{
                        return $this->redirect(array('/site/index'));
                    }
                }
//                } elseif (Yii::app()->user->checkAccess(WebUser::ROLE_STEWARD)) {
//                    return $this->redirect(array('/student/index'));
//                } elseif (Yii::app()->user->checkAccess(WebUser::ROLE_STUDENT)) {
//                    return $this->redirect(array('/student/index'));
//                } else {
//                    return $this->redirect(array('/site/page', 'view' => 'undefined'));
//                }
//            }elseif ($identity->authenticateWorker() && Yii::app()->user->login($identity)) {
//                    return $this->redirect(array('/site/index'));
            } else {
                if (RUserIdentity::ERROR_NEED_LOGOUT == $identity->errorCode) {
                    return $this->redirect(['/site/index', 'code' => $code, 'type' => $type]);
                }
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        }
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_news\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_news (
                                              id INT(11) NOT NULL AUTO_INCREMENT,
                                              title TINYTEXT NOT NULL,
                                              annonce TEXT NOT NULL,
                                              content TEXT NOT NULL,
                                              status TINYINT(4) NOT NULL,
                                              createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }


        $criteria = new CDbCriteria();
        if (!Yii::app()->user->isGuest and Yii::app()->user->getPerStatus()) { //препод
            $criteria->compare('status', array(News::STATUS_SHOW_ALL, News::STATUS_SHOW_PPS));
        } elseif (!Yii::app()->user->isGuest) { //студент
            $criteria->compare('status', array(News::STATUS_SHOW_ALL, News::STATUS_SHOW_STUDENTS));
        } else { //гость
            $criteria->compare('status', array(News::STATUS_SHOW_ALL));
        }


//        $criteria->limit = 5;
        $criteria->order = 'createdAt DESC';
        $newsProvider = new CActiveDataProvider('News', array('criteria' => $criteria, 'pagination' => false));

        $this->render('index', array('type' => $type, 'newsProvider' => $newsProvider));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {

        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the login page
     */
    /*public function actionLogin() {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }*/

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        if (!(defined('YII_DEBUG') && YII_DEBUG)) {
            $this->redirect('http://omgtu.ru/?logout=yes');
        } else {
            $this->redirect(Yii::app()->homeUrl);
        }
    }

    public function actionAjax() {
        echo Yii::app()->user->isGuest ? 'guest' : Yii::app()->user->getId();
    }

}
