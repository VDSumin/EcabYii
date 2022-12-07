<?php

/**
 * This is the model class for table "skard".
 *
 * The followings are the available columns in table 'skard':
 * @property integer $npp
 * @property integer $fnpp
 * @property string $prudal
 * @property string $gruppa
 * @property string $fak
 * @property string $spec
 * @property string $form
 * @property string $fin
 * @property string $du
 * @property string $dr
 * @property string $user
 * @property integer $kurs
 * @property integer $contract
 * @property integer $ytransf
 * @property integer $ustate
 * @property integer $ifak
 * @property integer $iform
 * @property integer $ifin
 * @property integer $ispec
 * @property integer $knpp
 * @property integer $zorder
 * @property string $qualif
 * @property integer $iqualif
 * @property string $gal_chair
 * @property string $gal_group
 * @property string $gal_faculty
 * @property string $gal_nation
 * @property string $gal_specname
 * @property string $gal_speccode
 */
class Skard extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'skard';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('fnpp, kurs, contract, ytransf, ustate, ifak, iform, ifin, ispec, knpp, zorder, iqualif', 'numerical', 'integerOnly' => true),
            array('prudal', 'length', 'max' => 1),
            array('gruppa', 'length', 'max' => 10),
            array('fak, spec, form, fin', 'length', 'max' => 255),
            array('user, gal_group', 'length', 'max' => 16),
            array('qualif, gal_faculty', 'length', 'max' => 50),
            array('gal_chair', 'length', 'max' => 64),
            array('gal_nation', 'length', 'max' => 30),
            array('gal_specname', 'length', 'max' => 125),
            array('gal_speccode', 'length', 'max' => 20),
            array('du, dr', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('npp, fnpp, prudal, gruppa, fak, spec, form, fin, du, dr, user, kurs, contract, ytransf, ustate, ifak, iform, ifin, ispec, knpp, zorder, qualif, iqualif, gal_chair, gal_group, gal_faculty, gal_nation, gal_specname, gal_speccode', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'data' => array(self::BELONGS_TO, 'Fdata', 'fnpp'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'npp' => 'Npp',
            'fnpp' => 'Fnpp',
            'prudal' => 'Prudal',
            'gruppa' => 'Gruppa',
            'fak' => 'Fak',
            'spec' => 'Spec',
            'form' => 'Form',
            'fin' => 'Fin',
            'du' => 'Du',
            'dr' => 'Dr',
            'user' => 'User',
            'kurs' => 'Kurs',
            'contract' => 'Contract',
            'ytransf' => 'Ytransf',
            'ustate' => 'Ustate',
            'ifak' => 'Ifak',
            'iform' => 'Iform',
            'ifin' => 'Ifin',
            'ispec' => 'Ispec',
            'knpp' => 'Knpp',
            'zorder' => 'Zorder',
            'qualif' => 'Qualif',
            'iqualif' => 'Iqualif',
            'gal_chair' => 'Gal Chair',
            'gal_group' => 'Gal Group',
            'gal_faculty' => 'Gal Faculty',
            'gal_nation' => 'Gal Nation',
            'gal_specname' => 'Gal Specname',
            'gal_speccode' => 'Gal Speccode',
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

        $criteria->compare('npp', $this->npp);
        $criteria->compare('fnpp', $this->fnpp);
        $criteria->compare('prudal', $this->prudal, true);
        $criteria->compare('gruppa', $this->gruppa, true);
        $criteria->compare('fak', $this->fak, true);
        $criteria->compare('spec', $this->spec, true);
        $criteria->compare('form', $this->form, true);
        $criteria->compare('fin', $this->fin, true);
        $criteria->compare('du', $this->du, true);
        $criteria->compare('dr', $this->dr, true);
        $criteria->compare('user', $this->user, true);
        $criteria->compare('kurs', $this->kurs);
        $criteria->compare('contract', $this->contract);
        $criteria->compare('ytransf', $this->ytransf);
        $criteria->compare('ustate', $this->ustate);
        $criteria->compare('ifak', $this->ifak);
        $criteria->compare('iform', $this->iform);
        $criteria->compare('ifin', $this->ifin);
        $criteria->compare('ispec', $this->ispec);
        $criteria->compare('knpp', $this->knpp);
        $criteria->compare('zorder', $this->zorder);
        $criteria->compare('qualif', $this->qualif, true);
        $criteria->compare('iqualif', $this->iqualif);
        $criteria->compare('gal_chair', $this->gal_chair, true);
        $criteria->compare('gal_group', $this->gal_group, true);
        $criteria->compare('gal_faculty', $this->gal_faculty, true);
        $criteria->compare('gal_nation', $this->gal_nation, true);
        $criteria->compare('gal_specname', $this->gal_specname, true);
        $criteria->compare('gal_speccode', $this->gal_speccode, true);

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
     * @return Skard the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
