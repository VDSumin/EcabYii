<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 24.04.2020
 * Time: 17:55
 */

/**
 * This is the model class for table "remote_extra_task".
 *
 * The followings are the available columns in table 'remote_extra_task':
 * @property integer $id
 * @property string $discipline
 * @property integer $group
 * @property integer $teacher
 * @property integer $chair
 * @property string $create_date
 * @property integer $author_fnpp
 * @property string $send_mail_date
 */
class RemoteExtraTask extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'remote_extra_task';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('group, teacher, chair, author_fnpp', 'numerical', 'integerOnly'=>true),
            array('discipline', 'length', 'max'=>8),
            array('create_date, send_mail_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, discipline, group, teacher, chair, create_date, author_fnpp, send_mail_date', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'discipline' => 'Discipline',
            'group' => 'Group',
            'teacher' => 'Teacher',
            'chair' => 'Chair',
            'create_date' => 'Create Date',
            'author_fnpp' => 'Author Fnpp',
            'send_mail_date' => 'Send Mail Date',
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
        $criteria->compare('discipline',$this->discipline,true);
        $criteria->compare('group',$this->group);
        $criteria->compare('teacher',$this->teacher);
        $criteria->compare('chair',$this->chair);
        $criteria->compare('create_date',$this->create_date,true);
        $criteria->compare('author_fnpp',$this->author_fnpp);
        $criteria->compare('send_mail_date',$this->send_mail_date,true);

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
     * @return RemoteExtraTask the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}