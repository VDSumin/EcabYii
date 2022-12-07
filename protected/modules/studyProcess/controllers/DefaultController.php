<?php

class DefaultController extends Controller {

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index'),
                'users' => array('*'),
                'expression' => 'Controller::checkfnpp()',
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    protected function beforeAction($action) {
        if(Yii::app()->user->getFnpp() == null){
            $this->redirect(array('/site'));
        }
        return true;
    }

    public function actionIndex() {
        $this->redirect(array('/studyProcess/mark'));
        $this->render('index', []);
    }

}
