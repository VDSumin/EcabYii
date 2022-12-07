<?php

class ExpendableController extends Controller
{

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'create', 'update', 'delete'),
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

    public function actionIndex()
    {
        $structs = ZakModule::getMyFinStruct();
        $model = ItdepExpendableRequest::model()->findAllByAttributes(array('struct' => $structs), array('order' => "id DESC"));

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }

        $pager = new CPagination();
        $pager->pageSize = 30;
        $dataProvider = new CArrayDataProvider($filter->arrayFilter($model), array('pagination' => $pager, 'keyField' => 'id'));

        $this->render('index', [
            'model' => $dataProvider,
            'filter' => $filter,]);
    }

    /**
     * Создание новой записи
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new ItdepExpendableRequest;
        $model->creater = Yii::app()->user->model->attributes['lastName'] . " " . Yii::app()->user->model->attributes['firstName'];
        if(isset($_POST['ItdepExpendableRequest']))
        {
            $model->attributes=$_POST['ItdepExpendableRequest'];
            if($model->save())
                $this->redirect(array('index'));
        }

        $model->responsible = Yii::app()->user->model->attributes['lastName'] . " " . Yii::app()->user->model->attributes['firstName'];
//        $model->contacts = Yii::app()->user->model->attributes['email'];

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
        $model= ItdepExpendableRequest::model()->findByPk($id);

        if(isset($_POST['ItdepExpendableRequest']))
        {
            $model->attributes=$_POST['ItdepExpendableRequest'];
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
        ItdepExpendableRequest::model()->findByPk($id)->delete();

        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

//    public function actionCreateDevice()
//    {
//        $model = new ItdepExpendableDevice();
//
//        if(isset($_POST['ItdepExpendableDevice']))
//        {
//            $model->attributes=$_POST['ItdepExpendableDevice'];
//            if($model->save())
//                $this->redirect(array('index'));
//        }
//
//        $this->render('createdevice',array(
//            'model'=>$model,
//        ));
//    }
//
//    public function actionCreatefabricator()
//    {
//        $model = new ItdepExpendableFabrication();
//
//        if(isset($_POST['ItdepExpendableFabrication']))
//        {
//            $model->attributes=$_POST['ItdepExpendableFabrication'];
//            if($model->save())
//                $this->redirect(array('index'));
//        }
//
//        $this->render('createfabricator',array(
//            'model'=>$model,
//        ));
//    }
//
//    public function actionCreatetypeCart()
//    {
//        $model = new ItdepExpendableTypecart();
//
//        if(isset($_POST['ItdepExpendableTypecart']))
//        {
//            $model->attributes=$_POST['ItdepExpendableTypecart'];
//            if($model->save())
//                $this->redirect(array('index'));
//        }
//
//        $this->render('createtypeCart',array(
//            'model'=>$model,
//        ));
//    }

}
