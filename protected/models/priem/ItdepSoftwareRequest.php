<?php

/**
 * This is the model class for table "itdep_software_request".
 *
 * The followings are the available columns in table 'itdep_software_request':
 * @property integer $id
 * @property string $auction
 * @property string $softName
 * @property string $versionSW
 * @property string $editionSW
 * @property integer $amount
 * @property string $kindOfActivity
 * @property string $purpose
 * @property integer $struct
 * @property integer $finsource
 * @property string $placement
 * @property string $responsible
 * @property string $contacts
 * @property string $comment
 * @property string $last_date_time
 * @property string $creater
 */
class ItdepSoftwareRequest extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'itdep_software_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('auction, softName, versionSW, editionSW, amount, kindOfActivity, purpose, struct, finsource, responsible, contacts', 'required'),
			array('amount, struct, finsource', 'numerical', 'integerOnly'=>true),
			array('auction, softName, versionSW, editionSW, kindOfActivity, purpose, placement, responsible, contacts, comment, creater', 'length', 'max'=>255),
			array('last_date_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, auction, softName, versionSW, editionSW, amount, kindOfActivity, purpose, struct, finsource, placement, responsible, contacts, comment, last_date_time, creater', 'safe', 'on'=>'search'),
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
            'softName' => 'Наименование ПО',
			'versionSW' => 'Версия ПО',
			'editionSW' => 'Редакция ПО',
			'amount' => 'Количество',
			'kindOfActivity' => 'Вид деятельности',
			'purpose' => 'Назначение (основание) приобретения',
			'struct' => 'Подразделение',
			'finsource' => 'Источник финансирования',
			'placement' => 'место установки ПО',
			'responsible' => 'материально ответственное лицо',
			'contacts' => 'Контактная информация',
			'comment' => 'Комментарий',
			'last_date_time' => 'Дата и время последнего редактирования',
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
        $criteria->compare('softName',$this->softName,true);
		$criteria->compare('versionSW',$this->versionSW,true);
		$criteria->compare('editionSW',$this->editionSW,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('kindOfActivity',$this->kindOfActivity,true);
		$criteria->compare('purpose',$this->purpose,true);
		$criteria->compare('struct',$this->struct);
		$criteria->compare('finsource',$this->finsource);
		$criteria->compare('placement',$this->placement,true);
		$criteria->compare('responsible',$this->responsible,true);
		$criteria->compare('contacts',$this->contacts,true);
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
	 * @return ItdepSoftwareRequest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
