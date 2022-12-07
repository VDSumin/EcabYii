<?php

class ChiefReportsWeek extends CActiveRecord
{

    const FORMAT_DISTANT = 0;
    const FORMAT_PARTLY = 1;
    const FORMAT_INSIDE = 2;
    const FORMAT_QUARANTINE = 3;
    const FORMAT_OTHER = 4;

    public static function getFormat($type)
    {
        switch ($type) {
            case self::FORMAT_DISTANT:
                return 'Работает удаленно';
            case self::FORMAT_PARTLY:
                return 'Частично посещает здание';
            case self::FORMAT_INSIDE:
                return 'В здании организации';
            case self::FORMAT_QUARANTINE:
                return 'На карантине или самоизоляции';
            case self::FORMAT_OTHER:
                return 'Иная причина отсутствия';
            default:
                return $type;
        }
    }

    const REASON_EMPTY = 0;
    const REASON_VACATION = 1;
    const REASON_BABY = 2;
    const REASON_DISABLED = 3;
    const REASON_TRIP = 4;
    const REASON_OTHER = 5;

    public static function getReasonId($type)
    {
        switch ($type) {
            case self::REASON_EMPTY:
                return '';
            case self::REASON_VACATION:
                return 'Отпуск';
            case self::REASON_BABY:
                return 'Отпуск по уходу за ребенком';
            case self::REASON_DISABLED:
                return 'Временная нетрудоспособность';
            case self::REASON_TRIP:
                return 'Командировка';
            case self::REASON_OTHER:
                return 'Иное';
            default:
                return $type;
        }
    }

    const CATEGORY_EMPTY = 0;
    const CATEGORY_RETIRED = 1;
    const CATEGORY_PREGNANT = 2;
    const CATEGORY_CHILDREN = 3;
    const CATEGORY_OLD = 4;
    const CATEGORY_SICK = 5;

    public static function getCategory($type)
    {
        switch ($type) {
            case self::CATEGORY_EMPTY:
                return '-';
            case self::CATEGORY_RETIRED:
                return 'Достигшие пенсионного возраста';
            case self::CATEGORY_PREGNANT:
                return 'Беременные женщины';
            case self::CATEGORY_CHILDREN:
                return 'Женщины с детьми до 14 лет';
            case self::CATEGORY_OLD:
                return 'Старше 65 лет';
            case self::CATEGORY_SICK:
                return 'Имеющие заболевания';
            default:
                return $type;
        }
    }

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
        return '{{chief_reports_week}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('fnpp, format', 'required'),
            array('fnpp, format', 'numerical', 'integerOnly' => true),
            array('format', 'default', 'value' => self::FORMAT_DISTANT),
            array('reasonId', 'default', 'value' => self::REASON_EMPTY),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, format, reasonId, reason, confirmedAt, createdAt', 'safe', 'on' => 'search'),
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
            'format' => 'Формат работы',
            'reason' => 'Причина',
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
