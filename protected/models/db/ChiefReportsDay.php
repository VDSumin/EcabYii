<?php

class ChiefReportsDay extends CActiveRecord
{

    const STATUS_IN = 0;
    const STATUS_OUT = 1;
    const STATUS_ABROAD = 2;
    const STATUS_UNKNOWN = 3;

    public static function getStatus($type)
    {
        switch ($type) {
            case self::STATUS_IN:
                return 'На территории региона';
            case self::STATUS_OUT:
                return 'За территорией региона';
            case self::STATUS_ABROAD:
                return 'За пределами РФ';
            case self::STATUS_UNKNOWN:
                return 'Не известно';
            default:
                return $type;
        }
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{chief_reports_day}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('fnpp, status', 'required'),
            array('fnpp, status', 'numerical', 'integerOnly' => true),
            array('status', 'default', 'value' => self::STATUS_IN),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, status, country, additional, confirmedAt, createdAt', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'fnpp' => 'fnpp',
            'status' => 'Статус',
            'country' => 'Страна',
            'additional' => 'Примечание',
            'confirmedAt' => 'Время подтверждения',
            'createdAt' => 'Время создания',
        );
    }

    public function search()
    {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('status', $this->status);
        $criteria->compare('county', $this->title, true);
        $criteria->compare('additional', $this->title, true);
//        $criteria->compare('createdAt', $this->createdAt, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
