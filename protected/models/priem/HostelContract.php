<?php

/**
 * This is the model class for table "hostel_contract".
 *
 * The followings are the available columns in table 'hostel_contract':
 * @property integer $id
 * @property integer $fnpp
 * @property string $student
 * @property string $contNumber
 * @property string $contDate
 * @property integer $contType
 * @property string $contBegin
 * @property string $contEnd
 * @property string $order
 * @property string $orderDate
 * @property integer $housingId
 * @property integer $hcostid
 * @property string $costTotal
 * @property integer $status
 * @property string $change
 * @property string $reason
 * @property string $filename
 * @property string $filenameRVP
 *
 * The followings are the available model relations:
 * @property HostelAgreement[] $hostelAgreements
 * @property Fdata $fdata
 * @property HostelCatalog $catalog
 * @property HostelCost $hcost
 * @property HostelHousing $housing
 * @property HostelDebtPayment[] $hostelDebtPayments
 */
class HostelContract extends CActiveRecord
{
	const STATUS_ACTIVE = 1;        //Активный договор
	const STATUS_CLOSE = 0;         //Закрытый договор

	public $fio;
	public $hostel;


    public $debt;
	public $rvp;
	public $agr;
	public $nopay;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hostel_contract';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fnpp, contNumber, contDate, contType, contBegin, contEnd, hcostid', 'required'),
			array('fnpp, contType, housingId, hcostid, status', 'numerical', 'integerOnly'=>true),
			array('student', 'length', 'max'=>20),
			array('contNumber, order, reason', 'length', 'max'=>255),
			array('costTotal', 'length', 'max'=>9),
			array('filename, filenameRVP', 'length', 'max'=>32),
			array('orderDate, change', 'safe'),
            array('housingId', 'required', 'message' => 'Необходимо выбрать данные из жилищного фонда'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fnpp, student, contNumber, contDate, contType, contBegin, contEnd, order, orderDate, housingId, hcostid, costTotal, status, change, reason, filename, filenameRVP', 'safe', 'on'=>'search'),
			array('id, fnpp, student, contNumber, contDate, contType, contBegin, contEnd, order, orderDate, housingId, hcostid, costTotal, status, change, reason, filename, filenameRVP', 'safe', 'on'=>'searchForAgr'),
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
			'hostelAgreements' => array(self::HAS_MANY, 'HostelAgreement', 'hcid'),
			'fdata' => array(self::BELONGS_TO, 'Fdata', 'fnpp'),
			'catalog' => array(self::BELONGS_TO, 'HostelCatalog', 'contType'),
			'hcost' => array(self::BELONGS_TO, 'HostelCost', 'hcostid'),
			'housing' => array(self::BELONGS_TO, 'HostelHousing', 'housingId'),
            'hostelDebtPayments' => array(self::HAS_MANY, 'HostelDebtPayment', 'hcid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Уникальный ключ',
			'fnpp' => 'связь с таблицей fdata',
			'student' => 'Nrec студента в галактике',
			'contNumber' => 'Номер договора',
			'contDate' => 'Дата договора',
			'contType' => 'Тип договора (ссылка на hostel_catalog)',
			'contBegin' => 'Дата начала договора',
			'contEnd' => 'Дата окончания договора',
			'order' => 'Номер приказа',
			'orderDate' => 'Дата приказа на заселение',
			'housingId' => 'Жилищный фонд',
			'hcostid' => 'стоимость в месяц (ссылка на hostel_cost)',
			'costTotal' => 'стоимость за весь период',
			'status' => 'Статус договора',
			'change' => 'Дата изменения статуса договора',
			'reason' => 'Причина изменения статуса договора',
			'filename' => 'Хэш имени файла',
			'filenameRVP' => 'Хэш имени файла для РВП'
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
		$criteria=new CDbCriteria;

		$criteria->with = array('fdata' => array(
			'condition' => 'CONCAT_WS(" ", TRIM(fam), TRIM(nam), TRIM(otc)) like :fam',
			'params' => array(':fam' => '%' . $this->fio . '%')
		),

		);


		$criteria->compare('id',$this->id);
		$criteria->compare('fnpp',$this->fnpp);
		$criteria->compare('student',$this->student,true);
		$criteria->compare('contNumber',$this->contNumber,true);
		$criteria->compare('contDate',$this->contDate,true);
		$criteria->compare('contType',$this->contType);
		$criteria->compare('contBegin',$this->contBegin,true);
		$criteria->compare('contEnd',$this->contEnd,true);
		$criteria->compare('order',$this->order,true);
		$criteria->compare('orderDate',$this->orderDate,true);
		$criteria->compare('housingId',$this->housingId);
		$criteria->compare('hcostid',$this->hcostid);
		$criteria->compare('costTotal',$this->costTotal,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('change',$this->change,true);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('filenameRVP',$this->filenameRVP,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function searchForAgr()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->with = array('fdata' => array(
			'condition' => 'CONCAT_WS(" ", TRIM(fam), TRIM(nam), TRIM(otc)) like :fam',
			'params' => array(':fam' => '%' . $this->fio . '%')
		));

		$criteria->compare('id',$this->id);
		$criteria->compare('fnpp',$this->fnpp);
		$criteria->compare('student',$this->student,true);
		$criteria->compare('contNumber',$this->contNumber,true);
		$criteria->compare('contDate',$this->contDate,true);
		$criteria->compare('contType',$this->contType);
		$criteria->compare('contBegin',$this->contBegin,true);
		$criteria->compare('contEnd',$this->contEnd,true);
		$criteria->compare('order',$this->order,true);
		$criteria->compare('orderDate',$this->orderDate,true);
		$criteria->compare('housingId',$this->housingId);
		$criteria->compare('hcostid',$this->hcostid);
		$criteria->compare('costTotal',$this->costTotal,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('change',$this->change,true);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('filenameRVP',$this->filenameRVP,true);
		$criteria->addInCondition('contType', [HostelCatalog::TYPE_BUDGET, HostelCatalog::TYPE_COMMERCIAL]);

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
	 * @return HostelContract the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function recently($limit = 1) {
		$this->getDbCriteria()->mergeWith(array(
			'order' => 'id DESC',
			'limit' => $limit,
		));

		return $this;
	}

	/**
	 * @param $fnpp Fnpp
	 */
	public function getActiveContractByFnpp($fnpp, $contNumber = null){
		$criteria = new CDbCriteria();
		$criteria->compare('fnpp', $fnpp);
		$criteria->compare('status', 1);
		if ($contNumber) {
			$criteria->addSearchCondition('contNumber', $contNumber, true, 'AND', 'NOT LIKE');
		}

		return $this->findAll($criteria);;
	}
		
}
