<?php

class DefaultController extends Controller
{

    public $layout = '//layouts/column1';

    public function actionIndex()
    {
        $this->checkTables();
        $fnpp = Yii::app()->user->getFnpp();
        if (!$fnpp) {
            $this->redirect(array('/site'));
        }
        $list = MonitorAccess::getDepartments($fnpp);

        if ($list) {
            $this->render('index', array(
                'departments' => $list,
                'boss' => (($fnpp == 704162) || ($fnpp == 512) || ($fnpp == 710) || ($fnpp == 688231)
                || in_array($fnpp, self::ADMIN_FNPP))
            ));
        } else {
            $this->redirect(array('/site'));
        }

    }

    private function checkTables()
    {
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_chief_reports_day\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_chief_reports_day (
                                              id INT(11) NOT NULL AUTO_INCREMENT,
                                              fnpp INT(11) NOT NULL,
                                              status TINYINT(4) NOT NULL DEFAULT 0,
                                              country VARCHAR(255) DEFAULT \'\',
                                              wasAbroad TINYINT(1) DEFAULT 0,
                                              country2 VARCHAR(255) DEFAULT \'\',
                                              additional VARCHAR(255) DEFAULT \'\',
                                              createdAt DATE NOT NULL DEFAULT 0,
                                              confirmedAt DATE NOT NULL DEFAULT 0,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }
        if ($sql = (Yii::app()->db->createCommand('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name=\'tbl_chief_reports_day\'')->queryAll())) {
            $needCreation = true;
            foreach ($sql as $item) {
                if ($item['COLUMN_NAME'] == 'wasAbroad') {
                    $needCreation = false;
                }
            }
            if ($needCreation) {
                Yii::app()->db->createCommand('ALTER TABLE tbl_chief_reports_day ADD COLUMN wasAbroad TINYINT(1) DEFAULT 0 AFTER country;
                    ALTER TABLE tbl_chief_reports_day ADD COLUMN country2 VARCHAR(255) DEFAULT \'\' AFTER wasAbroad;')->query();
            }
        }
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_chief_reports_week\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_chief_reports_week (
                                              id INT(11) NOT NULL AUTO_INCREMENT,
                                              fnpp INT(11) NOT NULL,
                                              format TINYINT(4) NOT NULL DEFAULT 0,
                                              reasonId TINYINT(4) NOT NULL DEFAULT 0,
                                              reason VARCHAR(255) DEFAULT \'\',
                                              createdAt DATE NOT NULL DEFAULT 0,
                                              confirmedAt DATE NOT NULL DEFAULT 0,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }
        if ($sql = (Yii::app()->db->createCommand('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name=\'tbl_chief_reports_week\'')->queryAll())) {
            $needCreation = true;
            foreach ($sql as $item) {
                if ($item['COLUMN_NAME'] == 'reasonId') {
                    $needCreation = false;
                }
            }
            if ($needCreation) {
                Yii::app()->db->createCommand('ALTER TABLE tbl_chief_reports_week ADD COLUMN reasonId TINYINT(4) NOT NULL DEFAULT 0 AFTER format;')->query();
            }
        }
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_chief_reports_category\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_chief_reports_category (
                                              id INT(11) NOT NULL AUTO_INCREMENT,
                                              fnpp INT(11) NOT NULL UNIQUE,
                                              category TINYINT(4) NOT NULL DEFAULT 0,
                                              createdAt DATE NOT NULL DEFAULT 0,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }
        $sql = Yii::app()->db->createCommand('SELECT column_type type FROM information_schema.columns WHERE table_name=\'tbl_chief_reports_week\' AND column_name = \'createdAt\'')->queryRow();
        if ($sql['type'] == 'timestamp') {
            Yii::app()->db->createCommand('ALTER TABLE `tbl_chief_reports_week`
                CHANGE `createdAt` `createdAt` DATE DEFAULT 0 NOT NULL AFTER `reason`,
                COMMENT=\'\';')->query();
        }
        $sql = Yii::app()->db->createCommand('SELECT column_type type FROM information_schema.columns WHERE table_name=\'tbl_chief_reports_week\' AND column_name = \'confirmedAt\'')->queryRow();
        if ($sql['type'] == 'timestamp') {
            Yii::app()->db->createCommand('ALTER TABLE `tbl_chief_reports_week`
                CHANGE `confirmedAt` `confirmedAt` DATE DEFAULT 0 NOT NULL AFTER `createdAt`,
                COMMENT=\'\';')->query();
        }
        $sql = Yii::app()->db->createCommand('SELECT column_type type FROM information_schema.columns WHERE table_name=\'tbl_chief_reports_day\' AND column_name = \'createdAt\'')->queryRow();
        if ($sql['type'] == 'timestamp') {
            Yii::app()->db->createCommand('ALTER TABLE `tbl_chief_reports_day`
                CHANGE `createdAt` `createdAt` DATE DEFAULT 0 NOT NULL AFTER `additional`,
                COMMENT=\'\';')->query();
        }
        $sql = Yii::app()->db->createCommand('SELECT column_type type FROM information_schema.columns WHERE table_name=\'tbl_chief_reports_day\' AND column_name = \'confirmedAt\'')->queryRow();
        if ($sql['type'] == 'timestamp') {
            Yii::app()->db->createCommand('ALTER TABLE `tbl_chief_reports_day`
                CHANGE `confirmedAt` `confirmedAt` DATE DEFAULT 0 NOT NULL AFTER `createdAt`,
                COMMENT=\'\';')->query();
        }
        $sql = Yii::app()->db->createCommand('SELECT column_type type FROM information_schema.columns WHERE table_name=\'tbl_chief_reports_category\' AND column_name = \'createdAt\'')->queryRow();
        if ($sql['type'] == 'timestamp') {
            Yii::app()->db->createCommand('ALTER TABLE `tbl_chief_reports_category`
                CHANGE `createdAt` `createdAt` DATE DEFAULT 0 NOT NULL AFTER `category`,
                COMMENT=\'\';')->query();
        }

        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_chief_reports_covid\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_chief_reports_covid (
                                                id          int auto_increment
                                                    primary key,
                                                fnpp        int                       not null,
                                                covidStatus      tinyint  default 0            not null,
                                                date        date default \'1000-01-01\' not null,
                                                createdAt   date default \'1000-01-01\' not null,
                                                confirmedAt date default \'1000-01-01\' not null
                                            )                                           
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }
    }
}
