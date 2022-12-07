<?php

class SoftwarerequestController extends Controller
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
        $model = ItdepSoftwareRequest::model()->findAllByAttributes(array('auction' => $id));

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
        $auction = ItdepSoftwareAuction::model()->findByPk($id);
        $model = ItdepSoftwareRequest::model()->findAllByAttributes(array('auction' => $id));

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $auction->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, $auction->info);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Статус: ' . ItdepSoftwareAuction::model()->state($id));

        $i = 5;
        $key = 0;
        foreach([
                    'Номер' => 4,
                    'Подразделение' => 15,
                    'Наименование ПО' => 15,
                    'Версия ПО' => 15,
                    'Редакция ПО' => 15,
                    'Количество' => 12,
                    'Вид деятельности' => 15,
                    'Назначение (основание) приобретения' => 15,
                    'Планируемый источник финансирования' => 15,
                    'Место нахождения ПО' => 15,
                    'Материально ответственное лицо' => 15,
                    'Составитель заявки' => 15,
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

            $objPHPExcel->getActiveSheet()
                ->setCellValueByColumnAndRow($key++, $i, ($ii++))
                ->setCellValueByColumnAndRow($key++, $i, StructD_rp::model()->findByPk(CHtml::value($row, "struct", 0))->name)
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'softName', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'versionSW', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'editionSW', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'amount', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'kindOfActivity', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'purpose', ''))
                ->setCellValueByColumnAndRow($key++, $i, ZakClass::finsourse(CHtml::value($row, "finsource")))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'placement', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'responsible', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'creater', ''))
                ->setCellValueByColumnAndRow($key++, $i, CHtml::value($row, 'contacts', ''))
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

        header('Status: 200 OK');
        header('Location');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Заявки на ПО.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save('php://output');
    }





}