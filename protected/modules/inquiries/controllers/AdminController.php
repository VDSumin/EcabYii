<?php

class AdminController extends Controller
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
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        if ($_POST) {
            if (isset($_POST['InquiriesTypes'])) {
                $model = new InquiriesTypes();
                $model->name = $_POST['InquiriesTypes']['name'];
                $model->createdAt = date('Y-m-d H:i:s');
                $model->save();
            } elseif (isset($_POST['InquiriesResponsibles'])) {
                $model = new InquiriesResponsibles();
                $model->responsibleNpp = $_POST['InquiriesResponsibles']['responsibleNpp'];
                $model->typeId = $_POST['InquiriesResponsibles']['typeId'];
                $model->save();
            }
        }

        $types = InquiriesTypes::model();
        $responsibles = InquiriesResponsibles::model();
        $requests = InquiriesRequests::model();

        $this->layout = '//layouts/column1';
        $this->render('index', array(
            'types' => $types,
            'responsibles' => $responsibles,
            'requests' => $requests,
        ));
    }


    public function actionDownloadFile($id)
    {
        $requests = InquiriesRequests::model();
        if ($requests->countByAttributes(array(
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/admin'));
        }
        $save_path = InquiriesModule::uploadPath();
        $requests = $requests->findByPk($id);
        $file = $save_path . $requests->filePath;
        if (file_exists($file)) {
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"" . str_replace($requests->id . '/', '', $requests->filePath) . "\"");
            readfile($file);
        }
    }

    public function actionDelete($id)
    {
        $requests = InquiriesRequests::model();
        if ($requests->countByAttributes(array(
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/admin'));
        }
        $requests = $requests->findByPk($id);
        if ($requests->filePath != '' && strpos($requests->filePath, '!declined') === false && $requests->filePath != '!accepted') {
            $path = InquiriesModule::uploadPath();
            unlink($path . $requests->filePath);
            rmdir($path . $requests->id);
        }
        $requests->delete();
        $this->redirect(array('/inquiries/admin'));
    }

    public function actionDecline($id, $comment = null)
    {
        $requests = InquiriesRequests::model();
        if ($requests->countByAttributes(array(
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/admin'));
        }
        $requests = $requests->findByPk($id);
        $requests->modifiedAt = date("Y-m-d H:i:s");
        $requests->filePath = '!declined' . $comment;
        $requests->update();
        $student = Fdata::model()->findByPk($requests->studentNpp)->getFIO();
        Yii::app()->user->setFlash('success', 'Заявка студента ' . $student . ' успешно отклонена и помещена в архив.');
        $this->redirect(array('/inquiries/admin'));
    }

    public function actionAccept($id)
    {
        $requests = InquiriesRequests::model();
        if ($requests->countByAttributes(array(
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/admin'));
        }
        $requests = $requests->findByPk($id);
        $requests->modifiedAt = date("Y-m-d H:i:s");
        $requests->filePath = '!accepted';
        $requests->update();
        $student = Fdata::model()->findByPk($requests->studentNpp)->getFIO();
        Yii::app()->user->setFlash('success', 'Заявка студента ' . $student . ' успешно подтверждена и помещена в архив.');
        $this->sendEmailAccepted($requests);
        $this->redirect(array('/inquiries/responsible'));
    }

    public function actionDeleteResponsible($id)
    {
        $responsibles = InquiriesResponsibles::model();
        if ($responsibles->countByAttributes(array(
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/admin'));
        }
        $responsibles->findByPk($id)->delete();
        $this->redirect(array('/inquiries/admin'));
    }

    public function actionDeleteType($id)
    {
        $types = InquiriesTypes::model();
        if ($types->countByAttributes(array(
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/admin'));
        }
        $types->findByPk($id)->delete();
        $this->redirect(array('/inquiries/admin'));
    }

    protected function beforeAction($action)
    {
        if (!in_array(Yii::app()->user->getFnpp(), Controller::ADMIN_FNPP)) {
            $this->redirect(array('/site'));
        }
        return true;
    }

}
