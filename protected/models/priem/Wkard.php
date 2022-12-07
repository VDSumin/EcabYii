<?php

/**
 * This is the model class for table "wkard".
 *
 * The followings are the available columns in table 'wkard':
 * @property integer $npp
 * @property integer $fnpp
 * @property string $place
 * @property string $dolgnost
 * @property string $dr
 * @property string $user
 * @property string $kategoria
 * @property double $stavka
 * @property string $prudal
 * @property string $ncontract
 * @property string $sovm
 * @property string $du
 * @property string $podrazdelenie
 * @property integer $podr
 * @property string $otdel
 *
 * The followings are the available model relations:
 * @property Fdata $fnpp0
 */
class Wkard extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'wkard';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('fnpp', 'required'),
            array('fnpp, podr', 'numerical', 'integerOnly' => true),
            array('stavka', 'numerical'),
            array('place, dolgnost, podrazdelenie, otdel', 'length', 'max' => 255),
            array('user', 'length', 'max' => 16),
            array('kategoria', 'length', 'max' => 5),
            array('prudal', 'length', 'max' => 1),
            array('ncontract', 'length', 'max' => 25),
            array('sovm', 'length', 'max' => 10),
            array('dr, du', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('npp, fnpp, place, dolgnost, dr, user, kategoria, stavka, prudal, ncontract, sovm, du, podrazdelenie, podr, otdel', 'safe', 'on' => 'search'),
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
            'npp' => 'Npp',
            'fnpp' => 'Fnpp',
            'place' => 'Place',
            'dolgnost' => 'Dolgnost',
            'dr' => 'Dr',
            'user' => 'User',
            'kategoria' => 'Kategoria',
            'stavka' => 'Stavka',
            'prudal' => 'Prudal',
            'ncontract' => 'Ncontract',
            'sovm' => 'Sovm',
            'du' => 'Du',
            'podrazdelenie' => 'Podrazdelenie',
            'podr' => 'Podr',
            'otdel' => 'Otdel',
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
        $criteria->compare('place', $this->place, true);
        $criteria->compare('dolgnost', $this->dolgnost, true);
        $criteria->compare('dr', $this->dr, true);
        $criteria->compare('user', $this->user, true);
        $criteria->compare('kategoria', $this->kategoria, true);
        $criteria->compare('stavka', $this->stavka);
        $criteria->compare('prudal', $this->prudal, true);
        $criteria->compare('ncontract', $this->ncontract, true);
        $criteria->compare('sovm', $this->sovm, true);
        $criteria->compare('du', $this->du, true);
        $criteria->compare('podrazdelenie', $this->podrazdelenie, true);
        $criteria->compare('podr', $this->podr);
        $criteria->compare('otdel', $this->otdel, true);

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
     * @return Wkard the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
