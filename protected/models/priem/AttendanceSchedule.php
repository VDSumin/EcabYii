<?php

/**
 * This is the model class for table "attendance_schedule".
 *
 * The followings are the available columns in table 'attendance_schedule':
 * @property integer $id
 * @property integer $ruzId
 * @property string $dateTimeStartOfClasses
 * @property string $dateTimeEndOfClasses
 * @property string $discipline
 * @property string $disciplineNrec
 * @property integer $kindOfWorkId
 * @property string $studGroupName
 * @property integer $studGroupId
 * @property integer $typeOfWorkId
 * @property string $teacherFio
 * @property integer $teacherFnpp
 * @property string $auditorium
 * @property string $modifiedTime
 * @property string $totalHours
 * @property integer $yearOfEducation
 * @property string $semesterStartDate
 *
 * The followings are the available model relations:
 * @property Fdata $fdata
 * @property AttendanceGalruzGroup $studGroup
 * @property AttendanceKindofwork $kindOfWork
 * @property AttendanceTypeofwork $typeOfWork
 */
class AttendanceSchedule extends CActiveRecord
{
    public $formEdu;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'attendance_schedule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ruzId, dateTimeStartOfClasses, dateTimeEndOfClasses, discipline, kindOfWorkId, studGroupName, typeOfWorkId, teacherFio, modifiedTime, totalHours, yearOfEducation, semesterStartDate', 'required'),
			array('ruzId, kindOfWorkId, studGroupId, typeOfWorkId, teacherFnpp, yearOfEducation', 'numerical', 'integerOnly'=>true),
			array('disciplineNrec', 'length', 'max'=>20),
            array('studGroupName, teacherFio, auditorium', 'length', 'max'=>255),
			array('totalHours', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ruzId, dateTimeStartOfClasses, dateTimeEndOfClasses, discipline, disciplineNrec, kindOfWorkId, studGroupName, studGroupId, typeOfWorkId, teacherFio, teacherFnpp, auditorium, modifiedTime, totalHours, yearOfEducation, semesterStartDate', 'safe', 'on'=>'search'),
            array('id, ruzId, dateTimeStartOfClasses, dateTimeEndOfClasses, discipline, disciplineNrec, kindOfWorkId, studGroupName, formEdu, studGroupId, typeOfWorkId, teacherFio, teacherFnpp, auditorium, modifiedTime, totalHours, yearOfEducation, semesterStartDate', 'safe', 'on'=>'searchDis'),
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
			'fdata' => array(self::BELONGS_TO, 'Fdata', 'teacherFnpp'),
			'studGroup' => array(self::BELONGS_TO, 'AttendanceGalruzGroup', 'studGroupId'),
			'kindOfWork' => array(self::BELONGS_TO, 'AttendanceKindofwork', 'kindOfWorkId'),
			'typeOfWork' => array(self::BELONGS_TO, 'AttendanceTypeofwork', 'typeOfWorkId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Уникальный ключ',
			'ruzId' => 'Уникальный ключ из РУЗ',
			'dateTimeStartOfClasses' => 'Дата и время начала занятие',
			'dateTimeEndOfClasses' => 'Дата и время окончания занятия',
			'discipline' => 'Дисциплина',
			'disciplineNrec' => 'NREC дисциплины',
			'kindOfWorkId' => 'Ссылка на вид занятий',
			'studGroupName' => 'Группа (поток) из расписания',
			'studGroupId' => 'Ссылка на таблицу связей групп',
			'typeOfWorkId' => 'Ссылка на тип занятий',
			'teacherFio' => 'ФИО преподавателя из расписания',
			'teacherFnpp' => 'Ссылка на fdata ',
            'auditorium' => 'Аудитория строки расписания',
			'modifiedTime' => 'Дата изменения занятия',
			'totalHours' => 'Общее количество часов по дисциплине',
			'yearOfEducation' => 'Учебный год',
            'semesterStartDate' => 'Дата начала семестра',
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
		$criteria->compare('ruzId',$this->ruzId);
		$criteria->compare('dateTimeStartOfClasses',$this->dateTimeStartOfClasses,true);
		$criteria->compare('dateTimeEndOfClasses',$this->dateTimeEndOfClasses,true);
		$criteria->compare('discipline',$this->discipline,true);
		$criteria->compare('disciplineNrec',$this->disciplineNrec,true);
		$criteria->compare('kindOfWorkId',$this->kindOfWorkId);
		$criteria->compare('studGroupName',$this->studGroupName,true);
		$criteria->compare('studGroupId',$this->studGroupId);
		$criteria->compare('typeOfWorkId',$this->typeOfWorkId);
		$criteria->compare('teacherFio',$this->teacherFio,true);
		$criteria->compare('teacherFnpp',$this->teacherFnpp);
        $criteria->compare('auditorium',$this->auditorium,true);
		$criteria->compare('modifiedTime',$this->modifiedTime,true);
		$criteria->compare('totalHours',$this->totalHours,true);
		$criteria->compare('yearOfEducation',$this->yearOfEducation);
        $criteria->compare('semesterStartDate',$this->semesterStartDate,true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function searchDis()
    {
        //var_dump( $this->dateTimeStartOfClasses); die;
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('ruzId',$this->ruzId);
        $criteria->compare('dateTimeStartOfClasses',$this->dateTimeStartOfClasses,true);
        $criteria->compare('dateTimeEndOfClasses',$this->dateTimeEndOfClasses,true);
        $criteria->compare('discipline',$this->discipline,true);
        $criteria->compare('disciplineNrec',$this->disciplineNrec,true);
        $criteria->compare('kindOfWorkId',$this->kindOfWorkId);
        $criteria->compare('studGroupName',$this->studGroupName,true);
        $criteria->compare('studGroupId',$this->studGroupId);
        $criteria->compare('typeOfWorkId',$this->typeOfWorkId);
        $criteria->compare('teacherFio',$this->teacherFio,true);
        $criteria->compare('teacherFnpp',$this->teacherFnpp);
        $criteria->compare('modifiedTime',$this->modifiedTime,true);
        $criteria->compare('totalHours',$this->totalHours,true);
        $criteria->compare('yearOfEducation',$this->yearOfEducation);
        $criteria->compare('semesterStartDate',$this->semesterStartDate,true);
        $criteria->with = 'studGroup';
        $criteria->compare('studGroup.wformed', $this->formEdu);
        $criteria->order = 't.dateTimeStartOfClasses ASC';

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
	 * @return AttendanceSchedule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getDisActualByPersonAndYear(){
	    $criteria = new CDbCriteria();
	    $criteria->distinct = true;
	    $criteria->select = 'discipline';
	    $criteria->compare('teacherFnpp', Yii::app()->session['fnpp']);
	    $criteria->compare('yearOfEducation', Yii::app()->session['yearEdu']);

	    $model = self::model()->findAll($criteria);
	    return $model;
    }

    public static function getGroupActualByPersonAndYear(){
        $criteria = new CDbCriteria();
        $criteria->distinct = true;
        $criteria->select = 'studGroupName, studGroupId';
        $criteria->compare('teacherFnpp', Yii::app()->session['fnpp']);
        $criteria->compare('yearOfEducation', Yii::app()->session['yearEdu']);

        $model = self::model()->findAll($criteria);
        return $model;
    }

    public static function getMyAttendanceStudent($fnpp){
        $listAttendance = [];
        if($fnpp != null) {
            $result = Yii::app()->db2->createCommand()
                ->selectDistinct(' (SELECT agg.id FROM attendance_galruz_group agg WHERE agg.name = s.gruppa) npp, s.gruppa')
                ->from('skard s')
                ->where('s.fnpp = ' . Yii::app()->user->getFnpp())
                ->andWhere('s.prudal = 0')
                ->andWhere('s.gruppa NOT LIKE \'ABIT\'')
                ->andWhere('s.gruppa NOT LIKE \'%+%\'')
                ->andWhere('(SELECT agg.id FROM attendance_galruz_group agg WHERE agg.name = s.gruppa) IS NOT NULL')
                ->group('s.gruppa')
                ->queryAll();

            $listGroupId = [];
            foreach ($result as $one){
                $listGroupId[] = $one['npp'];

            }
            if( count($listGroupId) != 0) {
                $listAttendance = Yii::app()->db2->createCommand()
                    ->select('ats.*, lga.ldate date, CONVERT(ats.dateTimeStartOfClasses, TIME) stime
                , CONVERT(ats.dateTimeEndOfClasses, TIME) etime, ak.name kind')
                    ->from('(SELECT CURDATE() ldate UNION SELECT DATE_ADD(CURDATE(), INTERVAL 1 DAY)) lga')
                    ->leftJoin('attendance_schedule ats', 'CONVERT(ats.dateTimeStartOfClasses, date) = lga.ldate AND ats.studGroupId IN (' . implode(',', $listGroupId) . ')')
                    ->leftJoin('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
                    ->order('lga.ldate, ats.dateTimeStartOfClasses')
                    ->queryAll();
            }
        }

        return $listAttendance;
    }

    public static function getMyAttendanceTeacher($fnpp){
        if($fnpp != null) {
            $listAttendance = Yii::app()->db2->createCommand()
                ->select('ats.*, lga.ldate date, CONVERT(ats.dateTimeStartOfClasses, TIME) stime
                , CONVERT(ats.dateTimeEndOfClasses, TIME) etime, ak.name kind')
                ->from('(SELECT CURDATE() ldate UNION SELECT DATE_ADD(CURDATE(), INTERVAL 1 DAY)) lga')
                ->leftJoin('attendance_schedule ats', 'CONVERT(ats.dateTimeStartOfClasses, date) = lga.ldate AND ats.teacherFnpp = '. $fnpp)
                ->leftJoin('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
                ->order('lga.ldate, ats.dateTimeStartOfClasses')
                ->queryAll();
        }else{
            $listAttendance = [];
        }
//        var_dump($listAttendance);die;
        return $listAttendance;
    }


}