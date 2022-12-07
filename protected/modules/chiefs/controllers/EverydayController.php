<?php

class EverydayController extends Controller
{

    public $layout = '//layouts/column1';

    public function actionIndex($id = null)
    {
        if (!Yii::app()->user->getFnpp() || is_null($id)) {
            $this->redirect(array('/chiefs'));
        }
        $fnpp = Yii::app()->user->getFnpp();
        if (
        !(in_array($id, MonitorAccess::getStructuresByFnpp($fnpp)))
        ) {
            if (!($sql = Yii::app()->db2->createCommand()
                ->selectDistinct('*')
                ->from('wkardc_rp w')
                ->where('w.struct = ' . $id . ' AND w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL) AND w.fnpp = ' . Yii::app()->user->getFnpp())
                ->queryRow()
            )) {
                $this->redirect(array('/chiefs'));
            }
        }

        if ($_POST) {
            return $this->updateDatabase();
        }

        $departmentName = Yii::app()->db2->createCommand()
            ->select('struct_getpath2_rp(w.struct)')
            ->from('wkardc_rp w')
            ->where('w.struct = ' . $id)
            ->queryScalar();
        $status = $this->checkStatus($id);
        $filter = new FilterEverydayForm();
        $session = new CHttpSession();
        $session->open();

        if (isset($_GET['FilterEverydayForm'])) {
            $session['FilterEverydayForm'] = $_GET['FilterEverydayForm'];
            $filter->filters = $_GET['FilterEverydayForm'];
        } elseif (isset($session['FilterEverydayForm']) && $session['FilterEverydayForm']) {
            $filter->filters = $session['FilterEverydayForm'];
        }
        $session->close();
        $this->render('everyday', array(
            'filter' => $filter,
            'id' => $id,
            'departmentName' => $departmentName,
            'status' => $status
        ));
    }

    private function updateDatabase()
    {

        if (isset($_POST['id'])) {
            $fnpp = Yii::app()->user->getFnpp();
            if (
                Yii::app()->db2->createCommand()
                    ->select('w.struct num, struct_getpath2_rp(w.struct) department')
                    ->from('wkardc_rp w')
                    ->where('w.prudal=\'0\' AND
                (w.vpo1cat LIKE \'рп\'  OR
                (w.vpo1cat LIKE \'итп\' AND w.dolgnost LIKE \'%начальник%\') OR                 
                (w.vpo1cat LIKE \'офицеры\' AND w.dolgnost LIKE \'%начальник%\') OR             
                (w.vpo1cat LIKE \'НР\' AND (w.dolgnost LIKE \'%начальник%\' OR w.dolgnost LIKE \'старший научный сотрудник\' OR w.dolgnost LIKE \'главный научный сотрудник\')) OR
                w.dolgnost LIKE \'%заведующий%\' OR 
                w.dolgnost LIKE \'%декан%\'  OR 
                w.dolgnost LIKE \'%директор%\'  OR 
                w.dolgnost LIKE \'%начальник%\') AND 
                fnpp = ' . $fnpp . ' AND 
                w.struct = ' . $_POST['id'])
                    ->queryAll()
                || (in_array($_POST['id'], MonitorAccess::getStructuresByFnpp($fnpp)))
            ) {
                $employees = Yii::app()->db2->createCommand()
                    ->selectDistinct('f.*')
                    ->from('wkardc_rp w')
                    ->join('fdata f', 'f.npp=w.fnpp')
                    ->where('w.struct in (' . (new FilterEverydayForm)->getSubstructures($_POST['id']) . ') and w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->order('f.fam')
                    ->queryAll();
                foreach ($employees as $employee) {
                    FilterEverydayForm::checkFnppInDatabase($employee['npp']);
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_week 
                        SET confirmedAt = CURRENT_DATE
                        WHERE fnpp = ' . $employee['npp'] . ' AND createdAt = CURRENT_DATE')->query();
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_day 
                        SET confirmedAt = CURRENT_DATE
                        WHERE fnpp = ' . $employee['npp'] . ' AND createdAt = CURRENT_DATE')->query();
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_covid 
                        SET confirmedAt = CURRENT_DATE
                        WHERE fnpp = ' . $employee['npp'] . ' AND createdAt = CURRENT_DATE')->query();
                }
                if ((new FilterEverydayForm())->getSubstructures($_POST['id'])) {
                    return true;
                }
            }
        } elseif (isset($_POST['fnpp'])) {
            $chiefFnpp = Yii::app()->user->getFnpp();
            $employeeFnpp = $_POST['fnpp'];
            if ((new FilterEverydayForm)->isItMyChief($employeeFnpp, $chiefFnpp)) {
                FilterEverydayForm::checkFnppInDatabase($employeeFnpp);
                if (isset($_POST['status'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_day 
                        SET status = ' . $_POST['status'] . '
                        WHERE fnpp = ' . $employeeFnpp . ' AND createdAt = CURRENT_DATE')->query();
                }
                if (isset($_POST['country'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_day 
                        SET country = \'' . (($_POST['country'] != '') ? $_POST['country'] : '') . '\'
                        WHERE fnpp = ' . $employeeFnpp . ' AND createdAt = CURRENT_DATE')->query();
                }
                if (isset($_POST['bool'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_day 
                        SET wasAbroad = ' . $_POST['bool'] . '
                        WHERE fnpp = ' . $employeeFnpp . ' AND createdAt = CURRENT_DATE')->query();
                }
                if (isset($_POST['country2'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_day 
                        SET country2 = \'' . (($_POST['country2'] != '') ? $_POST['country2'] : '') . '\'
                        WHERE fnpp = ' . $employeeFnpp . ' AND createdAt = CURRENT_DATE')->query();
                }
                if (isset($_POST['additional'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_day 
                        SET additional = \'' . (($_POST['additional'] != '') ? $_POST['additional'] : '') . '\'
                        WHERE fnpp = ' . $employeeFnpp . ' AND createdAt = CURRENT_DATE')->query();
                }
                if (isset($_POST['format'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_week 
                        SET format = ' . $_POST['format'] . '
                        WHERE fnpp = ' . $employeeFnpp . ' AND createdAt  = CURRENT_DATE')->query();
                }
                if (isset($_POST['reason'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_week
                        SET reason = \'' . (($_POST['reason'] != '') ? $_POST['reason'] : '') . '\'
                        WHERE fnpp = ' . $employeeFnpp . ' AND createdAt  = CURRENT_DATE')->query();
                }
                if (isset($_POST['reasonId'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_week
                        SET reasonId = ' . $_POST['reasonId'] . '
                        WHERE fnpp = ' . $employeeFnpp . ' AND createdAt  = CURRENT_DATE')->query();
                }
                if (isset($_POST['category'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_category
                        SET category = ' . $_POST['category'] . '
                        WHERE fnpp = ' . $employeeFnpp)->query();
                }
                if (isset($_POST['covidStatus'])) {
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_covid
                        SET covidStatus = ' . $_POST['covidStatus'] . '
                        WHERE fnpp = ' . $employeeFnpp)->query();
                }
                if (isset($_POST['date'])) {
                    echo date("Y-m-d", strtotime($_POST['date']));
                    //var_dump($_POST['date']);die;
                    Yii::app()->db->createCommand('UPDATE tbl_chief_reports_covid
                        SET date = \'' . date("Y-m-d", strtotime($_POST['date'])) . '\'
                        WHERE fnpp = ' . $employeeFnpp)->query();
                }
            }
        } elseif (isset($_POST['copy'])) {
            $npps = Yii::app()->db2->createCommand()
                ->select('GROUP_CONCAT(f.npp)')
                ->from('wkardc_rp w')
                ->join('fdata f', 'f.npp=w.fnpp')
                ->join('struct_d_rp s', 's.npp = w.struct')
                ->where('w.struct in (' . (new FilterEverydayForm())->getSubstructures($_GET['id']) . ')')
                ->andWhere('w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                ->andWhere('f.fam NOT LIKE \'%Test%\'')
                ->queryScalar();
            $date = date('Y-m-d', strtotime($_POST['copy']));
            var_dump($date);
            Yii::app()->db->createCommand(
                'DROP temporary table if exists t_temp;

                CREATE TEMPORARY TABLE `t_temp`
                as (
                SELECT
                fnpp, status, country, wasAbroad, country2, additional, CURRENT_DATE as createdAt 
                FROM tbl_chief_reports_day t
                WHERE t.fnpp IN (' . $npps . ')
                AND t.createdAt = \'' . $date . '\'
                );
                
                DELETE FROM tbl_chief_reports_day
                WHERE fnpp IN (' . $npps . ')
                AND createdAt = CURRENT_DATE;
                
                INSERT INTO tbl_chief_reports_day (fnpp, status, country, wasAbroad, country2, additional, createdAt)
                SELECT * FROM t_temp;
                
                DROP temporary table if exists t_temp;
                
                CREATE TEMPORARY TABLE `t_temp`
                as (
                SELECT
                fnpp, format, reasonId, reason, CURRENT_DATE as createdAt 
                FROM tbl_chief_reports_week t
                WHERE t.fnpp IN (' . $npps . ')
                AND t.createdAt = \'' . $date . '\'
                );
                
                DELETE FROM tbl_chief_reports_week
                WHERE fnpp IN (' . $npps . ')
                AND createdAt = CURRENT_DATE;
                
                INSERT INTO tbl_chief_reports_week (fnpp, format, reasonId, reason, createdAt)
                SELECT * FROM t_temp;
            ')->query();
            $sql = Yii::app()->db->createCommand('
             DROP temporary table if exists t_temp_covid;
                
                CREATE TEMPORARY TABLE `t_temp_covid`
                as (
                SELECT
                fnpp, covidStatus, t.date as \'date\', CURRENT_DATE as createdAt 
                FROM tbl_chief_reports_covid t
                WHERE t.fnpp IN (' . $npps . ')
                AND t.createdAt = \'' . $date . '\'
                );
                
                DELETE FROM tbl_chief_reports_covid
                WHERE fnpp IN (' . $npps . ')
                AND createdAt = CURRENT_DATE;
                
                INSERT INTO tbl_chief_reports_covid (fnpp, covidStatus, date, createdAt)
                SELECT * FROM t_temp_covid;
                ')->query();
            //var_dump($sql);

            if ((new FilterEverydayForm())->getSubstructures($_GET['id'])) {
                return true;
            }
        }
        return '';
    }

    private function checkStatus($id)
    {
        $status = true;
        $employees = Yii::app()->db2->createCommand()
            ->selectDistinct('f.*')
            ->from('wkardc_rp w')
            ->join('fdata f', 'f.npp=w.fnpp')
            ->where('w.struct in (' . (new FilterEverydayForm)->getSubstructures($id) . ') AND w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
            ->order('f.fam')
            ->queryAll();
        foreach ($employees as $employee) {
            if (!
            Yii::app()->db->createCommand()
                ->select('*')
                ->from('tbl_chief_reports_day')
                ->where('fnpp = ' . $employee['npp'] . ' AND confirmedAt = CURRENT_DATE')
                ->queryRow()
            ) {
                $status = false;
            }
        }
        return $status;
    }
}
