<?php

/**
 * This is the model class for table "zak_auction".
 *
 * The followings are the available columns in table 'zak_auction':
 * @property integer $quart
 * @property integer $yyyy
 * @property string $da
 * @property integer $npp
 * @property integer $state
 * @property string $dt
 * @property string $info
 * @property string $typ
 * @property integer $init_struct
 *
 * The followings are the available model relations:
 * @property ZakAuctionStates $state0
 * @property ZakAuctionDet[] $zakAuctionDets
 * @property ZakZakaz[] $zakZakazs
 */
class ZakAuction extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'zak_auction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quart, yyyy, state, init_struct', 'numerical', 'integerOnly'=>true),
			array('info', 'length', 'max'=>255),
			array('typ', 'length', 'max'=>25),
			array('da, dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('quart, yyyy, da, npp, state, dt, info, typ, init_struct', 'safe', 'on'=>'search'),
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
			'state0' => array(self::BELONGS_TO, 'ZakAuctionStates', 'state'),
			'zakAuctionDets' => array(self::HAS_MANY, 'ZakAuctionDet', 'auction'),
			'zakZakazs' => array(self::HAS_MANY, 'ZakZakaz', 'auction'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'quart' => 'Квартал',
			'yyyy' => 'Год',
			'da' => 'Дата аукциона',
			'npp' => 'Npp',
			'state' => 'Статус',
			'dt' => 'Дата торгов',
			'info' => 'Основная информация',
			'typ' => 'Тип',
			'init_struct' => 'Init Struct',
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

		$criteria->compare('quart',$this->quart);
		$criteria->compare('yyyy',$this->yyyy);
		$criteria->compare('da',$this->da,true);
		$criteria->compare('npp',$this->npp);
		$criteria->compare('state',$this->state);
		$criteria->compare('dt',$this->dt,true);
		$criteria->compare('info',$this->info,true);
		$criteria->compare('typ',$this->typ,true);
		$criteria->compare('init_struct',$this->init_struct);

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
	 * @return ZakAuction the static model class
	 */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function countRecords($id)
    {
        $return = ZakZakaz::model()->findAllByAttributes(['auction' => $id]);
        return count($return);
    }

    public function bigButton($id)
    {
        $return = CHtml::link('Просмотреть', array('/ItDept/zak/zakaz', 'id' => $id), array('class' => 'btn btn-info'));
        return $return;
    }

    public function state($id)
    {
        $arrayState = ['1' => 'Инициализация', '2' =>  'Идут торги', '3' => 'Завершено'];
        $model = ZakAuction::model()->findByPk($id);
        $return = $arrayState [ $model->state ];
        return $return;
    }

    public function auctionName($id)
    {
        $model = ZakAuction::model()->findByPk($id);
        //$return = $model->yyyy." год / ".$model->quart." квартал (".$model->typ.") : \"".$model->info."\" ". date( "d.m.Y", strtotime($model->da));
        $return = $model->yyyy." год / ".$model->quart." квартал (".$model->typ.") ". date( "d.m.Y", strtotime($model->da));
        return $return;
    }

    public function activeAuctionName()
    {
        $model = ZakAuction::model()->findAllByAttributes(array('state' => 1));
        $return =[];
        foreach ($model as $item) {
            //$return[] = ['npp' => $item->npp, 'name' => $item->yyyy." год / ".$item->quart." квартал (".$item->typ.") : \"".$item->info."\" ". date( "d.m.Y", strtotime($item->da))];
            $return[] = ['npp' => $item->npp, 'name' => $item->yyyy." год / ".$item->quart." квартал (".$item->typ.") ". date( "d.m.Y", strtotime($item->da))];
        }
        return $return;
    }


}
