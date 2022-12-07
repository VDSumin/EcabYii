<?php

class InquiriesResponsibles extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RemoteTaskList the static model class
     */
    public static function model($className = __CLASS__)
    {
        self::checkTables();
        return parent::model($className);
    }

    public static function checkTables()
    {
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_inquiries_requests\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_inquiries_requests (
                                              id INT(11) NOT NULL AUTO_INCREMENT,
                                              groupNpp INT(11) NOT NULL,
                                              studentNpp INT(11) NOT NULL,
                                              typeId INT(11) NOT NULL DEFAULT 0,
                                              startDate DATE NOT NULL DEFAULT 0,
                                              finishDate DATE NOT NULL DEFAULT 0,
                                              filePath VARCHAR(255) DEFAULT \'\',
                                              additional VARCHAR(255) DEFAULT \'\',
                                              facultyNrec VARBINARY(8) DEFAULT NULL,
                                              createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                              modifiedAt TIMESTAMP NOT NULL DEFAULT 0,
                                              takePickUp int(11) DEFAULT NULL,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }
        if ($sql = (Yii::app()->db->createCommand('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name=\'tbl_inquiries_requests\'')->queryAll())) {
            $needCreation = true;
            foreach ($sql as $item) {
                if ($item['COLUMN_NAME'] == 'facultyNrec') {
                    $needCreation = false;
                }
            }
            if ($needCreation) {
                Yii::app()->db->createCommand('ALTER TABLE tbl_inquiries_requests ADD COLUMN facultyNrec VARBINARY(8) DEFAULT NULL AFTER additional;')->query();
            }
        }
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_inquiries_types\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_inquiries_types (
                                              id INT(11) NOT NULL AUTO_INCREMENT,
                                              name VARCHAR(255) DEFAULT \'\',
                                              createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                              modifiedAt TIMESTAMP NOT NULL DEFAULT 0,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_inquiries_responsibles\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_inquiries_responsibles (
                                              id INT(11) NOT NULL AUTO_INCREMENT,
                                              typeId INT(11) NOT NULL,
                                              responsibleNpp INT(11) NOT NULL,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            AUTO_INCREMENT = 1,
                                            AVG_ROW_LENGTH = 3780,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci')->query();
        }

        if ($sql = Yii::app()->db->createCommand()
            ->select('id, groupNpp, studentNpp')
            ->from(InquiriesRequests::model()->tableName())
            ->where('facultyNrec IS NULL')
            ->queryAll()
        ) {
            foreach ($sql as $item) {
                if ($item['groupNpp'] !== '-1') {
                    $faculty = Yii::app()->db2->createCommand()
                        ->selectDistinct('gs.cfaculty')
                        ->from(Fdata::model()->tableName() . ' f')
                        ->leftJoin(Skard::model()->tableName() . ' s', 's.fnpp = f.npp')
                        ->join('gal_u_student gs', 'gs.nrec = s.gal_srec')
                        ->where('s.npp = ' . $item['groupNpp'] . ' AND gs.cfaculty IS NOT NULL')
                        ->queryScalar();
                } else {
                    $faculty = Yii::app()->db2->createCommand()
                        ->selectDistinct('gs.cfaculty')
                        ->from(Fdata::model()->tableName() . ' f')
                        ->leftJoin(Skard::model()->tableName() . ' s', 's.fnpp = f.npp')
                        ->join('gal_u_student gs', 'gs.nrec = s.gal_srec')
                        ->where('f.npp = ' . $item['studentNpp'] . ' AND gs.cfaculty IS NOT NULL')
                        ->order('gs.warch, gs.appdate')
                        ->queryScalar();
                }
                Yii::app()->db->createCommand()->update(
                    InquiriesRequests::model()->tableName(),
                    array(
                        'facultyNrec' => $faculty,
                    ),
                    'id = ' . $item['id']
                );
            }
        }

    }

    public static function amIResponsible()
    {
        if (!Yii::app()->user->getPerStatus()) return false;
        if (InquiriesResponsibles::model()->countByAttributes(array(
                'responsibleNpp' => Yii::app()->user->getFnpp()
            )) > 0) {
            return true;
        }
        return (!is_null(self::getDeanFaculties()));
    }

    public static function getDeanFaculties()
    {//TODO API
        if ($sql = Yii::app()->db2->createCommand()
            ->selectDistinct('g.cdepartment')
            ->from(Fdata::model()->tableName() . ' f')
            ->join(Keylinks::model()->tableName() . ' k', 'k.fnpp = f.npp')
            ->join('gal_up_roles g', 'k.gal_unid = g.personNrec')
            ->where('g.role LIKE \'%dean%\' and f.npp = ' . Yii::app()->user->getFnpp())
            ->queryAll()
        ) {
            $arr = array();
            foreach ($sql as $item) {
                array_push($arr, '0x' . bin2hex($item['cdepartment']));
            }
            return $arr;
        } else {
            return null;
        }
    }

    public static function checkFacultyRights($id)
    {
        if (is_null($faculties = self::getDeanFaculties())) return false;
        $criteria = new CDbCriteria();
        $criteria->addCondition('id = ' . $id);
        $criteria->addInCondition('typeId', array(
                InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::PFR))->id,
                InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::PLACE_OF_STUDY))->id)
        );
        $criteria->addCondition('facultyNrec IN (' . implode(', ', $faculties) . ')');
        return (InquiriesRequests::model()->count($criteria) != 0);
    }

    public static function getResponsibleTypes()
    {
        $model = self::model();
        $query = $model->findAllByAttributes(array(
            'responsibleNpp' => Yii::app()->user->getFnpp()
        ));
        $arr = array();
        foreach ($query as $t) {
            array_push($arr, $t->typeId);
        }
        array_unique($arr);
        return $arr;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{inquiries_responsibles}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, typeId, responsibleNpp', 'numerical', 'integerOnly' => true),
            array('id, typeId, responsibleNpp', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'typeId' => 'Тип заявки',
            'responsibleNpp' => 'Ответственный'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db;
    }
}