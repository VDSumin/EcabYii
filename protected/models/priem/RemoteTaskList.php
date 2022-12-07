<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 16.03.2020
 * Time: 18:35
 */
/**
 * This is the model class for table "remote_task_list".
 *
 * The followings are the available columns in table 'remote_task_list':
 * @property integer $id
 * @property string $discipline
 * @property integer $group
 * @property string $comment
 * @property string $create_date
 * @property integer $author_fnpp
 * @property integer $file_from
 * @property integer $send_mail
 */
class RemoteTaskList extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'remote_task_list';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, group, author_fnpp, file_from, send_mail', 'numerical', 'integerOnly'=>true),
            array('discipline', 'length', 'max'=>8),
            array('comment, create_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, discipline, group, comment, create_date, author_fnpp, file_from, send_mail', 'safe', 'on'=>'search'),
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
            'discipline' => 'Дисциплина',
            'group' => 'Группа',
            'comment' => 'Комментарий',
            'create_date' => 'Дата создания',
            'author_fnpp' => 'Автор',
            'file_from' => 'Ссылка на файлы',
            'send_mail' => 'Письмо отправлено',
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
        $criteria->compare('comment',$this->comment,true);
        $criteria->compare('create_date',$this->create_date,true);
        $criteria->compare('author_fnpp',$this->author_fnpp);
        $criteria->compare('author_fnpp',$this->file_from);
        $criteria->compare('author_fnpp',$this->send_mail);

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
     * @return RemoteTaskList the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}