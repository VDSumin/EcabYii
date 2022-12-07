<?php

/**
 * This is the model class for table "zak_zakaz".
 *
 * The followings are the available columns in table 'zak_zakaz':
 * @property integer $npp
 * @property integer $oborud
 * @property integer $ttype
 * @property integer $tovar
 * @property integer $struct
 * @property integer $kolvo
 * @property string $dz
 * @property integer $auction
 * @property integer $finsource
 * @property string $invNumber
 * @property string $addres
 * @property integer $fnpp
 * @property string $user
 * @property string $dr
 *
 * The followings are the available model relations:
 * @property StructD_rp $struct0
 * @property ZakAuction $auction0
 * @property ZakFinsources $finsource0
 * @property ZakOborud $oborud0
 * @property ZakTovar $tovar0
 * @property ZakTovartype $ttype0
 */
class ZakZakaz extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'zak_zakaz';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('oborud, tovar, struct, kolvo, auction, finsource, invNumber, addres, fnpp, dz', 'required'),
            array('npp, oborud, ttype, tovar, struct, kolvo, auction, finsource, fnpp', 'numerical', 'integerOnly'=>true),
            array('invNumber, addres', 'length', 'max'=>255),
            array('user', 'length', 'max'=>80),
            array('dz, dr', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('npp, oborud, ttype, tovar, struct, kolvo, dz, auction, finsource, invNumber, addres, fnpp, user, dr', 'safe', 'on'=>'search'),
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
            'struct0' => array(self::BELONGS_TO, 'StructD_rp', 'struct'),
            'auction0' => array(self::BELONGS_TO, 'ZakAuction', 'auction'),
            'finsource0' => array(self::BELONGS_TO, 'ZakFinsources', 'finsource'),
            'oborud0' => array(self::BELONGS_TO, 'ZakOborud', 'oborud'),
            'tovar0' => array(self::BELONGS_TO, 'ZakTovar', 'tovar'),
            'ttype0' => array(self::BELONGS_TO, 'ZakTovartype', 'ttype'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'npp' => 'Npp',
            'oborud' => 'Принтер',
            'ttype' => 'Ttype',
            'tovar' => 'Картридж',
            'struct' => 'Подразделение',
            'kolvo' => 'Количество',
            'dz' => 'Дата заказа',
            'auction' => 'Аукцион',
            'finsource' => 'Источник финансирования',
            'invNumber' => 'Инвертарный номер',
            'addres' => 'Адрес нахождения',
            'fnpp' => 'Ответственный',
            'user' => 'User',
            'dr' => 'Dr',
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
        $criteria->compare('oborud',$this->oborud);
        $criteria->compare('ttype',$this->ttype);
        $criteria->compare('tovar',$this->tovar);
        $criteria->compare('struct',$this->struct);
        $criteria->compare('kolvo',$this->kolvo);
        $criteria->compare('dz',$this->dz,true);
        $criteria->compare('auction',$this->auction);
        $criteria->compare('finsource',$this->finsource);
        $criteria->compare('invNumber',$this->invNumber,true);
        $criteria->compare('addres',$this->addres,true);
        $criteria->compare('fnpp',$this->fnpp);
        $criteria->compare('user',$this->user,true);
        $criteria->compare('dr',$this->dr,true);

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
     * @return ZakZakaz the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function getYears()
    {
        $return = Yii::app()->db2->createCommand("SELECT DISTINCT year(zz.dz) FROM zak_zakaz zz")->queryAll();

        return $return;
    }



}
