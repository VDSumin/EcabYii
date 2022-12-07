<?php

/**
 * This is the model class for table "gal_u_disciplineload".
 *
 * The followings are the available columns in table 'gal_u_disciplineload':
 * @property string $nrec
 * @property string $cloadid
 * @property string $cdiscipline
 * @property string $cfaculty
 * @property string $cinstitut
 * @property integer $wcurrgrcount
 * @property string $ctypework
 * @property integer $wcourse
 * @property integer $semester
 * @property string $csemester
 * @property integer $wformed
 * @property string $ccontingent
 * @property double $dcontcount
 * @property integer $wvirtgrcount
 * @property double $dload
 * @property double $dsize
 * @property double $dcredload
 * @property double $dcredsize
 * @property string $cstgr
 * @property integer $istudcount
 * @property integer $wstat
 * @property integer $wtypeload
 * @property string $sname
 * @property integer $dbeg
 * @property integer $dend
 * @property integer $wunitedstream
 * @property string $sdiscipname
 * @property integer $wseason
 * @property integer $info
 * @property string $cvariant
 */
class GalUDisciplineload extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gal_u_disciplineload';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nrec, cloadid, cdiscipline, cfaculty, cinstitut, wcurrgrcount, ctypework, wcourse, semester, csemester, wformed, ccontingent, dcontcount, wvirtgrcount, dload, dsize, dcredload, dcredsize, cstgr, istudcount, wstat, wtypeload, sname, dbeg, dend, wunitedstream, sdiscipname, wseason, info, cvariant', 'required'),
			array('wcurrgrcount, wcourse, semester, wformed, wvirtgrcount, istudcount, wstat, wtypeload, dbeg, dend, wunitedstream, wseason, info', 'numerical', 'integerOnly'=>true),
			array('dcontcount, dload, dsize, dcredload, dcredsize', 'numerical'),
			array('nrec, cloadid, cdiscipline, cfaculty, cinstitut, ctypework, csemester, ccontingent, cstgr, cvariant', 'length', 'max'=>8),
			array('sname, sdiscipname', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('nrec, cloadid, cdiscipline, cfaculty, cinstitut, wcurrgrcount, ctypework, wcourse, semester, csemester, wformed, ccontingent, dcontcount, wvirtgrcount, dload, dsize, dcredload, dcredsize, cstgr, istudcount, wstat, wtypeload, sname, dbeg, dend, wunitedstream, sdiscipname, wseason, info, cvariant', 'safe', 'on'=>'search'),
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
			'cloadid' => 'Cloadid',
			'cdiscipline' => 'Cdiscipline',
			'cfaculty' => 'Cfaculty',
			'cinstitut' => 'Cinstitut',
			'wcurrgrcount' => 'Wcurrgrcount',
			'ctypework' => 'Ctypework',
			'wcourse' => 'Wcourse',
			'semester' => 'Semester',
			'csemester' => 'Csemester',
			'wformed' => 'Wformed',
			'ccontingent' => 'Ccontingent',
			'dcontcount' => 'Dcontcount',
			'wvirtgrcount' => 'Wvirtgrcount',
			'dload' => 'Dload',
			'dsize' => 'Dsize',
			'dcredload' => 'Dcredload',
			'dcredsize' => 'Dcredsize',
			'cstgr' => 'Cstgr',
			'istudcount' => 'Istudcount',
			'wstat' => 'Wstat',
			'wtypeload' => 'Wtypeload',
			'sname' => 'Sname',
			'dbeg' => 'Dbeg',
			'dend' => 'Dend',
			'wunitedstream' => 'Wunitedstream',
			'sdiscipname' => 'Sdiscipname',
			'wseason' => 'Wseason',
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
		$criteria->compare('cloadid',$this->cloadid,true);
		$criteria->compare('cdiscipline',$this->cdiscipline,true);
		$criteria->compare('cfaculty',$this->cfaculty,true);
		$criteria->compare('cinstitut',$this->cinstitut,true);
		$criteria->compare('wcurrgrcount',$this->wcurrgrcount);
		$criteria->compare('ctypework',$this->ctypework,true);
		$criteria->compare('wcourse',$this->wcourse);
		$criteria->compare('semester',$this->semester);
		$criteria->compare('csemester',$this->csemester,true);
		$criteria->compare('wformed',$this->wformed);
		$criteria->compare('ccontingent',$this->ccontingent,true);
		$criteria->compare('dcontcount',$this->dcontcount);
		$criteria->compare('wvirtgrcount',$this->wvirtgrcount);
		$criteria->compare('dload',$this->dload);
		$criteria->compare('dsize',$this->dsize);
		$criteria->compare('dcredload',$this->dcredload);
		$criteria->compare('dcredsize',$this->dcredsize);
		$criteria->compare('cstgr',$this->cstgr,true);
		$criteria->compare('istudcount',$this->istudcount);
		$criteria->compare('wstat',$this->wstat);
		$criteria->compare('wtypeload',$this->wtypeload);
		$criteria->compare('sname',$this->sname,true);
		$criteria->compare('dbeg',$this->dbeg);
		$criteria->compare('dend',$this->dend);
		$criteria->compare('wunitedstream',$this->wunitedstream);
		$criteria->compare('sdiscipname',$this->sdiscipname,true);
		$criteria->compare('wseason',$this->wseason);
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
	 * @return GalUDisciplineload the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
