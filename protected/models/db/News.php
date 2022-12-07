<?php

/**
 * This is the model class for table "{{news}}".
 *
 * The followings are the available columns in table '{{news}}':
 * @property integer $id
 * @property string $title
 * @property string $annonce
 * @property string $content
 * @property integer $status
 * @property string $createdAt
 */
class News extends CActiveRecord
{

    const STATUS_HIDED = 0;
    const STATUS_SHOW_ALL = 1;
    const STATUS_SHOW_PPS = 2;
    const STATUS_SHOW_STUDENTS = 3;

    public static function GetStatus($type)
    {
        switch ($type) {
            case self::STATUS_HIDED:
                return 'Скрыто';
            case self::STATUS_SHOW_ALL:
                return 'Для всех';
            case self::STATUS_SHOW_PPS:
                return 'ППС';
            case self::STATUS_SHOW_STUDENTS:
                return 'Студентам';
            default:
                return $type;
        }
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return News the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{news}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('title, annonce', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('status', 'default', 'value' => self::STATUS_SHOW_ALL),
            array('content, createdAt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, status, createdAt', 'safe', 'on' => 'search'),
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
            'title' => 'Заголовок',
            'annonce' => 'Анонс',
            'content' => 'Содержимое',
            'status' => 'Статус',
            'createdAt' => 'Время создания',
        );
    }

    public function search()
    {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('createdAt', $this->createdAt, true);
        $criteria->order = 'createdAt DESC';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        if ('0000-00-00 00:00:00' == $this->createdAt) {
            $this->createdAt = date('Y-m-d H:i:s');
        }
        return parent::beforeSave();
    }

}
