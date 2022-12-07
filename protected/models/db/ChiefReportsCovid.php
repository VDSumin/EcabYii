<?php

/**
 * @property integer $statusCovid
 * @property integer $id
 * @property datetime $date
 * @property datetime $confirmedAt
 * @property datetime $createdAt
 * @property integer $fnpp
 */
class ChiefReportsCovid extends CActiveRecord
{


    //статус из таблицы tbl_chief_reports_covid
    const CATEGORY_COVID_EMPTY = 0;
    const CATEGORY_COVID_RECOVER = 1;
    const CATEGORY_COVID_FIRST = 2;
    const CATEGORY_COVID_SECOND = 3;
    const CATEGORY_COVID_RECUSAL = 4;
    const CATEGORY_COVID_GOSUSLUGI = 5;
    const CATEGORY_COVID_OT = 6;
    const CATEGORY_COVID_REFUSING = 7;
    const CATEGORY_COVID_DISMIS = 8;
    const CATEGORY_COVID_ANOTHER = 9;
    const CATEGORY_COVID_REVAC = 10;

    public static function getCovidStatus($type)
    {
        switch ($type) {
            case self::CATEGORY_COVID_EMPTY:
                return '-';
            case self::CATEGORY_COVID_RECOVER:
                return 'Переболел COVID-19';
            case self::CATEGORY_COVID_FIRST:
                return 'Сделана первая прививка';
            case self::CATEGORY_COVID_SECOND:
                return 'Сделана вторая прививка';
            case self::CATEGORY_COVID_RECUSAL:
                return 'Официальный мед.отвод';
            case self::CATEGORY_COVID_GOSUSLUGI:
                return 'Подал заявку (Госуслуги)';
            case self::CATEGORY_COVID_OT:
                return 'Подал заявку (Охрана труда)';
            case self::CATEGORY_COVID_REFUSING:
                return 'Отказался делать прививку';
            case self::CATEGORY_COVID_DISMIS:
                return 'Отстранен от работы';
            case self::CATEGORY_COVID_ANOTHER:
                return 'Иное (пояснить в примечании)';
            case self::CATEGORY_COVID_REVAC:
                return 'Ревакцинация';
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
        return '{{chief_reports_covid}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('fnpp, statusCovid', 'required'),
            array('fnpp, statusCovid', 'numerical', 'integerOnly' => true),
            array('statusCovid', 'default', 'value' => self::CATEGORY_COVID_EMPTY),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, fnpp, statusCovid, confirmedAt, createdAt', 'safe', 'on' => 'search'),
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
            'statusCovid' => 'Статус',
            'date' => 'Дата получения статуса',
            'confirmedAt' => 'Время подтверждения',
            'createdAt' => 'Время создания',
        );
    }

    public function search()
    {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('statusCovid', $this->statusCovid);
        $criteria->compare('fnpp', $this->fnpp);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
