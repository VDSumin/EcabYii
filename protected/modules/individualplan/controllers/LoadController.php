<?php

class LoadController extends Controller
{
    public $menu = [
        /*[
            'label' => 'Назад',
            'url' => ['/individualplan'],
        ],*/
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
        [
            'label' => 'Скрыть меню',
            'url' => ['', 'menu' => 0]
        ],

    ];


    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }
    /**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
            array('allow',
                'actions' => array('showLoad',
                    'showdisactualload',
                    'showactualload',
                    'educmethLoad',
                    'orgmethLoad',
                    'researchLoad',
                    'educationalLoad',
                    'profworkLoad',
                    'addhoursplan',
                    'otherWork',
                    'summInSectionForAjax',
                    'summInSection',
                    'getFormConfirm',
                    'addmodalrate',
                    'addmodaltext',
                    'addmodallink',
                    'delmodallink',
                    'addmodalfile',
                    'viewfile',
                    'deletemodalfile'),
                'users' => array('*'),
                'expression' => 'Controller::checkfnpp()',
            ),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


	public function actionShowLoad($menu = true){
//	    var_dump(Yii::app()->session['chairNpp']);die;
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}
	    $chair = StructD_rp::model()->find('npp = :npp', [':npp' => Yii::app()->session['chairNpp']]);

	    $person = Fdata::model()->findByPk(Yii::app()->session['fnpp']);
        $totalHours = WorkloadPlanActual::getSummLoadByStuff();

        return $this->render('showLoad', [
            'person' => $person,
            'chair' => $chair->npp,
            'menu' => $menu,
            'totalHours' => $totalHours,
        ]);
    }

    public function actionShowactualload($menu = true){
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}
        $discipline = IndividualPlanForm::getInfoForChart();

        return $this->render('showActualLoad', [
            'discipline' => $discipline,
            'menu' => $menu,
        ]);
    }

    public function actionShowdisactualload($menu = true){
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}
        $model = new AttendanceSchedule('searchDis');
        $model->unsetAttributes();  // clear any default values
        $model->teacherFnpp = Yii::app()->session['fnpp'];
        $model->yearOfEducation = Yii::app()->session['yearEdu'];

        if (isset($_GET['AttendanceSchedule'])) {
            $model->attributes = $_GET['AttendanceSchedule'];
            $model->dateTimeEndOfClasses = $model->dateTimeEndOfClasses ? date('Y-m-d', strtotime($model->dateTimeEndOfClasses)) : $model->dateTimeEndOfClasses;
            $model->dateTimeStartOfClasses = $model->dateTimeStartOfClasses ? date('Y-m-d', strtotime($model->dateTimeStartOfClasses)) : $model->dateTimeStartOfClasses;
            $model->semesterStartDate = $model->semesterStartDate ? date('Y-m-d', strtotime($model->semesterStartDate)) : $model->semesterStartDate;
        }

        return $this->render('showDisActualLoad', [
            'model' => $model,
            'menu' => $menu,
        ]);
    }

    public function actionEducmethLoad($menu = true){
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}
        $chair = StructD_rp::model()->find('npp = :npp', [':npp' => Yii::app()->session['chairNpp']]);

        $person = Fdata::model()->findByPk(Yii::app()->session['fnpp']);
        $idFromCatalog = 1;
        $listWork = IndividualplanModule::listWorkToIndividualPlan($idFromCatalog);
        $year = Yii::app()->session['yearEdu'];
        $CanWriteArray = IndividualplanModule::CanWriteIndividualPlan($year, $chair['npp']);

       /* //удалить
        $CanWriteArray['openfact'] = '1';
        //var_dump($CanWriteArray);die;*/
        $breadcrumbs = array(
            'Индивидуальный план' => ['/individualplan'],
            'Структура плана' => ['default/struct', 'chair' => $chair->npp],
            'Учебно-методическая работа'
        );
        $titlepage = 'Учебно-методическая нагрузка преподавателя';
        return $this->render('individualPlanLoad', [
            'person' => $person,
            'chair' => $chair->npp,
            'listWork' => $listWork,
            'menu' => $menu,
            'idFromCatalog' => $idFromCatalog,
            'breadcrumbs' => $breadcrumbs,
            'titlepage' => $titlepage,
            'CanWriteArray' => $CanWriteArray
        ]);
    }

    public function actionOrgmethLoad($menu = true){
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}
        $chair = StructD_rp::model()->find('npp = :npp', [':npp' => Yii::app()->session['chairNpp']]);

        $person = Fdata::model()->findByPk(Yii::app()->session['fnpp']);
        $idFromCatalog = 16;
        $listWork = IndividualplanModule::listWorkToIndividualPlan($idFromCatalog);
        $year = Yii::app()->session['yearEdu'];
        $CanWriteArray = IndividualplanModule::CanWriteIndividualPlan($year, $chair['npp']);
        $CanWriteArray['openfact'] = '1';
        $breadcrumbs = array(
            'Индивидуальный план' => ['/individualplan'],
            'Структура плана' => ['default/struct', 'chair' => $chair->npp],
            'Организационно-методическая работа'
        );
        $titlepage = 'Организационно-методическая нагрузка преподавателя';
        return $this->render('individualPlanLoad', [
            'person' => $person,
            'chair' => $chair->npp,
            'listWork' => $listWork,
            'menu' => $menu,
            'idFromCatalog' => $idFromCatalog,
            'breadcrumbs' => $breadcrumbs,
            'titlepage' => $titlepage,
            'CanWriteArray' => $CanWriteArray
        ]);
    }

    public function actionResearchLoad($menu = true){
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}
        $chair = StructD_rp::model()->find('npp = :npp', [':npp' => Yii::app()->session['chairNpp']]);

        $person = Fdata::model()->findByPk(Yii::app()->session['fnpp']);
        $idFromCatalog = 38;
        $listWork = IndividualplanModule::listWorkToIndividualPlan($idFromCatalog);
        $year = Yii::app()->session['yearEdu'];
        $CanWriteArray = IndividualplanModule::CanWriteIndividualPlan($year, $chair['npp']);
        $CanWriteArray['openfact'] = '1';
        $breadcrumbs = array(
            'Индивидуальный план' => ['/individualplan'],
            'Структура плана' => ['default/struct', 'chair' => $chair->npp],
            'Научно-исследовательская работа'
        );
        $titlepage = 'Научно-исследовательская нагрузка';
        return $this->render('individualPlanLoad', [
            'person' => $person,
            'chair' => $chair->npp,
            'listWork' => $listWork,
            'menu' => $menu,
            'idFromCatalog' => $idFromCatalog,
            'breadcrumbs' => $breadcrumbs,
            'titlepage' => $titlepage,
            'CanWriteArray' => $CanWriteArray
        ]);
    }

    public function actionEducationalLoad($menu = true){
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}
        $chair = StructD_rp::model()->find('npp = :npp', [':npp' => Yii::app()->session['chairNpp']]);

        $person = Fdata::model()->findByPk(Yii::app()->session['fnpp']);
        $idFromCatalog = 64;
        $listWork = IndividualplanModule::listWorkToIndividualPlan($idFromCatalog);
        $year = Yii::app()->session['yearEdu'];
        $CanWriteArray = IndividualplanModule::CanWriteIndividualPlan($year, $chair['npp']);
        $CanWriteArray['openfact'] = '1';
        $breadcrumbs = array(
            'Индивидуальный план' => ['/individualplan'],
            'Структура плана' => ['default/struct', 'chair' => $chair->npp],
            'Учебно-воспитательная работа'
        );
        $titlepage = 'Учебно-воспитательная нагрузка';
        return $this->render('individualPlanLoad', [
            'person' => $person,
            'chair' => $chair->npp,
            'listWork' => $listWork,
            'menu' => $menu,
            'idFromCatalog' => $idFromCatalog,
            'breadcrumbs' => $breadcrumbs,
            'titlepage' => $titlepage,
            'CanWriteArray' => $CanWriteArray
        ]);
    }

    public function actionProfworkLoad($menu = true){
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}
        $chair = StructD_rp::model()->find('npp = :npp', [':npp' => Yii::app()->session['chairNpp']]);

        $person = Fdata::model()->findByPk(Yii::app()->session['fnpp']);
        $idFromCatalog = 69;
        $listWork = IndividualplanModule::listWorkToIndividualPlan($idFromCatalog);
        $year = Yii::app()->session['yearEdu'];
        $CanWriteArray = IndividualplanModule::CanWriteIndividualPlan($year, $chair['npp']);
        $CanWriteArray['openfact'] = '1';
        $breadcrumbs = array(
            'Индивидуальный план' => ['/individualplan'],
            'Структура плана' => ['default/struct', 'chair' => $chair->npp],
            'Профориентационная работа и довузовская подготовка'
        );
        $titlepage = 'Нагрузка преподавателя по профориентационной и довузовская подготовке';
        return $this->render('individualPlanLoad', [
            'person' => $person,
            'chair' => $chair->npp,
            'listWork' => $listWork,
            'menu' => $menu,
            'idFromCatalog' => $idFromCatalog,
            'breadcrumbs' => $breadcrumbs,
            'titlepage' => $titlepage,
            'CanWriteArray' => $CanWriteArray
        ]);
    }

    public function actionAddhoursplan() {
        $value = $_POST['value'];
        $id = $_POST['id'];
        $kindOfLoad = $_POST['kind'];
        $fnpp = $_POST['fnpp'];
        $year = $_POST['year'];
        $chair = $_POST['chair'];
        $chairNrec = Yii::app()->session['chairNrec'];
        $value = str_replace(',', '.', $value);
        if(is_numeric($value)) {
            $nrec = Yii::app()->db2->createCommand('SELECT ipf.id FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = ' . $fnpp . ' AND ipf.kind = ' . $id . ' AND ipf.chair = ' . $chair
                . ' AND ipf.year = ' . $year . ' AND ipf.kindOfLoad = ' . $kindOfLoad)->queryScalar();
            $block = Yii::app()->db2->createCommand('SELECT ipf.isBlock FROM individualplan_planned_fixation ipf WHERE ipf.fnpp = ' . $fnpp . ' AND ipf.kind = ' . $id . ' AND ipf.chair = ' . $chair
                . ' AND ipf.year = ' . $year . ' AND ipf.kindOfLoad = ' . $kindOfLoad)->queryScalar();
            //var_dump($nrec);die;
            if($block == 0) {
                if ($nrec) {
                    $sql = Yii::app()->db2->createCommand()->update('individualplan_planned_fixation', array(
                        'hours' => $value,
                    ), 'id =' . $nrec);
                } else {
                    $sql = Yii::app()->db2->createCommand()->insert('individualplan_planned_fixation', array(
                        'fnpp' => $fnpp,
                        'kind' => $id,
                        'hours' => $value,
                        'year' => $year,
                        'chair' => $chair,
                        'kindOfLoad' => $kindOfLoad,
                    ));
                }
                $result = true;
            }else{$result = false;}
            $sql = 'SELECT COALESCE(SUM(CASE WHEN ipf.status = 1 THEN COALESCE(ipf.correctHours, 0) ELSE ipf.hours END), 0)
              FROM individualplan_catalog ic 
              LEFT JOIN individualplan_planned_fixation ipf ON ic.id = ipf.Kind AND ic.year = ipf.year AND ipf.kindOfLoad = '. $kindOfLoad .' 
              AND ipf.fnpp = ' . $fnpp . ' 
              AND ipf.chair = ' . $chair . '
              WHERE ic.parent =' . $_POST['idCatalog'] . ' AND ic.year = ' . $year;

                $item = Yii::app()->db2->createCommand($sql)->queryScalar();
            $sql = 'SELECT ROUND(tt.load+(tt.stavka*60) ,2) \'load\', 1440*tt.stavka \'needload\'
              FROM (SELECT DISTINCT f.npp,  CONCAT_WS(\' \', f.fam, f.nam, f.otc) \'fio\',
              COALESCE((SELECT ier.rate FROM individualplan_employee_rate ier
                WHERE ier.fnpp = f.npp AND ier.chair = sdr.npp AND ier.year = '.$year.' ORDER BY ier.date DESC LIMIT 1),
                (SELECT SUM(wrq.stavka) FROM wkardc_rp wrq 
                WHERE wrq.fnpp = f.npp AND wrq.struct = sdr.npp AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') and wrq.prudal = 0 )) \'stavka\',
              0.75*(SELECT COALESCE(SUM(CONVERT(ww.valueOfLoad, DOUBLE)), 0) FROM workload_plan_actual ww WHERE ww.fnpp = f.npp AND ww.yearOfLoad = '.$year.' 
              AND ww.kindOfLoad = '.$kindOfLoad.' AND ww.chairNrec = '.CMisc::_id(bin2hex($chairNrec)).') +
              (SELECT COALESCE(SUM(CASE WHEN ipf.status = 1 THEN COALESCE(ipf.correctHours, 0) ELSE ipf.hours END), 0) FROM individualplan_planned_fixation ipf 
                LEFT JOIN individualplan_catalog ic ON ic.id = ipf.kind 
                WHERE ipf.fnpp = f.npp AND ipf.chair = sdr.npp AND ipf.year = '.$year.' AND ipf.kindOfLoad = '. $kindOfLoad .') \'load\'
              FROM fdata f
              LEFT JOIN wkardc_rp wr ON wr.fnpp = f.npp AND (wr.prudal = 0 OR (wr.prudal = 1 AND wr.du > NOW() ))
              LEFT JOIN struct_d_rp sdr ON sdr.npp = wr.struct WHERE f.npp = '.$fnpp.' AND wr.struct = '.$chair.') AS tt';


            $currentload = Yii::app()->db2->createCommand($sql)->queryRow();
            echo CJSON::encode(array('success' => $result, 'valueT' => $item, 'currentload' => $currentload));
        }else{
            echo CJSON::encode(array('success' => false));
        }
    }

    public function actionOtherWork($menu = true){
        if($menu){
            $this->layout='//layouts/column2';
            $this->menu=array_merge( [['label' => 'Назад','url' => ['/individualplan/default/struct', 'chair'=>Yii::app()->session['chairNpp']] ]],$this->menu);
        }else{$this->layout='//layouts/column1';}

        $person = Fdata::model()->findByPk(Yii::app()->session['fnpp']);
        $fnpp = Yii::app()->session['fnpp'];
        $year = Yii::app()->session['yearEdu'];
        $chair = Yii::app()->session['chairNpp'];
        $chairNrec = Yii::app()->session['chairNrec'];

        $sql = 'SELECT *, CASE WHEN tt.educload > 0 THEN 60*tt.stavka ELSE 0 END \'peremen\', \'Перемены\' AS \'kind\', \'60 часов на ставку\' AS \'norm\' FROM(
              SELECT DISTINCT f.npp,  CONCAT_WS(\' \', f.fam, f.nam, f.otc) \'fio\',
              COALESCE((SELECT ier.rate FROM individualplan_employee_rate ier
                WHERE ier.fnpp = f.npp AND ier.chair = sdr.npp AND ier.year = '.$year.' ORDER BY ier.date DESC LIMIT 1),
                (SELECT SUM(wrq.stavka) FROM wkardc_rp wrq 
                WHERE wrq.fnpp = f.npp AND wrq.struct = sdr.npp AND (wrq.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wrq.dolgnost LIKE \'%преподават%\') and wrq.prudal = 0 )) \'stavka\',
              (SELECT COALESCE(SUM(CONVERT(ww.valueOfLoad, DOUBLE)), 0) FROM workload_plan_actual ww WHERE ww.fnpp = f.npp AND ww.yearOfLoad = '.$year.' 
              AND ww.kindOfLoad = 1 AND ww.chairNrec = '.CMisc::_id(bin2hex($chairNrec)).') \'educload\'
              FROM fdata f
              LEFT JOIN wkardc_rp wr ON wr.fnpp = f.npp AND (wr.prudal = 0 OR (wr.prudal = 1 AND wr.du > NOW() ))
              LEFT JOIN struct_d_rp sdr ON sdr.npp = wr.struct WHERE f.npp = '.Yii::app()->session['fnpp'].' AND wr.struct = '.$chair.') AS tt';

        $rate = Yii::app()->db2->createCommand($sql)->queryRow();

        return $this->render('otherWork', [
            'person' => $person,
            'chair' => $chair,
            'rate' => $rate,
            'menu' => $menu,
        ]);
    }

    public static function summInSection($idFromCatalog = 0, $kind = 1){
        $fnpp = Yii::app()->session['fnpp'];
        $year = Yii::app()->session['yearEdu'];
        $chair = Yii::app()->session['chairNpp'];
        $sql = 'SELECT COALESCE(SUM(CASE WHEN ipf.status = 1 THEN COALESCE(ipf.correctHours, 0) ELSE ipf.hours END), 0)
          FROM individualplan_catalog ic 
          LEFT JOIN individualplan_planned_fixation ipf ON ic.id = ipf.Kind AND ic.year = ipf.year 
          AND ipf.fnpp = '.$fnpp.' 
          AND ipf.chair = '.$chair.'
          WHERE ic.parent ='.$idFromCatalog.' AND ic.year = '.$year .' AND ipf.kindOfLoad = '.$kind;

        $item = Yii::app()->db2->createCommand($sql)->queryScalar();
        return $item;
    }

    public function actionsummInSectionForAjax(){
	    if($_POST['value']) {
            $fnpp = Yii::app()->session['fnpp'];
            $year = Yii::app()->session['yearEdu'];
            $chair = Yii::app()->session['chairNpp'];
            $sql = 'SELECT  COALESCE(SUM(CASE WHEN ipf.status = 1 THEN COALESCE(ipf.correctHours, 0) ELSE ipf.hours END), 0)
          FROM individualplan_catalog ic 
          LEFT JOIN individualplan_planned_fixation ipf ON ic.id = ipf.Kind AND ic.year = ipf.year 
          AND ipf.fnpp = ' . $fnpp . ' 
          AND ipf.chair = ' . $chair . '
          WHERE ic.parent =' . $_POST['value'] . ' AND ic.year = ' . $year;

            $item = Yii::app()->db2->createCommand($sql)->queryScalar();
            echo CJSON::encode(array('success' => true, 'valueT' => $item));
        }else{
            echo CJSON::encode(array('success' => false));
        }


    }

    public function actiongetFormConfirm(){
        if (isset($_POST) && is_array($_POST)) {
            $result = LoadClass::getFormConfirm($_POST);
            $sql = 'SELECT ic.name FROM individualplan_catalog ic WHERE ic.id = '.$_POST['id'].' AND ic.year = '.$_POST['year'];
            $title = Yii::app()->db2->createCommand($sql)->queryScalar();
        } else {
            $result = false;
        }

        echo CJSON::encode(array('success' => $result, 'title' => $title));
    }


    public function actionAddmodalrate(){
        $result = false;
        if (isset($_POST) && is_array($_POST)) {
            $sql = Yii::app()->db2->createCommand()->update('individualplan_confirm', array(
                'comments' => $_POST['val'],
            ), 'id =' . $_POST['id_conf']);
            $result = true;
        }

        echo CJSON::encode(array('success' => $result));
    }

    public function actionAddmodaltext(){
        $result = false;
        if (isset($_POST) && is_array($_POST)) {
            $sql = Yii::app()->db2->createCommand()->update('individualplan_confirm', array(
                'text' => $_POST['val'],
            ), 'id =' . $_POST['id_conf']);
            $result = true;
        }

        echo CJSON::encode(array('success' => $result));
    }

    public function actionAddmodallink(){
        $result = false;
        if (isset($_POST) && is_array($_POST)) {
            $id = $_POST['id'];
            if($_POST['id'] != ''){
                $sql = Yii::app()->db2->createCommand()->update('individualplan_confirm_link', array(
                    'text' => $_POST['val'],
                ), 'id =' . $_POST['id']);
            }else{
                $sql = Yii::app()->db2->createCommand()->insert('individualplan_confirm_link', array(
                    'cconfirm' => $_POST['id_conf'],
                    'text' => $_POST['val'],
                ));
                $sql = 'SELECT icl.id FROM individualplan_confirm_link icl WHERE icl.cconfirm = '.$_POST['id_conf'].' ORDER BY icl.id DESC';
                $id = Yii::app()->db2->createCommand($sql)->queryScalar();
            }
            $result = true;
        }

        echo CJSON::encode(array('success' => $result, 'id_val' => $id));
    }

    public function actionDelmodallink(){
        $result = false;
        if (isset($_POST) && is_array($_POST)) {
            if($_POST['id'] != ''){
                $sql = Yii::app()->db2->createCommand()->delete('individualplan_confirm_link', 'id =' . $_POST['id']);
            }
            $result = true;
        }

        echo CJSON::encode(array('success' => $result));
    }

    public function actionAddmodalfile(){
        $result = false;
        $error = '';
        $file_info = false;
        $maxsize = 10485760;
        if (isset($_POST) && is_array($_POST)) {
            $fnpp = Yii::app()->session['fnpp'];
            $mime = $_FILES['file']['type'];
            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $tmp_name = $_FILES['file']['tmp_name'];
            $wname = $_POST['fieldname'];
            $cconfirm = $_POST['cconfirm'];
            if($size>$maxsize){
                $error = 'Размер файла превышает максимально допустимый';
            }else{
                $f = fopen($tmp_name,"rb");
                $upload = fread($f,$size);
                fclose($f);
                //$upload = addslashes($upload);
                $sql = Yii::app()->db2->createCommand()->insert('individualplan_confirm_document', array(
                    'cconfirm' => $cconfirm,
                    'fnpp' => $fnpp,
                    'name' => $wname,
                    'nameFile' => $name,
                    'size' => $size,
                    'mime' => $mime,
                ));
                $result = ($sql?true:false);
                if($result) {
                    $sql = 'SELECT icl.id FROM individualplan_confirm_document icl WHERE icl.cconfirm = ' . $cconfirm . ' ORDER BY icl.id DESC';
                    $id = Yii::app()->db2->createCommand($sql)->queryScalar();
                    $sql = Yii::app()->db2->createCommand()->update('individualplan_confirm_files', array(
                        'file' => $upload,
                    ), 'id =' . $id);
                    $result = ($sql?true:false);
                    if($result){
                        $file_info['name'] = $wname;
                        $file_info['size'] = $size;
                        $file_info['id'] = $id;
                    }
                }
            }
        }

        echo CJSON::encode(array('success' => $result, 'main_err' => $error, 'file_info' => $file_info));
    }

    public function actionViewfile($id=false, $confirm=false){
        if($id) {
            $sql = 'SELECT icd.name, icd.cconfirm, icd.size, icd.mime, icd.nameFile, icf.file FROM individualplan_confirm_document icd INNER JOIN individualplan_confirm_files icf ON icf.id = icd.id '
                .'WHERE icd.id = ' . $id;
            $file = Yii::app()->db2->createCommand($sql)->queryRow();
            if($confirm == $file['cconfirm']) {
                header('Content-type: ' . $file['mime']);
                header("Content-Disposition: attachment; filename=\"" . $file['nameFile'] . "\"");
                header('Content-Length: ' . $file['size']);
                //Yii::$app->response->sendFile($file['file'])->send();
                echo $file['file'];
                die;
            }
        }else{
            echo '<center><h2>Файл не выбран</h2></center>';
        }

    }

    public function actionDeletemodalfile(){
        $result = false;
        if (isset($_POST) && is_array($_POST)) {
            $sql = 'SELECT icd.name, icd.cconfirm, icd.size, icd.mime, icd.nameFile, icf.file FROM individualplan_confirm_document icd INNER JOIN individualplan_confirm_files icf ON icf.id = icd.id '
                .'WHERE icd.id = ' . $_POST['id'];
            $file = Yii::app()->db2->createCommand($sql)->queryRow();
            if($_POST['cconfirm'] == $file['cconfirm']){
                $sql = Yii::app()->db2->createCommand()->delete('individualplan_confirm_document', 'id =' . $_POST['id']);
                $result = true;
            }else{
                $result = false;
            }

            $result = true;
        }
        echo CJSON::encode(array('success' => $result));
    }



}
