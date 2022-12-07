<?php

/**
 * This is the model class for table "attendance_galruz_group".
 *
 * The followings are the available columns in table 'attendance_galruz_group':
 * @property integer $id
 * @property string $gal_nrec
 * @property string $name
 * @property string $stwpRec
 * @property string $curpRec
 * @property string $cfaculty
 * @property string $cchair
 * @property integer $wformed
 * @property integer $warch
 * @property integer $course
 *
 * The followings are the available model relations:
 * @property AttendanceSchedule[] $attendanceSchedules
 */
class AttendanceGalruzGroup extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'attendance_galruz_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('gal_nrec, name', 'required'),
            array('wformed, warch, course', 'numerical', 'integerOnly'=>true),
            array('gal_nrec, stwpRec, curpRec, cfaculty, cchair', 'length', 'max'=>8),
            array('name', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, gal_nrec, name, stwpRec, curpRec, cfaculty, cchair, wformed, warch, course', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'attendanceSchedules' => array(self::HAS_MANY, 'AttendanceSchedule', 'studGroupId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Уникальный ключ',
            'gal_nrec' => 'NREC группы из галактики',
            'name' => 'Имя группы',
            'stwpRec' => 'NREC старосты из галактики',
            'curpRec' => 'NREC куратора из галактики',
            'cfaculty' => 'NREC факультета, ссылка на каталог',
            'cchair' => 'NREC кафедры, ссылка на каталог',
            'wformed' => 'Форма обучения',
            'warch' => 'Признак архива группы',
            'course' => 'Курс группы',
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
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('gal_nrec',$this->gal_nrec,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('stwpRec',$this->stwpRec,true);
        $criteria->compare('curpRec',$this->curpRec,true);
        $criteria->compare('cfaculty',$this->cfaculty,true);
        $criteria->compare('cchair',$this->cchair,true);
        $criteria->compare('wformed',$this->wformed);
        $criteria->compare('warch',$this->warch);
        $criteria->compare('course',$this->course);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db2;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AttendanceGalruzGroup the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function inSchedule()
    {
        $result = Yii::app()->db2->createCommand()
            ->select('COUNT(*)')
            ->from('attendance_schedule ats')
            ->where(array('and',
                'ats.studGroupId = '.$this->id,
                'ats.dateTimeStartOfClasses > \'2020-04-01\''
            ))
            ->queryScalar();

        return $result;
    }
} 