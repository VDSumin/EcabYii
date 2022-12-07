<?php

/**
 * This is the model class for table "gal_catalog".
 *
 * The followings are the available columns in table 'gal_catalog':
 * @property string $id
 * @property string $nrec
 * @property string $lastuser
 * @property integer $lasttime
 * @property integer $lastdate
 * @property integer $filialno
 * @property string $cparent
 * @property integer $groupcode
 * @property integer $syscode
 * @property string $addinf
 * @property string $name
 * @property string $code
 * @property string $catdata
 * @property string $mainlink
 * @property integer $lpr
 * @property integer $bmulti
 * @property integer $bpick
 * @property integer $isleaf
 * @property string $cref
 * @property integer $datn
 * @property integer $datok
 * @property string $sdopinf
 * @property string $cref1
 * @property string $cref2
 * @property string $cref3
 * @property integer $wkod
 * @property integer $dat1
 * @property integer $dat2
 * @property string $dopstr
 * @property string $longname
 */
class Catalog extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'gal_catalogs';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nrec, lastuser, lasttime, lastdate, filialno, cparent, groupcode, syscode, addinf, name, code, catdata, mainlink, lpr, bmulti, bpick, isleaf, cref, datn, datok, sdopinf, cref1, cref2, cref3, wkod, dat1, dat2, dopstr, longname', 'required'),
            array('lasttime, lastdate, filialno, groupcode, syscode, lpr, bmulti, bpick, isleaf, datn, datok, wkod, dat1, dat2', 'numerical', 'integerOnly'=>true),
            array('nrec, cparent, mainlink, cref, cref1, cref2, cref3', 'length', 'max'=>8),
            array('lastuser, addinf, catdata, dopstr', 'length', 'max'=>20),
            array('name', 'length', 'max'=>255),
            array('code, sdopinf', 'length', 'max'=>100),
            array('longname', 'length', 'max'=>250),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nrec, lastuser, lasttime, lastdate, filialno, cparent, groupcode, syscode, addinf, name, code, catdata, mainlink, lpr, bmulti, bpick, isleaf, cref, datn, datok, sdopinf, cref1, cref2, cref3, wkod, dat1, dat2, dopstr, longname', 'safe', 'on'=>'search'),
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
            'lastuser' => 'Lastuser',
            'lasttime' => 'Lasttime',
            'lastdate' => 'Lastdate',
            'filialno' => 'Filialno',
            'cparent' => 'Cparent',
            'groupcode' => 'Groupcode',
            'syscode' => 'Syscode',
            'addinf' => 'Addinf',
            'name' => 'Name',
            'code' => 'Code',
            'catdata' => 'Catdata',
            'mainlink' => 'Mainlink',
            'lpr' => 'Lpr',
            'bmulti' => 'Bmulti',
            'bpick' => 'Bpick',
            'isleaf' => 'Isleaf',
            'cref' => 'Cref',
            'datn' => 'Datn',
            'datok' => 'Datok',
            'sdopinf' => 'Sdopinf',
            'cref1' => 'Cref1',
            'cref2' => 'Cref2',
            'cref3' => 'Cref3',
            'wkod' => 'Wkod',
            'dat1' => 'Dat1',
            'dat2' => 'Dat2',
            'dopstr' => 'Dopstr',
            'longname' => 'Longname',
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
        $criteria->compare('lastuser',$this->lastuser,true);
        $criteria->compare('lasttime',$this->lasttime);
        $criteria->compare('lastdate',$this->lastdate);
        $criteria->compare('filialno',$this->filialno);
        $criteria->compare('cparent',$this->cparent,true);
        $criteria->compare('groupcode',$this->groupcode);
        $criteria->compare('syscode',$this->syscode);
        $criteria->compare('addinf',$this->addinf,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('catdata',$this->catdata,true);
        $criteria->compare('mainlink',$this->mainlink,true);
        $criteria->compare('lpr',$this->lpr);
        $criteria->compare('bmulti',$this->bmulti);
        $criteria->compare('bpick',$this->bpick);
        $criteria->compare('isleaf',$this->isleaf);
        $criteria->compare('cref',$this->cref,true);
        $criteria->compare('datn',$this->datn);
        $criteria->compare('datok',$this->datok);
        $criteria->compare('sdopinf',$this->sdopinf,true);
        $criteria->compare('cref1',$this->cref1,true);
        $criteria->compare('cref2',$this->cref2,true);
        $criteria->compare('cref3',$this->cref3,true);
        $criteria->compare('wkod',$this->wkod);
        $criteria->compare('dat1',$this->dat1);
        $criteria->compare('dat2',$this->dat2);
        $criteria->compare('dopstr',$this->dopstr,true);
        $criteria->compare('longname',$this->longname,true);

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
     * @return Catalog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}