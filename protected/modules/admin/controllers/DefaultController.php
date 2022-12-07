<?php

class DefaultController extends Controller
{

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
        if(!in_array(Yii::app()->user->getFnpp(), Controller::ADMIN_FNPP)){
            $this->redirect(array('/site'));
        }
        return true;
    }

    public function actionIndex()
    {
        /*надо будет придумать основную страницу*/
        $this->redirect(array('/admin/apikeys'));
    }
}
