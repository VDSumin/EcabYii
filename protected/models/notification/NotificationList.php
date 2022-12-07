<?php


class NotificationList extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getDbConnection()
    {
        return Yii::app()->db2;
    }

    public function tableName()
    {
        return 'notification_list';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('note_id, notification_type_id, destination', 'required'),
            array('note_id, notification_type_id', 'numerical', 'integerOnly' => true),
            /*            array('block', 'length', 'max' => 255),
                        array('area', 'length', 'max' => 10),*/
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, note_id, notification_type, destination', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'note' => array(self::BELONGS_TO, 'Note', 'note_id', 'joinType' => 'INNER JOIN'),
            'type' => array(self::BELONGS_TO, 'NotificationType', 'notification_type_id', 'joinType' => 'INNER JOIN'),
        );
    }

}