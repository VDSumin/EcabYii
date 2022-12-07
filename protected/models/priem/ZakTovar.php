<?php

/**
 * This is the model class for table "zak_tovar".
 *
 * The followings are the available columns in table 'zak_tovar':
 * @property integer $npp
 * @property integer $num
 * @property string $name
 * @property string $name2
 * @property string $precost
 * @property integer $num1
 * @property string $precost1
 * @property integer $struct
 * @property string $nds
 * @property integer $type
 *
 * The followings are the available model relations:
 * @property ZakAnalogs[] $zakAnalogs
 * @property ZakAuctionDet[] $zakAuctionDets
 * @property ZakOborud[] $zakOboruds
 * @property ZakTovartype $type0
 * @property ZakZakaz[] $zakZakazs
 */
class ZakTovar extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'zak_tovar';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('num, num1, struct, type', 'numerical', 'integerOnly'=>true),
			array('name, name2', 'length', 'max'=>150),
			array('precost, precost1, nds', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('npp, num, name, name2, precost, num1, precost1, struct, nds, type', 'safe', 'on'=>'search'),
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
			'zakAnalogs' => array(self::HAS_MANY, 'ZakAnalogs', 'tovar'),
			'zakAuctionDets' => array(self::HAS_MANY, 'ZakAuctionDet', 'tovar'),
			'zakOboruds' => array(self::MANY_MANY, 'ZakOborud', 'zak_obor_tovar(tovar, oborud)'),
			'type0' => array(self::BELONGS_TO, 'ZakTovartype', 'type'),
			'zakZakazs' => array(self::HAS_MANY, 'ZakZakaz', 'tovar'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'npp' => 'ID',
			'num' => 'Какой то номер',
			'name' => 'Название',
			'name2' => 'Типа принтер',
			'precost' => 'Precost',
			'num1' => 'Num1',
			'precost1' => 'Precost1',
			'struct' => 'Подразделение',
			'nds' => 'Nds',
			'type' => 'Type',
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

		$criteria->compare('npp',$this->npp);
		$criteria->compare('num',$this->num);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name2',$this->name2,true);
		$criteria->compare('precost',$this->precost,true);
		$criteria->compare('num1',$this->num1);
		$criteria->compare('precost1',$this->precost1,true);
		$criteria->compare('struct',$this->struct);
		$criteria->compare('nds',$this->nds,true);
		$criteria->compare('type',$this->type);

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
	 * @return ZakTovar the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
