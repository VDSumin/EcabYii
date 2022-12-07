<?php

class DefaultController extends Controller
{

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array(''),
                'users' => array('*'),
                'expression' => 'Controller::checkfnpp()',
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {

    }

    protected function beforeAction($action)
    {
        if (Yii::app()->user->getFnpp() == null) {
            $this->redirect(array('/site'));
        }
        if (Yii::app()->user->getPerStatus()) {
            $this->redirect(array('/inquiries/responsible'));
        } else {
            $this->redirect(array('/inquiries/student'));
        }
        return true;
    }
}
