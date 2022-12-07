<?php

class OborudauctionController extends Controller
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
        $model = new ItdepOborudAuction('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['ItdepOborudAuction'])) {
            $model->attributes = $_GET['ItdepOborudAuction'];
        }

        $this->render('index', ['model' => $model]);
    }

    /**
     * Создание новой записи
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new ItdepOborudAuction;

        $model->last_user = 70 ;//надо поправить
        $model->Last_date = date("Y-m-d H:i:s");

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

    /**
     * Изменение записи
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model= ItdepOborudAuction::model()->findByPk($id);

        $model->Last_date = date("Y-m-d H:i:s");

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

    /**
     * Удаление записи
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        ItdepOborudAuction::model()->findByPk($id)->delete();

        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

}