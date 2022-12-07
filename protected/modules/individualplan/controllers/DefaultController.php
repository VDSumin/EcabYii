<?php

class DefaultController extends Controller {

    public $layout = '//layouts/column2';

    public function actionIndex() {
        $form = new IndividualPlanForm();
        $date = getdate();
        if (($date['mon'] >= 1) && ($date['mon'] <= 5)) { //теперь след год доступен с июня
            $year[strval($date['year'] - 1)] = strval($date['year'] - 1);
        } else {
            $year[strval($date['year'])] = strval($date['year']);
        }
        $yearList = $form->getYearFromLoad();
        $yearList += $year;
        krsort($yearList);

        if (!Yii::app()->session['yearEdu']){
            if ($yearList){
                Yii::app()->session['yearEdu'] = reset($yearList);
            } else {
                Yii::app()->session['yearEdu'] = date('Y');
            }
        }

        $this->render('index', [
            'menu' => $form->getAppointmentsForMenu(),
            'yearEdu' => Yii::app()->session['yearEdu'],
            'yearList' => $yearList
        ]);
    }

    public function actionStruct($chair) {
        Yii::app()->session['chairNpp'] = $chair;
        $chair = StructD_rp::model()->findByPk($chair);
        $this->menu = [
            [
                'label' => 'Назад',
                'url' => ['/individualplan'],
            ],
            [
                'label' => 'Учебная нагрузка',
                'url' => ['load/showLoad'],
            ],
            [
                'label' => 'Учебно-методическая работа',
                'url' => ['load/educmethLoad'],
            ],
            [
                'label' => 'Организационно-методическая работа',
                'url' => ['load/orgmethLoad']
            ],
            [
                'label' => 'Научно-исследовательская работа',
                'url' => ['load/researchLoad']
            ],
            [
                'label' => 'Учебно-воспитательная работа',
                'url' => ['load/educationalLoad']
            ],
            [
                'label' => 'Профориентационная работа и довузовская подготовка',
                'url' => ['load/profworkLoad']
            ],
            [
                'label' => 'Прочие (перемены)',
                'url' => ['load/otherWork']
            ],

        ];

        //var_dump($chair);die;
        $model = Catalog::model()->find('name LIKE \''.$chair->name.'\' and sdopinf = \'К\' and (datok = 0 or datok > '.CMisc::toGalDate(date('Y-m-d')).')');
        $chairNrec = $model->nrec;
/*
        $chairNrec = null;
        if (preg_match("/\"(.*?)\"/",$chair->name, $matches)){
            $model = Catalog::model()->find('name LIKE \'%'.$matches[1].'%\' and sdopinf = \'К\' and (datok = 0 or datok > '.CMisc::toGalDate(date('Y-m-d')).')');
            //$model = Catalog::model()->find('name = \''.$matches[1].'\' and sdopinf = \'К\'');
            if ($model instanceof Catalog){
                //var_dump(bin2hex($model->nrec));die;
                $chairNrec = $model->nrec;
            }
        }*/

        Yii::app()->session['chairNrec'] = $chairNrec;
        if (!isset(Yii::app()->session['fnpp'])){ return Yii::app()->controller->redirect(['/site/index']); }
        $totalLoad = WorkloadPlanActual::getSummLoadByStuff();
        $year = Yii::app()->session['yearEdu'];
        $sql = 'SELECT ier.rate FROM individualplan_employee_rate ier WHERE ier.fnpp = '.Yii::app()->session['fnpp'].' 
        AND ier.chair = '.Yii::app()->session['chairNpp'].' AND ier.year = '.Yii::app()->session['yearEdu']. ' ORDER BY ier.date DESC LIMIT 1';
        $rate['rate'] = Yii::app()->db2->createCommand($sql)->queryScalar();

        $sql = 'SELECT ier.isBlock FROM individualplan_employee_rate ier WHERE ier.fnpp = '.Yii::app()->session['fnpp'].' 
        AND ier.chair = '.Yii::app()->session['chairNpp'].' AND ier.year = '.Yii::app()->session['yearEdu']. ' ORDER BY ier.date DESC LIMIT 1';
        $rate['block'] = Yii::app()->db2->createCommand($sql)->queryScalar();

        $sql = 'SELECT SUM(wrq.stavka) FROM wkardc_rp wrq WHERE wrq.fnpp = '.Yii::app()->session['fnpp'].' AND wrq.struct = '.Yii::app()->session['chairNpp'].' 
          AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') AND (wrq.prudal = 0 OR (wrq.prudal = 1 AND wrq.du > \'01-09-'.$year.'\' ))';
        $rate['stavka'] = Yii::app()->db2->createCommand($sql)->queryScalar();


        $sql = 'SELECT *, CASE WHEN tt.educload > 0 THEN 60*tt.stavka ELSE 0 END \'peremen\' FROM(SELECT DISTINCT f.npp,  CONCAT_WS(\' \', f.fam, f.nam, f.otc) \'fio\',
              (SELECT COALESCE(SUM(ipf.hours), 0) FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1  ) \'sumH\', 
              (SELECT COALESCE(SUM(ipf.hours), 0) FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1 AND ipf.status = 1) \'sumSuccesH\',
              COALESCE((SELECT ier.rate FROM individualplan_employee_rate ier
                WHERE ier.fnpp = f.npp AND ier.chair = sdr.npp AND ier.year = '.$year.' ORDER BY ier.date DESC LIMIT 1),
                (SELECT SUM(wrq.stavka) FROM wkardc_rp wrq 
                WHERE wrq.fnpp = f.npp AND wrq.struct = sdr.npp AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') 
                and (wrq.prudal = 0 OR (wrq.prudal = 1 AND wrq.du > \'01-09-'.$year.'\' )) )) \'stavka\',
              (SELECT COALESCE(SUM(CONVERT(ww.valueOfLoad, DOUBLE)), 0) FROM workload_plan_actual ww WHERE ww.fnpp = f.npp AND ww.yearOfLoad = '.$year.' 
              AND ww.kindOfLoad = 1 AND ww.chairNrec = '.CMisc::_id(bin2hex($chairNrec)).') \'educload\',
           (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 1 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'educmethload\',
              (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 16 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'orgmethload\',
             (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 38 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'reseachload\',
            (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 64 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'educationalload\',
             (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 69 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'profworkload\'
              FROM fdata f
              LEFT JOIN wkardc_rp wr ON wr.fnpp = f.npp AND (wr.prudal = 0 OR (wr.prudal = 1 AND wr.du > \'01-09-'.$year.'\' ))
              LEFT JOIN struct_d_rp sdr ON sdr.npp = wr.struct WHERE f.npp = '.Yii::app()->session['fnpp'].' AND wr.struct = '.$chair->npp.') AS tt';

        $generalTable = Yii::app()->db2->createCommand($sql)->queryRow();

        $sql = 'SELECT *, CASE WHEN tt.educload > 0 THEN 60*tt.stavka ELSE 0 END \'peremen\' FROM(SELECT DISTINCT f.npp,  CONCAT_WS(\' \', f.fam, f.nam, f.otc) \'fio\',
              (SELECT COALESCE(SUM(ipf.hours), 0) FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2  ) \'sumH\', 
              (SELECT COALESCE(SUM(ipf.hours), 0) FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2 AND ipf.status = 1) \'sumSuccesH\',
              COALESCE((SELECT ier.rate FROM individualplan_employee_rate ier
                WHERE ier.fnpp = f.npp AND ier.chair = sdr.npp AND ier.year = '.$year.' ORDER BY ier.date DESC LIMIT 1),
                (SELECT SUM(wrq.stavka) FROM wkardc_rp wrq 
                WHERE wrq.fnpp = f.npp AND wrq.struct = sdr.npp AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') 
                and (wrq.prudal = 0 OR (wrq.prudal = 1 AND wrq.du > \'01-09-'.$year.'\' )) )) \'stavka\',
              (SELECT COALESCE(SUM(CONVERT(ww.valueOfLoad, DOUBLE)), 0) FROM workload_plan_actual ww WHERE ww.fnpp = f.npp AND ww.yearOfLoad = '.$year.' 
              AND ww.kindOfLoad = 2 AND ww.chairNrec = '.CMisc::_id(bin2hex($chairNrec)).') \'educload\',
           (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 1 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'educmethload\',
              (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 16 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'orgmethload\',
             (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 38 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'reseachload\',
            (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 64 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'educationalload\',
             (SELECT COALESCE(SUM(ipf.hours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 69 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'profworkload\'
              FROM fdata f
              LEFT JOIN wkardc_rp wr ON wr.fnpp = f.npp AND (wr.prudal = 0 OR (wr.prudal = 1 AND wr.du > \'01-09-'.$year.'\' ))
              LEFT JOIN struct_d_rp sdr ON sdr.npp = wr.struct WHERE f.npp = '.Yii::app()->session['fnpp'].' AND wr.struct = '.$chair->npp.') AS tt';

        $generalTable1 = Yii::app()->db2->createCommand($sql)->queryRow();



        $sql = 'SELECT *, CASE WHEN tt.educload > 0 THEN 60*tt.stavka ELSE 0 END \'peremen\' FROM(SELECT DISTINCT f.npp,  CONCAT_WS(\' \', f.fam, f.nam, f.otc) \'fio\',
              (SELECT COALESCE(SUM(ipf.correctHours), 0) FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'sumH\', 
              (SELECT COALESCE(SUM(ipf.correctHours), 0) FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1 AND ipf.status = 1) \'sumSuccesH\',
              COALESCE((SELECT ier.rate FROM individualplan_employee_rate ier
                WHERE ier.fnpp = f.npp AND ier.chair = sdr.npp AND ier.year = '.$year.' ORDER BY ier.date DESC LIMIT 1),
                (SELECT SUM(wrq.stavka) FROM wkardc_rp wrq 
                WHERE wrq.fnpp = f.npp AND wrq.struct = sdr.npp AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') 
                and (wrq.prudal = 0 OR (wrq.prudal = 1 AND wrq.du > \'01-09-'.$year.'\' )) )) \'stavka\',
              (SELECT COALESCE(SUM(CONVERT(ww.valueOfLoad, DOUBLE)), 0) FROM workload_plan_actual ww WHERE ww.fnpp = f.npp AND ww.yearOfLoad = '.$year.' 
              AND ww.kindOfLoad = 1 AND ww.chairNrec = '.CMisc::_id(bin2hex($chairNrec)).') \'educload\',
           (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 1 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'educmethload\',
              (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 16 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'orgmethload\',
             (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 38 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'reseachload\',
            (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 64 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'educationalload\',
             (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 69 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 1) \'profworkload\'
              FROM fdata f
              LEFT JOIN wkardc_rp wr ON wr.fnpp = f.npp AND (wr.prudal = 0 OR (wr.prudal = 1 AND wr.du > \'01-09-'.$year.'\' ))
              LEFT JOIN struct_d_rp sdr ON sdr.npp = wr.struct WHERE f.npp = '.Yii::app()->session['fnpp'].' AND wr.struct = '.$chair->npp.') AS tt';

        $generalTable2= Yii::app()->db2->createCommand($sql)->queryRow();

        $sql = 'SELECT *, CASE WHEN tt.educload > 0 THEN 60*tt.stavka ELSE 0 END \'peremen\' FROM(SELECT DISTINCT f.npp,  CONCAT_WS(\' \', f.fam, f.nam, f.otc) \'fio\',
              (SELECT COALESCE(SUM(ipf.correctHours), 0) FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'sumH\', 
              (SELECT COALESCE(SUM(ipf.correctHours), 0) FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2 AND ipf.status = 1) \'sumSuccesH\',
              COALESCE((SELECT ier.rate FROM individualplan_employee_rate ier
                WHERE ier.fnpp = f.npp AND ier.chair = sdr.npp AND ier.year = '.$year.' ORDER BY ier.date DESC LIMIT 1),
                (SELECT SUM(wrq.stavka) FROM wkardc_rp wrq 
                WHERE wrq.fnpp = f.npp AND wrq.struct = sdr.npp AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') 
                and (wrq.prudal = 0 OR (wrq.prudal = 1 AND wrq.du > \'01-09-'.$year.'\' )) )) \'stavka\',
              (SELECT COALESCE(SUM(CONVERT(ww.valueOfLoad, DOUBLE)), 0) FROM workload_plan_actual ww WHERE ww.fnpp = f.npp AND ww.yearOfLoad = '.$year.' 
              AND ww.kindOfLoad = 2 AND ww.chairNrec = '.CMisc::_id(bin2hex($chairNrec)).') \'educload\',
           (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 1 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'educmethload\',
              (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 16 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'orgmethload\',
             (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 38 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'reseachload\',
            (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 64 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'educationalload\',
             (SELECT COALESCE(SUM(ipf.correctHours), 0)  FROM individualplan_planned_fixation ipf LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
              WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ic.parent = 69 AND ipf.year = '.$year.' AND ipf.kindOfLoad = 2) \'profworkload\'
              FROM fdata f
              LEFT JOIN wkardc_rp wr ON wr.fnpp = f.npp AND (wr.prudal = 0 OR (wr.prudal = 1 AND wr.du > \'01-09-'.$year.'\' ))
              LEFT JOIN struct_d_rp sdr ON sdr.npp = wr.struct WHERE f.npp = '.Yii::app()->session['fnpp'].' AND wr.struct = '.$chair->npp.') AS tt';

        $generalTable3= Yii::app()->db2->createCommand($sql)->queryRow();

        //var_dump($generalTable);die;
        $this->render('struct', [
            'chair' => $chair,
            'year' => Yii::app()->session['yearEdu'],
            'totalLoad' => $totalLoad,
            'rate' => $rate,
            'generalTable' => $generalTable,
            'generalTable1' => $generalTable1,
            'generalTable2' => $generalTable2,
            'generalTable3' => $generalTable3
        ]);
    }

    public function actionUpdateYearEdu(){
        $result = null;
        if (isset($_POST)) {
            foreach ($_POST as $one){

                Yii::app()->session['yearEdu'] = $one;
            }

            $result = true;

        }

        echo CJSON::encode(array('success' => $result));
    }

    public function actionAddRate() {
        $value = $_POST['value'];
        $fnpp = $_POST['fnpp'];
        $year = $_POST['year'];
        $chair = $_POST['chair'];
        $value = str_replace(',', '.', $value);
        if(is_numeric($value)) {
            $nrec = Yii::app()->db2->createCommand('SELECT ier.id FROM individualplan_employee_rate ier WHERE ier.fnpp = ' . $fnpp
                . ' AND ier.chair = ' . $chair
                . ' AND ier.year = ' . $year)->queryScalar();
            $block = Yii::app()->db2->createCommand('SELECT ier.isBlock FROM individualplan_employee_rate ier WHERE ier.fnpp = ' . $fnpp
                . ' AND ier.chair = ' . $chair
                . ' AND ier.year = ' . $year)->queryScalar();
            //var_dump($nrec, $block);die;
            if($block == 0 and $value <= 1.5) {
                if ($nrec) {
                    $sql = Yii::app()->db2->createCommand()->update('individualplan_employee_rate', array(
                        'rate' => $value,
                    ), 'id =' . $nrec);
                } else {
                    $sql = Yii::app()->db2->createCommand()->insert('individualplan_employee_rate', array(
                        'fnpp' => $fnpp,
                        'rate' => $value,
                        'year' => $year,
                        'chair' => $chair,
                    ));
                }
                $result = $sql;
            }else{$result = false;}
            echo CJSON::encode(array('success' => $result));
        }else{
            echo CJSON::encode(array('success' => false));
        }
    }


    public function actionCheckRate() {
        $sql = 'SELECT ier.id id, ier.rate rate, ier.isBlock block, ier.date \'date\' FROM individualplan_employee_rate ier WHERE ier.fnpp = '.$_POST['fnpp'].' 
            AND ier.chair = '.$_POST['chair'].' AND ier.year = '.$_POST['year'];
        $rate = Yii::app()->db2->createCommand($sql)->queryAll();
        if(!empty($rate)) {
            echo CJSON::encode(array('success' => true));
        }else{
            echo CJSON::encode(array('success' => false));
        }
    }

    /**
     * Функция для печати в виде pdf индивидуального плана на основе отчета fast report. Все данные формируются
     * из сессии.
     */
    public function actionLoadPdfOfPlanFromFR(){
        $fnpp = Yii::app()->session['fnpp'];
        $struct = Yii::app()->session['chairNpp'];
        $year = Yii::app()->session['yearEdu'];
        //$nrec = ;
        $chairNrec = bin2hex(Yii::app()->session['chairNrec']);
        //var_dump('http://olap.omgtu/cgi/list.pdf?templatefile=D:\report\IndPlan.fr3&fnpp='.$fnpp.'&struct='.$struct.'&year='.$year.'&chairNrec='.$chairNrec);die;

        header('Content-Type: application/pdf');

        echo file_get_contents('http://olap.omgtu/cgi/list.pdf?templatefile=D:\report\IndPlan.fr3&fnpp='.$fnpp.'&struct='.$struct.'&year='.$year.'&chairNrec='.$chairNrec);
    }


    public function actiongetFormRate(){
        if (isset($_POST) && is_array($_POST)) {
            $sql = 'SELECT ier.id id, ier.rate rate, ier.isBlock block, ier.date \'date\', ier.dateend, kindOfLoad FROM individualplan_employee_rate ier WHERE ier.fnpp = '.$_POST['fnpp'].' 
            AND ier.chair = '.$_POST['chair'].' AND ier.year = '.$_POST['year'];
            $rate = Yii::app()->db2->createCommand($sql)->queryAll();
            $sql = 'SELECT SUM(wrq.stavka) FROM wkardc_rp wrq WHERE wrq.fnpp = '.Yii::app()->session['fnpp'].' AND wrq.struct = '.Yii::app()->session['chairNpp'].' 
            AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') AND wrq.prudal = 0';
            $currentStavka = Yii::app()->db2->createCommand($sql)->queryScalar();
            /*$result = '<div class="form-group">
            <div class="col-xs-3">Ставка на 01.09.'.$_POST['year'].'</div> 
            <div class="col-xs-2"><input type="text" class="form-control" value="'.$rate['rate'].'"></div>
            <input type="button" class="btn btn-default" value="Подтвердить"></div>';*/
            $result = '<h2 style="color: #ff0000;"><center>Проверьте корректность сведений</center></h2><hr />';
            $result .= '<table width="100%"><tr ><th><center>Общий объем ставки ППС по кафедре</center></th><th><center>Дата назначения</center></th>'
                .'<th><center>Дата окончания назначения</center></th><th><center>Тип показателя</center></th><th><center>Подтвердить</center></th></tr>';
            foreach ($rate as $value){
                $result .= '<tr>';
                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-6"><input type="text" class="form-control rate" '.(($value['block'])?'disabled="disabled"':'').' value="'.$value['rate'].'"></div>';
                $result .= '<input type="hidden" class="id" value="'.$value['id'].'">';
                $result .= '</td>';
                $rowdate = explode('-', $value['date']);
                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-9"><input type="text" class="form-control date" '.(($value['block'])?'disabled="disabled"':'').' name="date" value="'.date("d-m-Y", mktime(0, 0, 0, $rowdate[1], $rowdate[2], $rowdate[0])).'"></div></td>';
                $rowdate = explode('-', $value['dateend']);
                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-9"><input type="text" class="form-control dateend" '.(($value['block'])?'disabled="disabled"':'').' name="date" value="'.date("d-m-Y", mktime(0, 0, 0, $rowdate[1], $rowdate[2], $rowdate[0])).'"></div></td>';
                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><center><div class="panel-body" style="padding: 0px;">';

                if($value['block']) {
                    $result .= '<b>' . (($value['kindOfLoad']==1)?'Плановый':'Фактический') . '</b>';
                }else{
                    $result .= '<label><input type="radio" class="rowtypeRate1" name="typeRate' . $value['id'] . '" value="1" '.(($value['kindOfLoad']==1)?'checked':'').'> Плановый</label>';
                    $result .= '<label><input type="radio" class="rowtypeRate2" name="typeRate' . $value['id'] . '" value="2" '.(($value['kindOfLoad']==2)?'checked':'').'> Фактический</label>';
                }

                $result .= '</div></center></td>';
                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><center>'.(($value['block'])?'':'<input type="button" class="btn btn-default js-modal-update" value="Подтвердить">').'</center></td>';
                $result .= '</tr>';
            }

            for ($i = 1; $i <= 3; $i++){
                $result .= '<tr hidden="hidden" class="modal-hidden-row">';
                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-6"><input type="text" class="form-control rate" name="rate" value="'.$currentStavka.'"></div></td>';
                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-9"><input type="text" class="form-control date" name="date" value="'.'01-09-'.($_POST['year']).'"></div></td>';
                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-9"><input type="text" class="form-control dateend" name="date" value="'.'30-06-'.($_POST['year']+1).'"></div></td>';

                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><center><div class="panel-body" style="padding: 0px;">'
                    .'<label><input type="radio" class="rowtypeRate1" name="typeRatenew'.$i.'" value="1" checked> Плановый</label>'
                    .'<label><input type="radio" class="rowtypeRate2" name="typeRatenew'.$i.'" value="2" > Фактический</label>'
                    .'</div></center></td>';

                $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><center><input type="button" class="btn btn-default js-modal-save" name="button" value="Сохранить"></center></td>';
                $result .= '</tr>';
            }
            $result .= '</table>';
            $result .= '<br /><center><input type="button" class="btn btn-info js-modal-addRow" value="Добавить сведения об изменении ставки" onclick=""></center>';
        } else {
            $result = false;
        }

        echo CJSON::encode(array('success' => $result));
    }

    public function actiongetFormFirstRate(){
        if (isset($_POST) && is_array($_POST)) {
            $sql = 'SELECT ier.id id, ier.rate rate, ier.isBlock block, ier.date \'date\' FROM individualplan_employee_rate ier WHERE ier.fnpp = '.$_POST['fnpp'].' 
            AND ier.chair = '.$_POST['chair'].' AND ier.year = '.$_POST['year'].' ORDER BY ier.date';
            $rate = Yii::app()->db2->createCommand($sql)->queryAll();
            $sql = 'SELECT SUM(wrq.stavka) FROM wkardc_rp wrq WHERE wrq.fnpp = '.Yii::app()->session['fnpp'].' AND wrq.struct = '.Yii::app()->session['chairNpp'].' 
            AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') AND wrq.prudal = 0';
            $currentStavka = Yii::app()->db2->createCommand($sql)->queryScalar();

            $result = '<h2 style="color: #ff0000;"><center>Вам необходимо заполнить плановую ставку на <u>01.09.'.$_POST['year'].'</u></center></h2><hr />';
            //$result .= '<h4></h4>';
            $result .= '<table width="100%"><tr ><th><center>Общий объем ставки ППС по кафедре</center></th><th><center>Дата назначения</center></th>'
                .'<th><center>Дата окончания назначения</center></th><th><center>Подтвердить</center></th></tr>';

            $result .= '<tr class="modal-hidden-row">';
            $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-4"><input type="text" class="form-control rate" name="rate" value="'.$currentStavka.'"></div></td>';
            $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-8"><input type="text" class="form-control date" name="date" value="'.'01-09-'.$_POST['year'].'"></div></td>';
            $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><div class="col-xs-8"><input type="text" class="form-control dateend" name="date" value="'.'30-06-'.($_POST['year']+1).'"></div></td>';
            $result .= '<td style="padding-bottom: 10px; padding-top: 10px; display: none"><center><div class="panel-body" style="padding: 0px;">'
                .'<label><input type="radio" class="rowtypeRate1" name="typeRatenew" value="1" checked> Плановый</label>'
                .'<label><input type="radio" class="rowtypeRate2" name="typeRatenew" value="2" > Фактический</label>'
                .'</div></center></td>';
            $result .= '<td style="padding-bottom: 10px; padding-top: 10px;"><center><input type="button" class="btn btn-default js-modal-save" name="button" value="Сохранить"></center></td>';
            $result .= '</tr>';
            $result .= '</table>';
        } else {
            $result = false;
        }

        echo CJSON::encode(array('success' => $result));
    }


    public function actionAddmodalrate() {
        $fnpp = $_POST['fnpp'];
        $year = $_POST['year'];
        $chair = $_POST['chair'];
        $rate = $_POST['rate'];
        $date = $_POST['date'];
        $dateend = $_POST['dateend'];
        $kindOfLoad = $_POST['typerate'];
        $value = str_replace(',', '.', $rate);
        if(is_numeric($value) and $date != '') {
            if($value <= 1.5) {
                $rowdate = explode('-', $date);
                $date = date("Y-m-d", mktime(0, 0, 0, $rowdate[1], $rowdate[0], $rowdate[2]));
                $rowdate = explode('-', $dateend);
                $dateend = date("Y-m-d", mktime(0, 0, 0, $rowdate[1], $rowdate[0], $rowdate[2]));
                $sql = Yii::app()->db2->createCommand()->insert('individualplan_employee_rate', array(
                    'fnpp' => $fnpp,
                    'rate' => $value,
                    'year' => $year,
                    'chair' => $chair,
                    'kindOfLoad' => $kindOfLoad,
                    'date' => $date,
                    'dateend' => $dateend,
                    //'isBlock' => 1,
                ));
                $result = true;
            }else{$result = false;}
            echo CJSON::encode(array('success' => $result));
        }else{
            echo CJSON::encode(array('success' => false));
        }
    }

    public function actionUpdatemodalrate() {
        $fnpp = $_POST['fnpp'];
        $year = $_POST['year'];
        $chair = $_POST['chair'];
        $id = $_POST['id'];
        $rate = $_POST['rate'];
        $date = $_POST['date'];
        $dateend = $_POST['dateend'];
        $kindOfLoad = $_POST['typerate'];
        $value = str_replace(',', '.', $rate);
        if(is_numeric($value) and $date != '' and $id != '') {
            if($value <= 1.5) {
                $rowdate = explode('-', $date);
                $date = date("Y-m-d", mktime(0, 0, 0, $rowdate[1], $rowdate[0], $rowdate[2]));
                $rowdate = explode('-', $dateend);
                $dateend = date("Y-m-d", mktime(0, 0, 0, $rowdate[1], $rowdate[0], $rowdate[2]));
                $sql = Yii::app()->db2->createCommand()->update('individualplan_employee_rate', array(
                    'rate' => $value,
                    'kindOfLoad' => $kindOfLoad,
                    'date' => $date,
                    'dateend' => $dateend,
                    //'isBlock' => 1,
                ), 'id =' . $id);
                $result = true;
            }else{$result = false;}
            echo CJSON::encode(array('success' => $result));
        }else{
            echo CJSON::encode(array('success' => false));
        }
    }
}
