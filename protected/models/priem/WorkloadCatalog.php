<?php

/**
 * This is the model class for table "workload_catalog".
 *
 * The followings are the available columns in table 'workload_catalog':
 * @property integer $id
 * @property string $nameFull
 * @property string $nameShort
 *
 * The followings are the available model relations:
 * @property WorkloadPlanActual[] $workloadPlanActuals
 */
class WorkloadCatalog extends CActiveRecord
{
    const TW_LECTION = 1; //Лекция
    const TW_PRACTICE_CLASSES = 2; //Практические занятия
    const TW_LAB = 3; //Лабораторные работы
    const TW_CURRENT_CONST = 4; //Консультации текущие
    const TW_CONST_KSR = 5; //Консультации КСР
    const TW_CONST_BEFOR_EXAM = 7; //Консультации перед экзаменом
    const TW_EXAM = 9; //Экзамен
    const TW_CHIEF = 10; //Кафедра
    const TW_PRACTICE = 11; //Практика
    const TW_VKR_LEAD = 12; //ВКР руководство
    const TW_VKR_CONST = 13; //ВКР консультации
    const TW_VKR_EXAM = 14; //ВКР - ГЭК
    const TW_LEAD_GRADUATE = 16; //Руководство аспирантами

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'workload_catalog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nameFull, nameShort', 'required'),
			array('nameFull, nameShort', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, nameFull, nameShort', 'safe', 'on'=>'search'),
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
			'workloadPlanActuals' => array(self::HAS_MANY, 'WorkloadPlanActual', 'typeOfLoad'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Уникальный ключ',
			'nameFull' => 'Наименование полное',
			'nameShort' => 'Наименование короткое',
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
		$criteria->compare('nameFull',$this->nameFull,true);
		$criteria->compare('nameShort',$this->nameShort,true);

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
	 * @return WorkloadCatalog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getTypeWorkFromGalaxy($typework){
	    $galaxy = [
            WorkloadCatalog::TW_LECTION => uTypework::TW_LECTION,
            WorkloadCatalog::TW_LAB => uTypework::TW_LAB,
            WorkloadCatalog::TW_PRACTICE_CLASSES => uTypework::TW_PRACTICE_CLASSES,
            WorkloadCatalog::TW_EXAM => uTypework::TW_EXAM,
            WorkloadCatalog::TW_CONST_BEFOR_EXAM => uTypework::TW_EXAM_CONSULTATION,
            WorkloadCatalog::TW_CURRENT_CONST => uTypework::TW_CUR_CONSULTATION,
            WorkloadCatalog::TW_CONST_KSR => uTypework::TW_KSR
        ];

        return isset($galaxy[$typework]) ? $galaxy[$typework] : null;
    }

    public static function getTypeWorkFromRuz($typework){
        $galaxy = [
            WorkloadCatalog::TW_LECTION => [1],
            WorkloadCatalog::TW_LAB => [90],
            WorkloadCatalog::TW_PRACTICE_CLASSES => [91],
            WorkloadCatalog::TW_EXAM => [92],
            WorkloadCatalog::TW_CONST_BEFOR_EXAM => [93],
            //WorkloadCatalog::TW_CURRENT_CONST => [11]
        ];

        return isset($galaxy[$typework]) ? $galaxy[$typework] : null;
    }

    public static function getTypeWorkFromRuz2015($typework){
        $galaxy = [
            WorkloadCatalog::TW_LECTION => [1,6],
            WorkloadCatalog::TW_LAB => [7],
            WorkloadCatalog::TW_PRACTICE_CLASSES => [8, 2],
            WorkloadCatalog::TW_EXAM => [9],
            WorkloadCatalog::TW_CONST_BEFOR_EXAM => [10],
            WorkloadCatalog::TW_CURRENT_CONST => [11]
        ];

        return isset($galaxy[$typework]) ? $galaxy[$typework] : null;
    }
}
