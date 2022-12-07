<?php

class ExpendablerequestController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters() {
		return array(
			'accessControl',
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array(
			array('allow',
                'actions' => array('index', 'print', 'bid'),
                'users' => array('*'),
			),
			array('deny', // deny all users
				'users' => array('*'),
			),
		);
	}

    /**
     * Список всех ключей
     */
    public function actionIndex($id)
    {
        $model = ItdepExpendableRequest::model()->findAllByAttributes(array('auction' => $id));

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }
        $pager = new CPagination();
        $pager->pageSize = 15;
        $dataProvider = new CArrayDataProvider($model, array(
            'pagination' => $pager,
            'keyField' => 'id',
        ));

        $this->render('index', ['dataProvider' => $dataProvider, 'filter' => $filter]);
    }

    /**
     * Выгрузка в xlsx
     */
    public function actionPrint($id)
    {
        set_time_limit(0);
        $auction = ItdepExpendableAuction::model()->findByPk($id);
        $model = ItdepExpendableRequest::model()->findAllByAttributes(array('auction' => $id));

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $auction->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, $auction->info);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Статус: ' . ItdepExpendableAuction::model()->state($id));

        $i = 5;
        $key = 0;
        foreach([
                    'Номер' => 4,
                    'Подразделение' => 15,
                    'Устройство печати' => 15,
                    'Инвертарный номер' => 15,
                    'Тип картриджа' => 15,
                    'Количество' => 12,
                    'Место нахождения объекта' => 15,
                    'Материально ответственное лицо' => 15,
                    'Составитель заявки' => 15,
                    'Телефонный номер' => 15,
                    'Электронный почтовый адрес' => 15,
                    'Комментарий' => 15,
                    'Дата и время последнего редактирования' => 15,
                ] as $title => $width) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $title);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setWidth($width);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key++, $i)->getFont()->setBold(true);
        }

        $ii = 1;
        foreach ($model as $row){
            $i++;
            $key = 0;

            $objPHPExcel->getActiveSheet()
                ->setCellValueByColumnAndRow($key++, $i, ($ii++))
                ->setCellValueByColumnAndRow($key++, $i, StructD_rp::model()->findByPk(CHtml::value($row, "struct", 0))->name)
                ->setCellValueByColumnAndRow($key++, $i, ItdepExpendableDevice::model()->findByPk(CHtml::value($row, "device"))->name)
                ->setCellValueByColumnAndRow($key++, $i, '\''.CHtml::value($row, 'invertNumber', ''))
                ->setCellValueByColumnAndRow($key++, $i, ItdepExpendableTypecart::model()->findByPk(CHtml::value($row, "typeCart"))->name)
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'amount', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'placement', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'responsible', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'creater', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'phone', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'email', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'comment', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'last_date_time', ''));
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(-1);
            for ($j = 0; $j < $key; $j++) {
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j, $i)->applyFromArray(array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                    )
                ));
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
            }

        }

        $objPHPExcel->getActiveSheet()->setTitle(substr($auction->name,0, 30));
//        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getProperties()
            ->setCreator("OmSTU EduCab")
            ->setLastModifiedBy("OmSTU EduCab");

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Заявки на расходные материалы.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

//        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="request.xls"');

        $objWriter->save('php://output');
    }

    public function actionBid($id)
    {
        set_time_limit(0);
        $auction = ItdepExpendableAuction::model()->findByPk($id);
        $model = ItdepExpendableRequest::model()->findAllByAttributes(array('auction' => $id));

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Заявка на выполнение услуг по заправке картриджей для устройств печати');

        $i = 3;
        $key = 0;
        foreach([
                    '№ п/п' => 4,
                    'Наименование подразделения' => 50,
                    'модель картриджа / расходного материала' => 30,
                    'плановое количество, штук' => 30,
                    'ФИО ответственного лица' => 30,
                    'Контактная информация' => 40,
                ] as $title => $width) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, $title);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setWidth($width);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($key++, $i)->getFont()->setBold(true);
        }

        $ii = 1;
        foreach ($model as $row){
            $i++;
            $key = 0;

            $objPHPExcel->getActiveSheet()
                ->setCellValueByColumnAndRow($key++, $i, ($ii++))
                ->setCellValueByColumnAndRow($key++, $i, StructD_rp::model()->findByPk(CHtml::value($row, "struct", 0))->name)
                ->setCellValueByColumnAndRow($key++, $i, ItdepExpendableTypecart::model()->findByPk(CHtml::value($row, "typeCart"))->name)
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'amount', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'responsible', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'contacts', ''));
//            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(-1);
            for ($j = 0; $j < $key; $j++) {
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j, $i)->applyFromArray(array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                    )
                ));
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle(substr($auction->name,0, 30));
//        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getProperties()
            ->setCreator("OmSTU EduCab")
            ->setLastModifiedBy("OmSTU EduCab");

//        var_dump(123);
//        die;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="сформироватьЗаявку.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }



}