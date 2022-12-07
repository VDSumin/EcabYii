<?php

class ExtralistController extends Controller {

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index',
                    'downLoadWorkFile',
                    'list', 'listPrint', 'draftPrint', 'saveMark', 'kursTheme', 'saveKursTheme'
                ),
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

    /**
     * Список всех направлений
     * @param bool $year
     * @throws CException
     */
    public function actionIndex() {
        $fnpp = Yii::app()->user->getFnpp();
        $galId = bin2hex(Yii::app()->user->getGalId());

        $data = ApiKeyService::queryApi('getExtraListForStudent', array("nrec" => CMisc::_id($galId)), 'a2f187fc-9c37-4b77-a7e9-07e5860c6739','GET');
        ApiKeyService::checkResponseApi($data, 'getExtraListForStudent');
        $data = $data['json_data'];
//        var_dump($data);die;
        $active = [];
        $passive = [];

        foreach ($data as $key => $value){
            if($value['status'] == 1){
                $active[] = $value;
            }else{
                $passive[] = $value;
            }
        }

        $pager = new CPagination();
        $pageSize = 25;
        $pager->pageSize = $pageSize;
        $dataProvider = new CArrayDataProvider(
            $active, array('pagination' => $pager, 'keyField' => 'numDoc')
        );
        $pagerPas = new CPagination();
        $pagerPas->pageSize = $pageSize;
        $dataProviderPas = new CArrayDataProvider(
            $passive, array('pagination' => $pagerPas, 'keyField' => 'numDoc')
        );

        $this->render('index', [
            'dataProvider' => $dataProvider,
            'dataProviderPas' => $dataProviderPas,
        ]);
    }

}
