<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 22.04.2020
 * Time: 11:00
 */

class DeanClass
{
    public static function getMyDeanCard(){
        $fnpp = Yii::app()->user->getFnpp();
        $wkards = Wkardc_rp::model()->findAllByAttributes(array('fnpp' => $fnpp, 'prudal' => 0));
        foreach ($wkards as $one_kard){
            if(in_array($one_kard->dolgnost, ['декан'])){
                return $one_kard->npp;
            }
        }
        return null;
    }

    public static function getMyStruct($real = true){
        $fnpp = Yii::app()->user->getFnpp();
        if (in_array($fnpp, [705, 640, 2107, 1639, 1637, 4318])){// доступ методистам ИЗО
            return [7];
        }
        $wnpp = self::getMyDeanCard();
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
            $right = RemoteRights::model()->findByAttributes(array('fnpp'=>$fnpp, 'role' => 2));
            if($right instanceof RemoteRights){
                return $right->struct;
            }
            if ($sql = Yii::app()->db2->createCommand()
                ->selectDistinct('g.cdepartment')
                ->from(Fdata::model()->tableName() . ' f')
                ->join(Keylinks::model()->tableName() . ' k', 'k.fnpp = f.npp')
                ->join('gal_up_roles g', 'k.gal_unid = g.personNrec')
                ->where('g.role LIKE \'%dean%\' and f.npp = ' . Yii::app()->user->getFnpp())
                ->queryAll()
            ) {
                return [0];
            }
        }
        return null;
    }

    public static function getFacultyGroup($struct){
        $name = StructD_rp::model()->findByPK($struct)->name;
        $nrecFac = Catalog::model()->findByAttributes(array('name' => $name))->nrec;
        //$nrecFac = CMisc::_id(bin2hex($nrecFac));
        $listGroup = AttendanceGalruzGroup::model()->findAllByAttributes(array('cfaculty' => $nrecFac, 'warch' => 0));

        $list = [];
        foreach ($listGroup as $item) {
            if($item->inSchedule()){
                $list[]=$item;
            }
        }

        return $list;
    }

    public static function getFacultyGroupFromNrec($fnpp){

        $nrecFac = [];
        if ($sql = Yii::app()->db2->createCommand()
            ->selectDistinct('HEX(g.cdepartment) cdepartment')
            ->from(Fdata::model()->tableName() . ' f')
            ->join(Keylinks::model()->tableName() . ' k', 'k.fnpp = f.npp')
            ->join('gal_up_roles g', 'k.gal_unid = g.personNrec')
            ->where('g.role LIKE \'%dean%\' and f.npp = ' . Yii::app()->user->getFnpp())
            ->queryAll()
        ) {
            foreach ($sql as $item) {
                $nrecFac[] = '0x'.$item['cdepartment'];
            }
        }

        $criteria = new CDbCriteria();
        $criteria->compare('warch', 0);
        $criteria->addCondition('cfaculty IN (' . implode(', ', $nrecFac) . ')');

        $listGroup = AttendanceGalruzGroup::model()->findAll($criteria);
//        var_dump($listGroup);die;

        $list = [];
        foreach ($listGroup as $item) {
            if($item->inSchedule()){
                $list[]=$item;
            }
        }

        return $list;
    }



}