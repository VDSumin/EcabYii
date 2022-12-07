<?php

class IndividualplanModule extends CWebModule {

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'individualplan.models.*',
            'individualplan.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else {
            return false;
        }
    }

    public static function listWorkToIndividualPlan($parent){
        $fnpp = Yii::app()->session['fnpp'];
        $year = Yii::app()->session['yearEdu'];
        $chair = Yii::app()->session['chairNpp'];
        $sql = 'SELECT ic.id, ic.parent, ic.name, ic.timeNorms, ic.ReportingForm, ic.year, ipf.fnpp, ipf.hours, COALESCE(ipf.correctHours, 0) correctHours, ipf.isBlock, ipf.status,
  fipf.fnpp ffnpp, fipf.hours fhours, COALESCE(fipf.correctHours, 0) fcorrectHours, fipf.isBlock fisBlock, fipf.status fstatus, ic.cconfirm
          FROM individualplan_catalog ic 
          LEFT JOIN individualplan_planned_fixation ipf ON ic.id = ipf.Kind AND ic.year = ipf.year AND ipf.fnpp = '.$fnpp.' AND ipf.chair = '.$chair.' AND ipf.kindOfLoad = 1
          LEFT JOIN individualplan_planned_fixation fipf ON ic.id = fipf.Kind AND ic.year = fipf.year AND fipf.fnpp = '.$fnpp.' AND fipf.chair = '.$chair.' AND fipf.kindOfLoad = 2
          WHERE ic.parent ='.$parent.' AND ic.year = '.$year.' ORDER BY ic.id';

        $list = Yii::app()->db2->createCommand($sql)->queryAll();
        //var_dump($list);die;
        return $list;
    }

    public static function CanWriteIndividualPlan($year = null, $chair){
        if(!$year) {
            $date = getdate();
            if (($date['mon'] >= 1) && ($date['mon'] <= 8)) {
                $year = ($date['year']-1);
            } else {
                $year = $date['year'];
            }
        }

        $sql = 'SELECT igs.openplan, igs.openfact FROM individualplan_global_setting igs WHERE igs.kind = 1 AND igs.year = '.$year;

        $list = Yii::app()->db2->createCommand($sql)->queryRow();

        $sql = 'SELECT igs.openplan, igs.openfact FROM individualplan_global_setting igs WHERE igs.kind = 2 AND igs.year = '.$year.' AND igs.chair = ' . $chair;

        $list_unique = Yii::app()->db2->createCommand($sql)->queryRow();

        if(!empty($list_unique)){
            $list = $list_unique;
        }

        if(empty($list)){
            $list = [];
            $list['openplan'] = '0';
            $list['openfact'] = '0';
        }
        return $list;
    }

}
