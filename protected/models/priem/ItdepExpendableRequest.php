<?php

/**
 * This is the model class for table "itdep_expendable_request".
 *
 * The followings are the available columns in table 'itdep_expendable_request':
 * @property integer $id
 * @property string $auction
 * @property integer $struct
 * @property integer $device
 * @property string $invertNumber
 * @property integer $typeCart
 * @property integer $amount
 * @property string $placement
 * @property string $responsible
 * @property string $phone
 * @property string $email
 * @property string $comment
 * @property string $last_date_time
 * @property string $creater
 */
class ItdepExpendableRequest extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'itdep_expendable_request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('auction, struct, device, invertNumber, typeCart, amount, placement, responsible, phone, email', 'required'),
            array('struct, device, typeCart, amount', 'numerical', 'integerOnly'=>true),
            array('auction, invertNumber, placement, responsible, phone, comment, creater', 'length', 'max'=>255),
            array('email', 'length', 'max'=>50),
            array('last_date_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, auction, struct, device, invertNumber, typeCart, amount, placement, responsible, phone, email, comment, last_date_time, creater', 'safe', 'on'=>'search'),
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
            'auction' => 'Аукцион',
            'struct' => 'Струтурное подразделение',
            'device' => 'Устройство из каталога',
            'invertNumber' => 'Инвертарный номер устройства',
            'typeCart' => 'Тип картриджа устройства',
            'amount' => 'Количество',
            'placement' => 'Место размещения устройства',
            'responsible' => 'Материально ответственное лицо',
            'phone' => 'Телефонный номер',
            'email' => 'Электронный почтовый адрес',
            'comment' => 'Комментарий',
            'last_date_time' => 'Дата последнего изменения',
            'creater' => 'Составитель заявки',
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
        $criteria->compare('auction',$this->auction,true);
        $criteria->compare('struct',$this->struct);
        $criteria->compare('device',$this->device);
        $criteria->compare('invertNumber',$this->invertNumber,true);
        $criteria->compare('typeCart',$this->typeCart);
        $criteria->compare('amount',$this->amount);
        $criteria->compare('placement',$this->placement,true);
        $criteria->compare('responsible',$this->responsible,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('comment',$this->comment,true);
        $criteria->compare('last_date_time',$this->last_date_time,true);
        $criteria->compare('creater',$this->creater,true);

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
     * @return ItdepExpendableRequest the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
