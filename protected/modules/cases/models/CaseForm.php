<?php

use PhpOffice\Common\Autoloader;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class CaseForm extends CFormModel
{
    public $model;
    const connectionString = 'odbc:Driver=FreeTDS;Server=galdb.omgtu;Port=1433;Database=OMGTU910;ClientCharset=CP1251;TDS_Version=8.0;Regional=no;';
//    const connectionString = 'odbc:OMGTU910'; //localhost
    const username = 'OMGTU910#up_omgtu';
    const pass = 'lP8aFnC0KKIj';

    /**
     * CaseForm constructor.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        $this->model = $this->GetUserModel();
        parent::__construct($scenario);
    }

    /**
     * @return mixed
     */
    private function GetUserModel()
    {
        return Yii::app()->user->getModel();
    }

    /**
     * @param $name
     * @return string|false
     */
    private function GetChairNrec($name)
    {
        if (preg_match("/\"(.*?)\"/", $name, $matches)) {
            $model = Catalog::model()->find('name LIKE \'%' . $matches[1] . '%\' and sdopinf = \'К\' and (datok = 0 or datok > ' . CMisc::toGalDate(date('Y-m-d')) . ')');
            if ($model instanceof Catalog) {
                return '0x' . bin2hex($model->nrec);
            }
        }
        return false;
    }

    /**
     * @param $fnpp
     * @return string
     */
    private static function GetFIO($fnpp)
    {
        $sql = Yii::app()->db2->createCommand()
            ->select('fam F, nam I, otc O')
            ->from(Fdata::model()->tableName())
            ->where('npp = ' . $fnpp)
            ->queryRow();
        if (!is_null($sql)) {
            $fioShort = mb_strtoupper(mb_substr($sql['F'], 0, 1)) . mb_strtolower(mb_substr($sql['F'], 1, mb_strlen($sql['F']))) . ' ';
            if (strlen($sql['I']) > 0) {
                $fioShort .= mb_substr($sql['I'], 0, 1) . '.';
            }
            if (strlen($sql['O']) > 0) {
                $fioShort .= mb_substr($sql['O'], 0, 1) . '.';
            }
            return $fioShort;
        } else {
            return '';
        }
    }

    /**
     * @param $fnpp
     * @param $chair
     * @return string|false
     */
    private static function GetPosition($fnpp, $chair)
    {
        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('wr.dolgnost')
            ->from(Wkardc_rp::model()->tableName() . ' wr')
            ->leftJoin(StructD_rp::model()->tableName() . ' sdr', 'sdr.npp = wr.struct')
            ->leftJoin('gal_catalogs gc', 'sdr.name LIKE CONCAT(\'%\',gc.name,\'%\')')
            ->where('wr.prudal != "1" AND sdr.prudal != 1 AND wr.fnpp = ' . $fnpp . ' AND gc.nrec = 0x' . $chair . ' AND (wr.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR wr.dolgnost LIKE \'%преподават%\')')
            ->queryRow();
        if ($sql) {

            return ($sql['dolgnost'] == 'заведующий кафедрой') ? '' : $sql['dolgnost'];
        } else {
            return false;
        }
    }

    /**
     * @param $chair
     * @return string|false
     */
    private static function GetChairFnpp($chair)
    {
        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('wr.fnpp')
            ->from(Wkardc_rp::model()->tableName() . ' wr')
            ->leftJoin(StructD_rp::model()->tableName() . ' sdr', 'sdr.npp = wr.struct')
            ->leftJoin('gal_catalogs gc', 'sdr.name LIKE CONCAT(\'%\',gc.name,\'%\')')
            ->where('wr.prudal != "1" AND sdr.prudal != 1 AND gc.nrec = 0x' . $chair . ' AND wr.dolgnost LIKE \'заведующий кафедрой\'')
            ->queryRow();
        if ($sql) {
            return $sql['fnpp'];
        } else {
            return false;
        }
    }

    /**
     * @param $fnpp
     * @return array|false
     */
    public function GetChairs($fnpp)
    {
        if ($fnpp) {
            $sql = Yii::app()->db2->createCommand()
                ->selectDistinct('structd_rp.name')
                ->from(Wkardc_rp::model()->tableName() . ' t')
                ->leftJoin(StructD_rp::model()->tableName() . ' structd_rp', 'structd_rp.npp = t.struct')
                ->where('t.prudal != "1" AND structd_rp.prudal != 1 AND t.fnpp = ' . $fnpp . ' AND (t.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR t.dolgnost LIKE \'%преподават%\')')
                ->queryAll();
        }

        if ($sql) {
            $hexArr = array();
            foreach ($sql as $item) {
                if ($this->GetChairNrec($item['name'])) {
                    $hexArr[] = $this->GetChairNrec($item['name']);
                }
            }
            return $hexArr;
        } else {
            return false;
        }

    }

    /**
     * @param $fnpp
     * @return array|false
     */
    public function GetChairsName($fnpp)
    {
        if ($fnpp) {
            $sql = Yii::app()->db2->createCommand()
                ->selectDistinct('structd_rp.name')
                ->from(Wkardc_rp::model()->tableName() . ' t')
                ->leftJoin(StructD_rp::model()->tableName() . ' structd_rp', 'structd_rp.npp = t.struct')
                ->where('structd_rp.prudal != 1 and ikafcode != \'\'');
            if (!in_array(Yii::app()->user->getFnpp(), Controller::ADMIN_FNPP)) {
                $sql = $sql->andWhere('t.prudal != "1" AND t.fnpp = ' . $fnpp . ' AND (t.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR t.dolgnost LIKE \'%преподават%\')');
            }
        }
        if ($sql) {
            $chairs = $sql->order('name')->queryColumn();
            return array_combine($chairs, $chairs);
        } else {
            return false;
        }
    }


    /**
     * @return array|false
     */
    public function GetDisciplines()
    {
        $chairs = $this->GetChairs((new WebUser)->getFnpp());
        if ($chairs) {
            $str = implode(', ', $chairs);
        } else {
            $str = 'NULL';
        }

        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('HEX(gud.nrec) nrec, HEX(gc.nrec) cchair, gud.name discipline, gc.name chair')
            ->from('gal_u_curr_dis gucd')
            ->leftJoin('gal_u_discipline gud', 'gud.nrec = gucd.cdis')
            ->leftJoin('gal_catalogs gc', 'gucd.cchair=gc.nrec')
            ->rightJoin('gal_u_curriculum guc', 'guc.nrec=gucd.ccurr AND guc.wtype = 1 AND guc.status != 2')
            ->where('gud.name IS NOT NULL');
        if (!in_array(Yii::app()->user->getFnpp(), Controller::ADMIN_FNPP)) {
            $sql = $sql->andWhere('gucd.cchair IN (' . $str . ')');
        }
        $sql = $sql->queryAll();
        if ($sql) {
            return $sql;
        } else {
            return array();
        }
    }

    /**
     * @param $discipline
     * @param $chair
     * @return array|false
     */
    public function GetDiscipline($discipline, $chair)
    {
        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('gud.name name, gc.name chair')
            ->from('gal_u_curr_dis gucd')
            ->leftJoin('gal_u_discipline gud', 'gud.nrec = gucd.cdis')
            ->leftJoin('gal_catalogs gc', 'gucd.cchair=gc.nrec')
            ->where('gud.nrec = 0x' . $discipline . ' AND gc.nrec = 0x' . $chair)
            ->queryRow();
        if ($sql) {
            return $sql;
        } else {
            return false;
        }
    }

    /**
     * @param $discipline
     * @param $chair
     * @return array|false
     */
    public function GetSpecialities($discipline, $chair)
    {
        $sql = Yii::app()->db2->createCommand()
            /*   ->selectDistinct('HEX(guc.nrec) nrec,
               CASE
                   WHEN LENGTH(guc.regnum)>0 THEN CONCAT(guc.specialitycode,\' - \',gc.name,\' (\',guc.regnum,\')\')
                   ELSE CONCAT(guc.specialitycode,\' - \',gc.name)
               END codeName')*/
            ->selectDistinct("HEX(guc.nrec) nrec,
            CASE
                    WHEN LENGTH(guc.regnum) > 0 THEN CONCAT(guc.specialitycode, ' - ',gc.name, ', ',
                    COALESCE(
                            if(guc.wformed = 0, 'Очная', null),
                        if(guc.wformed = 1, 'Заочная', null),
                        if(guc.wformed = 2, 'Вечерняя', null)
                            ), ' '
                    ,guc.yeared, ' г.набора, ')
                    ELSE CONCAT(guc.specialitycode,' - ',gc.name)
                END codeName
                ,group_concat(agg.name) nameConcat
                "
            )
            ->from('gal_u_curr_dis gucd')
            ->leftJoin('gal_u_discipline gud', 'gud.nrec = gucd.cdis')
            ->leftJoin('gal_u_curriculum guc', 'guc.nrec = gucd.ccurr')
            ->leftJoin('gal_catalogs gc', 'guc.cspeciality = gc.nrec')
            ->leftJoin('gal_u_curr_group gucg', 'gucg.ccurr = guc.nrec')
            ->leftJoin('attendance_galruz_group agg', 'agg.gal_nrec = gucg.cstgr')
            ->where('gud.nrec = 0x' . $discipline .
                ' AND gucd.cchair = 0x' . $chair .
                ' AND guc.wtype = 1 AND guc.status != 2
                  AND agg.warch != 1
                ')
            ->group('guc.nrec')
            ->queryAll();
        if ($sql) {
            return $sql;
        } else {
            return false;
        }
    }

    /**
     * @param $curr
     * @param $dis
     * @return array|false
     */
    public function GetCurDis($curr, $dis)
    {
        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('gud.name name, gc.name chair, HEX(gucd.nrec) curdis')
            ->from('gal_u_curr_dis gucd')
            ->leftJoin('gal_u_discipline gud', 'gud.nrec = gucd.cdis')
            ->leftJoin('gal_catalogs gc', 'gucd.cchair=gc.nrec')
            ->where('gud.nrec = 0x' . $dis . ' AND gucd.ccurr = 0x' . $curr)
            ->queryRow();
        if ($sql) {
            return $sql;
        } else {
            return false;
        }
    }

    /**
     * @param $curdis
     * @return array|false
     */
    private static function GetCurrDisInfo($curdis)
    {
        $sql = Yii::app()->db2->createCommand()
            ->select('gc.name speciality, gc.code, guc.wdegree, guc.wformed')
            ->from('gal_u_curriculum guc')
            ->leftJoin('gal_u_curr_dis gucd', 'gucd.ccurr=guc.nrec')
            ->leftJoin('gal_catalogs gc', 'guc.cspeciality=gc.nrec')
            ->where('gucd.nrec = 0x' . $curdis)
            ->queryRow();
        if ($sql) {
            return $sql;
        } else {
            return false;
        }
    }

    /**
     * @param $phpWord
     * @return mixed
     */
    private static function GetBlankTableLoad($phpWord)
    {
        $section = $phpWord->addSection();
        $styleTable = array(
            'borderSize' => 3,
            'borderColor' => '000000'
        );
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 14, 'valign' => 'center');

        $cellHCentered = array(
            'align' => 'center',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellHLeft = array(
            'align' => 'left',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellLeft = array(
            'align' => 'left',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0,
            'indent' => 0.5

        );
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable($styleTable);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(6000, $cellRowSpan)->addText('Вид занятий', null, $cellHCentered);
        $table->addCell(2000, $cellRowSpan)->addText('Всего (час./ зач.ед.)', null, $cellHCentered);
        $table->addCell(50, $cellColSpan)->addText('Семестры', null, $cellHCentered);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText($i, null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Всего аудиторных занятий:', array('bold' => true), $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Лекции', null, $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Практические занятия', null, $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Лабораторные работы', null, $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Самостоятельная работа:', array('bold' => true), $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Самостоятельная работа студента', null, $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Самостоятельное изучение материала дисциплины и подготовка к зачетам', null, $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Курсовой проект (работа)', null, $cellLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Расчетно-графическая работа', null, $cellLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Домашнее задание', null, $cellLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Количество часов на экзамен', null, $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Всего по дисциплине', array('bold' => true), $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('Вид аттестации за семестр (зачет, дифференцированный зачет, экзамен)', null, $cellHLeft);
        $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }
        return $table;
    }

    private static function GetFullTableLoad($phpWord, $curdis)
    {
        /**
         * @param $str
         * @return array
         */
        function GetControl($str)
        {
            $str = iconv('windows-1251', 'utf-8', $str);
            $practice = $zach = $difZach = $exam = $kurs = array();
            $arr = explode(', ', $str);
            foreach ($arr as $item) {
                $regs = array();
                mb_regex_encoding('UTF-8');
                mb_ereg('.*([0-9])', $item, $regs);
                $semestr = $regs[0];
                $type = str_replace($semestr, '', $item);
                switch ($type) {
                    case 'з':
                        array_push($zach, (int)$semestr);
                        break;
                    case 'дз':
                        array_push($difZach, (int)$semestr);
                        break;
                    case 'кп':
                    case 'кр':
                        array_push($kurs, (int)$semestr);
                        break;
                    case 'э':
                        array_push($exam, (int)$semestr);
                        break;
                    case 'п':
                        array_push($practice, (int)$semestr);
                        break;
                    default:
                        throw new Exception('Unknown type:' . $type);
                }
            }
            return array(
                'zach' => $zach,
                'difZach' => $difZach,
                'exam' => $exam,
                'kurs' => $kurs,
                'practice' => $practice
            );
        }

        /**
         * @param $arr
         * @return int|string
         */
        function GetPlus($arr)
        {
            $counter = 0;
            if ($arr)
                foreach ($arr as $item) {
                    $counter += $item;
                }
            return ($counter > 0) ? (int)round($counter) : '';
        }

        /**
         * @param $first
         * @param $arr
         * @return int|string
         */
        function GetMinus($first, $arr)
        {
            foreach ($arr as $item) {
                $first -= $item;
            }
            return ($first > 0) ? (int)round($first) : '';
        }

        /**
         * @param $val
         * @return int|string
         */
        function CheckZero($val)
        {
            return ($val == 0) ? '' : (int)round($val);
        }

        $length = 2 * (int)Yii::app()->db2->createCommand()
                ->select('guc.term terms')
                ->from('gal_u_curriculum guc')
                ->leftJoin('gal_u_curr_dis gucd', 'gucd.ccurr=guc.nrec')
                ->where('gucd.nrec = 0x' . $curdis)
                ->queryRow()['terms'];

        $data = ApiKeyService::queryApi('getWorkCurrStruct', array("nrec" => '0x' . $curdis), Yii::app()->session['ApiKey'], 'GET');
        $sql = $data['json_data'];
//        GetControl((string)$sql['AttAll']);
//        $control = GetControl(iconv('windows-1251','utf-8',(string)$sql['AttAll']));
        $control = GetControl(mb_convert_encoding((string)$sql['AttAll'], 'Windows-1251', 'UTF-8'));

        $section = $phpWord->addSection();
        $styleTable = array(
            'borderSize' => 3,
            'borderColor' => '000000'
        );
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => $length, 'valign' => 'center');

        $cellHCentered = array(
            'align' => 'center',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellHLeft = array(
            'align' => 'left',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellLeft = array(
            'align' => 'left',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0,
            'indent' => 0.5

        );
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable($styleTable);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(6000, $cellRowSpan)->addText('Вид занятий', null, $cellHCentered);
        $table->addCell(2000, $cellRowSpan)->addText('Всего (час./ зач.ед.)', null, $cellHCentered);
        $table->addCell(50, $cellColSpan)->addText('Семестры', null, $cellHCentered);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        for ($i = 1; $i <= $length; $i++) {
            $table->addCell(50, $cellVCentered)->addText($i, null, $cellHCentered);
        }

        $table->addRow();
        if ($control['practice']) {
            $table->addCell(6000, $cellVCentered)->addText('Практика', array('bold' => true), $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText((int)round($sql['Hour_All']) . '/' . (int)round($sql['ZE_All']), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                if (in_array($i, $control['practice'])) {
                    $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['PrIGAs' . $i . 's']), null, $cellHCentered);
                } else {
                    $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
                }
            }
        } else {
            $table->addCell(6000, $cellVCentered)->addText('Всего аудиторных занятий:', array('bold' => true), $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText(CheckZero($sql['Aud_Pl']), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText(GetPlus(array($sql['Lec' . $i . 's'], $sql['PZs' . $i . 's'], $sql['LRs' . $i . 's'])), null, $cellHCentered);
            }

            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Лекции', null, $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText(CheckZero($sql['Lec_Pl']), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['Lec' . $i . 's']), null, $cellHCentered);
            }


            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Практические занятия', null, $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText(CheckZero($sql['Pr_Pl']), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['PZs' . $i . 's']), null, $cellHCentered);
            }

            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Лабораторные работы', null, $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText(CheckZero($sql['Lab_Pl']), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['LRs' . $i . 's']), null, $cellHCentered);
            }

            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Самостоятельная работа:', array('bold' => true), $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText(CheckZero($sql['SRS_Pl']), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['SRSs' . $i . 's']), null, $cellHCentered);
            }


            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Самостоятельная работа студента', null, $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
            }

            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Самостоятельное изучение материала дисциплины и подготовка к зачетам', null, $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText(GetMinus($sql['SRS_Pl'], array($sql['KSR_Pl'])), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText(GetMinus($sql['SRSs' . $i . 's'], array($sql['KSRs' . $i . 's'])), null, $cellHCentered);
            }

            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Курсовой проект (работа)', null, $cellLeft);
            $kurs = array();
            if ($control['kurs']) {
                foreach ($control['kurs'] as $i) {
                    array_push($kurs, $sql['KSRs' . $i . 's']);
                }
            }
            $table->addCell(2000, $cellVCentered)->addText(GetPlus($kurs), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                if ($control['kurs']) {
                    if (in_array($i, $control['kurs'])) {
                        $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['KSRs' . $i . 's']), null, $cellHCentered);
                    } else {
                        $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
                    }
                } else {
                    $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
                }
            }


            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Расчетно-графическая работа', null, $cellLeft);
            $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
            }

            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Домашнее задание', null, $cellLeft);
            $notKurs = array();
            if ($control['kurs']) {
                for ($i = 1; $i <= $length; $i++) {
                    if (!in_array($i, $control['kurs'])) {
                        array_push($notKurs, $sql['KSRs' . $i . 's']);
                    }
                }
            } else {
                for ($i = 1; $i <= $length; $i++) {
                    array_push($notKurs, $sql['KSRs' . $i . 's']);
                }
            }
            $table->addCell(2000, $cellVCentered)->addText(GetPlus($notKurs), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                if ($control['kurs']) {
                    if (!in_array($i, $control['kurs'])) {
                        $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['KSRs' . $i . 's']), null, $cellHCentered);
                    } else {
                        $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
                    }
                } else {
                    $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['KSRs' . $i . 's']), null, $cellHCentered);
                }
            }


            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Количество часов на экзамен', null, $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText(CheckZero($sql['IGA']), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText(CheckZero($sql['PrIGAs' . $i . 's']), null, $cellHCentered);
            }


            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Всего по дисциплине', array('bold' => true), $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText((int)round($sql['Hour_All']) . '/' . (int)round($sql['ZE_All']), array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
            }

            $table->addRow();
            $table->addCell(6000, $cellVCentered)->addText('Вид аттестации за семестр (зачет, дифференцированный зачет, экзамен)', null, $cellHLeft);
            $table->addCell(2000, $cellVCentered)->addText('', array('bold' => true), $cellHCentered);
            for ($i = 1; $i <= $length; $i++) {
                $isNotSet = true;
                if ($control['zach']) {
                    if (in_array($i, $control['zach'])) {
                        $table->addCell(50, $cellVCentered)->addText('з', array('bold' => true), $cellHCentered);
                        $isNotSet = false;
                    }
                }
                if ($control['difZach']) {
                    if (in_array($i, $control['difZach'])) {
                        $table->addCell(50, $cellVCentered)->addText('дз', array('bold' => true), $cellHCentered);
                        $isNotSet = false;
                    }
                }
                if ($control['exam']) {
                    if (in_array($i, $control['exam'])) {
                        $table->addCell(50, $cellVCentered)->addText('э', array('bold' => true), $cellHCentered);
                        $isNotSet = false;
                    }
                }
                if ($isNotSet) {
                    $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
                }
            }
        }
        return $table;
    }

    private static function GetBlankTableCompetence($phpWord, $code)
    {
        $section = $phpWord->addSection();
        $styleTable = array(
            'borderSize' => 3,
            'borderColor' => '000000'
        );
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');

        $cellHCentered = array(
            'align' => 'center',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellHLeft = array(
            'align' => 'left',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable($styleTable);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(2000, $cellVCentered)->addText('Шифр направления', array('bold' => true), $cellHCentered);
        $table->addCell(9000, $cellVCentered)->addText('Формируемая компетенция ((шифр) - формулировка)', array('bold' => true), $cellHCentered);

        $table->addRow();
        $table->addCell(null, $cellRowSpan)->addText($code, null, $cellHCentered);
        $table->addCell(9000, $cellVCentered)->addText('', null, $cellHLeft);
        for ($i = 1; $i <= 3; $i++) {
            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(9000, $cellVCentered)->addText('', null, $cellHLeft);
        }

        return $table;
    }

    private static function GetFullTableCompetence($phpWord, $curdis, $code)
    {

        function GetCompetenceText($cell, $competence, $code)
        {
            $t = $cell->createTextRun(array(
                'align' => 'both',
                'space' => array('before' => 0, 'after' => 0),
                'hanging' => 0
            ));
            $t->addText('— ' . $competence . ' ');
            $t->addText('(' . $code . ')', array('bold' => true));
            return $cell;
        }

        $sql = Yii::app()->db2->createCommand()
            ->select('guc.code code, guc.sdop2 competence')
            ->from('gal_u_curr_dis_competent gucdc')
            ->leftJoin('gal_u_competence guc', 'gucdc.ccompetence=guc.nrec')
            ->leftJoin('gal_u_curr_dis gucd', 'gucd.nrec=gucdc.ccurr_dis')
            ->where('gucd.nrec = 0x' . $curdis)
            ->queryAll();

        if ($sql) {
            $section = $phpWord->addSection();
            $styleTable = array(
                'borderSize' => 3,
                'borderColor' => '000000'
            );
            $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
            $cellRowContinue = array('vMerge' => 'continue');

            $cellHCentered = array(
                'align' => 'center',
                'space' => array('before' => 0, 'after' => 0),
                'hanging' => 0
            );
            $cellHLeft = array(
                'align' => 'left',
                'space' => array('before' => 0, 'after' => 0),
                'hanging' => 0
            );
            $cellVCentered = array('valign' => 'center');

            $table = $section->addTable($styleTable);

            $table->addRow(null, array('tblHeader' => true));
            $table->addCell(2000, $cellVCentered)->addText('Шифр направления', array('bold' => true), $cellHCentered);
            $table->addCell(9000, $cellVCentered)->addText('Формируемая компетенция ((шифр) - формулировка)', array('bold' => true), $cellHCentered);


            if (sizeof($sql) > 1) {
                $table->addRow();
                $table->addCell(null, $cellRowSpan)->addText($code, null, $cellHCentered);
                $cell = $table->addCell(9000, $cellVCentered);
                GetCompetenceText($cell, $sql[0]['competence'], $sql[0]['code']);
                for ($i = 1; $i < count($sql); $i++) {
                    $table->addRow();
                    $table->addCell(null, $cellRowContinue);
                    $cell = $table->addCell(9000, $cellVCentered);
                    GetCompetenceText($cell, $sql[$i]['competence'], $sql[$i]['code']);
                }
            } else {
                $table->addRow();
                $table->addCell(null, $cellVCentered)->addText($code, null, $cellHCentered);
                $cell = $table->addCell(9000, $cellVCentered);
                GetCompetenceText($cell, $sql[0]['competence'], $sql[0]['code']);
            }

            return $table;
        } else {
            return self::GetBlankTableCompetence($phpWord, $code);
        }
    }

    private static function GetBlankTableIndicator($phpWord, $discipline)
    {
        $section = $phpWord->addSection();
        $styleTable = array(
            'borderSize' => 3,
            'borderColor' => '000000'
        );
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 3, 'valign' => 'center');

        $cellHCentered = array(
            'align' => 'center',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable($styleTable);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(2000, $cellRowSpan)->addText('Индекс компетенции', null, $cellHCentered);
        $table->addCell(1500, $cellColSpan)->addText('Проектируемые результаты освоения дисциплины «' . $discipline . '» и индикаторы формирования компетенций', null, $cellHCentered);
        $table->addCell(2000, $cellRowSpan)->addText('Средства и технологии оценки', null, $cellHCentered);
        $table->addCell(2000, $cellRowSpan)->addText('Технологии формирования компетенции', null, $cellHCentered);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(null, $cellRowContinue);
        $table->addCell(1500, $cellVCentered)->addText('Знания (3)', null, $cellHCentered);
        $table->addCell(1500, $cellVCentered)->addText('Умения (У)', null, $cellHCentered);
        $table->addCell(1500, $cellVCentered)->addText('Навыки (В)', null, $cellHCentered);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('', null, $cellHCentered);
        $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
        $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
        $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('', null, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('', null, $cellHCentered);

        return $table;
    }

    private static function GetFullTableIndicator($phpWord, $discipline, $curdis)
    {
        $sql = Yii::app()->db2->createCommand()
            ->select('guc.code code')
            ->from('gal_u_curr_dis_competent gucdc')
            ->leftJoin('gal_u_competence guc', 'gucdc.ccompetence=guc.nrec')
            ->leftJoin('gal_u_curr_dis gucd', 'gucd.nrec=gucdc.ccurr_dis')
            ->where('gucd.nrec = 0x' . $curdis)
            ->queryAll();

        if (!$sql) {
            return self::GetBlankTableIndicator($phpWord, $discipline);
        } else {
            $section = $phpWord->addSection();
            $styleTable = array(
                'borderSize' => 3,
                'borderColor' => '000000'
            );
            $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
            $cellRowContinue = array('vMerge' => 'continue');
            $cellColSpan = array('gridSpan' => 3, 'valign' => 'center');

            $cellHCentered = array(
                'align' => 'center',
                'space' => array('before' => 0, 'after' => 0),
                'hanging' => 0
            );
            $cellHLeft = array(
                'align' => 'left',
                'space' => array('before' => 0, 'after' => 0),
                'hanging' => 0
            );
            $cellLeft = array(
                'align' => 'left',
                'space' => array('before' => 0, 'after' => 0),
                'hanging' => 0,
                'indent' => 0.5

            );
            $cellVCentered = array('valign' => 'center');

            $table = $section->addTable($styleTable);

            $table->addRow(null, array('tblHeader' => true));
            $table->addCell(2000, $cellRowSpan)->addText('Индекс компетенции', null, $cellHCentered);
            $table->addCell(1500, $cellColSpan)->addText('Проектируемые результаты освоения дисциплины «' . $discipline . '» и индикаторы формирования компетенций', null, $cellHCentered);
            $table->addCell(2000, $cellRowSpan)->addText('Средства и технологии оценки', null, $cellHCentered);
            $table->addCell(2000, $cellRowSpan)->addText('Технологии формирования компетенции', null, $cellHCentered);

            $table->addRow(null, array('tblHeader' => true));
            $table->addCell(null, $cellRowContinue);
            $table->addCell(1500, $cellVCentered)->addText('Знания (3)', null, $cellHCentered);
            $table->addCell(1500, $cellVCentered)->addText('Умения (У)', null, $cellHCentered);
            $table->addCell(1500, $cellVCentered)->addText('Навыки (В)', null, $cellHCentered);
            $table->addCell(null, $cellRowContinue);
            $table->addCell(null, $cellRowContinue);

            if (sizeof($sql) == 1) {
                $table->addRow();
                $table->addCell(2000, $cellVCentered)->addText($sql[0]['code'], null, $cellHCentered);
                $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText('', null, $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText('', null, $cellHCentered);
            } else {
                $table->addRow();
                $table->addCell(2000, $cellVCentered)->addText($sql[0]['code'], null, $cellHCentered);
                $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                $table->addCell(2000, $cellRowSpan)->addText('', null, $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText('', null, $cellHCentered);
                for ($i = 1; $i < sizeof($sql); $i++) {
                    $table->addRow();
                    $table->addCell(2000, $cellVCentered)->addText($sql[$i]['code'], null, $cellHCentered);
                    $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                    $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                    $table->addCell(1500, $cellVCentered)->addText('', null, $cellHCentered);
                    $table->addCell(2000, $cellRowContinue)->addText('', null, $cellHCentered);
                    $table->addCell(2000, $cellVCentered)->addText('', null, $cellHCentered);
                }
            }
            return $table;
        }
    }

    private static function GetBlankTableSrs($phpWord)
    {
        $section = $phpWord->addSection();
        $styleTable = array(
            'borderSize' => 3,
            'borderColor' => '000000'
        );
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 14, 'valign' => 'center');

        $cellHCentered = array(
            'align' => 'center',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellHLeft = array(
            'align' => 'left',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellHRight = array(
            'align' => 'right',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable($styleTable);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(6000, $cellRowSpan)->addText('Вид СРС', array('bold' => true), $cellHCentered);
        $table->addCell(500, $cellColSpan)->addText('Семестры (кол-во часов)', null, $cellHCentered);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(null, $cellRowContinue);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(500, $cellVCentered)->addText($i, null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('1. Самостоятельное изучение материала дисциплины', null, $cellHLeft);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('2. Выполнение курсвого проекта (работы)', null, $cellHLeft);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }


        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('3. Выполнение расчетно-графической работы', null, $cellHLeft);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }


        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('4. Выполнение домашнего задания', null, $cellHLeft);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('ИТОГО', array('bold' => true), $cellHRight);
        for ($i = 1; $i <= 14; $i++) {
            $table->addCell(50, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('ИТОГО по дисциплине', array('bold' => true), $cellHRight);
        $table->addCell(500, $cellColSpan)->addText('', array('bold' => true), $cellHCentered);

        return $table;
    }

    private static function GetFullTableSrs($phpWord, $curdis)
    {
        $length = 2 * (int)Yii::app()->db2->createCommand()
                ->select('guc.term terms')
                ->from('gal_u_curriculum guc')
                ->leftJoin('gal_u_curr_dis gucd', 'gucd.ccurr=guc.nrec')
                ->where('gucd.nrec = 0x' . $curdis)
                ->queryRow()['terms'];

        $data = ApiKeyService::queryApi('getWorkCurrStruct', array("nrec" => '0x' . $curdis), Yii::app()->session['ApiKey'], 'GET');
        $sql = $data['json_data'];
        $control = GetControl(mb_convert_encoding((string)$sql['AttAll'], 'Windows-1251', 'UTF-8'));

        $section = $phpWord->addSection();
        $styleTable = array(
            'borderSize' => 3,
            'borderColor' => '000000'
        );
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => $length, 'valign' => 'center');

        $cellHCentered = array(
            'align' => 'center',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellHLeft = array(
            'align' => 'left',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellHRight = array(
            'align' => 'right',
            'space' => array('before' => 0, 'after' => 0),
            'hanging' => 0
        );
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable($styleTable);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(6000, $cellRowSpan)->addText('Вид СРС', array('bold' => true), $cellHCentered);
        $table->addCell(500, $cellColSpan)->addText('Семестры (кол-во часов)', null, $cellHCentered);

        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(null, $cellRowContinue);
        for ($i = 1; $i <= $length; $i++) {
            $table->addCell(500, $cellVCentered)->addText($i, null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('1. Самостоятельное изучение материала дисциплины', null, $cellHLeft);
        for ($i = 1; $i <= $length; $i++) {
            $table->addCell(500, $cellVCentered)->addText(GetMinus($sql['SRSs' . $i . 's'], array($sql['KSRs' . $i . 's'])), null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('2. Выполнение курсвого проекта (работы)', null, $cellHLeft);
        for ($i = 1; $i <= $length; $i++) {
            if ($control['kurs']) {
                if (in_array($i, $control['kurs'])) {
                    $table->addCell(500, $cellVCentered)->addText(CheckZero($sql['KSRs' . $i . 's']), null, $cellHCentered);
                } else {
                    $table->addCell(500, $cellVCentered)->addText('', null, $cellHCentered);
                }
            } else {
                $table->addCell(500, $cellVCentered)->addText('', null, $cellHCentered);
            }
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('3. Выполнение расчетно-графической работы', null, $cellHLeft);
        for ($i = 1; $i <= $length; $i++) {
            $table->addCell(500, $cellVCentered)->addText('', null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('4. Выполнение домашнего задания', null, $cellHLeft);
        $notKurs = array();
        if ($control['kurs']) {
            for ($i = 1; $i <= $length; $i++) {
                if (!in_array($i, $control['kurs'])) {
                    array_push($notKurs, $sql['KSRs' . $i . 's']);
                }
            }
        } else {
            for ($i = 1; $i <= $length; $i++) {
                array_push($notKurs, $sql['KSRs' . $i . 's']);
            }
        }
        for ($i = 1; $i <= $length; $i++) {
            if ($control['kurs']) {
                if (!in_array($i, $control['kurs'])) {
                    $table->addCell(500, $cellVCentered)->addText(CheckZero($sql['KSRs' . $i . 's']), null, $cellHCentered);
                } else {
                    $table->addCell(500, $cellVCentered)->addText('', null, $cellHCentered);
                }
            } else {
                $table->addCell(500, $cellVCentered)->addText(CheckZero($sql['KSRs' . $i . 's']), null, $cellHCentered);
            }
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('ИТОГО', array('bold' => true), $cellHRight);
        for ($i = 1; $i <= $length; $i++) {
            $table->addCell(500, $cellVCentered)->addText(CheckZero($sql['SRSs' . $i . 's']), null, $cellHCentered);
        }

        $table->addRow();
        $table->addCell(6000, $cellVCentered)->addText('ИТОГО по дисциплине', array('bold' => true), $cellHRight);
        $table->addCell(500, $cellColSpan)->addText(CheckZero($sql['SRS_Pl']), array('bold' => true), $cellHCentered);

        return $table;
    }


    /**
     * @param $curdis
     * @return string
     * @throws CException
     */
    private static function GetDisciplineType($curdis)
    {
        Yii::app()->session['ApiKey'] = Yii::app()->session['ApiKey'] ?: '04911454-e211-499d-bcd9-c97f85d77484';
        $data = ApiKeyService::queryApi('getWorkCurrDisciplineType', array("nrec" => '0x' . $curdis), Yii::app()->session['ApiKey'], 'GET');
        $sql = $data['json_data'][0];
        $comp = $sql['comp'];
        $block = $sql['block'];
        mb_regex_encoding('UTF-8');
        if (mb_ereg('Практик', $block)) {
            return 'практик';
        } elseif (mb_ereg('Факультатив', $block)) {
            return 'факультативных дисциплин';
        } elseif (mb_ereg('итоговая аттестация', $block)) {
            return 'государственной итоговой аттестации';
        } elseif (mb_ereg('Базовая часть', $comp)) {
            return 'дисциплин базовой части';
        } elseif (mb_ereg('Обязательная часть', $comp)) {
            return 'дисциплин обязательной части';
        } elseif (mb_ereg('Вариативная часть', $comp)) {
            return 'дисциплин вариативной части';
        } elseif (mb_ereg('по выбору', $comp)) {
            return 'дисциплин по выбору';
        } else {
            throw new Exception('Unknown structure: block-' . var_dump($block) . ' comp-' . var_dump($comp));
        }
    }

    private static function GetViceRector()
    {
        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('f.fam, f.nam, f.otc')
            ->from(Wkardc_rp::model()->tableName() . ' w')
            ->leftJoin(Fdata::model()->tableName() . ' f', 'f.npp = w.fnpp')
            ->where('w.prudal = 0 AND w.dolgnost LIKE \'%проректор по образовательной деятельности%\'')
            ->queryRow();
        if ($sql) {
            return mb_substr($sql['nam'], 0, 1) . (($sql['otc']) ? '.' . mb_substr($sql['otc'], 0, 1) : '') . '. ' . $sql['fam'];
        } else {
            return '[ФИО]';
        }
    }

    public static function GetDocument($info = null)
    {
        //DEFAULT VALUES
        $viceRector = self::GetViceRector();
        $disciplineType = '[обязательных дисциплин| дисциплин по выбору]';
        $terms = '[номер семестра] семестре';
        $discipline = '[дисциплина]';
        $chair = '[кафедра]';
        $fio = self::GetFIO((new WebUser())->getFnpp());
        $position = '[должность]';
        $chairPosition = '[должность]';
        $chairFIO = '[ФИО]';
        $caseLeaderPosition = '[должность]';
        $caseLeaderFIO = '[ФИО]';
        $curInfo = array(
            'speciality' => '[специальность]',
            'code' => '[код]',
            'wdegree' => '[уровень]',
            'wformed' => '[форма обучения]'
        );
        //DEFAULT VALUES

        if (!is_null($info)) {
            $discipline = $info['disName'];
            $chair = $info['chairName'];
            $position = self::GetPosition((new WebUser())->getFnpp(), $info['chair']);
            $chairFnpp = self::GetChairFnpp($info['chair']);
            if ($chairFnpp) {
                $chairPosition = self::GetPosition($chairFnpp, $info['chair']);
                $chairFIO = self::GetFIO($chairFnpp);
            }
            //$caseLeaderPosition = $chairPosition;   // TODO: узнать откуда нужно тянуть
            //$caseLeaderFIO = $chairFIO;             //
            if ($info['curdis'] != 'undefined') {
                $curInfo = self::GetCurrDisInfo($info['curdis']);
                $disciplineType = self::GetDisciplineType($info['curdis']);
            }
        }

        require_once Yii::getPathOfAlias('application.extensions.PhpWord.Autoloader') . '.php';
        \PhpOffice\PhpWord\Autoloader::register();
        require_once Yii::getPathOfAlias('application.extensions.Common.Autoloader') . '.php';
        Autoloader::register();

        $phpWord = new PhpWord();
        Settings::setTempDir('/data/www/up/tmp');
        $document = new TemplateProcessor(Yii::getPathOfAlias('webroot') . '/protected/modules/cases/templates/caseTemplate.docx');

        $phpWord->addParagraphStyle('pStyle', array('align' => 'both', 'spacing' => 0, 'spaceAfter' => 0, 'spaceBefore' => 0));

        $document->setValue('viceRector', $viceRector);
        $document->setValue('currentYear', date('Y'));
        $document->setValue('discipline', $discipline);
        switch ($curInfo['wdegree']) {
            case '0':
                $document->setValue('eduForm', 'специалитета');
                break;
            case '1':
            case '7':
                $document->setValue('eduForm', 'бакалавриата');
                break;
            case '2':
                $document->setValue('eduForm', 'магистратуры');
                break;
            case '3':
            case '4':
                $document->setValue('eduForm', 'СПО');
                break;
            case '5':
                $document->setValue('eduForm', 'аспирантуры');
                break;
            default:
                $document->setValue('eduForm', '[уровень]');
        }
        $document->setValue('code', $curInfo['code']);
        $document->setValue('speciality', $curInfo['speciality']);
        $document->setValue('position', $position);
        $document->setValue('fio', $fio);
        $document->setValue('chair', $chair);
        $document->setValue('chairPosition', $chairPosition);
        $document->setValue('chairFio', $chairFIO);
        $document->setValue('caseLeaderPosition', $caseLeaderPosition);
        $document->setValue('caseLeaderFio', $caseLeaderFIO);
        $document->setValue('disciplineType', $disciplineType);
        $document->setValue('terms', $terms);

        $tableCompetence = (is_null($info)) ? self::GetBlankTableCompetence($phpWord, $curInfo['code']) : self::GetFullTableCompetence($phpWord, $info['curdis'], $curInfo['code']);
        $document->setComplexBlock('tableCompetence', $tableCompetence);

        $tableIndicator = (is_null($info)) ? self::GetBlankTableIndicator($phpWord, $discipline) : self::GetFullTableIndicator($phpWord, $discipline, $info['curdis']);
        $document->setComplexBlock('tableIndicator', $tableIndicator);

        $loadTable = (is_null($info)) ? self::GetBlankTableLoad($phpWord) : self::GetFullTableLoad($phpWord, $info['curdis']);

        //возможность клонирования блоков
        $document->cloneBlock('loadBlock', 1, true, true);
        //клону будет добавляться индекс #<i>
        switch ($curInfo['wformed']) {
            case '0':
                $document->setValue('loadFormEdu#1', 'Очная форма обучения');
                break;
            case '1':
                $document->setValue('loadFormEdu#1', 'Заочная форма обучения');
                break;
            case '2':
                $document->setValue('loadFormEdu#1', 'Вечерняя форма обучения');
                break;
            default:
                $document->setValue('loadFormEdu#1', '[форма обучения]');
        }
        $document->setComplexBlock('loadTable#1', $loadTable);
//        $document->setValue('loadFormEdu#2', 'Заочная форма обучения<w:br/>');
//        $document->setValue('loadTable#2', '');

        $srsTable = (is_null($info)) ? self::GetBlankTableSrs($phpWord) : self::GetFullTableSrs($phpWord, $info['curdis']);
        $document->cloneBlock('srsBlock', 1, true, true);
        switch ($curInfo['wformed']) {
            case '0':
                $document->setValue('srsFormEdu#1', 'Очная форма обучения');
                break;
            case '1':
                $document->setValue('srsFormEdu#1', 'Заочная форма обучения');
                break;
            case '2':
                $document->setValue('srsFormEdu#1', 'Вечерняя форма обучения');
                break;
            default:
                $document->setValue('srsFormEdu#1', '[форма обучения]');
        }
        $document->setComplexBlock('srsTable#1', $srsTable);

        $filename = Yii::getPathOfAlias('webroot') . '/protected/runtime/' . $discipline . '.docx';

        $document->saveAs($filename); // Save to temp file
        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        unlink($filename);
    }

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array();
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array();
    }
}