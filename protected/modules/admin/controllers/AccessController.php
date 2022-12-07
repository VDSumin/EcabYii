<?php

class AccessController extends Controller
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
        if(Yii::app()->user->getFnpp() == null){
            $this->redirect(array('/site'));
        }
        if(!in_array(Yii::app()->user->getFnpp(), Controller::ADMIN_FNPP)){
            $this->redirect(array('/site'));
        }
        MonitorAccess::checkTables();
        return true;
    }

    /**
     * Список всех ключей
     */
    public function actionIndex()
    {
        $modelMonitor = new MonitorAccess('search');
        $modelMonitor->unsetAttributes();  // clear any default values
        if (isset($_GET['MonitorAccess'])) {
            $modelMonitor->attributes = $_GET['MonitorAccess'];
        }

        $this->render('index', ['model' => $modelMonitor]);
    }


    /**
     * Создание новой записи
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new MonitorAccess;

        if(isset($_POST['MonitorAccess']))
        {
            $model->attributes=$_POST['MonitorAccess'];
            $model->createdBy = Yii::app()->user->getFnpp();
            $model->createDate = date("Y-m-d H:i:s");
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
        $model= MonitorAccess::model()->findByPk($id);

        if(isset($_POST['MonitorAccess']))
        {
            $model->attributes=$_POST['MonitorAccess'];
            $model->createdBy = Yii::app()->user->getFnpp();
            $model->createDate = date("Y-m-d H:i:s");
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
        MonitorAccess::model()->findByPk($id)->delete();

        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

}
