<?php

class StudyProcessModule extends CWebModule {

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'studyProcess.models.*',
            'studyProcess.components.*',
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

    public static function checkEntHashRows ($hash){
        self::checkEntHashTable();
        $return = false;
        $model = UpSettings::model()->findByAttributes(array("name"=>"hashEnterprise"));
        if($model instanceof UpSettings){
            if($model->value == $hash){
                $return = true;
            }else{
                $return = false;
            }
        }else{
            $model = new UpSettings();
            $model->name = "hashEnterprise";
            $model->value = "";
            $model->description = "Поле определяющее актуальность записей о предприятиях";
            $model->save();
            $return = false;
        }
        return $return;
    }

    public static function insertEntRows ($data, $hash){
        self::checkEntTable();
        EnterpriseList::model()->deleteAll();
        foreach ($data as $row) {
            $model = new EnterpriseList();
            $model->nrec = $row['nrec'];
            $model->nrec64 = $row['nrecint64'];
            $model->name = $row['label'];
            $model->save();
        }
        $model = UpSettings::model()->findByAttributes(array("name"=>"hashEnterprise"));
        $model->value = $hash;
        $model->save();
    }

    public static function checkEntTable (){
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_enterprise_list\'')->queryAll())) {
            Yii::app()->db->createCommand('create table tbl_enterprise_list
                                            (
                                                id int(11) not null auto_increment,
                                                nrec varchar(30) not null comment \'Идентификатор строки из Галактики\',
                                                nrec64 varchar(30) null comment \'Идентификатор приведенный в 10ную форму\',
                                                name varchar(255) not null comment \'Наименование предприятия\',
                                                constraint tbl_enterprise_list_pk
                                                    primary key (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci
                                            comment \'Список предприятий из Галактики\';')->query();
        }
    }

    public static function checkEntHashTable (){
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_up_settings\'')->queryAll())) {
            Yii::app()->db->createCommand('create table tbl_up_settings
                                            (
                                                id int(11) not null auto_increment,
                                                name        varchar(25) not null,
                                                value       text        not null,
                                                description text        not null,
                                                    primary key (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci
                                            comment \'Содержит хэши списков предприятий из Галактики\';')->query();
        }
    }

}
