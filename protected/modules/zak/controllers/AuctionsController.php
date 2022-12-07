<?php

class AuctionsController extends Controller
{
    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index','create','update','delete'),
                'users' => array('*'),
                'expression' => 'Controller::checkfnpp()',
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    protected function beforeAction($action) {
        return true;
    }

    /**
     * Список всех ключей
     */
    public function actionIndex()
    {
        $modelO = new ItdepOborudAuction('search');
        $modelE = new ItdepExpendableAuction('search');
        $modelS = new ItdepSoftwareAuction('search');

        $modelO->unsetAttributes();  // clear any default values
        $modelE->unsetAttributes();  // clear any default values
        $modelS->unsetAttributes();  // clear any default values
        if(isset($_GET['ItdepOborudAuction'])) {
            $modelO->attributes = $_GET['ItdepOborudAuction'];
        }
        if(isset($_GET['ItdepExpendableAuction'])) {
            $modelE->attributes = $_GET['ItdepExpendableAuction'];
        }
        if(isset($_GET['ItdepSoftwareAuction'])) {
            $modelS->attributes = $_GET['ItdepSoftwareAuction'];
        }

        $this->render('index', ['modelO' => $modelO, 'modelE' => $modelE, 'modelS' => $modelS]);
    }


    public function actionCreateO()
    {
        $model = new ItdepOborudAuction;

        $model->last_user = Yii::app()->user->getFnpp();
        $model->last_date = date("Y-m-d H:i:s");

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ItdepOborudAuction']))
        {
            $model->attributes=$_POST['ItdepOborudAuction'];
            if($model->save())
            $this->redirect(array('index'));
        }

        $this->render('create',array(
            'model'=>$model,
        ));
    }

    public function actionUpdateO($id)
    {
        $model= ItdepOborudAuction::model()->findByPk($id);

        $model->last_date = date("Y-m-d H:i:s");

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ItdepOborudAuction']))
        {
            $model->attributes=$_POST['ItdepOborudAuction'];
            if($model->save())
            $this->redirect(array('index'));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }


    public function actionCreateE()
    {
        $model = new ItdepExpendableAuction;

        $model->last_user = Yii::app()->user->getFnpp();
        $model->last_date = date("Y-m-d H:i:s");

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ItdepExpendableAuction']))
        {
            $_POST['ItdepExpendableAuction']['date'] = $_POST['ItdepOborudAuction']['date'];
            $model->attributes=$_POST['ItdepExpendableAuction'];
            if($model->save())
                $this->redirect(array('index'));
        }

        $this->render('create',array(
            'model'=>$model,
        ));
    }

    public function actionUpdateE($id)
    {
        $model= ItdepExpendableAuction::model()->findByPk($id);

        $model->last_date = date("Y-m-d H:i:s");

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ItdepExpendableAuction']))
        {
            $_POST['ItdepExpendableAuction']['date'] = $_POST['ItdepOborudAuction']['date'];
            $model->attributes=$_POST['ItdepExpendableAuction'];
            if($model->save())
                $this->redirect(array('index'));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }


    public function actionCreateS()
    {
        $model = new ItdepSoftwareAuction;

        $model->last_user = Yii::app()->user->getFnpp();
        $model->last_date = date("Y-m-d H:i:s");

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ItdepSoftwareAuction']))
        {
            $_POST['ItdepSoftwareAuction']['date'] = $_POST['ItdepOborudAuction']['date'];
            $model->attributes=$_POST['ItdepSoftwareAuction'];
            if($model->save())
                $this->redirect(array('index'));
        }

        $this->render('create',array(
            'model'=>$model,
        ));
    }

    public function actionUpdateS($id)
    {
        $model= ItdepSoftwareAuction::model()->findByPk($id);

        $model->last_date = date("Y-m-d H:i:s");

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ItdepSoftwareAuction']))
        {
            $_POST['ItdepSoftwareAuction']['date'] = $_POST['ItdepOborudAuction']['date'];
            $model->attributes=$_POST['ItdepSoftwareAuction'];
            if($model->save())
                $this->redirect(array('index'));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

}