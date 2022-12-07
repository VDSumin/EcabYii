<?php

class RcfgeGalEnterprisesController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout='//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('index','create', 'update', 'delete'),
                'users'=>array('@'),
                'roles' => [WebUser::ROLE_ADMIN,
                    WebUser::ROLE_CHIEF,
                    WebUser::ROLE_ACTING_CHIEF,
                    WebUser::ROLE_PPS]
            ),
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('fulllist'),
                'users'=>array('@'),
                'roles' => [WebUser::ROLE_ADMIN,
                    WebUser::ROLE_CHIEF,
                    WebUser::ROLE_ACTING_CHIEF,
                    WebUser::ROLE_HR,
                    WebUser::ROLE_PPS]
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Manages all models.
     */
    public function actionIndex()
    {
        echo 1;die;
        $model = Yii::app()->odbc->makeCommand('SELECT tk.F$NREC \'nrec\', tk.F$NAME \'name\', tk.F$TEL \'tel\', tk.F$EMAIL \'mail\', tk.F$SHORTNAME \'short\',
          tk.F$SJURIDICALID \'id\', tk.F$CJURIDICALADDR \'nrecaddr\',
          CASE 
            WHEN ter.F$WTYPE = 1 THEN ter.F$SNAME
            WHEN ter1.F$WTYPE = 1 THEN ter1.F$SNAME + \', \' + ter.F$SNAME
            WHEN ter2.F$WTYPE = 1 THEN ter2.F$SNAME + \', \' + ter1.F$SNAME + \', \' + ter.F$SNAME
            WHEN ter3.F$WTYPE = 1 THEN ter3.F$SNAME + \', \' + ter2.F$SNAME + \', \' + ter1.F$SNAME + \', \' + ter.F$SNAME
          WHEN ter4.F$WTYPE = 1 THEN ter4.F$SNAME + \', \' + ter3.F$SNAME + \', \' + ter2.F$SNAME + \', \' + ter1.F$SNAME + \', \' + ter.F$SNAME
            ELSE \'\' END \'addr\'
        
          FROM T$KATORG tk
          LEFT JOIN T$ADDRESSN addr on addr.F$NREC = tk.F$CJURIDICALADDR
          LEFT JOIN T$STERR ter on ter.F$NREC = addr.F$CSTERR
          LEFT JOIN T$STERR ter1 on ter1.F$NREC = ter.F$CPARENT
          LEFT JOIN T$STERR ter2 on ter2.F$NREC = ter1.F$CPARENT
          LEFT JOIN T$STERR ter3 on ter3.F$NREC = ter2.F$CPARENT
          LEFT JOIN T$STERR ter4 on ter4.F$NREC = ter3.F$CPARENT
          LEFT JOIN T$STERR ter5 on ter5.F$NREC = ter4.F$CPARENT
          WHERE tk.F$TIPORG like \''.iconv("UTF-8", "Windows-1251", 'Место практики').'\' ORDER BY tk.F$NREC DESC')->queryAll();

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }
        $pager = new CPagination();
        $pager->pageSize = 25;
        $dataProvider = new CArrayDataProvider($filter->arrayFilter($model), array('pagination' => $pager, 'keyField' => 'nrec'));
        $success = Yii::app()->user->getState('success');
        Yii::app()->user->setState('success', false);
        $this->render('index',array(
            'filter' => $filter,
            'dataProvider' => $dataProvider,
            'success' => $success,
        ));
    }

    public function actionCreate()
    {
        $modelAdr = new Addressn;
        $modelKat = new Katorg;
        $addressError1 = false;
        $addressError2 = false;
        $addressError3 = false;
        //var_dump($modelAdr->sterrTemp->fname);die;
        if (isset($_POST['Katorg'])) {
            if($_POST['Addressn']['csterr'] == "" or $_POST['Addressn']['csterr'] == hex2bin('8000000000000000') or $_POST['Addressn']['csterr'] == '0x' or $_POST['Katorg']['name'] == "" or $_POST['Katorg']['shortname'] == ""){
                $modelKat->name = $_POST['Katorg']['name'];
                $modelKat->shortname = $_POST['Katorg']['shortname'];
                $modelKat->tel = $_POST['Katorg']['tel'];
                $modelKat->email = $_POST['Katorg']['email'];
                $modelKat->sjuridicalid = $_POST['Katorg']['sjuridicalid'];
                $modelAdr->csterr = $_POST['Addressn']['csterr'];
                if($_POST['Addressn']['csterr'] == "" or $_POST['Addressn']['csterr'] == hex2bin('8000000000000000') or $_POST['Addressn']['csterr'] == '0x'){$addressError1 = true;}
                if($_POST['Katorg']['name'] == ""){$addressError3 = true;}
                if($_POST['Katorg']['shortname'] == ""){$addressError2 = true;}
            }else{
                $modelAdr->csterr = $_POST['Addressn']['csterr'];
                $modelAdr->objtype = 3;
                $addresn = $modelAdr->save();
                if ($addresn) {
                    $katorg = Yii::app()->odbc->insert('.dbo.T$KATORG', array(
                        'F$NAME' => $_POST['Katorg']['name'],
                        'F$TIPORG' => 'Место практики',
                        'F$SHORTNAME' => $_POST['Katorg']['shortname'],
                        'F$CJURIDICALADDR' => ODBCE::nrec($addresn),
                        'F$TEL' => $_POST['Katorg']['tel'],
                        'F$EMAIL' => $_POST['Katorg']['email'],
                        'F$SJURIDICALID' => $_POST['Katorg']['sjuridicalid'],
                    ));
                    $katorgdescr = Yii::app()->odbc->insert('.dbo.T$KATORGDESCR', array(
                        'F$NAME' => $_POST['Katorg']['name'],
                        'F$SHORTNAME' => $_POST['Katorg']['shortname'],
                        'F$CREC' => ODBCE::nrec($katorg),
                        'F$CGROUP' => ODBCE::nrec('0x8000000000000004'),
                        'F$ISLEAF' => 1,
                        'F$CODE' => 1,
                    ));
                    $kontrier = Yii::app()->odbc->insert('.dbo.T$KONTRIER', array(
                        'F$NAME' => $_POST['Katorg']['name'],
                        'F$SHORTNAME' => $_POST['Katorg']['shortname'],
                        'F$CGROUP' => ODBCE::nrec($katorgdescr),
                        'F$CRECDS' => ODBCE::nrec($katorgdescr),
                    ));
                    $podrinfo = Yii::app()->odbc->insert('.dbo.T$PODRINFO', array(
                        'F$CPODR' => ODBCE::nrec($katorg),
                    ));
                    Yii::app()->user->setState('success', true);
                    $this->redirect(array('index'));
                }
            }
        }
        $this->render('create', array(
            'modelAdr' => $modelAdr,
            'modelKat' => $modelKat,
            'addressError1' => $addressError1,
            'addressError2' => $addressError2,
            'addressError3' => $addressError3,
        ));
    }

    public function actionfulllist($term, $force = false) {
        $supervisors = SterrTemp::model()->getFullList($term);

        /*$maxCnt = 30;
        $result = array();
        foreach($supervisors as $row) {
            if (mb_strstr(mb_strtolower($row['label'], Yii::app()->charset), mb_strtolower($term, Yii::app()->charset), false, Yii::app()->charset)) {
                $result[] = $row;
                if (--$maxCnt < 0) { break; }
            }
        }*/

        echo CJSON::encode($supervisors);
    }

    public function actionUpdate($id) {
        $modelKat = $this->loadModelKat($id);
        $modelAdr = $this->loadModelAdr($modelKat->cjuridicaladdr);
        $addressError1 = false;
        $addressError2 = false;
        $addressError3 = false;
        if (isset($_POST['Katorg'])) {
//            $modelAdr->csterr = $_POST['Addressn']['csterr'];
//            $modelAdr->objtype = 3;
//            $addresn = $modelAdr->save();
            $addresn = Yii::app()->odbc->update('.dbo.T$ADDRESSN', array(
                'F$CSTERR' => _d::nrec($_POST['Addressn']['csterr']),
            ), 'F$NREC =' . $modelAdr->nrec);
            if ($addresn) {
                $katorg = Yii::app()->odbc->update('.dbo.T$KATORG', array(
                    'F$NAME' => $_POST['Katorg']['name'],
                    'F$TIPORG' => 'Место практики',
                    'F$SHORTNAME' => $_POST['Katorg']['shortname'],
                    'F$CJURIDICALADDR' => ODBCE::nrec($modelAdr->nrec),
                    'F$TEL' => $_POST['Katorg']['tel'],
                    'F$EMAIL' => $_POST['Katorg']['email'],
                    'F$SJURIDICALID' => $_POST['Katorg']['sjuridicalid'],
                ), 'F$NREC =' . MyModel::_id($id));

                $katorgdescr = Yii::app()->odbc->update('.dbo.T$KATORGDESCR', array(
                    'F$NAME' => $_POST['Katorg']['name'],
                    'F$SHORTNAME' => $_POST['Katorg']['shortname'],
                    'F$ISLEAF' => 1,
                    'F$CODE' => 1,
                ), 'F$CREC =' . MyModel::_id($id));

                $katorgdescr = SQLBuilder::start()->select('tk.F$NREC')
                    ->from('T$KATORGDESCR', 'tk')
                    ->where('tk.F$CREC =' . MyModel::_id($id))
                    ->buildQuery()
                    ->queryScalar();

                $kontrier = Yii::app()->odbc->update('.dbo.T$KONTRIER', array(
                    'F$NAME' => $_POST['Katorg']['name'],
                    'F$SHORTNAME' => $_POST['Katorg']['shortname'],
                ), 'F$CRECDS =' . MyModel::_id($katorgdescr));

                $this->redirect(array('index'));
            }
        }
        $this->render('update', array(
            'modelKat' => $modelKat,
            'modelAdr' => $modelAdr,
            'addressError1' => $addressError1,
            'addressError2' => $addressError2,
            'addressError3' => $addressError3,
        ));
    }

    public function loadModelKat($id) {
        $model = Katorg::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

    public function loadModelAdr($id) {
        $model = Addressn::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

    public function actionDelete($id) {
        $modelKat = $this->loadModelKat($id);
        $modelAdr = $this->loadModelAdr($modelKat->cjuridicaladdr);

        Yii::app()->odbc->makeCommand('DELETE FROM T$PODRINFO WHERE F$CPODR = '.MyModel::_id($id))->execute();
        $katorgdescr = SQLBuilder::start()->select('tk.F$NREC')
            ->from('T$KATORGDESCR', 'tk')
            ->where('tk.F$CREC =' . MyModel::_id($id))
            ->buildQuery()
            ->queryScalar();
        Yii::app()->odbc->makeCommand('DELETE FROM T$KONTRIER WHERE F$CRECDS = '.MyModel::_id($katorgdescr))->execute();
        Yii::app()->odbc->makeCommand('DELETE FROM T$KATORGDESCR WHERE F$CREC = '.MyModel::_id($id))->execute();
        $modelKat->delete();
        $modelAdr->delete();

        if (!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
    }

    public function actionRepeater()
    {
        $model = Yii::app()->odbc->makeCommand(' SELECT \'1\' \'id\',* FROM (SELECT tk.F$NAME \'name\', COUNT(*) \'count\'
          FROM T$KATORG tk
          WHERE tk.F$TIPORG like \''.iconv("UTF-8", "Windows-1251", 'Место практики').'\' GROUP BY tk.F$NAME) tab
          WHERE tab.count > 1')->queryAll();
        //var_dump($model);die;
        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }
        $pager = new CPagination();
        $pager->pageSize = 25;
        $dataProvider = new CArrayDataProvider($filter->arrayFilter($model), array('pagination' => $pager,));
        $this->render('repeater',array(
            'filter' => $filter,
            'dataProvider' => $dataProvider,
        ));
    }

}
