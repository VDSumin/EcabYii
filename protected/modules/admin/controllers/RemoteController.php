<?php

class RemoteController extends Controller
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
                'users' => array('*'),
            ),
        );
    }

    /**
     * Если пользователь не вошел в систему, перенаправить на страницу входа. Если пользователь вошел в систему, но не
     * является администратором, перенаправление на страницу сайта. Если пользователь вошел в систему и является
     * администратором, продолжите выполнение запрошенного действия.
     *
     * @param action Имя действия, которое необходимо выполнить.
     *
     * @return Ничего.
     */
    protected function beforeAction($action)
    {
        if (Yii::app()->user->getFnpp() == null) {
            $this->redirect(array('/site'));
        }
        if (!in_array(Yii::app()->user->getFnpp(), Controller::ADMIN_FNPP)) {
            $this->redirect(array('/site'));
        }
        return true;
    }

    public function actionIndex()
    {
        $res = Yii::app()->db2->createCommand()
            ->selectDistinct('f.npp as fnpp,
                concat_ws(" ", fam, nam, otc) as fio,
                date_format(rogd, "%d.%m.%Y") as bd,
                gruppa,
                past.dc')
            ->from('skard s')
            ->join('fdata f', 'f.npp = s.fnpp')
            ->leftJoin('studentsforpasttask past', 'past.fnpp = f.npp')
            ->where('prudal = 0 and webpwd != \'\'')
            ->order('dc desc, fio')
            ->queryAll();

        //var_dump($res);die;
        foreach ($res as $key => $i) {
            $data[] = ['id' => $key + 1, 'fnpp' => $i['fnpp'], 'DateCreate' => $i['dc'],
                'group' => $i['gruppa'],
                'fio' => $i['fio'],
                'bd' => $i['bd']];
        }
        //var_dump($data);die;

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }

        $pager = new CPagination();
        $pageSize = 20;
        $pager->pageSize = $pageSize;

        $data = new CArrayDataProvider($filter->arrayFilter($data),
            array('pagination' => $pager,
                'keyField' => 'id'));
        //var_dump($data);die;
        $this->render('index', ['data' => $data, 'filter' => $filter]);
    }

    /**
     * Удаление записи
     * @param integer $id the fnpp of the row to be deleted
     */
    public function actionDelete($id)
    {
        $res = Yii::app()->db2->createCommand('delete from studentsforpasttask where fnpp =' . (int)$id)->execute();
        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

    /**
     * Вставьте новую запись в таблицу studentforpasttask
     * @param id fnpp студента.
     */
    public function actionCreate($id)
    {
        $res = Yii::app()->db2->createCommand()->insert('studentsforpasttask',array(
            'fnpp' => (int)$id,
            'dc' => date('Y-m-d H:m:s'),
        ));
        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

}