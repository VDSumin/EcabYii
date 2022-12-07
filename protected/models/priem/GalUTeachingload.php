<?php

/**
 * This is the model class for table "gal_u_teachingload".
 *
 * The followings are the available columns in table 'gal_u_teachingload':
 * @property string $nrec
 * @property string $cmain
 * @property integer $wyeared
 * @property string $cchair
 * @property string $clecture
 * @property double $dload
 * @property double $dcredload
 * @property double $dfactload
 * @property string $name
 * @property string $descr
 * @property string $desgr
 * @property integer $dcreate
 * @property string $numfactstaff
 * @property string $numteachstaff
 * @property string $numextndstaff
 * @property string $cfaculty
 * @property string $cinstitut
 * @property double $dloadaut
 * @property double $dcrloadaut
 * @property double $dloadspr
 * @property double $dcrloadspr
 * @property double $dloadint
 * @property double $dcrloadint
 * @property double $dloaddist
 * @property double $dcrloaddist
 * @property double $dloadintdist
 * @property double $dcrloadintdist
 * @property double $dloadautint
 * @property double $dcrloadautint
 * @property double $dloadsprint
 * @property double $dcrloadsprint
 * @property double $dloadautdist
 * @property double $dcrloadautdist
 * @property double $dloadsprdist
 * @property double $dcrloadsprdist
 * @property double $dloadautintdist
 * @property double $dcrloadautintdist
 * @property double $dloadsprintdist
 * @property double $dcrloadsprintdist
 * @property integer $info
 * @property string $cvariant
 */
class GalUTeachingload extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gal_u_teachingload';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nrec, cmain, wyeared, cchair, clecture, dload, dcredload, dfactload, name, descr, desgr, dcreate, numfactstaff, numteachstaff, numextndstaff, cfaculty, cinstitut, dloadaut, dcrloadaut, dloadspr, dcrloadspr, dloadint, dcrloadint, dloaddist, dcrloaddist, dloadintdist, dcrloadintdist, dloadautint, dcrloadautint, dloadsprint, dcrloadsprint, dloadautdist, dcrloadautdist, dloadsprdist, dcrloadsprdist, dloadautintdist, dcrloadautintdist, dloadsprintdist, dcrloadsprintdist, info, cvariant', 'required'),
			array('wyeared, dcreate, info', 'numerical', 'integerOnly'=>true),
			array('dload, dcredload, dfactload, dloadaut, dcrloadaut, dloadspr, dcrloadspr, dloadint, dcrloadint, dloaddist, dcrloaddist, dloadintdist, dcrloadintdist, dloadautint, dcrloadautint, dloadsprint, dcrloadsprint, dloadautdist, dcrloadautdist, dloadsprdist, dcrloadsprdist, dloadautintdist, dcrloadautintdist, dloadsprintdist, dcrloadsprintdist', 'numerical'),
			array('nrec, cmain, cchair, clecture, cfaculty, cinstitut, cvariant', 'length', 'max'=>8),
			array('name', 'length', 'max'=>200),
			array('descr', 'length', 'max'=>20),
			array('desgr', 'length', 'max'=>4),
			array('numfactstaff, numteachstaff, numextndstaff', 'length', 'max'=>24),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('nrec, cmain, wyeared, cchair, clecture, dload, dcredload, dfactload, name, descr, desgr, dcreate, numfactstaff, numteachstaff, numextndstaff, cfaculty, cinstitut, dloadaut, dcrloadaut, dloadspr, dcrloadspr, dloadint, dcrloadint, dloaddist, dcrloaddist, dloadintdist, dcrloadintdist, dloadautint, dcrloadautint, dloadsprint, dcrloadsprint, dloadautdist, dcrloadautdist, dloadsprdist, dcrloadsprdist, dloadautintdist, dcrloadautintdist, dloadsprintdist, dcrloadsprintdist, info, cvariant', 'safe', 'on'=>'search'),
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
			'nrec' => 'Nrec',
			'cmain' => 'Cmain',
			'wyeared' => 'Wyeared',
			'cchair' => 'Cchair',
			'clecture' => 'Clecture',
			'dload' => 'Dload',
			'dcredload' => 'Dcredload',
			'dfactload' => 'Dfactload',
			'name' => 'Name',
			'descr' => 'Descr',
			'desgr' => 'Desgr',
			'dcreate' => 'Dcreate',
			'numfactstaff' => 'Numfactstaff',
			'numteachstaff' => 'Numteachstaff',
			'numextndstaff' => 'Numextndstaff',
			'cfaculty' => 'Cfaculty',
			'cinstitut' => 'Cinstitut',
			'dloadaut' => 'Dloadaut',
			'dcrloadaut' => 'Dcrloadaut',
			'dloadspr' => 'Dloadspr',
			'dcrloadspr' => 'Dcrloadspr',
			'dloadint' => 'Dloadint',
			'dcrloadint' => 'Dcrloadint',
			'dloaddist' => 'Dloaddist',
			'dcrloaddist' => 'Dcrloaddist',
			'dloadintdist' => 'Dloadintdist',
			'dcrloadintdist' => 'Dcrloadintdist',
			'dloadautint' => 'Dloadautint',
			'dcrloadautint' => 'Dcrloadautint',
			'dloadsprint' => 'Dloadsprint',
			'dcrloadsprint' => 'Dcrloadsprint',
			'dloadautdist' => 'Dloadautdist',
			'dcrloadautdist' => 'Dcrloadautdist',
			'dloadsprdist' => 'Dloadsprdist',
			'dcrloadsprdist' => 'Dcrloadsprdist',
			'dloadautintdist' => 'Dloadautintdist',
			'dcrloadautintdist' => 'Dcrloadautintdist',
			'dloadsprintdist' => 'Dloadsprintdist',
			'dcrloadsprintdist' => 'Dcrloadsprintdist',
			'info' => 'Info',
			'cvariant' => 'Cvariant',
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

		$criteria->compare('nrec',$this->nrec,true);
		$criteria->compare('cmain',$this->cmain,true);
		$criteria->compare('wyeared',$this->wyeared);
		$criteria->compare('cchair',$this->cchair,true);
		$criteria->compare('clecture',$this->clecture,true);
		$criteria->compare('dload',$this->dload);
		$criteria->compare('dcredload',$this->dcredload);
		$criteria->compare('dfactload',$this->dfactload);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('descr',$this->descr,true);
		$criteria->compare('desgr',$this->desgr,true);
		$criteria->compare('dcreate',$this->dcreate);
		$criteria->compare('numfactstaff',$this->numfactstaff,true);
		$criteria->compare('numteachstaff',$this->numteachstaff,true);
		$criteria->compare('numextndstaff',$this->numextndstaff,true);
		$criteria->compare('cfaculty',$this->cfaculty,true);
		$criteria->compare('cinstitut',$this->cinstitut,true);
		$criteria->compare('dloadaut',$this->dloadaut);
		$criteria->compare('dcrloadaut',$this->dcrloadaut);
		$criteria->compare('dloadspr',$this->dloadspr);
		$criteria->compare('dcrloadspr',$this->dcrloadspr);
		$criteria->compare('dloadint',$this->dloadint);
		$criteria->compare('dcrloadint',$this->dcrloadint);
		$criteria->compare('dloaddist',$this->dloaddist);
		$criteria->compare('dcrloaddist',$this->dcrloaddist);
		$criteria->compare('dloadintdist',$this->dloadintdist);
		$criteria->compare('dcrloadintdist',$this->dcrloadintdist);
		$criteria->compare('dloadautint',$this->dloadautint);
		$criteria->compare('dcrloadautint',$this->dcrloadautint);
		$criteria->compare('dloadsprint',$this->dloadsprint);
		$criteria->compare('dcrloadsprint',$this->dcrloadsprint);
		$criteria->compare('dloadautdist',$this->dloadautdist);
		$criteria->compare('dcrloadautdist',$this->dcrloadautdist);
		$criteria->compare('dloadsprdist',$this->dloadsprdist);
		$criteria->compare('dcrloadsprdist',$this->dcrloadsprdist);
		$criteria->compare('dloadautintdist',$this->dloadautintdist);
		$criteria->compare('dcrloadautintdist',$this->dcrloadautintdist);
		$criteria->compare('dloadsprintdist',$this->dloadsprintdist);
		$criteria->compare('dcrloadsprintdist',$this->dcrloadsprintdist);
		$criteria->compare('info',$this->info);
		$criteria->compare('cvariant',$this->cvariant,true);

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
	 * @return GalUTeachingload the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
