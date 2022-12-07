<?php


class EmployeePermissions
{
    private static $permissions = array(
        'faculty' => false,
        'groups' => false,
        'students' => false,
        'all' => false,
    );
    private $whiteList = array(
        'structs' => array(
            //   63 => array('name' => 'Отдел АСУ ВУЗ', 'permissions' => 'all'),
            //  61 => array('name' => 'Радиотехнический факультет', 'permissions' => array('faculty' => 'РТФ')),
            72 => array('name' => 'Сектор информационно-аналитический', 'permissions' => 'all'),
        ),
        'wkards' => array(
            36410 => array('name' => 'Ложников Павел Сергеевич',
                'permissions' =>
                    array('groups' => array('БИТ-161', 'БИТ-171', 'КЗИ-171', 'БИТ-172', 'БИТ-182', 'КЗИ-181', 'БИТ-181', 'БИТ-191',
                        'БИТ-192', 'КЗИ-191', 'БИТ-201', 'БИТ-202', 'КЗИ-201', 'КЗИ-202')
                    )
            ),
            // 36484 => array('name' => 'Стадников Денис', 'permissions' => 'all'),
        )
    );

    public function __construct()
    {
            $wknpps = self::getWknppByFnpp();
            $structsNpps = array();
            foreach ($wknpps as $wknpp) {
                $this->addPermissionsByWknpp($wknpp);
         //       $structsNpps = array_merge($structsNpps, $this->getStructsId($wknpp));
            }
          /*  foreach ($structsNpps as $structsNpp) {
                $this->addPermissionsByStructNpp($structsNpp);
            }*/

            self::addDeanPermissions(Yii::app()->user->getFnpp());

    }

    public function getPermissions()
    {
        if (self::$permissions['faculty'])
            self::$permissions['faculty'] = array_unique(self::$permissions['faculty'], SORT_STRING);
        if (self::$permissions['groups'])
            self::$permissions['groups'] = array_unique(self::$permissions['groups'], SORT_STRING);

        return self::$permissions;
    }

    private static function getWknppByFnpp()
    {
        $fnpp = Yii::app()->user->getFnpp();
        $wkards = Wkardc_rp::model()->findAllByAttributes(array('fnpp' => $fnpp, 'prudal' => 0));
        $wknpps = array();
        foreach ($wkards as $elem) {
            $wknpps[] = $elem['npp'];
        }
        return $wknpps;
    }

    private function getStructsId($wknpp)
    {
        $statement = "
        SELECT CONCAT_WS(',',sdr.npp, sdr1.npp, sdr2.npp) as npps FROM wkardc_rp wr
LEFT JOIN struct_d_rp sdr ON sdr.npp = wr.struct
LEFT JOIN struct_d_rp sdr1 ON sdr1.npp = sdr.pnpp
LEFT JOIN struct_d_rp sdr2 ON sdr2.npp = sdr1.pnpp
where wr.npp = $wknpp
and wr.prudal != 1;
        ";
        $structs = Yii::app()->db2->createCommand($statement)->queryAll();
        $structs = array_column($structs, 'npps')[0];
        $structs = explode(',', $structs);
        return $structs;

    }

    private function addPermissionsByWknpp($wknpp)
    {
        $wknpp = (int)$wknpp;
        if (isset($this->whiteList['wkards'][$wknpp])) {
            $rule = $this->whiteList['wkards'][$wknpp]['permissions'];
            if (gettype($rule) === 'string') {
                if ($rule === 'all') {
                    $this->addAllFacultyPermissions();
                    self::$permissions['all'] = true;
                    return;
                }
            } else if (gettype($rule) === 'array') {

                if (isset($rule['faculty'])) {
                    if ($rule['faculty'] === 'all') {
                        $this->addAllFacultyPermissions();
                    } else {
                        self::$permissions['faculty'] = array();
                        if (gettype($rule['faculty']) == 'array') {
                            foreach ($rule['faculty'] as $fac) {
                                self::$permissions['faculty'][] = $fac;
                            }
                        } else {
                            self::$permissions['faculty'][] = $rule['faculty'];
                        }
                    }
                    //  self::$permissions['faculty'] = array_merge(self::$permissions['faculty'], $rule['faculty']);
                }
                if (isset($rule['groups'])) {
                    self::$permissions['groups'] = array();
                    if (gettype($rule['groups']) === 'array') {
                        foreach ($rule['groups'] as $grp) {
                            self::$permissions['groups'][] = $grp;
                        }
                    } else {
                        if ($rule['groups'] === 'all') {
                            self::$permissions['groups'] = $this->getGroupsByFacultyNames(self::$permissions['faculty']);
                        } else {
                            self::$permissions['groups'] = $rule['groups'];
                        }
                    }

                    // self::$permissions['groups'] = array_merge(self::$permissions['groups'], $rule['groups']);
                }
            }
        }
    }

    private function addPermissionsByStructNpp($structNpp)
    {
        $structNpp = (int)$structNpp;
        if (isset($this->whiteList['structs'][$structNpp])) {
            $rule = $this->whiteList['structs'][$structNpp]['permissions'];
            if (gettype($rule) === 'string') {
                if ($rule === 'all') {
                    $this->addAllFacultyPermissions();
                    self::$permissions['all'] = true;
                    return;
                }
            } elseif (gettype($rule) === 'array') {
                if (isset($rule['faculty'])) {
                    self::$permissions['faculty'] = array();
                    if (gettype($rule['faculty']) == 'array') {
                        foreach ($rule['faculty'] as $fac) {
                            self::$permissions['faculty'][] = $fac;
                        }
                    } else {
                        self::$permissions['faculty'][] = $rule['faculty'];
                    }
                    //  self::$permissions['faculty'] = array_merge(self::$permissions['faculty'], $rule['faculty']);
                }
                if (isset($rule['groups'])) {
                    self::$permissions['groups'] = array();
                    if (gettype($rule['groups']) == 'array') {
                        foreach ($rule['groups'] as $grp) {
                            self::$permissions['groups'][] = $grp;
                        }
                    } else {
                        self::$permissions['groups'] = $rule['groups'];
                    }

                    // self::$permissions['groups'] = array_merge(self::$permissions['groups'], $rule['groups']);
                }

            }
        }
    }

    private function addAllFacultyPermissions()
    {
        $statement = "
        select distinct gc.longname longname from skard sk
inner join gal_u_student tus on tus.nrec = sk.gal_srec
inner join gal_catalogs gc on tus.cfaculty = gc.nrec
        ";
        $allFaculties = Yii::app()->db2->createCommand($statement)->queryAll();
        $allFaculties = array_column($allFaculties, 'longname');
        self::$permissions['faculty'] = $allFaculties;


    }

    public static function isAnyPermissions()
    {
        if(isset(Yii::app()->session['isAnyNotePerm']))
        {
            return Yii::app()->session['isAnyNotePerm'];
        }
        new EmployeePermissions();
        $isPerm = false;
        foreach (self::$permissions as $perm) {
            //var_dump($perm);
            if ($perm !== false) {
                $isPerm = true;
                break;
            }
        }
        Yii::app()->session['isAnyNotePerm'] = $isPerm;
        return $isPerm;
    }

    private function addDeanPermissions($fnpp)
    {
        if (!$fnpp)
            return

                $deanPerm = array();

        $statement = " 
 select distinct HEX(gc.nrec) as nrec, gc.longname as longname
from fdata f
         inner join gal_up_roles gur ON gur.npp = f.npp
         inner JOIN gal_catalogs gc ON gc.nrec = gur.cdepartment
where 1 = 1
  AND f.npp = $fnpp
  AND (gur.role = 'dean' or role = 'DeanA' or role = 'DeanZ')
  and gc.sdopinf = 'Ф'
";
        $result = Yii::app()->db2->createCommand($statement)->queryAll();
        $facultiesNames = array_column($result, 'longname');
        $facultyNrecs = array_column($result, 'nrec');

        $deanPerm['faculties'] = $facultiesNames;
        $deanPerm['groups'] = array();

        foreach ($facultyNrecs as $nrec) {
            $statement = "
select agg.name as name
from attendance_galruz_group agg
where cfaculty = 0x$nrec
and agg.warch = 0
order by agg.name
            ";
            $result = Yii::app()->db2->createCommand($statement)->queryAll();
            $groupNames = array_column($result, 'name');
            $deanPerm['groups'] = array_merge($deanPerm['groups'], $groupNames);
        }

        if ($facultyNrecs) {
            if (!self::$permissions['faculty'])
                self::$permissions['faculty'] = array();

            self::$permissions['faculty'] = array_merge(self::$permissions['faculty'], $deanPerm['faculties']);

            if (!self::$permissions['groups'])
                self::$permissions['groups'] = array();

            self::$permissions['groups'] = array_merge(self::$permissions['groups'], $deanPerm['groups']);
        }
        /*if (self::$permissions['groups'] && count(self::$permissions['groups']) > 0)
            self::$permissions['students'] = $this->getStudentsFromGroups(self::$permissions['groups']);*/
    }

    private function getStudentsFromGroups($groupNames)
    {
        $students = [];
        foreach ($groupNames as &$name) {
            $name = "'" . $name . "'";
        }
        $impNames = implode(', ', $groupNames);
        /*        $statement = "
                select f.npp as fnpp, f.fam as fam, f.nam as nam, f.otc as otc, group_concat( distinct ' ', sk.gruppa) as gruppa
        from skard sk
                 inner join gal_u_student gus ON gus.nrec = sk.gal_srec and gus.warch = 0
                 inner join gal_u_curr_group gucg ON gus.ccurr = gucg.ccurr
                 inner join gal_u_curriculum guc ON gucg.ccurr = guc.nrec
                 inner join fdata f ON f.npp = sk.fnpp
        where gucg.number in ($impNames)
        group by f.npp, f.fam, f.nam, f.otc

                ";*/
        $statement = "
        select distinct f.npp as fnpp, gp.fio as fio, gus.sdepcode as gruppa from gal_u_student gus
inner  join gal_persons gp ON gp.nrec = gus.cpersons
inner  join skard sk ON sk.gal_srec = gus.nrec
inner join fdata f ON sk.fnpp = f.npp
where sdepcode in ($impNames) and gus.warch = 0

        ";

        $result = Yii::app()->db2->createCommand($statement)->queryAll();
        // var_dump($result); die;
        foreach ($result as $res) {
            //   $students[] = array('fnpp' => $res['fnpp'], 'fio' => $res['fam'] . ' ' . $res['nam'] . ' ' . $res['otc'] . '   '. $res['gruppa']);
            $students[] = array('fnpp' => $res['fnpp'], 'fio' => $res['fio'] . '-' . $res['gruppa']);
        }
        return $students;

    }

    private function getGroupsByFacultyNames($names)
    {
        foreach ($names as &$name) {
            $name = "'" . $name . "'";
        }
        $impNames = implode(', ',$names);
        $statement = "select agg.name as name
from attendance_galruz_group agg
where cfaculty in (select gc.nrec from gal_catalogs gc where  longname in ($impNames))
and agg.warch = 0
order by agg.name";
        $result = Yii::app()->db2->createCommand($statement)->queryAll();
        $result = array_column($result,'name');
        return $result;
    }
}