<?php

class ChiefsModule extends CWebModule
{

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'chiefs.models.*',
            'chiefs.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else {
            return false;
        }
    }

    public static function getMinDate()
    {
        $date = date('Y-m-d');
        if ($sql = Yii::app()->db->createCommand()
            ->select('createdAt')
            ->from('tbl_chief_reports_week')
            ->group('createdAt')
            ->order('createdAt')
            ->queryRow()) {
            $date = ($date > $sql['createdAt']) ? $sql['createdAt'] : $date;
        }
        if ($sql = Yii::app()->db->createCommand()
            ->select('createdAt')
            ->from('tbl_chief_reports_day')
            ->group('createdAt')
            ->order('createdAt')
            ->queryRow()) {
            $date = ($date > $sql['createdAt']) ? $sql['createdAt'] : $date;
        }
        return $date;
    }

}
