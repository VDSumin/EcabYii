<?php


class NotificationType extends CActiveRecord
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
        return 'notification_type';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'note_list' => array(self::HAS_MANY, 'NotificationList', 'notification_type_id', 'joinType' => 'INNER JOIN'),
        );
    }

}