<?php

class StudentController extends Controller
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
        if (!InquiriesRequests::getGroups()) {
            $this->redirect(array('/site'));
        }
        $requests = InquiriesRequests::model();
        $requests->unsetAttributes();
        $requests->findAllByAttributes(array(
            'studentNpp' => Yii::app()->user->getFnpp()
        ));
        $count = $requests->countByAttributes(array(
            'studentNpp' => Yii::app()->user->getFnpp()
        ));

        $types = InquiriesTypes::model();
        $types->unsetAttributes();
        $types->findAll();

        $this->layout = '//layouts/column1';
        $this->render('index', array(
            'requests' => $requests,
            'types' => $types,
            'showTable' => ($count > 0),
        ));
    }

    public function actionDownloadFile($id)
    {
        $requests = InquiriesRequests::model();
        if ($requests->countByAttributes(array(
                'studentNpp' => Yii::app()->user->getFnpp(),
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/site'));
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
                'studentNpp' => Yii::app()->user->getFnpp(),
                'filePath' => '',
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/site'));
        }
        $requests = $requests->findByPk($id);
        if ($requests->filePath != '' && $requests->filePath != '!declined' && $requests->filePath != '!accepted') {
            $path = InquiriesModule::uploadPath();
            unlink($path . $requests->filePath);
            rmdir($path . $requests->id);
        }
        $requests->delete();
        $this->redirect(array('/inquiries/student'));
    }

    public function actionAdd()
    {
        if (!isset($_POST['InquiriesRequests'])) {
            $this->redirect(array('/site'));
        }
        $data = $_POST['InquiriesRequests'];
        if (
            ($data['groupNpp'] == '')
            || ($data['typeId'] == '')
            || ($data['startYear'] == '')
            || ($data['startMonth'] == '')
            || ($data['finishYear'] == '')
            || ($data['finishMonth'] == '')
        ) {
            Yii::app()->user->setFlash('error', 'Ошибка. Выберите тип заявки.');
            $this->redirect(array('/inquiries/student'));
        }
        if (($data['takePickup'] == '')) {
            Yii::app()->user->setFlash('error', 'Ошибка. Выберите место получения справки.');
            $this->redirect(array('/inquiries/student'));
        }

        //var_dump($_POST['InquiriesRequests']);die;

        $model = new InquiriesRequests();

        if (InquiriesTypes::getTypeString($data['typeId']) == InquiriesTypes::PLACE_OF_STUDY) {
            if ($data['place'] == '') {
                Yii::app()->user->setFlash('error', 'Ошибка. Для справок с места учебы место требования - обязательное поле');
                $this->redirect(array('/inquiries/student'));
            } else {
                $model->additional = $data['place'];
            }
        } elseif (InquiriesTypes::getTypeString($data['typeId']) == InquiriesTypes::HOSTEL) {
            if ($data['reason'] == '') {
                Yii::app()->user->setFlash('error', 'Ошибка. Выберите причину.');
                $this->redirect(array('/inquiries/student'));
            } elseif (
                !preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}$/", $data['startDate'])
                || !preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}$/", $data['finishDate'])
            ) {
                Yii::app()->user->setFlash('error', 'Ошибка. Неверный формат даты.');
                $this->redirect(array('/inquiries/student'));
            } else {
                $hostel = HostelContract::model()->findByAttributes(array('fnpp' => Yii::app()->user->getFnpp(), 'status' => 1), array('order' => 'id DESC'));
                $model->additional = $hostel->id . '_' . $data['reason'];
                $model->startDate = date('Y-m-d', strtotime($data['startDate']));
                $model->finishDate = date('Y-m-d', strtotime($data['finishDate']));
            }
        } else {
            $model->startDate = date('Y-m-d', strtotime($data['startYear'] . '-' . $data['startMonth'] . '-1'));
            $model->finishDate = date('Y-m-d', strtotime($data['finishYear'] . '-' . $data['finishMonth'] . '-1'));
        }
        $model->groupNpp = $data['groupNpp'];
        $model->studentNpp = Yii::app()->user->getFnpp();
        $model->facultyNrec = InquiriesRequests::getFacultyNrec($model->groupNpp);
        $model->typeId = $data['typeId'];
        $model->takePickUp = $data['takePickup'];

        if (InquiriesTypes::getTypeString($model->typeId) == InquiriesTypes::INCOME && $model->finishDate >= date('Y-m-01')) {
            Yii::app()->user->setFlash('error', 'Ошибка. Выбрана некорректная дата. ' . InquiriesTypes::INCOME . ' доступна только для прошедших месяцев.');
            $this->redirect(array('/inquiries/student'));
        }
        if ($model->startDate > $model->finishDate) {
            Yii::app()->user->setFlash('error', 'Ошибка. Дата начала периода не может быть позже даты окончания.');
            $this->redirect(array('/inquiries/student'));
        }
        $model->createdAt = date('Y-m-d H:i:s');
        $model->save();
        Yii::app()->user->setFlash('success', 'Ваша заявка успешно обработана. Вы можете следить за ее статусом здесь, в разделе "Поданные заявки".');
        $this->sendEmail($model);
        $this->redirect(array('/inquiries/student'));
    }

    private function sendEmail($model)
    {
        if (in_array(InquiriesTypes::getTypeString($model->typeId), array(InquiriesTypes::PLACE_OF_STUDY, InquiriesTypes::PFR))) {
            $place = (InquiriesTypes::getTypeString($model->typeId) == InquiriesTypes::PLACE_OF_STUDY) ? '<b>Место требования - </b>' . $model->additional . '<br/>' : '';
            $fio = Fdata::model()->findByPk($model->studentNpp)->getFIO();
            $date = Fdata::model()->findByPk($model->studentNpp)->rogd;
            if (!($facultyEmail = InquiriesRequests::getFacultyEmail($model->facultyNrec))) {
                $facultyEmail = 'ias@omgtu.tech';
            };
//            $faculty = ($model->groupNpp == '-1') ? '-' : Skard::model()->findByPk($model->groupNpp)->fak;
            $group = ($model->groupNpp == '-1') ? 'Ак/отпуск' : Skard::model()->findByPk($model->groupNpp)->gruppa;
            $mail = new YiiMailer();
            $mail->setFrom('edu.noreply@omgtu.tech', 'ОмГТУ. Рассылка учебного портала');
//            $mail->setTo('ias@omgtu.tech');
            $mail->setTo($facultyEmail);
            $mail->setSubject('Новая заявка на справку');
            $text = 'Получена заявка на справку - <b>' . InquiriesTypes::getTypeString($model->typeId) . '</b>.<hr />
                ' . $place . '
                <b>Информация о студенте:</b><br/>
                ФИО: ' . $fio . '<br/>
                Дата рождения: ' . date('d.m.Y', strtotime($date)) . '<br/>
                Группа: ' . $group;
            $mail->setBody($text);
            $mail->send();
        }
    }

    protected function beforeAction($action)
    {
        if (Yii::app()->user->getFnpp() == null) {
            $this->redirect(array('/site'));
        }
        return true;
    }
}
