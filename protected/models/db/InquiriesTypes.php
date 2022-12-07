<?php

class InquiriesTypes extends CActiveRecord
{
    const HOSTEL = 'Заявление об отмене начислений платы за проживание в период выезда из общежития';
    const PLACE_OF_STUDY = 'Справка с места учебы';
    const PFR = 'Справка в ПФР';
    const INCOME = 'Справка о доходах';

    public static function getTypeString($id)
    {
        $model = self::model();
        if ($model->findByPk($id) == null) {
            return $id;
        }
        return $model->findByPk($id)->name;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RemoteTaskList the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getTypes()
    {
        $model = self::model();
        return $model->getCommandBuilder()
            ->createFindCommand($model->tableSchema, $model->dbCriteria)
            ->queryAll();
    }

    public static function getHostelReasons()
    {
        return array(
            0 => 'для прохождения дистанционного обучения',
            1 => 'для выезда на практику',
            2 => 'в период каникул'
        );
    }

    public static function getTakesPickup($type = 1)
    {
        if($type == 1) {
            return array(
                1 => 'Получить скан. справки',
                2 => 'Забрать справку лично',
                3 => 'Отправить скан и получить справку лично'
            );
        }elseif($type == 2){
            return array(
                2 => 'Забрать справку лично'
            );
        }elseif($type == 3){
            return array(
                null => '',
                1 => 'Получить скан. справки',
                2 => 'Забрать справку лично',
                3 => 'Отправить скан и получить справку лично'
            );
        }else{
            return [];
        }
    }

    public static function getHostelReasonString($id)
    {
        return (isset(self::getHostelReasons()[$id]) ? self::getHostelReasons()[$id] : false);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{inquiries_types}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'numerical', 'integerOnly' => true),
            array('id, name, modifiedAt, createdAt', 'safe', 'on' => 'search'),
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
            'name' => 'Тип заявки',
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