<?php

class AdminModule extends CWebModule
{

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'admin.models.*',
            'admin.components.*',
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

    public static function checkExistTable(){
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_api_keys\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_api_keys (
                                              id INT(11) NOT NULL AUTO_INCREMENT,
                                              fnpp INT(11) DEFAULT NULL,
                                              fio VARCHAR(100) NOT NULL,
                                              glogin VARCHAR(100) NOT NULL,
                                              apikey VARCHAR(100) NOT NULL,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }
        return true;
    }

}
