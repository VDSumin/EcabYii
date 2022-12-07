<?php

/**
 * This is the model class for table "attendance_journal".
 *
 * The followings are the available columns in table 'attendance_journal':
 * @property integer $id
 * @property integer $scheduleId
 * @property string $studentNrec
 * @property integer $studentFnpp
 * @property integer $stwpMarkId
 * @property integer $teacherMarkId
 *
 * The followings are the available model relations:
 * @property AttendanceCatalog $teacherMark
 * @property AttendanceCatalog $stwpMark
 * @property AttendanceSchedule $schedule
 * @property Fdata $studentFnpp0
 */
class AttendanceJournal extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'attendance_journal';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('scheduleId, studentNrec, stwpMarkId, teacherMarkId', 'required'),
			array('scheduleId, studentFnpp, stwpMarkId, teacherMarkId', 'numerical', 'integerOnly'=>true),
			array('studentNrec', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, scheduleId, studentNrec, studentFnpp, stwpMarkId, teacherMarkId', 'safe', 'on'=>'search'),
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
			'teacherMark' => array(self::BELONGS_TO, 'AttendanceCatalog', 'teacherMarkId'),
			'stwpMark' => array(self::BELONGS_TO, 'AttendanceCatalog', 'stwpMarkId'),
			'schedule' => array(self::BELONGS_TO, 'AttendanceSchedule', 'scheduleId'),
			'studentFnpp0' => array(self::BELONGS_TO, 'Fdata', 'studentFnpp'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Уникальный ключ',
			'scheduleId' => 'Ссылка на пару в расписании',
			'studentNrec' => 'Gal u_student nrec',
			'studentFnpp' => 'Ссылка на fdata',
			'stwpMarkId' => 'Оценка старосты',
			'teacherMarkId' => 'Оценка преподавателя',
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
		$criteria->compare('scheduleId',$this->scheduleId);
		$criteria->compare('studentNrec',$this->studentNrec,true);
		$criteria->compare('studentFnpp',$this->studentFnpp);
		$criteria->compare('stwpMarkId',$this->stwpMarkId);
		$criteria->compare('teacherMarkId',$this->teacherMarkId);

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
	 * @return AttendanceJournal the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
