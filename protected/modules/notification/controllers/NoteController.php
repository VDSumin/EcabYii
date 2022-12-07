<?php


class NoteController extends Controller
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


    public function actionIndex($id)
    {

    }

    private function xssEnjection(&$note)
    {
        $note['title'] = htmlentities($note['title']);
        $note['text'] = htmlentities($note['text']);
    }

    public function actionAdd()
    {
        try {


            $post_data = file_get_contents('php://input');
            if (count($post_data) != 0) {
                $data = json_decode($post_data, true);
                if (isset($data['note']) && isset($data['notification_lists'])) {
                    $note = $data['note'];
                    //   $this->xssEnjection($note);
                    if (array_search($note['owner'], NotificationClass::getWknppByFnpp()) !== false || isset($data['debug'])) {
                        $note['create_at'] = (new DateTime())->format('Y-m-d H:i:s');
                        $model = new Note();
                        $model->attributes = $note;
                        $success = $model->save();
                        if ($success) {
                            $listArray = $data['notification_lists'];
                            foreach ($listArray as $list) {
                                $list['note_id'] = $model['id'];
                                $listModel = new NotificationList();
                                $listModel->attributes = $list;
                                $listModel->save();
                            }
                        }
                    }
                }
            }
            Yii::$app->response->statusCode = 200;
        } catch (Exception $e) {
            Yii::$app->response->statusCode = 500;
        }

    }

    public function actionRemove($id)
    {
        $notes = NotificationClass::getEmployeeNotes();
        $key = array_search($id, array_column($notes, 'id'));
        if ($key !== false) {
            $model = Note::model()->findByPk($id);
            $model['valid_until'] = (new DateTime())->format('Y-m-d H:i:s');
            $model->save();
            // $model->delete();

        }

    }

    public function actionConfirm($id)
    {
        //  $sknpps = NotificationClass::getSknppByFnpp();
//        foreach ($sknpps as $npp)
//        {
        $atr = array(
            'note_id' => $id,
            'user_id' => Yii::app()->user->getFnpp(),
            'create_at' => date('Y-m-d H:m:s'),
        );
        $noteConfirm = new NoteConfirm();
        $noteConfirm->attributes = $atr;
        $success = $noteConfirm->save();
        if ($success) {
            echo('Coool');
            if(isset(Yii::app()->session['NotificationCount']))
            {
                Yii::app()->session['NotificationCount'] -= 1;
            }
        } else {
            echo 'Foooooooo';
        }
        //  }
    }

    public function actionCount($force = false)
    {
        if (!Yii::app()->user->isGuest) {
            $pps = Yii::app()->user->getPerStatus();
            if (!$pps) {
                $this->layout = false;
                $data = NotificationClass::getStudentNotesCount($force);
                header('Content-type: application/json');
                echo CJavaScript::jsonEncode($data);
                Yii::app()->end();
            }
        }

    }

    public function actionPrint($id)
    {
        header('Content-Type: application/pdf');
        echo file_get_contents('http://olap.omgtu/cgi/list.pdf?templatefile=D:\report\reportNOTE.fr3&id='.$id);
    }
}