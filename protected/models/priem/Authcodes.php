<?php

/**
 * This is the model class for table "authcodes".
 *
 * The followings are the available columns in table 'authcodes':
 * @property integer $fnpp
 * @property string $ts
 * @property string $code
 */
class Authcodes extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'authcodes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('fnpp', 'required'),
            array('fnpp', 'numerical', 'integerOnly' => true),
            array('code', 'length', 'max' => 255),
            array('ts', 'safe'),
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
            'fnpp' => 'Fnpp',
            'ts' => 'Ts',
            'code' => 'Code',
        );
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
     * @return Authcodes the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
