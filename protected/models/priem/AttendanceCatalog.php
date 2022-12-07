<?php

/**
 * This is the model class for table "attendance_catalog".
 *
 * The followings are the available columns in table 'attendance_catalog':
 * @property integer $id
 * @property string $nameShort
 * @property string $nameFull
 * @property integer $gal_wmark
 * @property integer $countHours
 *
 * The followings are the available model relations:
 * @property AttendanceJournal[] $attendanceJournals
 * @property AttendanceJournal[] $attendanceJournals1
 */
class AttendanceCatalog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'attendance_catalog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nameShort, nameFull, gal_wmark, countHours', 'required'),
			array('gal_wmark, countHours', 'numerical', 'integerOnly'=>true),
			array('nameShort, nameFull', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, nameShort, nameFull, gal_wmark, countHours', 'safe', 'on'=>'search'),
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
			'attendanceJournals' => array(self::HAS_MANY, 'AttendanceJournal', 'teacherMarkId'),
			'attendanceJournals1' => array(self::HAS_MANY, 'AttendanceJournal', 'stwpMarkId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Уникальный ключ',
			'nameShort' => 'Короткое наименование ',
			'nameFull' => 'Полное наименование',
			'gal_wmark' => 'Оценка из галактики',
			'countHours' => 'Количество часов',
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
		$criteria->compare('nameShort',$this->nameShort,true);
		$criteria->compare('nameFull',$this->nameFull,true);
		$criteria->compare('gal_wmark',$this->gal_wmark);
		$criteria->compare('countHours',$this->countHours);

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
	 * @return AttendanceCatalog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
