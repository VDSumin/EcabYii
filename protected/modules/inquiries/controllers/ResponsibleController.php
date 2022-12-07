<?php

class ResponsibleController extends Controller
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
        $requests = InquiriesRequests::model();
        if (!empty($_POST['InquiriesRequests'])) {
            $requests = $requests->findByPk($_POST['InquiriesRequests']['id']);
            $requests->modifiedAt = date("Y-m-d H:i:s");

            $save_path = InquiriesModule::uploadPath();
            if (!file_exists($save_path)) {
                mkdir($save_path);
            }
            if (!file_exists($save_path . $requests->id)) {
                mkdir($save_path . $requests->id);
            }

            if (!empty($_FILES)) {
                $files = $_FILES['InquiriesRequests'];
                $count_file = count($files['name']);
                $file = $files['name']['filePath'];
                $path = pathinfo($file);
                $filename = $path['filename'];
                $ext = $path['extension'];
                $temp_name = $files['tmp_name']['filePath'];
                $path_filename_ext = $save_path . $requests->id . '/' . $filename . "." . $ext;

                if (!file_exists($path_filename_ext)) {
                    move_uploaded_file($temp_name, $path_filename_ext);
                }
                $requests->filePath = str_replace($save_path, '', $path_filename_ext);
                $requests->update();
                $student = Fdata::model()->findByPk($requests->studentNpp)->getFIO();
                Yii::app()->user->setFlash('success', 'Заявка студента ' . $student . ' успешно обработана и помещена в архив.');

                $this->sendEmailFile($requests);
            }
        }

        $requests->unsetAttributes();

        $types = InquiriesTypes::model();
        $types->unsetAttributes();

        $hostel = $this->searchResponsibleHostelWithFilter();
        $this->layout = '//layouts/column1';
        $this->render('index', array(
            'requests' => $requests,
            'types' => $types,
            'hostel' => $hostel['data'],
            'filter' => $hostel['filter']
        ));
    }

    public function searchResponsibleHostelWithFilter()
    {
        $sql = '';

        $criteria = new CDbCriteria;

        //$criteria->compare('id', $this->id);
        $criteria->addCondition('filePath = ""');
        $criteria->addCondition('typeId = ' . InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::HOSTEL))->id);
        $inquiries = InquiriesRequests::model()->findAll($criteria);
        //var_dump($inquiries);die;
        foreach ($inquiries as $i) {
            $additional = explode('_', $i->additional);
            $fdata = Fdata::model()->findByPk($i->studentNpp);
            $skard = Skard::model()->findByPk($i->groupNpp);
            $hostel = HostelContract::model()->findByPk($additional[0]);
            $data[] = ['id' => $i->id, 'fnpp' => $i->studentNpp, 'startDate' => InquiriesRequests::getDate($i->startDate, 1, $i->typeId)
                , 'finishDate' => InquiriesRequests::getDate($i->finishDate, 0, $i->typeId),
                'cont_id' => $additional[0], 'reason' => $additional[1],
                'cont_num' => $hostel->contNumber,
                'fak' => $skard ? $skard->fak : '-',
                'fio' => $fdata ? $fdata->getFIO() : '?',
                'group' => $skard ? $skard->gruppa
                    : "Ак/отпуск, дата рождения - " . date("d.m.Y", strtotime($fdata->rogd)),
                'created' => date("d.m.Y", strtotime($i->createdAt)),
                'takePickup' => InquiriesTypes::getTakesPickup(3)[$i->takePickUp],
                'hostel' => $hostel->housing->hostel == 9 ? 3 : $hostel->housing->hostel,
            ];
        }
        //var_dump($data);die;

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }


        $pager = new CPagination();
        $pageSize = 15;
        $pager->pageSize = $pageSize;

        $data = new CArrayDataProvider($filter->arrayFilter($data),
            array('pagination' => $pager,
                'keyField' => 'id'));
        //var_dump($data);die;
        return ['data' => $data, 'filter' => $filter];
    }

    public function actionDownloadFile($id)
    {
        $requests = InquiriesRequests::model();
        if (!InquiriesResponsibles::checkFacultyRights($id) && $requests->countByAttributes(array(
                'typeId' => InquiriesResponsibles::getResponsibleTypes(),
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
        if (!InquiriesResponsibles::checkFacultyRights($id) && $requests->countByAttributes(array(
                'typeId' => InquiriesResponsibles::getResponsibleTypes(),
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/responsible'));
        }
        $requests = $requests->findByPk($id);
        if ($requests->filePath != '' && $requests->filePath != '!declined' && $requests->filePath != '!accepted') {
            $path = InquiriesModule::uploadPath();
            unlink($path . $requests->filePath);
            rmdir($path . $requests->id);
        }
        $requests->delete();
        $this->redirect(array('/inquiries/responsible'));
    }

    public function actionDecline($id, $comment = null)
    {
        $requests = InquiriesRequests::model();
        if (!InquiriesResponsibles::checkFacultyRights($id) && $requests->countByAttributes(array(
                'typeId' => InquiriesResponsibles::getResponsibleTypes(),
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/responsible'));
        }
        $requests = $requests->findByPk($id);
        $requests->modifiedAt = date("Y-m-d H:i:s");
        $requests->filePath = '!declined' . $comment;
        $requests->update();
        $student = Fdata::model()->findByPk($requests->studentNpp)->getFIO();
        Yii::app()->user->setFlash('success', 'Заявка студента ' . $student . ' успешно отклонена и помещена в архив.');
        $this->sendEmailDeclined($requests);
        $this->redirect(array('/inquiries/responsible'));
    }

    public function actionAccept($id)
    {
        $requests = InquiriesRequests::model();
        if (!InquiriesResponsibles::checkFacultyRights($id) && $requests->countByAttributes(array(
                'typeId' => InquiriesResponsibles::getResponsibleTypes(),
                'id' => $id)) == 0
        ) {
            $this->redirect(array('/inquiries/responsible'));
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


    protected function beforeAction($action)
    {
        if (Yii::app()->user->getFnpp() == null) {
            $this->redirect(array('/site'));
        }
        if (!InquiriesResponsibles::amIResponsible()) {
            $this->redirect(array('/site'));
        }
        return true;
    }

    private function sendEmailFile($model)
    {
        $student = Fdata::model()->findByPk($model->studentNpp);
        if (filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
            $mail = new YiiMailer();
            $mail->setFrom('edu.noreply@omgtu.tech', 'ОмГТУ. Рассылка учебного портала');
            $mail->setTo($student->email);
            $mail->setSubject(InquiriesTypes::getTypeString($model->typeId));
//            $mail->addAttachment(InquiriesModule::uploadPath() . $model->filePath, str_replace($model->id . '/', '', $model->filePath));
//            $text = $student->getFIO() . ', Ваша заявка успешно обработана.<br />Если к данному письму не прикреплен файл, Вы сможете найти его на Учебном портале -> раздел "Мои заявки".';
            $text = $student->getFIO() . ', Ваша заявка успешно обработана.<br />Для того, чтобы скачать файл, прикрепленный к Вашей заявке, перейдите на Учебный портал -> раздел "Мои заявки".';
            $mail->setBody($text);

            if ($mail->send()) {
                Yii::app()->user->setFlash('success', 'Заявка студента ' . $student->getFIO() . ' успешно обработана и помещена в архив. Письмо с оповещением отправлено на указанную студентом почту.');
            } else {
                Yii::app()->user->setFlash('error', 'Заявка студента ' . $student->getFIO() . ' успешно обработана и помещена в архив. Однако письмо с оповещением не доставлено.');
            }
        }
    }

    private function sendEmailAccepted($model)
    {
        $student = Fdata::model()->findByPk($model->studentNpp);
        if (filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
            $mail = new YiiMailer();
            $mail->setFrom('edu.noreply@omgtu.tech', 'ОмГТУ. Рассылка учебного портала');
            $mail->setTo($student->email);
            $mail->setSubject(InquiriesTypes::getTypeString($model->typeId));
            $text = $student->getFIO() . ', Ваша заявка выполнена.<br />Для того чтобы отслеживать статус заявок, перейдите на Учебный портал -> раздел "Мои заявки".';
            $mail->setBody($text);

            if ($mail->send()) {
                Yii::app()->user->setFlash('success', 'Заявка студента ' . $student->getFIO() . ' успешно обработана и помещена в архив. Письмо с оповещением отправлено на указанную студентом почту.');
            } else {
                Yii::app()->user->setFlash('warning', 'Заявка студента ' . $student->getFIO() . ' успешно обработана и помещена в архив. Однако письмо с оповещением не доставлено.');
            }
        }
    }

    private function sendEmailDeclined($model)
    {
        $student = Fdata::model()->findByPk($model->studentNpp);
        if (filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
            $comment = substr($model->filePath, 9, strlen($model->filePath) - 9);
            if (strlen($comment) > 0) {
                $comment = '<br />Комментарий: <i>"' . $comment . '"</i><br />';
            }
            $mail = new YiiMailer();
            $mail->setFrom('edu.noreply@omgtu.tech', 'ОмГТУ. Рассылка учебного портала');
            $mail->setTo($student->email);
            $mail->setSubject(InquiriesTypes::getTypeString($model->typeId));
            $text = $student->getFIO() . ', Ваша заявка отклонена.<br />' .
                $comment .
                '<br />Вы можете подать заявку повторно, предварительно проверив все требуемые поля.<br />' .
                'Для этого перейдите на Учебный портал -> раздел "Мои заявки".';
            $mail->setBody($text);

            if ($mail->send()) {
                Yii::app()->user->setFlash('success', 'Заявка студента ' . $student->getFIO() . ' отклонена и помещена в архив. Письмо с оповещением отправлено на указанную студентом почту.');
            } else {
                Yii::app()->user->setFlash('warning', 'Заявка студента ' . $student->getFIO() . ' отклонена и помещена в архив. Однако письмо с оповещением не доставлено.');
            }
        }
    }

    public function actionGetHostelExcel($date = 'today', $number = 0)
    {
        $number = $number == 3 ? 9 : $number;
        if (in_array(InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::HOSTEL))->id, InquiriesResponsibles::getResponsibleTypes())) {
            $this->getFile($date, $number);
        }
    }

    public function actionGetHostelExcelAll($date = 'today', $number = 0)
    {
        if (in_array(InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::HOSTEL))->id, InquiriesResponsibles::getResponsibleTypes())) {
            $this->getFile($date, $number, true);
        }
    }

    public static function getEduForm($form)
    {
        switch ($form) {
            case '10':
            case 'заочная':
            case 'Заочная форма':
                return 'заочное обучение';
                break;
            case '11':
            case 'очная':
            case 'Очная форма':
                return 'очное обучение';
                break;
            case '12':
            case 'Очно-заочная (вечерняя)':
                return 'очно-заочное обучение';
                break;
            default:
                return $form;
        }
    }

    public static function getFin($fin)
    {
        switch ($fin) {
            case '15':
            case 'С оплатой обучения':
            case 'Заочная форма':
                return 'Комм.';
                break;
            case '14':
            case 'Бюджетные места':
            case 'с финансированием за счет средств федерального бюджета':
                return 'Г/б';
                break;
            case '16':
            case 'Целевой прием':
                return 'ЦП';
                break;
            default:
                return $fin;
        }
    }

    private function getFile($date, $number, $all = false)
    {
        function setCellsBold($activeSheet, $arr)
        {
            foreach ($arr as $element) {
                $activeSheet->getStyleByColumnAndRow($element[0], $element[1])->getFont()->setBold(true);
            }
        }

        function setColumnsWidth($activeSheet, $arr)
        {
            foreach ($arr as $element) {
                $activeSheet->getColumnDimension($element[0])->setWidth($element[1]);
            }
        }

        function setCellsLeft($activeSheet, $arr)
        {
            foreach ($arr as $element) {
                $activeSheet->getStyleByColumnAndRow($element[0], $element[1])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            }
        }

        function writeOneRow($activeSheet, $row, $request)
        {
            setCellsLeft($activeSheet, array(
                [1, $row],
                [6, $row],
                [7, $row],
            ));
            $person = Fdata::model()->findByPk($request->studentNpp);
            $activeSheet->setCellValueByColumnAndRow(0, $row, $row - 2);
            $activeSheet->setCellValueByColumnAndRow(1, $row, mb_strtoupper(mb_substr($person->fam, 0, 1)) . mb_strtolower(mb_substr($person->fam, 1, mb_strlen($person->fam) - 1)) . " " . $person->nam . " " . $person->otc);
            $activeSheet->setCellValueByColumnAndRow(2, $row, (Skard::model()->findByPk($request->groupNpp))
                ? Skard::model()->findByPk($request->groupNpp)->gruppa
                : '-');
            $activeSheet->setCellValueByColumnAndRow(3, $row, (Skard::model()->findByPk($request->groupNpp))
                ? ResponsibleController::getEduForm(Skard::model()->findByPk($request->groupNpp)->form) . ' ' .
                ResponsibleController::getFin(Skard::model()->findByPk($request->groupNpp)->fin)
                : '-');
            $activeSheet->setCellValueByColumnAndRow(4, $row, (Skard::model()->findByPk($request->groupNpp))
                ? Skard::model()->findByPk($request->groupNpp)->fak
                : '-');
            $activeSheet->setCellValueByColumnAndRow(5, $row, InquiriesRequests::getHostelAddress($request->additional));
            $activeSheet->setCellValueByColumnAndRow(6, $row, InquiriesRequests::getContractDates($request));
            $activeSheet->setCellValueByColumnAndRow(7, $row, InquiriesRequests::getHostelNumberAndDate($request->additional));
        }

        function checkRow($row, $number)
        {
            return (InquiriesRequests::getHostel($row['additional']) == $number);
        }

        require_once Yii::getPathOfAlias('application.extensions.phpexcel.Classes.PHPExcel') . '.php';
        set_time_limit(0);

        $obj = new PHPExcel();
        $obj->getDefaultStyle()->getFont()->applyFromArray(array(
            'name' => 'Times New Roman',
            'size' => 12,
            'bold' => false,
            'italic' => false
        ));
        $obj->getDefaultStyle()->getAlignment()->applyFromArray([
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => true,
            'shrinkToFit' => false
        ]);

        $sheetNumber = 0;
        $obj->createSheet($sheetNumber);
        $obj->setActiveSheetIndex($sheetNumber);
        $activeSheet = $obj->getActiveSheet();
        $activeSheet->setTitle('Список');
        setColumnsWidth($activeSheet, array(
            ['A', 5],
            ['B', 15],
            ['C', 10],
            ['D', 6],
            ['E', 12],
            ['F', 20],
            ['G', 12],
            ['H', 12],
            ['I', 15],
        ));

        $row = 1;
        $activeSheet->mergeCellsByColumnAndRow(0, $row, 8, $row);
        $activeSheet->setCellValueByColumnAndRow(0, $row, 'Список студентов подавших заявление на перерасчет за проживание в общежитии в период выезда во время Коронавируса');
        setCellsBold($activeSheet, array([0, $row]));

        $row++;
        $activeSheet->setCellValueByColumnAndRow(0, $row, '№');
        $activeSheet->setCellValueByColumnAndRow(1, $row, 'ФИО');
        $activeSheet->setCellValueByColumnAndRow(2, $row, 'Группа');
        $activeSheet->setCellValueByColumnAndRow(3, $row, 'Форма обучения');
        $activeSheet->setCellValueByColumnAndRow(4, $row, 'Факультет');
        $activeSheet->setCellValueByColumnAndRow(5, $row, 'Общежитие № /блок/ адрес, комната');
        $activeSheet->setCellValueByColumnAndRow(6, $row, 'Период проживания / отсутствия');
        $activeSheet->setCellValueByColumnAndRow(7, $row, '№ и дата договора');
        $activeSheet->setCellValueByColumnAndRow(8, $row, 'Право на бесплатное проживание');
        setCellsBold($activeSheet, array(
            [0, $row],
            [1, $row],
            [2, $row],
            [3, $row],
            [4, $row],
            [5, $row],
            [6, $row],
            [7, $row],
            [8, $row],
            [9, $row],
        ));
        $condition = $all ? '' : ' AND filePath = \'!accepted\' ';
        $requests = InquiriesRequests::model()->findAll(array(
            'condition' => 'typeId=:typeId 
            AND modifiedAt >= :date1
            AND modifiedAt < :date2
            ' . $condition,
            'params' => array(
                ':typeId' => InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::HOSTEL))->id,
                ':date1' => date('Y-m-d', strtotime($date)),
                ':date2' => date('Y-m-d', strtotime($date . '+ 1 days')),
            ),
        ));

        foreach ($requests as $request) {
            if (checkRow($request, $number)) {
                $row++;
                writeOneRow($activeSheet, $row, $request);
            }
        }

        $sheetNumber++;
        $obj->removeSheetByIndex($sheetNumber);
        $obj->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
        $path = 'protected/data/uploads/temp/';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $filename = $path . 'tmpInquiriesHostel' . rand(1, 999) . '.xlsx';
        $objWriter->save($filename);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename = Заявки.xlsx');
        header('Cache-Control: max-age=0');

        echo file_get_contents($filename);
        unlink($filename);
    }
}
