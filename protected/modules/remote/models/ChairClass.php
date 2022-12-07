<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 20.03.2020
 * Time: 16:49
 */

class ChairClass
{
    public static function getMyChairCard(){
        $fnpp = Yii::app()->user->getFnpp();
        $wkards = Wkardc_rp::model()->findAllByAttributes(array('fnpp' => $fnpp, 'prudal' => 0));
        foreach ($wkards as $one_kard){
            if(in_array($one_kard->dolgnost, ['заведующий кафедрой'])){
                return $one_kard->npp;
            }
        }
        return null;
    }

    public static function getMyStruct($real = true){
        $fnpp = Yii::app()->user->getFnpp();
        $wnpp = self::getMyChairCard();
        if($wnpp != null) {
            $listStruct = [];
            $wmodel = Wkardc_rp::model()->findByPk($wnpp);
            $structs = StructD_rp::model()->findAllByAttributes(array('pnpp' => $wmodel->structD->npp));
            $listStruct[] = $wmodel->structD->npp;
            foreach ($structs as $item){
                $listStruct[] = $item->npp;
            }
            return $listStruct;
        }
        if(!$real){
            $listStruct = [];
            $right = RemoteRights::model()->findByAttributes(array('fnpp'=>$fnpp, 'role' => 1));
            if($right instanceof RemoteRights){
                $listStruct[] = $right->struct;
                return $listStruct;
            }
        }
        return null;
    }

    public static function getChairNpp($struct){
        $list_struct = Wkardc_rp::model()->findAllByAttributes(array('prudal' => 0, 'struct' => $struct));
        $npp_chair_list = [];
        foreach ($list_struct as $row){
            $npp_chair_list[] = $row->fnpp;
        }
        return $npp_chair_list;
    }

    public static function getChairList($struct){
        $npp_chair_list = self::getChairNpp($struct);
        $list = Fdata::model()->findAllByAttributes(array('npp' => $npp_chair_list), array('order' => 'fam ASC'));
        return $list;
    }

    public static function getPersonTask($fnpp, $struct){
        $list = Yii::app()->db2->createCommand()
            ->selectDistinct('ats.discipline, agg.name \'group\', agg.id groupId, HEX(ats.disciplineNrec) disciplineId
            , (SELECT COUNT(*) FROM remote_task_list rtl WHERE rtl.`group` = ats.studGroupId AND rtl.discipline = ats.disciplineNrec) \'count\'')
            ->from('attendance_schedule ats')
            ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
            ->where(array('and',
                'ats.teacherFnpp = '.$fnpp,
                'ats.dateTimeStartOfClasses > \''.date(RemoteModule::startDate()).'\'',
                array('in','agg.wformed',array(0,2)),
                'agg.warch = 0'
            ))
            ->orWhere(array('and',
                'ats.teacherFnpp = '.$fnpp,
                'agg.wformed = 1',
                'agg.warch = 0',
                // TODO: Поменять даты семестра для заочников
                'ats.dateTimeStartOfClasses > \''.date('2022-09-01').'\'',
                'ats.dateTimeStartOfClasses < \''.date('2023-06-30').'\''
            ))
            ->queryAll();
        $listExtra = Yii::app()->db2->createCommand()
            ->selectDistinct('gud.name discipline, agg.name \'group\', agg.id groupId, HEX(ret.discipline) disciplineId
            , (SELECT COUNT(*) FROM remote_task_list rtl WHERE rtl.`group` = ret.group AND rtl.discipline = ret.discipline) \'count\''
            )
            ->from('remote_extra_task ret')
            ->join('attendance_galruz_group agg', 'agg.id = ret.group')
            ->join('gal_u_discipline gud', 'gud.nrec = ret.discipline')
            ->where(array('and',
                'ret.teacher = '.$fnpp,
                array('in', 'ret.chair', $struct),
            ))
            ->queryAll();
        $list = array_merge($list, $listExtra);

        $countSuccess = 0;
        $countAll = 0;
        foreach ($list as $item){
            $countAll++;
            if($item['count']>0){
                $countSuccess++;
            }
        }
        return ['all' => $countAll, 'success' => $countSuccess];
    }

    public static function getActing($list, $struct){
        $actingList = [];
        foreach ($list as $row){
            $actingList[$row->npp] = RemoteRights::model()->findByAttributes(array('fnpp'=>$row->npp, 'struct' => $struct, 'role' => 1));
        }

        return $actingList;
    }

    public static function getChairNrecFromStruct($id){
        return Yii::app()->db2->createCommand()
            ->selectDistinct('HEX(gc.nrec)')
            ->from('gal_catalogs gc')
            ->where(array('and',
                'gc.sdopinf LIKE \'К\'',
                'gc.datok = 0',
                '(SELECT sdr1.npp FROM struct_d_rp sdr1 WHERE sdr1.name = gc.name
                AND sdr1.npp IN ('. implode(", ", $id).') LIMIT 1) IN ('. implode(", ", $id).')'
            ))
            ->queryScalar();
    }

    public static function getChairGroups($id){
        $nrec = self::getChairNrecFromStruct($id);

        $return = AttendanceGalruzGroup::model()->findAllByAttributes(array('cchair' => hex2bin($nrec),
            'warch' => 0), array('order' => 'name ASC'));

        return $return;
    }

    public static function getAllDisciplineFromGroup($id){
        $nrec = self::getChairNrecFromStruct(self::getMyStruct(false));
        $return = Yii::app()->db2->createCommand()
            ->select('HEX(gud.nrec) id, gud.name name')
            ->from('attendance_galruz_group agg')
            ->join('gal_u_curr_group gucg', 'gucg.cstgr = agg.gal_nrec')
            ->join('gal_u_curriculum guc', 'guc.nrec = gucg.ccurr AND guc.wtype = 1')
            ->join('gal_u_curr_dis gucd', 'gucd.ccurr = guc.nrec')
            ->join('gal_u_discipline gud', 'gud.nrec = gucd.cdis')
            ->where('agg.id = '.$id.' AND gucd.cchair = UNHEX(\''.$nrec.'\') AND guc.nrec is not null AND gud.nrec is not null')
            ->order('gud.name')
            ->queryAll();

        return $return;
    }


}