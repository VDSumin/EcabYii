<?php

/**
 * This is the model class for table "itdep_oborud_request".
 *
 * The followings are the available columns in table 'itdep_oborud_request':
 * @property integer $id
 * @property integer $auction
 * @property string $struct
 * @property integer $coborud
 * @property string $kindOfActivity
 * @property string $purposeOfEquipment
 * @property string $composition
 * @property integer $amount
 * @property integer $finsource
 * @property string $replacement
 * @property string $placement
 * @property string $useOfExisting
 * @property string $finResponsible
 * @property string $reqResponsible
 * @property string $contacts
 * @property string $comment
 * @property string $dateAndTime
 */
class ItdepOborudRequest extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'itdep_oborud_request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('auction, struct, coborud, kindOfActivity, purposeOfEquipment, composition', 'required'),
            array('auction, coborud, amount, finsource', 'numerical', 'integerOnly'=>true),
            array('struct, kindOfActivity, replacement, placement, finResponsible, reqResponsible, contacts', 'length', 'max'=>255),
            array('purposeOfEquipment, composition, useOfExisting, comment, dateAndTime', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, auction, struct, coborud, kindOfActivity, purposeOfEquipment, composition, amount, finsource, replacement, placement, useOfExisting, finResponsible, reqResponsible, contacts, comment, dateAndTime', 'safe', 'on'=>'search'),
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
            'id' => 'Уникальный ключ',
            'auction' => 'Аукцион',
            'struct' => 'Подразделение',
            'coborud' => 'Тип оборудование',
            'kindOfActivity' => 'Вид деятельности',
            'purposeOfEquipment' => 'Назначение оборудования',
            'composition' => 'Характеристики',
            'amount' => 'Количество',
            'finsource' => 'Планируемый источник финансирования',
            'replacement' => 'Признак замены или нового',
            'placement' => 'Место нахождения обекта',
            'useOfExisting' => 'Дальнейшее использование имеющегося оборудования',
            'finResponsible' => 'Материально ответственный',
            'reqResponsible' => 'Ответственный за заявку',
            'contacts' => 'Контакты',
            'comment' => 'Комментарий',
            'dateAndTime' => 'Дата и время последнего редактирования',
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
        $criteria->compare('auction',$this->auction);
        $criteria->compare('struct',$this->struct,true);
        $criteria->compare('coborud',$this->coborud);
        $criteria->compare('kindOfActivity',$this->kindOfActivity,true);
        $criteria->compare('purposeOfEquipment',$this->purposeOfEquipment,true);
        $criteria->compare('composition',$this->composition,true);
        $criteria->compare('amount',$this->amount);
        $criteria->compare('finsource',$this->finsource);
        $criteria->compare('replacement',$this->replacement,true);
        $criteria->compare('placement',$this->placement,true);
        $criteria->compare('useOfExisting',$this->useOfExisting,true);
        $criteria->compare('finResponsible',$this->finResponsible,true);
        $criteria->compare('reqResponsible',$this->reqResponsible,true);
        $criteria->compare('contacts',$this->contacts,true);
        $criteria->compare('comment',$this->comment,true);
        $criteria->compare('dateAndTime',$this->dateAndTime,true);

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
	 * @return ItdepOborudRequest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
