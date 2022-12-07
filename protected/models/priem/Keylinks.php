<?php

/**
 * This is the model class for table "keylinks".
 *
 * The followings are the available columns in table 'keylinks':
 * @property integer $fnpp
 * @property string $lotus_unid
 * @property string $1c_unid
 * @property string $gal_unid
 * @property string $passprom
 * @property string $userprom
 * @property string $bitrix_unid
 * @property integer $fvalidator
 * @property integer $valid
 * @property string $dv
 *
 * The followings are the available model relations:
 * @property Fdata $fnpp0
 */
class Keylinks extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'keylinks';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('fnpp', 'required'),
            array('fnpp, fvalidator, valid', 'numerical', 'integerOnly' => true),
            array('lotus_unid, 1c_unid', 'length', 'max' => 32),
            array('gal_unid', 'length', 'max' => 8),
            array('passprom, userprom, bitrix_unid', 'length', 'max' => 255),
            array('dv', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('fnpp, lotus_unid, 1c_unid, gal_unid, passprom, userprom, bitrix_unid, fvalidator, valid, dv', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'data' => array(self::BELONGS_TO, 'Fdata', 'fnpp', 'joinType' => 'INNER JOIN'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'fnpp' => 'Fnpp',
            'lotus_unid' => 'Lotus Unid',
            '1c_unid' => '1c Unid',
            'gal_unid' => 'Gal Unid',
            'passprom' => 'Passprom',
            'userprom' => 'Userprom',
            'bitrix_unid' => 'Bitrix Unid',
            'fvalidator' => 'Fvalidator',
            'valid' => 'Valid',
            'dv' => 'Dv',
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
        $criteria = new CDbCriteria;

        $criteria->compare('fnpp', $this->fnpp);
        $criteria->compare('lotus_unid', $this->lotus_unid, true);
        $criteria->compare('g1c_unid', $this->g1c_unid, true);
        $criteria->compare('gal_unid', $this->gal_unid, true);
        $criteria->compare('passprom', $this->passprom, true);
        $criteria->compare('userprom', $this->userprom, true);
        $criteria->compare('bitrix_unid', $this->bitrix_unid, true);
        $criteria->compare('fvalidator', $this->fvalidator);
        $criteria->compare('valid', $this->valid);
        $criteria->compare('dv', $this->dv, true);

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
     * @return Keylinks the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
