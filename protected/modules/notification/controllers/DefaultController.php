<?php

class DefaultController extends Controller
{

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'users' => array('*'),
            )
        );
    }

//    protected function beforeAction($action)
//    {
//        if (Yii::app()->user->getFnpp() == null) {
//            $this->redirect(array('/site'));
//        }
//        if (ReadClass::checkpps()) {
//            $this->redirect(array('/remote'));
//        }
//        return true;
//    }

    public function actionIndex()
    {
        if (!Yii::app()->user->isGuest) {
            $this->layout = '//layouts/column1';
            $pps = Yii::app()->user->getPerStatus();
          // $pps = true;
            if ($pps) {
                $perm = (new EmployeePermissions())->getPermissions();
                $wkNotes = NotificationClass::getEmployeeNotes();
                foreach ($wkNotes as &$wkNote) {
                    $wkNote['text'] = html_entity_decode($wkNote['text']);
                    $wkNote['text'] = strip_tags($wkNote['text']);
                }

                $dataProvider = new CArrayDataProvider($wkNotes, array(
                    'pagination' => false
                ));
                $this->render('employeeIndex', array('permissions' => $perm, 'notes' => $wkNotes,
                    'wknpp' => NotificationClass::getWknppByFnpp()[0], 'dataProvider' => $dataProvider));
            } else {
                $notes = NotificationClass::getStudentNotes();
                $this->render('studentIndex', array('notes' => $notes));
            }

        } else {
            $this->redirect(array('/site'));
        }
    }


}