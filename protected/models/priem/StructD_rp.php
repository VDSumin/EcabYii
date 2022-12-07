<?php

/**
 * This is the model class for table "struct_d_rp".
 *
 * The followings are the available columns in table 'struct_d_rp':
 * @property integer $npp
 * @property integer $pnpp
 * @property string $name
 * @property integer $l
 * @property integer $r
 * @property integer $u
 * @property string $guid
 * @property string $cd
 * @property string $dd
 * @property string $dr
 * @property string $code
 * @property integer $prudal
 * @property string $head
 */
class StructD_rp extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'struct_d_rp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pnpp, l, r, u, prudal', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>500),
			array('guid', 'length', 'max'=>36),
			array('code', 'length', 'max'=>50),
			array('head', 'length', 'max'=>255),
			array('cd, dd, dr', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('npp, pnpp, name, l, r, u, guid, cd, dd, dr, code, prudal, head', 'safe', 'on'=>'search'),
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
			'npp' => 'Npp',
			'pnpp' => 'ссылка на родителя',
			'name' => 'наименование',
			'l' => 'лево',
			'r' => 'право',
			'u' => 'уровень',
			'guid' => 'гуид из 1с',
			'cd' => 'сформировано дата',
			'dd' => 'расформировано дата',
			'dr' => 'Dr',
			'code' => 'Code',
			'prudal' => 'Prudal',
			'head' => 'Head',
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
		$criteria->compare('pnpp',$this->pnpp);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('l',$this->l);
		$criteria->compare('r',$this->r);
		$criteria->compare('u',$this->u);
		$criteria->compare('guid',$this->guid,true);
		$criteria->compare('cd',$this->cd,true);
		$criteria->compare('dd',$this->dd,true);
		$criteria->compare('dr',$this->dr,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('prudal',$this->prudal);
		$criteria->compare('head',$this->head,true);

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
	 * @return StructD the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
