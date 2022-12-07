<?php


class NoteConfirm extends CActiveRecord
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
        return 'note_confirm';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('note_id, user_id, create_at', 'required'),
            array('note_id, user_id', 'numerical', 'integerOnly' => true),
            // @todo Please remove those attributes that should not be searched.
            array('id, note_id, user_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'note' => array(self::BELONGS_TO, 'Note', 'note_id', 'joinType' => 'INNER JOIN'),
        );
    }
}