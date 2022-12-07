<?php

/**
 * This is the model class for table "schet".
 *
 * The followings are the available columns in table 'schet':
 * @property integer $npp
 * @property integer $contract
 * @property integer $fnpp
 * @property string $number
 * @property string $ds
 * @property integer $typplat
 * @property string $fislico
 * @property integer $iorg
 * @property string $summa
 * @property integer $kolvosem
 * @property string $comment
 * @property integer $vidoplat
 * @property string $dr
 * @property integer $kard
 * @property string $unid
 * @property string $studunid
 * @property string $kontunid
 * @property string $_typplat
 * @property string $_period
 * @property string $organization
 * @property integer $inumber
 * @property string $user
 * @property string $edr
 * @property string $dot
 *
 * The followings are the available model relations:
 * @property Oplata[] $oplatas
 */
class Schet extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'schet';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('contract, fnpp, typplat, iorg, kolvosem, vidoplat, kard, inumber', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>10),
			array('fislico, comment, _typplat, _period, organization', 'length', 'max'=>255),
			array('summa', 'length', 'max'=>12),
			array('unid, studunid, kontunid', 'length', 'max'=>32),
			array('user', 'length', 'max'=>16),
			array('ds, dr, edr, dot', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('npp, contract, fnpp, number, ds, typplat, fislico, iorg, summa, kolvosem, comment, vidoplat, dr, kard, unid, studunid, kontunid, _typplat, _period, organization, inumber, user, edr, dot', 'safe', 'on'=>'search'),
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
			'oplatas' => array(self::HAS_MANY, 'Oplata', 'schet'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'npp' => 'Npp',
			'contract' => 'Contract',
			'fnpp' => 'Fnpp',
			'number' => 'Number',
			'ds' => 'Ds',
			'typplat' => 'Typplat',
			'fislico' => 'Fislico',
			'iorg' => 'Iorg',
			'summa' => 'Summa',
			'kolvosem' => 'Kolvosem',
			'comment' => 'Comment',
			'vidoplat' => 'Vidoplat',
			'dr' => 'Dr',
			'kard' => 'Kard',
			'unid' => 'Unid',
			'studunid' => 'Studunid',
			'kontunid' => 'Kontunid',
			'_typplat' => 'Typplat',
			'_period' => 'Period',
			'organization' => 'Organization',
			'inumber' => 'Inumber',
			'user' => 'User',
			'edr' => 'дата обмена',
			'dot' => 'дата отсрочки по счёту',
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
		$criteria->compare('contract',$this->contract);
		$criteria->compare('fnpp',$this->fnpp);
		$criteria->compare('number',$this->number,true);
		$criteria->compare('ds',$this->ds,true);
		$criteria->compare('typplat',$this->typplat);
		$criteria->compare('fislico',$this->fislico,true);
		$criteria->compare('iorg',$this->iorg);
		$criteria->compare('summa',$this->summa,true);
		$criteria->compare('kolvosem',$this->kolvosem);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('vidoplat',$this->vidoplat);
		$criteria->compare('dr',$this->dr,true);
		$criteria->compare('kard',$this->kard);
		$criteria->compare('unid',$this->unid,true);
		$criteria->compare('studunid',$this->studunid,true);
		$criteria->compare('kontunid',$this->kontunid,true);
		$criteria->compare('_typplat',$this->_typplat,true);
		$criteria->compare('_period',$this->_period,true);
		$criteria->compare('organization',$this->organization,true);
		$criteria->compare('inumber',$this->inumber);
		$criteria->compare('user',$this->user,true);
		$criteria->compare('edr',$this->edr,true);
		$criteria->compare('dot',$this->dot,true);

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
	 * @return Schet the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
