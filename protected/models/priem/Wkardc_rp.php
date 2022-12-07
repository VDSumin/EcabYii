<?php

/**
 * This is the model class for table "wkardc_rp".
 *
 * The followings are the available columns in table 'wkardc_rp':
 * @property integer $npp
 * @property integer $fnpp
 * @property string $ncontract
 * @property double $stavka
 * @property string $sovm
 * @property string $du
 * @property integer $struct
 * @property string $struct_state
 * @property string $prudal
 * @property string $dolgnost
 * @property string $dr
 * @property string $user
 * @property string $kategoria
 * @property integer $outsource
 * @property string $guid
 * @property string $di
 *
 * * The followings are the available model relations:
 * @property Fdata $data
 * @property StructD_rp $structD
 */
class Wkardc_rp extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Wkardc_rp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fnpp', 'required'),
			array('fnpp, struct, outsource', 'numerical', 'integerOnly'=>true),
			array('stavka', 'numerical'),
			array('ncontract', 'length', 'max'=>25),
			array('sovm', 'length', 'max'=>10),
			array('struct_state, dolgnost', 'length', 'max'=>255),
			array('prudal', 'length', 'max'=>1),
			array('user', 'length', 'max'=>16),
			array('kategoria', 'length', 'max'=>5),
			array('guid', 'length', 'max'=>36),
			array('du, dr, di', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('npp, fnpp, ncontract, stavka, sovm, du, struct, struct_state, prudal, dolgnost, dr, user, kategoria, outsource, guid, di', 'safe', 'on'=>'search'),
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
            'data' => array(self::BELONGS_TO, 'Fdata', 'fnpp', 'joinType' => 'INNER JOIN'),
            'structD' => array(self::BELONGS_TO, 'StructD_rp', 'struct', 'joinType' => 'INNER JOIN'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'npp' => 'Npp',
			'fnpp' => 'обязательно указать npp из fdata',
			'ncontract' => '№ контракта',
			'stavka' => 'ставка',
			'sovm' => 'признак совмешения виз работы',
			'du' => 'дата увольнения',
			'struct' => 'ссылка места работы',
			'struct_state' => 'ссылка на штаты',
			'prudal' => 'признак удаления',
			'dolgnost' => 'Dolgnost',
			'dr' => 'дата последней редакции',
			'user' => 'пользователь',
			'kategoria' => 'категория ППС,РП ...',
			'outsource' => 'признак внешнего совместителя',
			'guid' => 'с чёрточками',
			'di' => 'дата импорта',
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
		$criteria->compare('fnpp',$this->fnpp);
		$criteria->compare('ncontract',$this->ncontract,true);
		$criteria->compare('stavka',$this->stavka);
		$criteria->compare('sovm',$this->sovm,true);
		$criteria->compare('du',$this->du,true);
		$criteria->compare('struct',$this->struct);
		$criteria->compare('struct_state',$this->struct_state,true);
		$criteria->compare('prudal',$this->prudal,true);
		$criteria->compare('dolgnost',$this->dolgnost,true);
		$criteria->compare('dr',$this->dr,true);
		$criteria->compare('user',$this->user,true);
		$criteria->compare('kategoria',$this->kategoria,true);
		$criteria->compare('outsource',$this->outsource);
		$criteria->compare('guid',$this->guid,true);
		$criteria->compare('di',$this->di,true);

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
	 * @return Wkardc_rp the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
