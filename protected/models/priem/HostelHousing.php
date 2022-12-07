<?php

/**
 * This is the model class for table "hostel_housing".
 *
 * The followings are the available columns in table 'hostel_housing':
 * @property integer $id
 * @property integer $hostel
 * @property string $block
 * @property integer $flat
 * @property string $area
 * @property integer $flatType
 *
 * The followings are the available model relations:
 * @property HostelContract[] $hostelContracts
 */
class HostelHousing extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hostel_housing';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hostel, flat, area, flatType', 'required'),
			array('hostel, flat, flatType', 'numerical', 'integerOnly'=>true),
			array('block', 'length', 'max'=>255),
			array('area', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hostel, block, flat, area, flatType', 'safe', 'on'=>'search'),
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
			'hostelContracts' => array(self::HAS_MANY, 'HostelContract', 'housingId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Уникальный ключ',
			'hostel' => 'Номер общежития',
			'block' => 'Блок в общежитии',
			'flat' => 'Номер комнаты',
			'area' => 'Площадь комнаты',
			'flatType' => 'Количество мест в комнате',
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
		$criteria->compare('hostel',$this->hostel);
		$criteria->compare('block',$this->block,true);
		$criteria->compare('flat',$this->flat);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('flatType',$this->flatType);

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
	 * @return HostelHousing the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getHosuing($hostel, $flat, $block){
		$criteria = new CDbCriteria();
		$criteria->compare('hostel', $hostel);
		$criteria->compare('flat', $flat);

		if ($block && (mb_strlen($block, Yii::app()->charset) > 3)){
			$criteria->addSearchCondition('block', mb_substr($block, 0, 3, Yii::app()->charset));
		}

		return HostelHousing::model()->find($criteria);
	}

	public static function getTextHousing($id){
		$model = self::model()->findByPk($id);
		$result = null;

		if ($model instanceof HostelHousing){
			$result = 'Общежитие № ' . $model->hostel . ($model->block ? '; '. $model->block . ' блок; ' : '; ') . 'комната № ' .$model->flat . ' ( ' . $model->flatType .'-х местная)';
		}
		return $result;
	}
}
