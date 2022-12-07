<?php

/**
 * This is the model class for table "gal_dopinfo".
 *
 * The followings are the available columns in table 'gal_dopinfo':
 * @property string $id
 * @property string $nrec
 * @property string $lastuser
 * @property integer $lasttime
 * @property integer $lastdate
 * @property integer $filialno
 * @property string $cdoptbl
 * @property string $cperson
 * @property string $cfld#1#
 * @property string $cfld#2#
 * @property string $cfld#3#
 * @property string $cfld#4#
 * @property string $cfld#5#
 * @property integer $bfld#1#
 * @property integer $bfld#2#
 * @property integer $bfld#3#
 * @property integer $bfld#4#
 * @property integer $bfld#5#
 * @property string $ffldsum#1#
 * @property string $ffldsum#2#
 * @property string $ffldsum#3#
 * @property string $ffldsum#4#
 * @property string $ffldsum#5#
 * @property string $sfld#1#
 * @property string $sfld#2#
 * @property string $sfld#3#
 * @property string $sfld#4#
 * @property string $sfld#5#
 * @property integer $dfld#1#
 * @property integer $dfld#2#
 * @property integer $dfld#3#
 * @property integer $dfld#4#
 * @property integer $dfld#5#
 */
class Dopinfo extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'gal_dopinfo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nrec, lastuser, lasttime, lastdate, filialno, cdoptbl, cperson, cfld#1#, cfld#2#, cfld#3#, cfld#4#, cfld#5#, bfld#1#, bfld#2#, bfld#3#, bfld#4#, bfld#5#, ffldsum#1#, ffldsum#2#, ffldsum#3#, ffldsum#4#, ffldsum#5#, sfld#1#, sfld#2#, sfld#3#, sfld#4#, sfld#5#, dfld#1#, dfld#2#, dfld#3#, dfld#4#, dfld#5#', 'required'),
            array('lasttime, lastdate, filialno, bfld#1#, bfld#2#, bfld#3#, bfld#4#, bfld#5#, dfld#1#, dfld#2#, dfld#3#, dfld#4#, dfld#5#', 'numerical', 'integerOnly'=>true),
            array('nrec, cdoptbl, cperson, cfld#1#, cfld#2#, cfld#3#, cfld#4#, cfld#5#', 'length', 'max'=>8),
            array('lastuser', 'length', 'max'=>20),
            array('ffldsum#1#, ffldsum#2#, ffldsum#3#, ffldsum#4#, ffldsum#5#', 'length', 'max'=>31),
            array('sfld#1#, sfld#2#, sfld#3#, sfld#4#, sfld#5#', 'length', 'max'=>50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nrec, lastuser, lasttime, lastdate, filialno, cdoptbl, cperson, cfld#1#, cfld#2#, cfld#3#, cfld#4#, cfld#5#, bfld#1#, bfld#2#, bfld#3#, bfld#4#, bfld#5#, ffldsum#1#, ffldsum#2#, ffldsum#3#, ffldsum#4#, ffldsum#5#, sfld#1#, sfld#2#, sfld#3#, sfld#4#, sfld#5#, dfld#1#, dfld#2#, dfld#3#, dfld#4#, dfld#5#', 'safe', 'on'=>'search'),
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
            'cdoptbl' => 'Cdoptbl',
            'cperson' => 'Cperson',
            'cfld#1#' => 'Cfld#1#',
            'cfld#2#' => 'Cfld#2#',
            'cfld#3#' => 'Cfld#3#',
            'cfld#4#' => 'Cfld#4#',
            'cfld#5#' => 'Cfld#5#',
            'bfld#1#' => 'Bfld#1#',
            'bfld#2#' => 'Bfld#2#',
            'bfld#3#' => 'Bfld#3#',
            'bfld#4#' => 'Bfld#4#',
            'bfld#5#' => 'Bfld#5#',
            'ffldsum#1#' => 'Ffldsum#1#',
            'ffldsum#2#' => 'Ffldsum#2#',
            'ffldsum#3#' => 'Ffldsum#3#',
            'ffldsum#4#' => 'Ffldsum#4#',
            'ffldsum#5#' => 'Ffldsum#5#',
            'sfld#1#' => 'Sfld#1#',
            'sfld#2#' => 'Sfld#2#',
            'sfld#3#' => 'Sfld#3#',
            'sfld#4#' => 'Sfld#4#',
            'sfld#5#' => 'Sfld#5#',
            'dfld#1#' => 'Dfld#1#',
            'dfld#2#' => 'Dfld#2#',
            'dfld#3#' => 'Dfld#3#',
            'dfld#4#' => 'Dfld#4#',
            'dfld#5#' => 'Dfld#5#',
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
        $criteria->compare('cdoptbl',$this->cdoptbl,true);
        $criteria->compare('cperson',$this->cperson,true);
        /*$criteria->compare('cfld#1#',$this->cfld#1#,true);
        $criteria->compare('cfld#2#',$this->cfld#2#,true);
        $criteria->compare('cfld#3#',$this->cfld#3#,true);
        $criteria->compare('cfld#4#',$this->cfld#4#,true);
        $criteria->compare('cfld#5#',$this->cfld#5#,true);
        $criteria->compare('bfld#1#',$this->bfld#1#);
        $criteria->compare('bfld#2#',$this->bfld#2#);
        $criteria->compare('bfld#3#',$this->bfld#3#);
        $criteria->compare('bfld#4#',$this->bfld#4#);
        $criteria->compare('bfld#5#',$this->bfld#5#);
        $criteria->compare('ffldsum#1#',$this->ffldsum#1#,true);
        $criteria->compare('ffldsum#2#',$this->ffldsum#2#,true);
        $criteria->compare('ffldsum#3#',$this->ffldsum#3#,true);
        $criteria->compare('ffldsum#4#',$this->ffldsum#4#,true);
        $criteria->compare('ffldsum#5#',$this->ffldsum#5#,true);
        $criteria->compare('sfld#1#',$this->sfld#1#,true);
        $criteria->compare('sfld#2#',$this->sfld#2#,true);
        $criteria->compare('sfld#3#',$this->sfld#3#,true);
        $criteria->compare('sfld#4#',$this->sfld#4#,true);
        $criteria->compare('sfld#5#',$this->sfld#5#,true);
        $criteria->compare('dfld#1#',$this->dfld#1#);
        $criteria->compare('dfld#2#',$this->dfld#2#);
        $criteria->compare('dfld#3#',$this->dfld#3#);
        $criteria->compare('dfld#4#',$this->dfld#4#);
        $criteria->compare('dfld#5#',$this->dfld#5#);*/

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
     * @return Dopinfo the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}