<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 17.03.2020
 * Time: 13:25
 */

class ReadClass
{
    public static function checkpps(){
        if(Yii::app()->user->getPerStatus()){
            $pps = true;
        }else{
            $pps = false;
        }
        return $pps;
    }

    public static function mygroupId(){
        $id = Yii::app()->db2->createCommand()
            ->select('agg.id')
            ->from('fdata f')
            ->join('skard s', 's.fnpp = f.npp')
            ->join('attendance_galruz_group agg', 'agg.name = s.gruppa')
            ->where('f.npp = '.Yii::app()->user->getFnpp())
            ->andWhere('agg.warch = 0')
            ->andWhere('s.prudal = 0')
            ->andWhere('s.gal_UP IS NOT NULL')
            ->order('agg.gal_nrec DESC')
            ->queryAll();
        $return_array = [];
        foreach ($id as $item) {
            $return_array[] = $item['id'];
        }
        return $return_array;
    }

    public static function listfile($id, $action='icon'){
        $model = RemoteTaskList::model()->findByPk($id);
        if($model->file_from != 0){ $id = $model->file_from; }
        $save_path = RemoteModule::uploadPath($id) . $id.'/';
        if (!file_exists($save_path)) { mkdir($save_path);}
        $list = [];
        if ($handle = opendir($save_path)) {
            while (false !== ($entry = readdir($handle))) {
                if($entry != '.' && $entry != '..') {
                    $list[] = array('name' => $entry);
                }
            }
            closedir($handle);
        }
        $string_return = '';
        if($action == 'list') {
            $string_return .= 'Текущие файлы:<br/>';
        }
        foreach ($list as $row){
            if($action == 'icon') {
                $string_return .= CHtml::link('<h3 style="width: min-content; margin: auto;">
            <center><span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'' . $row['name'] . '\' class=\'glyphicon glyphicon-file\' style="color: black"></center></h3>
            <h4 style="width: min-content; margin: auto;">' . $row['name'] . ' </h4>',
                    ['/remote/read/downloadFile', 'id' => $id, 'name' => $row['name']], ['target' => '_blank']);
            }
            if($action == 'list') {
                $string_return .= CHtml::link('<h4 >' . $row['name'] . ' </h4>',
                    ['/remote/read/downloadFile', 'id' => $id, 'name' => $row['name']], ['target' => '_blank']);
            }
        }
        return $string_return;
    }

    /**
     * @return array
     */
    public static function getMyDisciplines(){
        if(Yii::app()->user->getFnpp()==768617) {
            $list = Yii::app()->db2->createCommand()
                ->selectDistinct('HEX(ats.disciplineNrec) name')
                ->from('attendance_schedule ats')
                ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
                ->where('1=1')
              //  ->where('ats.dateTimeStartOfClasses > (SELECT CASE when aga.wformed=1 then \'' . date(RemoteModule::startIZODate()) . '\' else \'' . date(RemoteModule::startDate()) . '\' end FROM attendance_galruz_group aga where aga.id=agg.id)')
//            ->andWhere('agg.id = '.ReadClass::mygroupId())
                ->andWhere(array('in', 'agg.id', ReadClass::mygroupId()))
                ->andWhere('HEX(ats.disciplineNrec) IS NOT NULL')
                ->group('ats.discipline, ats.disciplineNrec')
                ->queryAll();
            $listExtra = Yii::app()->db2->createCommand()
                ->selectDistinct('HEX(ret.discipline) name')
                ->from('remote_extra_task ret')
                ->where(array('in', 'ret.group', ReadClass::mygroupId()))
                ->group('ret.discipline')
                ->queryAll();
             } else
             {
                 $list = Yii::app()->db2->createCommand()
                     ->selectDistinct('HEX(ats.disciplineNrec) name')
                     ->from('attendance_schedule ats')
                     ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
                     ->where('ats.dateTimeStartOfClasses > (SELECT CASE when aga.wformed=1 then \'' . date(RemoteModule::startIZODate()) . '\' else \'' . date(RemoteModule::startDate()) . '\' end FROM attendance_galruz_group aga where aga.id=agg.id)')
     //            ->andWhere('agg.id = '.ReadClass::mygroupId())
                     ->andWhere(array('in', 'agg.id', ReadClass::mygroupId()))
                     ->andWhere('HEX(ats.disciplineNrec) IS NOT NULL')
                     ->group('ats.discipline, ats.disciplineNrec')
                     ->queryAll();
                 $listExtra = Yii::app()->db2->createCommand()
                     ->selectDistinct('HEX(ret.discipline) name')
                     ->from('remote_extra_task ret')
                     ->where(array('in', 'ret.group', ReadClass::mygroupId()))
                     ->group('ret.discipline')
                     ->queryAll();
             }

        $return_array = [];
        foreach ($list as $item) {
            $return_array[] = $item['name'];
        }
        foreach ($listExtra as $item) {
            $return_array[] = $item['name'];
        }
        return $return_array;
    }

    /**
     * @param $form
     * @param $discipline
     */
    public static function checkMyDiscipline($form, $discipline){
        if(!in_array($discipline, ReadClass::getMyDisciplines())){
            $form->redirect(array('index'));
        }

    }

    /**
     * @param $group
     * @param $discipline
     * @return mixed
     */
    public static function getAllDisciplineSemesterFromGroup($group, $discipline){

        $discipline = (!preg_match('/^(0x)?[A-Z0-9]{16}$/i', $discipline)) ? bin2hex($discipline) : $discipline;
        $discipline = CMisc::_id($discipline);
        $return = Yii::app()->db2->createCommand()
            ->selectDistinct('gucs.semester sem, (SELECT COUNT(*) FROM fdata f LEFT JOIN skard s ON s.fnpp = f.npp
LEFT JOIN ecab.vkrfiles v ON v.fnpp = f.npp
WHERE agg.name = s.gruppa AND v.disc = HEX(gud.nrec) AND v.semester = gucs.semester and prudal = 0) \'allWorks\'
, (SELECT COUNT(*) FROM fdata f LEFT JOIN skard s ON s.fnpp = f.npp
LEFT JOIN ecab.vkrfiles v ON v.fnpp = f.npp
WHERE agg.name = s.gruppa AND v.disc = HEX(gud.nrec) AND v.semester = gucs.semester AND v.state IN (1, 2) and prudal = 0) \'checkWorks\'')
            ->from('attendance_galruz_group agg')
            ->join('gal_u_curr_group gucg', 'gucg.cstgr = agg.gal_nrec')
            ->join('gal_u_curriculum guc', 'guc.nrec = gucg.ccurr AND guc.wtype = 1')
            ->join('gal_u_curr_dis gucd', 'gucd.ccurr = guc.nrec')
            ->join('gal_u_curr_discontent gucdc', 'gucdc.ccurr_dis = gucd.nrec')
            ->join('gal_u_curr_semester gucs', 'gucs.nrec = gucdc.csemester')
            ->join('gal_u_discipline gud', 'gud.nrec = gucd.cdis')
            ->where('agg.id = '.$group.' AND gud.nrec = '. $discipline)
            ->order('gucs.semester')
            ->queryAll();
        return $return;
    }

}