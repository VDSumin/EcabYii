<?php

/**
 * This is the model class for table "workload_plan_actual".
 *
 * The followings are the available columns in table 'workload_plan_actual':
 * @property integer $id
 * @property integer $fnpp
 * @property string $chairNrec
 * @property integer $typeOfLoad
 * @property string $valueOfLoad
 * @property integer $yearOfLoad
 * @property integer $formEdu
 * @property integer $seasonOfLoad
 * @property integer $kindOfLoad
 * @property integer $isHourlyWorker
 *
 * The followings are the available model relations:
 * @property Fdata $fnpp0
 * @property WorkloadCatalog $typeOfLoad0
 */
class WorkloadPlanActual extends CActiveRecord
{
    const TYPE_LOAD_PLAN = 1;
    const TYPE_LOAD_ACTUAL = 2;
    const HOURLYWORKER_TEXT = 'На условиях почасовой оплаты';

    const SEASON_AUTUMN = 1;
    const SEASON_SPRING = 2;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'workload_plan_actual';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('chairNrec, typeOfLoad, valueOfLoad, yearOfLoad, formEdu, seasonOfLoad, kindOfLoad', 'required'),
            array('fnpp, typeOfLoad, yearOfLoad, formEdu, seasonOfLoad, kindOfLoad, isHourlyWorker', 'numerical', 'integerOnly'=>true),
            array('chairNrec', 'length', 'max'=>8),
            array('valueOfLoad', 'length', 'max'=>10),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, fnpp, chairNrec, typeOfLoad, valueOfLoad, yearOfLoad, formEdu, seasonOfLoad, kindOfLoad, isHourlyWorker', 'safe', 'on'=>'search'),
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
            'fnpp0' => array(self::BELONGS_TO, 'Fdata', 'fnpp'),
            'typeOfLoad0' => array(self::BELONGS_TO, 'WorkloadCatalog', 'typeOfLoad'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Уникальный ключ',
            'fnpp' => 'Преподаватель (ссылка на fdata)',
            'chairNrec' => 'Кафедра',
            'typeOfLoad' => 'Вид нагрузки ',
            'valueOfLoad' => 'Количество часов',
            'yearOfLoad' => 'Учебный год нагрузки',
            'formEdu' => 'Форма обучения',
            'seasonOfLoad' => 'Семестр нагрузки',
            'kindOfLoad' => 'Тип - 1 план, 2 - факт',
            'isHourlyWorker' => 'Почасовая запись',
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
        $criteria->compare('fnpp',$this->fnpp);
        $criteria->compare('chairNrec',$this->chairNrec,true);
        $criteria->compare('typeOfLoad',$this->typeOfLoad);
        $criteria->compare('valueOfLoad',$this->valueOfLoad);
        $criteria->compare('yearOfLoad',$this->yearOfLoad);
        $criteria->compare('formEdu',$this->formEdu);
        $criteria->compare('seasonOfLoad',$this->seasonOfLoad);
        $criteria->compare('kindOfLoad',$this->kindOfLoad);
        $criteria->compare('isHourlyWorker',$this->isHourlyWorker);

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
     * @return WorkloadPlanActual the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Insert load for one stuff in db
     *
     * @param $seasonOfLoad
     * @param $chair
     * @param $yearOfLoad
     * @param $fnpp
     */
    public static function getValueOfLoad($fnpp, $typeOfLoad, $seasonOfLoad,$kindOfLoad, $formEdu)
    {

        $criteria = new CDbCriteria();
        $criteria->compare('chairNrec', Yii::app()->session['chairNrec']);
        $criteria->compare('fnpp', $fnpp);
        $criteria->compare('typeOfLoad', $typeOfLoad);
        $criteria->compare('yearOfLoad', Yii::app()->session['yearEdu']);
        $criteria->compare('formEdu', $formEdu);
        $criteria->compare('seasonOfLoad', $seasonOfLoad);
        $criteria->compare('kindOfLoad', $kindOfLoad);
        $criteria->compare('isHourlyWorker', 0);
        $model = WorkloadPlanActual::model()->find($criteria);


        if ($model instanceof WorkloadPlanActual){
            return $model->valueOfLoad;
        } else {
            return 0;
        }
    }

    public static function getSummValueOfLoad($fnpp, $seasonOfLoad,$kindOfLoad, $formEdu)
    {
        $sql = 'SELECT                        
                        SUM(valueOfLoad)                        
                  FROM workload_plan_actual 
                  WHERE fnpp = '. Yii::app()->session['fnpp'].' 
                        AND yearOfLoad = '. Yii::app()->session['yearEdu'].'
                        AND kindOfLoad = '. $kindOfLoad .'
                        AND formEdu = '. $formEdu .'
                        AND seasonOfLoad = '. $seasonOfLoad .'
                        AND isHourlyWorker = 0       
                        AND chairNrec = 0x'. bin2hex(Yii::app()->session['chairNrec']);
        return Yii::app()->db2->createCommand($sql)->queryScalar();


    }

    /**
     * @return mixed
     */
    public static function getSummLoadByStuff(){

        $sql = 'SELECT                        
                        SUM(valueOfLoad)                        
                  FROM workload_plan_actual 
                  WHERE fnpp = '. Yii::app()->session['fnpp'].' 
                        AND yearOfLoad = '. Yii::app()->session['yearEdu'].'
                        AND kindOfLoad = '. self::TYPE_LOAD_PLAN .'  
                        AND chairNrec = 0x'. bin2hex(Yii::app()->session['chairNrec']);
        $load['plan'] = Yii::app()->db2->createCommand($sql)->queryScalar();

        $sql = 'SELECT                        
                        SUM(valueOfLoad)                        
                  FROM workload_plan_actual 
                  WHERE fnpp = '. Yii::app()->session['fnpp'].' 
                        AND yearOfLoad = '. Yii::app()->session['yearEdu'].'
                        AND kindOfLoad = '. self::TYPE_LOAD_ACTUAL .'  
                        AND chairNrec = 0x'.  bin2hex(Yii::app()->session['chairNrec']);
        $load['actual'] = Yii::app()->db2->createCommand($sql)->queryScalar();

        return $load;
    }




}