<?php

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;

class HostelController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
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
            array('allow',
                'actions' => array('insertBill', 'createClearbill', 'createApplication', 'PrintApplication', 'updateApplication'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionInsertBill()
    {
        if (!empty($_REQUEST['pay_id'])) {
            try {
                $debt = HostelDebtPayment::model()->findByPk($_REQUEST['pay_id']);

                $criteria = new CDbCriteria();
                $criteria->addSearchCondition('number', 'H');
                $criteria->order = 'npp DESC';
                $criteria->limit = 1;
                $lastShect = Schet::model()->find($criteria);


                if (!$lastShect) {
                    if ($debt instanceof HostelDebtPayment) {
                        $contNumber = explode('-', $debt->hc->contNumber);
                        $model = new Schet();
                        $model->fnpp = $debt->hc->fnpp;
                        $model->number = 'H000000001';
                        $model->ds = date("Y-m-d");
                        $model->typplat = 1;
                        $model->fislico = $debt->hc->fdata->fam . ' ' . $debt->hc->fdata->nam . ' ' . $debt->hc->fdata->otc;
                        $model->summa = $debt->debt;
                        $model->kolvosem = 1;
                        $model->comment = 'Оплата за проживание в общежитии (студента) № ' . $debt->hc->housing->hostel . ', по договору ' . $contNumber[0] . '-' . $contNumber[1] . '. Студент ' . $model->fislico;
                        $model->vidoplat = 0;
                        $model->save();
                    }

                } else {
                    $schetNumber = explode('H', $lastShect->number);
                    $schetPart2 = $schetNumber[1] + 1;
                    $schetItog = 'H' . str_pad($schetPart2, 9, '0', STR_PAD_LEFT);

                    if ($debt instanceof HostelDebtPayment) {
                        $contNumber = explode('-', $debt->hc->contNumber);

                        $sql = 'SELECT s.npp
                            FROM schet s
                            WHERE 
                              s.fnpp = ' . $debt->hc->fnpp . ' AND s.number LIKE \'%H%\'
                              AND NOT EXISTS (SELECT * FROM www_dog.payments p WHERE s.number = p.o AND (p.state = 1 OR p.state = 2)) 
                            ORDER BY s.npp DESC
                            LIMIT 1
                            ';

                        $sql = Yii::app()->db2->createCommand($sql)->queryScalar();
                        if ($sql) {
                            $model = Schet::model()->findByPk($sql);
                            $model->fnpp = $debt->hc->fnpp;
                            $model->ds = date("Y-m-d");
                            $model->typplat = 1;
                            $model->fislico = $debt->hc->fdata->fam . ' ' . $debt->hc->fdata->nam . ' ' . $debt->hc->fdata->otc;
                            $model->summa = $debt->debt;
                            $model->kolvosem = 1;
                            $model->comment = 'Оплата за проживание в общежитии (студента) № ' . $debt->hc->housing->hostel . ', по договору ' . $contNumber[0] . '-' . $contNumber[1] . '. Студент ' . $model->fislico;
                            $model->vidoplat = 0;

                            $model->save();
                        } else {
                            $model = new Schet();
                            $model->fnpp = $debt->hc->fnpp;
                            $model->number = $schetItog;
                            $model->ds = date("Y-m-d");
                            $model->typplat = 1;
                            $model->fislico = $debt->hc->fdata->fam . ' ' . $debt->hc->fdata->nam . ' ' . $debt->hc->fdata->otc;
                            $model->summa = $debt->debt;
                            $model->kolvosem = 1;
                            $model->comment = 'Оплата за проживание в общежитии (студента) № ' . $debt->hc->housing->hostel . ', по договору ' . $contNumber[0] . '-' . $contNumber[1] . '. Студент ' . $model->fislico;
                            $model->vidoplat = 0;
                            $model->save();
                        }

                    }
                }
                $this->redirect('http://omgtu.ru/ecab/pay.php');
            } catch (Exception $e) {
                $this->redirect('http://omgtu.ru/ecab/up.php?hostel=1');
            }
        } else {
            $this->redirect('http://omgtu.ru/ecab/up.php?hostel=1');
        }

    }

    public function actionCreateClearbill()
    {
        if (!empty($_REQUEST['shapeBill'])) {
            if (!empty($_REQUEST['contId']) && !empty($_REQUEST['total']) && ($_REQUEST['total'] > 0)) {
                try {
                    if (!empty($_REQUEST['month']) && strlen($_REQUEST['month']) > 2) {
                        $month = $_REQUEST['month'];
                    } else {
                        $month = "";
                    }
                    $debt = HostelContract::model()->findByPk($_REQUEST['contId']);
                    $date = date("d.m.Y");
                    $contNumber = explode('-', $debt->contNumber);
                    $contNumber = $contNumber[0] . '-' . $contNumber[1];
                    if ($debt->contType == HostelCatalog::TYPE_COMMERCIAL) {
                        $hostel = $debt->housing->hostel . 'к';
                    } else {
                        $hostel = $debt->housing->hostel;
                    }

                    require_once Yii::getPathOfAlias('application.vendor.mpdf60.mpdf') . '.php';

                    $pdf = new mPDF('utf-8', 'A4', '10', 'Times', 25, 25, 25, 25, 5, 5);
                    $pdf->charset_in = 'utf-8';

                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('arial', '', 14, '', true);
                    $pdf->use_kwt = true;

                    $pdf->AddPage();

                    $html = <<<EOD
<html><head><title></title>
    <style>
        body {
            color: black;
            background-color: white;
        }
        
        table {
            width: 100%;
        }
         p {
    line-height: 0.3;
   }
</style>
</head>
<body>
<p style="text-align:center;">Омский государственный технический университет</p>
<p style="text-align:right;">Сч. 205.31.</p>
<p style="text-align:center; font-weight:bold">КВИТАНЦИЯ</p>
<p style="text-align:right;">от {$date} г.</p>
<div style="display: table-cell;">

<div style="width:10%; float:left">
        Ф.И.О.
        </div>
        <div style="width:90%; border-bottom: 1px solid; font-weight:bold; float:right">
        {$debt->fdata->fam} {$debt->fdata->nam} {$debt->fdata->otc}
        </div>


<div style="width:10%; float:left">
        Комната
        </div>
        <div style="width:40%; border-bottom: 1px solid; color:white; font-weight:bold; float:left">
        f
        </div>
        <div style="width:15%; float:left">
        Общежитие
        </div>
        <div style="width:35%; border-bottom: 1px solid; font-weight:bold; float:right">
        № {$hostel} 
        </div>
        
 <div style="width:100%; border-bottom: 1px solid; font-weight:bold; color:white" colspan=4>
        f
        </div>    
           
<div style="width:20%; float:left">
        Виды платежей
        </div>
        <div style="width:30%; border-bottom: 1px solid; font-weight:bold;  float:left">
        {$contNumber}
        </div>
        <div style="width:10%; float:left">
        Сумма
        </div>
        <div style="width:40%; border-bottom: 1px solid; font-weight:bold; float:right">
        {$_REQUEST['total']} руб.
        </div>           
<div style="width:100%; border-bottom: 1px solid; color:white; font-weight:bold; float:left">
        f
        </div>
          <div style="width:100%; ">
          
        За проживание в общежитии <font style="font-weight:bold;"> {$month} </font>
        </div>
        
</div>        

<p></p>
<p>Бухгалтер</p>

<div style="width:100%; border-bottom: 1px solid; color:white;">
        f
        </div>
</body>
</html>
EOD;

                    $pdf->writeHTML($html);

                    $pdf->Output("report.pdf", "I");
                } catch (Exception $e) {
                    $this->redirect('http://omgtu.ru/ecab/up.php?hostel=1');
                }
            }
        } elseif (!empty($_REQUEST['shape'])) {
            if (!empty($_REQUEST['contId']) && !empty($_REQUEST['total']) && ($_REQUEST['total'] > 0)) {
                try {
                    $debt = HostelContract::model()->findByPk($_REQUEST['contId']);
                    $contNumber = explode('-', $debt->contNumber);

                    $criteria = new CDbCriteria();
                    $criteria->addSearchCondition('number', 'H');
                    $criteria->order = 'npp DESC';
                    $criteria->limit = 1;
                    $lastShect = Schet::model()->find($criteria);

                    if (!$lastShect) {
                        if ($debt instanceof HostelContract) {
                            $model = new Schet();
                            $model->fnpp = $debt->fnpp;
                            $model->number = 'H000000001';
                            $model->ds = date("Y-m-d");
                            $model->typplat = 1;
                            $model->fislico = $debt->fdata->fam . ' ' . $debt->fdata->nam . ' ' . $debt->fdata->otc;
                            $model->summa = str_replace(',', '.', $_REQUEST['total']);
                            $model->kolvosem = 1;
                            $model->comment = 'Оплата за проживание в общежитии (студента) № ' . $debt->housing->hostel . ', по договору ' . $contNumber[0] . '-' . $contNumber[1] . '. Студент ' . $model->fislico;
                            $model->vidoplat = 0;
                            $model->save();
                        }

                    } else {
                        $schetNumber = explode('H', $lastShect->number);
                        $schetPart2 = $schetNumber[1] + 1;
                        $schetItog = 'H' . str_pad($schetPart2, 9, '0', STR_PAD_LEFT);

                        if ($debt instanceof HostelContract) {
                            $sql = 'SELECT s.npp
                            FROM schet s
                            WHERE 
                              s.fnpp = ' . $debt->fnpp . ' AND s.number LIKE \'%H%\'
                              AND NOT EXISTS (SELECT * FROM www_dog.payments p WHERE s.number = p.o AND (p.state = 1 OR p.state = 2)) 
                            ORDER BY s.npp DESC
                            LIMIT 1
                            ';

                            $sql = Yii::app()->db2->createCommand($sql)->queryScalar();
                            if ($sql) {
                                $model = Schet::model()->findByPk($sql);
                                $model->fnpp = $debt->fnpp;
                                $model->ds = date("Y-m-d");
                                $model->typplat = 1;
                                $model->fislico = $debt->fdata->fam . ' ' . $debt->fdata->nam . ' ' . $debt->fdata->otc;
                                $model->summa = str_replace(',', '.', $_REQUEST['total']);
                                $model->kolvosem = 1;
                                $model->comment = 'Оплата за проживание в общежитии (студента) № ' . $debt->housing->hostel . ', по договору ' . $contNumber[0] . '-' . $contNumber[1] . '. Студент ' . $model->fislico;
                                $model->vidoplat = 0;

                                $model->save();
                            } else {
                                $model = new Schet();
                                $model->fnpp = $debt->fnpp;
                                $model->number = $schetItog;
                                $model->ds = date("Y-m-d");
                                $model->typplat = 1;
                                $model->fislico = $debt->fdata->fam . ' ' . $debt->fdata->nam . ' ' . $debt->fdata->otc;
                                $model->summa = str_replace(',', '.', $_REQUEST['total']);
                                $model->kolvosem = 1;
                                $model->comment = 'Оплата за проживание в общежитии (студента) № ' . $debt->housing->hostel . ', по договору ' . $contNumber[0] . '-' . $contNumber[1] . '. Студент ' . $model->fislico;
                                $model->vidoplat = 0;
                                $model->save();
                            }
                        }
                    }
                    $this->redirect('http://omgtu.ru/ecab/pay.php');
                } catch (Exception $e) {
                    $this->redirect('http://omgtu.ru/ecab/up.php?hostel=1');
                }
            } else {
                $this->redirect('http://omgtu.ru/ecab/up.php?hostel=1');
            }
        }
    }

    public function actionCreateApplication($fnpp=null)
    {
        if (!empty($_REQUEST['fnpp'])) {
            $fnpp= $_REQUEST['fnpp'];
            $criteria = new CDbCriteria;
            $criteria->compare('fnpp', $fnpp);
            $model = HostelApplication::model()->find($criteria);
            if(!isset($model)){
                $model = new HostelApplication();
                $model->fnpp = $fnpp;
                $model->dateCreate = new CDbExpression('NOW()');
                $model->lgot = isset($_REQUEST['lgot']) ? true : $_REQUEST['lgot_unchecked'];
                $model->phone = $_REQUEST['phone']  ?: '';
                $model->phone2 = isset($_REQUEST['phone2']) ? $_REQUEST['phone2'] : '';
                $model->fio_pr = isset($_REQUEST['fio_pr']) ? $_REQUEST['fio_pr'] : '';
                $model->save();
            }

            self::actionPrintApplication($model->id);

            $answer = array('state' => 'ok', 'message' => 'Заявка зарегистрирована');
            echo json_encode($answer);
        }
        $answer = array('state' => 'error', 'message' => 'Ошибка! Не найдено физ. лицо! Напишите заявку на сайте в тех. поддержку!');
        echo json_encode($answer);

    }

    public function actionPrintApplication($id)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $id);
        $model = HostelApplication::model()->find($criteria);

        $sql = "SELECT distinct CONCAT_WS(' ', f.fam, f.nam, f.otc) fio, date_format(rogd, '%d.%m.%Y') rogd, 
                g.name gragd, s.gruppa , s.fak, s.npp, doc.pser, doc.pnom,  date_format(doc.pdat, '%d.%m.%Y') pasp_date, doc.pkem,
                CASE
                    WHEN f.mestogos = 1 AND mestokladr <> '0000000000000' THEN
                        CONCAT_WS(' ', kladr.street(f.mestokladr, f.mestogos), f.mestodom, f.mestokorp, f.mestokvart)
                    ELSE
                        CASE when (RIGHT(f.mestoadr, 1)) REGEXP '[0-9]+' THEN
                            CONCAT_WS(' ', f.mestoadr)
                            ELSE CONCAT_WS(' ', f.mestoadr, f.mestodom, f.mestokorp, f.mestokvart) END
                    END as                          passport_addr,

                    f.mestoindex        as                          passport_index
                , HEX(s.gal_srec) as gal_srec,
                s.gal_speccode,
                case when fak like 'фэо' or fak like 'Факультет элитного образования и магистратуры'  or fak like 'фэоим'
                    then (SELECT fak FROM skard s2 where s2.gal_speccode like
                                                                              CONCAT(LEFT(s.gal_speccode, 3), '03', RIGHT(s.gal_speccode, 3))
                and s2.fak not like 'изо' and s2.fak not like '%заочного обучения%'
                order by npp desc limit 1)
                ELSE s.fak
                end edu_fac
                FROM skard s
                left join fdata f on f.npp = s.fnpp
                LEFT JOIN identifydocuments doc on doc.npp= (select dd.npp from identifydocuments dd where f.npp = dd.fnpp order by dd.npp desc limit 1 )
                LEFT JOIN gragd g on g.id = f.gragd
                where s.fnpp =" . $model->fnpp . " AND (du >= NOW() or du is null)
                order by s.npp desc limit 1";
        $result = Yii::app()->db2->createCommand($sql)->queryRow();
        $curInfo = ApiKeyService::queryApi('getCurriculumInfoForHostel', array("nrec" => '0x'.$result['gal_srec']),  'a2f187fc-9c37-4b77-a7e9-07e5860c6739','GET')['json_data'][0];

        $model->dateBegin = $curInfo['begin'];
        $model->dateEnd = (date("m", strtotime($curInfo['end'])) == "08" and date("d",  strtotime($curInfo['end'])) == "31") ? date("y", strtotime($curInfo['end']))."-06-30" : $curInfo['end'];
        /*$model->edu_faculty = $curInfo['faculty'];
        $model->speciality = $curInfo['speciality'];

        $model->real_faculty = $result['fak'];*/


        $lgot = $model->lgot ? "Имею право на преимущественное заселение, отношусь к категории граждан, имеющих право на льготу. Обязуюсь при заселении предъявить документы, подтверждающие это право." : "";
        $phone = $model->phone ?: '__________________';
        $phone2 = $model->phone2 ?: '__________________';
        $dateBegin =  isset($model->dateBegin)  ? date("d.m.Y",  strtotime($curInfo['begin'])) : '"____"_________ 20___ ';
        $dateEnd = isset($model->dateEnd) ? date("d.m.Y",  strtotime($curInfo['end'])) : '"____"_________ 20___ ';
        $dateCreate = $model->dateCreate ? date("d.m.Y",  strtotime($model->dateCreate)) : '"____"_________ 20___ г.';
        $fio_pr = $model->fio_pr ?: '________________________________';
        $model->edu_faculty = $result['edu_fac'];
        $model->speciality = $result['gal_speccode'];
        $model->real_faculty = $result['fak'];
        $model->sector = HostelDistribution::getSector($model->edu_faculty);

        require_once 'protected/extensions/PhpWord/Autoloader.php';
        Autoloader::register();

        $phpWord = new  PhpWord();
        $document = new TemplateProcessor('protected/files/hostelApplication.docx');

        $document->setValue('fio', $result['fio']);
        $document->setValue('rogd', $result['rogd']);
        $document->setValue('group', $result['gruppa']);
        $document->setValue('faculty', $result['fak']);
        $document->setValue('pser', $result['pser']);
        $document->setValue('pnum', $result['pnom']);
        $document->setValue('pdate', $result['pasp_date']);
        $document->setValue('pkem', $result['pkem']);
        $document->setValue('gragd', $result['gragd']);
        $document->setValue('index', $result['passport_index']);
        $document->setValue('address', $result['passport_addr']);
        $document->setValue('phone', $phone);
        $document->setValue('phone2', $phone2);
        $document->setValue('fio_pr', $fio_pr);
        $document->setValue('dateBegin', $dateBegin);
        $document->setValue('dateEnd', $dateEnd);
        $document->setValue('dateCreate', $dateCreate);
        $document->setValue('lgot', $lgot);
        $document->setValue('hostel', $model->sector?: '__');
        $document->setValue('spec', $model->speciality?: '______');
        $document->setValue('hostelAddr', HostelDistribution::getHostelAddress($model->sector));



        $filename = Yii::getPathOfAlias('webroot') . '/protected/runtime//' . md5(date('d.m.Y H:m:s')) . '.docx';

        $document->saveAs($filename);
        $model->filename = md5($model->fnpp. $model->dateCreate);
        $model->save();
        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . $model->filename . '.docx');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        unlink($filename);
        header("Location: https://omgtu.ru/ecab/up.php?hostel=1");

    }

    public function actionUpdateApplication($fnpp=null)
    {
        $sql = "SELECT id from hostel_application ha where fnpp in (765996)";
        $result = Yii::app()->db2->createCommand($sql)->queryAll();
        //var_dump($result);die;
        foreach ($result as $row)
            self::updateApplication($row['id']);
        }


    private function updateApplication($id)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $id);
        $model = HostelApplication::model()->find($criteria);

        $sql = "SELECT distinct CONCAT_WS(' ', f.fam, f.nam, f.otc) fio, date_format(rogd, '%d.%m.%Y') rogd, 
                g.name gragd, s.gruppa , s.fak, s.npp, doc.pser, doc.pnom,  date_format(doc.pdat, '%d.%m.%Y') pasp_date, doc.pkem,
                CASE
                    WHEN f.mestogos = 1 AND mestokladr <> '0000000000000' THEN
                        CONCAT_WS(' ', kladr.street(f.mestokladr, f.mestogos), f.mestodom, f.mestokorp, f.mestokvart)
                    ELSE
                        CASE when (RIGHT(f.mestoadr, 1)) REGEXP '[0-9]+' THEN
                            CONCAT_WS(' ', f.mestoadr)
                            ELSE CONCAT_WS(' ', f.mestoadr, f.mestodom, f.mestokorp, f.mestokvart) END
                    END as                          passport_addr,

                    f.mestoindex        as                          passport_index
                , HEX(s.gal_srec) as gal_srec,
                s.gal_speccode,
                case when fak like 'фэо' or fak like 'фэоим' then (SELECT fak FROM skard s2 where s2.gal_speccode like
                                                                              CONCAT(LEFT(s.gal_speccode, 3), '03', RIGHT(s.gal_speccode, 3))
                and s2.fak not like 'изо'
                order by npp desc limit 1)
                ELSE s.fak
                end edu_fac
                FROM skard s
                left join fdata f on f.npp = s.fnpp
                LEFT JOIN identifydocuments doc on f.npp = doc.fnpp
                LEFT JOIN gragd g on g.id = f.gragd
                where s.fnpp =" . $model->fnpp . " AND (du >= NOW() or du is null)
                order by s.npp desc limit 1";
        $result = Yii::app()->db2->createCommand($sql)->queryRow();
        var_dump($id);
        var_dump($result['gal_srec']);
        $curInfo = ApiKeyService::queryApi('getCurriculumInfoForHostel', array("nrec" => '0x'.$result['gal_srec']),  'a2f187fc-9c37-4b77-a7e9-07e5860c6739','GET')['json_data'][0];
        var_dump($curInfo);
        $model->dateBegin = $curInfo['begin'];
        $model->dateEnd = (date("m", strtotime($curInfo['end'])) == "08" and date("d",  strtotime($curInfo['end'])) == "31") ? date("y", strtotime($curInfo['end']))."-06-30" : $curInfo['end'];
        /*$model->edu_faculty = $curInfo['faculty'];
        $model->speciality = $curInfo['speciality'];
        $model->sector = HostelDistribution::getSector($model->edu_faculty);
        $model->real_faculty = $result['fak'];
        $curInfo['begin'] = '2021-09-01';
        $curInfo['end'] = '2022-06-30';*/
        $model->edu_faculty = $result['edu_fac'];

        $model->speciality = $result['gal_speccode'];
        $model->real_faculty = $result['fak'];
        $model->sector = HostelDistribution::getSector($model->edu_faculty);


        $model->filename = md5($model->fnpp. $model->dateCreate);
        $model->save();

    }
}
