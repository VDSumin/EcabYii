<?php

/**
 * This is the model class for table "gal_u_student".
 *
 * The followings are the available columns in table 'gal_u_student':
 * @property string $id
 * @property string $nrec
 * @property string $cpersons
 * @property string $fio
 * @property string $uns
 * @property string $cstgr
 * @property string $department
 * @property integer $appdate
 * @property string $sdepcode
 * @property string $sdepartment
 * @property string $cpost
 * @property string $spost
 * @property string $codeprof
 * @property string $ceducation
 * @property integer $disdate
 * @property string $ccurr
 * @property integer $warch
 * @property integer $wformed
 * @property string $sfinsourcename
 * @property string $cfinsourcename
 * @property string $sfaculty
 * @property string $cfaculty
 * @property string $cinstitut
 * @property integer $wtransfer
 * @property string $cpaidcategory
 * @property integer $wotpusk
 * @property string $ckateg
 * @property string $skateg
 * @property string $cqualification
 * @property string $cstatus
 * @property string $sstatus
 * @property integer $wtype
 * @property string $cdeanoffice
 * @property integer $wcourse
 * @property integer $wdegree
 * @property string $sdegree
 * @property string $cforeigncat
 * @property integer $wstydyprogram
 * @property integer $wtransfercond
 */
class uStudent extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'gal_u_student';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nrec, cpersons, fio, uns, cstgr, department, appdate, sdepcode, sdepartment, cpost, spost, codeprof, ceducation, disdate, ccurr, warch, wformed, sfinsourcename, cfinsourcename, sfaculty, cfaculty, cinstitut, wtransfer, cpaidcategory, wotpusk, ckateg, skateg, cqualification, cstatus, sstatus, wtype, cdeanoffice, wcourse, wdegree, sdegree, cforeigncat, wstydyprogram, wtransfercond', 'required'),
            array('appdate, disdate, warch, wformed, wtransfer, wotpusk, wtype, wcourse, wdegree, wstydyprogram, wtransfercond', 'numerical', 'integerOnly' => true),
            array('nrec, cpersons, cstgr, department, cpost, ceducation, ccurr, cfinsourcename, cfaculty, cinstitut, cpaidcategory, ckateg, cqualification, cstatus, cdeanoffice, cforeigncat', 'length', 'max' => 8),
            array('fio, sfinsourcename', 'length', 'max' => 60),
            array('uns, sdepcode', 'length', 'max' => 20),
            array('sdepartment', 'length', 'max' => 200),
            array('spost, codeprof, skateg, sstatus', 'length', 'max' => 100),
            array('sfaculty', 'length', 'max' => 255),
            array('sdegree', 'length', 'max' => 30),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nrec, cpersons, fio, uns, cstgr, department, appdate, sdepcode, sdepartment, cpost, spost, codeprof, ceducation, disdate, ccurr, warch, wformed, sfinsourcename, cfinsourcename, sfaculty, cfaculty, cinstitut, wtransfer, cpaidcategory, wotpusk, ckateg, skateg, cqualification, cstatus, sstatus, wtype, cdeanoffice, wcourse, wdegree, sdegree, cforeigncat, wstydyprogram, wtransfercond', 'safe', 'on' => 'search'),
        );
    }

    public function getTableSchema() {
        $table = parent::getTableSchema();

        $table->columns['cpersons']->isForeignKey = true;
        $table->foreignKeys['cpersons'] = ['Person', 'nrec'];
        return $table;
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'personModel' => [self::BELONGS_TO, 'Person', 'cpersons'],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'nrec' => 'Nrec',
            'cpersons' => 'Cpersons',
            'fio' => 'Fio',
            'uns' => 'Uns',
            'cstgr' => 'Cstgr',
            'department' => 'Department',
            'appdate' => 'Appdate',
            'sdepcode' => 'Sdepcode',
            'sdepartment' => 'Sdepartment',
            'cpost' => 'Cpost',
            'spost' => 'Spost',
            'codeprof' => 'Codeprof',
            'ceducation' => 'Ceducation',
            'disdate' => 'Disdate',
            'ccurr' => 'Ccurr',
            'warch' => 'Warch',
            'wformed' => 'Wformed',
            'sfinsourcename' => 'Sfinsourcename',
            'cfinsourcename' => 'Cfinsourcename',
            'sfaculty' => 'Sfaculty',
            'cfaculty' => 'Cfaculty',
            'cinstitut' => 'Cinstitut',
            'wtransfer' => 'Wtransfer',
            'cpaidcategory' => 'Cpaidcategory',
            'wotpusk' => 'Wotpusk',
            'ckateg' => 'Ckateg',
            'skateg' => 'Skateg',
            'cqualification' => 'Cqualification',
            'cstatus' => 'Cstatus',
            'sstatus' => 'Sstatus',
            'wtype' => 'Wtype',
            'cdeanoffice' => 'Cdeanoffice',
            'wcourse' => 'Wcourse',
            'wdegree' => 'Wdegree',
            'sdegree' => 'Sdegree',
            'cforeigncat' => 'Cforeigncat',
            'wstydyprogram' => 'Wstydyprogram',
            'wtransfercond' => 'Wtransfercond',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('nrec', $this->nrec, true);
        $criteria->compare('cpersons', $this->cpersons, true);
        $criteria->compare('fio', $this->fio, true);
        $criteria->compare('uns', $this->uns, true);
        $criteria->compare('cstgr', $this->cstgr, true);
        $criteria->compare('department', $this->department, true);
        $criteria->compare('appdate', $this->appdate);
        $criteria->compare('sdepcode', $this->sdepcode, true);
        $criteria->compare('sdepartment', $this->sdepartment, true);
        $criteria->compare('cpost', $this->cpost, true);
        $criteria->compare('spost', $this->spost, true);
        $criteria->compare('codeprof', $this->codeprof, true);
        $criteria->compare('ceducation', $this->ceducation, true);
        $criteria->compare('disdate', $this->disdate);
        $criteria->compare('ccurr', $this->ccurr, true);
        $criteria->compare('warch', $this->warch);
        $criteria->compare('wformed', $this->wformed);
        $criteria->compare('sfinsourcename', $this->sfinsourcename, true);
        $criteria->compare('cfinsourcename', $this->cfinsourcename, true);
        $criteria->compare('sfaculty', $this->sfaculty, true);
        $criteria->compare('cfaculty', $this->cfaculty, true);
        $criteria->compare('cinstitut', $this->cinstitut, true);
        $criteria->compare('wtransfer', $this->wtransfer);
        $criteria->compare('cpaidcategory', $this->cpaidcategory, true);
        $criteria->compare('wotpusk', $this->wotpusk);
        $criteria->compare('ckateg', $this->ckateg, true);
        $criteria->compare('skateg', $this->skateg, true);
        $criteria->compare('cqualification', $this->cqualification, true);
        $criteria->compare('cstatus', $this->cstatus, true);
        $criteria->compare('sstatus', $this->sstatus, true);
        $criteria->compare('wtype', $this->wtype);
        $criteria->compare('cdeanoffice', $this->cdeanoffice, true);
        $criteria->compare('wcourse', $this->wcourse);
        $criteria->compare('wdegree', $this->wdegree);
        $criteria->compare('sdegree', $this->sdegree, true);
        $criteria->compare('cforeigncat', $this->cforeigncat, true);
        $criteria->compare('wstydyprogram', $this->wstydyprogram);
        $criteria->compare('wtransfercond', $this->wtransfercond);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection() {
        return Yii::app()->db2;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return uStudent the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
