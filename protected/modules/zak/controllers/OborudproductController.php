<?php

class OborudproductController extends Controller
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
        $model = new ItdepOborudProduct('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['ItdepOborudProduct'])) {
            $model->attributes = $_GET['ItdepOborudProduct'];
        }

        $this->render('index', ['model' => $model]);
    }

    /**
     * Создание новой записи
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new ItdepOborudProduct;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ItdepOborudProduct']))
        {
            $model->attributes=$_POST['ItdepOborudProduct'];
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
        $model= ItdepOborudProduct::model()->findByPk($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ItdepOborudProduct']))
        {
            $model->attributes=$_POST['ItdepOborudProduct'];
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
        ItdepOborudProduct::model()->findByPk($id)->delete();

        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

}