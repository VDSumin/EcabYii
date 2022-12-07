<?php

class OborudrequestController extends Controller
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
                'actions' => array('index', 'print'),
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
        $model = ItdepOborudRequest::model()->findAllByAttributes(array('auction' => $id));

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
        $auction = ItdepOborudAuction::model()->findByPk($id);
        $model = ItdepOborudRequest::model()->findAllByAttributes(array('auction' => $id));

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $auction->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, $auction->info);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Статус: ' . ItdepOborudAuction::model()->state($id));

        $i = 5;
        $key = 0;
        foreach([
                    'Номер' => 4,
                    'Подразделение' => 15,
                    'Тип оборудование' => 15,
                    'Вид деятельности' => 15,
                    'Назначение оборудования' => 30,
                    'Характеристики' => 40,
                    'Количество' => 12,
                    'Планируемый источник финансирования' => 15,
                    'Признак замены или нового' => 15,
                    'Место нахождения обекта' => 15,
                    'Дальнейшее использование имеющегося оборудования' => 15,
                    'Материально ответственный' => 15,
                    'Ответственный за заявку' => 15,
                    'Контакты' => 15,
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
            $oborud = ItdepOborudProduct::model()->findByPk(CHtml::value($row, "coborud", 0));
            if($oborud instanceof ItdepOborudProduct){$oborud = $oborud->name;}else{$oborud = "'";}

            $objPHPExcel->getActiveSheet()
                ->setCellValueByColumnAndRow($key++, $i, ($ii++))
                ->setCellValueByColumnAndRow($key++, $i, StructD_rp::model()->findByPk(CHtml::value($row, "struct", 0))->name)
                ->setCellValueByColumnAndRow($key++, $i, $oborud)
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'kindOfActivity', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, "purposeOfEquipment", ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, "composition", ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'amount', ''))
                ->setCellValueByColumnAndRow($key++, $i, ZakClass::finsourse(CHtml::value($row, "finsource")))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'replacement', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'useOfExisting', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'placement', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'finResponsible', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'reqResponsible', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'contacts', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'comment', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'dateAndTime', ''));
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

        $objPHPExcel->getActiveSheet()->setTitle($auction->name);
//        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getProperties()
            ->setCreator("OmSTU EduCab")
            ->setLastModifiedBy("OmSTU EduCab");

        header('Status: 200 OK');
        header('Location');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Заявки на оборудование.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save('php://output');
    }





}