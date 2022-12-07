<?php

/**
 * This is the model class for table "gal_u_discipline".
 *
 * The followings are the available columns in table 'gal_u_discipline':
 * @property string $id
 * @property string $nrec
 * @property string $name
 * @property string $code
 * @property string $abbr
 * @property string $cchair
 * @property string $ccycle
 * @property integer $wphyscultur
 * @property integer $wtype
 * @property integer $wcredcount
 * @property string $ccatlang
 * @property integer $wproperties
 */
class uDiscipline extends CActiveRecord
{

    const DIS_GOS_EXAM = '0x8001000000001d9e'; //дисциплина гос. экзамен

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'gal_u_discipline';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nrec, name, code, abbr, cchair, ccycle, wphyscultur, wtype, wcredcount, ccatlang, wproperties', 'required'),
            array('wphyscultur, wtype, wcredcount, wproperties', 'numerical', 'integerOnly'=>true),
            array('nrec, cchair, ccycle, ccatlang', 'length', 'max'=>8),
            array('name', 'length', 'max'=>200),
            array('code, abbr', 'length', 'max'=>20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nrec, name, code, abbr, cchair, ccycle, wphyscultur, wtype, wcredcount, ccatlang, wproperties', 'safe', 'on'=>'search'),
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
            'nrec' => 'Nrec',
            'name' => 'Name',
            'code' => 'Code',
            'abbr' => 'Abbr',
            'cchair' => 'Cchair',
            'ccycle' => 'Ccycle',
            'wphyscultur' => 'Wphyscultur',
            'wtype' => 'Wtype',
            'wcredcount' => 'Wcredcount',
            'ccatlang' => 'Ccatlang',
            'wproperties' => 'Wproperties',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('nrec',$this->nrec,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('abbr',$this->abbr,true);
        $criteria->compare('cchair',$this->cchair,true);
        $criteria->compare('ccycle',$this->ccycle,true);
        $criteria->compare('wphyscultur',$this->wphyscultur);
        $criteria->compare('wtype',$this->wtype);
        $criteria->compare('wcredcount',$this->wcredcount);
        $criteria->compare('ccatlang',$this->ccatlang,true);
        $criteria->compare('wproperties',$this->wproperties);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
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
     * @return uDiscipline the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}