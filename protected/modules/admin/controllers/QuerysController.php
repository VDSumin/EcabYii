<?php

class QuerysController extends Controller
{

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index'),
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
        return true;
    }

    /**
     * Список всех ключей
     */
    public function actionIndex()
    {
        $data = [];
        if(!empty($_POST['querys'])){
            $base = $_POST['querys']['base'];
            $text = $_POST['querys']['text'];
            if($base == 'db') {
                if(mb_stripos($text,"SELECT") !== false or mb_stripos($text,"SHOW") !== false) {
                    $data = Yii::app()->db->createCommand($text)->queryAll();
                }else{
                    $data = Yii::app()->db->createCommand($text)->execute();
                }
            }
        }
//        var_dump($data);

        $this->render('index',array('data' => $data));
    }

}
