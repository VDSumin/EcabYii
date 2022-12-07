<?php

class DeanController extends Controller {

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index',
                    'listGroupTask'),
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
        if(DeanClass::getMyStruct(false) == null){
            $this->redirect(array('/remote'));
        }
        return true;
    }


    public function actionIndex() {
        $dean = DeanClass::getMyStruct(false);
        if($dean != [0]){
            $list = DeanClass::getFacultyGroup($dean);
        }else{
            $list = DeanClass::getFacultyGroupFromNrec($dean);
        }
//        var_dump($dean, $list);die;

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }

        $pager = new CPagination();
        $pageSize = 15;
        $pager->pageSize = $pageSize;
        $dataProvider = new CArrayDataProvider(
            $filter->arrayFilter($list),
            array('pagination' => $pager, 'keyField' => 'id')
        );

        $this->render('index', ['dataProvider' => $dataProvider,
            'filter' => $filter,
            'dean' => $dean]);
    }

    public function actionListGroupTask($id) {

        $group = AttendanceGalruzGroup::model()->findByPk($id);
        $list = RemoteTaskList::model()->findAllByAttributes(array('group' => $id), array('order' => 'create_date DESC'));
//        var_dump($list);die;
        $listGroup = [];
        foreach ($list as $row){
            $listGroup[] = [
                'id' => $row['id'],
                'discipline' => uDiscipline::model()->findByPk($row["discipline"])->name,
                'comment' => $row['comment'],
                'teacher' => Fdata::model()->findByPk($row["author_fnpp"])->getFIO(),
                'create_date' => $row['create_date']
                ];
        }

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }

        $pager = new CPagination();
        $pageSize = 15;
        $pager->pageSize = $pageSize;
        $dataProvider = new CArrayDataProvider(
            $filter->arrayFilter($listGroup),
            array('pagination' => $pager, 'keyField' => 'id')
        );

        $this->render('listGroupTask', ['dataProvider' => $dataProvider,
            'filter' => $filter,
            'group' => $group]);
    }

}
