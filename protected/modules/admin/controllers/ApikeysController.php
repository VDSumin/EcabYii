<?php

class apikeysController extends Controller
{

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index','create','update','delete', 'import', 'Export', 'askApikey'),
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
        AdminModule::checkExistTable();
        return true;
    }

    /**
     * Список всех ключей
     */
    public function actionIndex()
    {
        $model = new ApiKeys('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['ApiKeys'])) {
            $model->attributes = $_GET['ApiKeys'];
        }

        $this->render('index', ['model' => $model]);
    }

    /**
     * Создание новой записи
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new ApiKeys;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ApiKeys']))
        {
            $model->attributes=$_POST['ApiKeys'];
            if($model->fio != '') {
                $fio = explode(" ", $model->fio, 3);
                $fdata = Fdata::model()->findByAttributes(array('fam' => $fio[0], 'nam' => $fio[1], 'otc' => (isset($fio[2]) ? $fio[2] : '')));
                $model->fnpp = trim(($fdata instanceof Fdata) ? $fdata->npp : '');
            }

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
        $model= ApiKeys::model()->findByPk($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ApiKeys']))
        {
            $model->attributes=$_POST['ApiKeys'];
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
        ApiKeys::model()->findByPk($id)->delete();

        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

    /*public function actionTruncate()
    {
        Yii::app()->db->createCommand('TRUNCATE '.ApiKeys::model()->tableName())->query();

        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }*/

    public function actionImport()
    {
        $importList = [];
        if(isset($_POST['importText'])){
            $array_str = explode("\n", $_POST['importText']);
            foreach ($array_str as $row){
                if(trim($row) != ''){
                    $data_str = explode("\t", $row);
                    $fio = explode(" ", $data_str[0],3);
                    $fdata = Fdata::model()->findByAttributes(array('fam'=> $fio[0], 'nam' => $fio[1], 'otc' => (isset($fio[2])?$fio[2]:''),));
                    $model = ApiKeys::model()->findByAttributes(array('glogin' => trim($data_str[1])));
                    if($model instanceof ApiKeys){
                        $model->fio = trim($data_str[0]);
                        $model->apikey = trim($data_str[2]);
                        $importList[$data_str[0]] = ['success' => $model->save(), 'type' => 1];
                    }else{
                        $model = ApiKeys::model()->findByAttributes(array('fio' => trim($data_str[0])));
                        if($model instanceof ApiKeys){
                            $model->apikey = trim($data_str[2]);
                            $importList[$data_str[0]] = ['success' => $model->save(), 'type' => 2];
                        }else{
                            $model = new ApiKeys;
                            $model->fnpp = trim(($fdata instanceof Fdata)?$fdata->npp:'');
                            $model->fio = trim($data_str[0]);
                            $model->glogin = trim($data_str[1]);
                            $model->apikey = trim($data_str[2]);
                            $importList[$data_str[0]] = ['success' => $model->save(), 'type' => 3];
                        }
                    }
                }
            }
//            foreach ($importList as $key => $row){
//                echo $key. " " .$row['success'].' '.$row['type'].'<br />';
//            }
//            die;
        }

        $this->render('import',array(
            'result'=>$importList,
        ));
//        $this->redirect(array('index'));
    }

    public function actionExport()
    {
        $models = ApiKeys::model()->findAll();
        $return = '';
        $return .= '<div class="jumbotron" style="height: 500px;">';
        $return .= '<center><b>Экспорт данных</b></center><br/>';
        $return .= '<div style="overflow-y: scroll; height: 80%;">';
        $return .= '<table border="1" id="importdata">';
        foreach ($models as $model){
            $return .= '<tr>';
            $return .= '<td>'.$model->fio.'</td>';
            $return .= '<td>'.$model->glogin.'</td>';
            $return .= '<td>'.$model->apikey.'</td>';
            $return .= '</tr>';
        }
        $return .= '</table>';
        $return .= '</div>';
        $return .= '<br /><button class="btn btn-primary" onclick="CopyData();">Копировать в буфер</button>';
        $return .= '</div>';
        echo CJSON::encode(array('success' => $return));
    }

    public function actionAskApikey()
    {
        if(isset($_POST['login'])) {
            $return = true;
            $login = $_POST['login'];
            $data = ApiKeyService::queryApi('createUser', array("login" => $login), '7aeab129-8295-44c4-b770-02ab78334557', 'PUT2');
            $data = $data['json_data'];
            if(is_array($data)){
                $return = false;
                $text = '';
            }else{
                $return = true;
                $text = $data;
            }
            echo CJSON::encode(array('success' => $return, 'text' => $text));
        }
    }

}
