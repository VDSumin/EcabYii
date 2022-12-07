<?php

/**
 * This is the model class for table "itdep_software_auction".
 *
 * The followings are the available columns in table 'itdep_software_auction':
 * @property integer $id
 * @property string $date
 * @property string $name
 * @property string $info
 * @property integer $status
 * @property integer $last_user
 * @property string $last_date
 */
class ItdepSoftwareAuction extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'itdep_software_auction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, name, last_user, last_date', 'required'),
			array('status, last_user', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
			array('info', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, name, info, status, last_user, last_date', 'safe', 'on'=>'search'),
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
			'date' => 'Date',
			'name' => 'Name',
			'info' => 'Info',
			'status' => 'Status',
			'last_user' => 'Last User',
			'last_date' => 'Last Date',
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('info',$this->info,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('last_user',$this->last_user);
		$criteria->compare('last_date',$this->last_date,true);

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
	 * @return ItdepSoftwareAuction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    public  function state($id)
    {
        $arrayState = ['0' => 'Черновик', '1' =>  'В работе', '2' => 'Завершено'];
        $model = ItdepSoftwareAuction::model()->findByPk($id);
        $return = $arrayState [ $model->status ];
        return $return;
    }


    public function countRecords($id)
    {
        $return = ItdepSoftwareRequest::model()->findAllByAttributes(['auction' => $id]);
        return count($return);
    }

    public function bigButton($id)
    {
        $return = CHtml::link('Просмотреть', array('/zak/softwarerequest/index', 'id' => $id), array('class' => 'btn btn-info'));
        return $return;
    }

    public function printButton($id)
    {
        $return = CHtml::link('Выгрузить в xls', array('/zak/softwarerequest/print', 'id' => $id), array('class' => 'btn btn-info'));
        return $return;
    }

    public static function getActionStatus($id) {
        $return = ItdepSoftwareAuction::model()->findByPk($id)->status;
        if( in_array($return,[0,1])){$return = true;} else{$return = false;}
        return $return;
    }

    public function activeAuctionName()
    {
        $model = ItdepSoftwareAuction::model()->findAllByAttributes(array('status' => [0,1,2]));
        $return =[];
        foreach ($model as $item) {
            $return[] = ['npp' => $item->id, 'name' => $item->name." от ". date( "d.m.Y г.", strtotime($item->date)) . " (".$item->info.")"];
        }
        return $return;
    }
}
