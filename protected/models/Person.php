<?php

/**
 * This is the model class for table "gal_person".
 *
 * The followings are the available columns in table 'gal_person':
 * @property string $id
 * @property string $nrec
 * @property string $lastuser
 * @property integer $lasttime
 * @property integer $lastdate
 * @property integer $filialno
 * @property string $fio
 * @property integer $borndate
 * @property string $bornaddr
 * @property integer $dependants
 * @property string $sex
 * @property string $nationality
 * @property string $passprus
 * @property string $passpfor
 * @property string $liveaddr
 * @property string $passpaddr
 * @property string $familystate
 * @property string $education
 * @property integer $publications
 * @property integer $inventions
 * @property string $passnmb
 * @property string $department
 * @property string $galdep
 * @property string $galdephost
 * @property integer $tabnmb
 * @property integer $testperiod
 * @property string $jobnature
 * @property string $complsrc
 * @property string $invalidgrp
 * @property integer $pensiondate
 * @property string $pensioncause
 * @property integer $disorderdate
 * @property string $disordernmb
 * @property integer $disdate
 * @property string $disreason
 * @property string $dismotive
 * @property string $disprofnmb
 * @property integer $disprofdate
 * @property integer $appdate
 * @property string $appointcur
 * @property string $appointfirst
 * @property string $appointlast
 * @property string $isemployee
 * @property integer $ispersbuh
 * @property string $gr
 * @property string $csovm
 * @property integer $disdatepr
 * @property string $caddnrec1
 * @property integer $dadddate1
 * @property string $caddnrec2
 * @property integer $dadddate2
 * @property integer $waddword
 * @property string $cbaseprof
 * @property string $caddprof
 * @property string $cdopref1
 * @property string $cdopref2
 * @property string $cdopref3
 * @property integer $wprizn1
 * @property integer $wprizn2
 * @property integer $wprizn3
 * @property integer $ddat1
 * @property integer $ddat2
 * @property integer $iattr
 * @property string $cprizn1
 * @property string $cprizn2
 * @property string $cprizn3
 * @property integer $wfeature1
 * @property integer $wfeature2
 * @property integer $ddop1
 * @property integer $ddop2
 * @property string $strhost
 * @property string $strtabn
 */
class Person extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'gal_persons';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nrec, lastuser, lasttime, lastdate, filialno, fio, borndate, bornaddr, dependants, sex, nationality, passprus, passpfor, liveaddr, passpaddr, familystate, education, publications, inventions, passnmb, department, galdep, galdephost, tabnmb, testperiod, jobnature, complsrc, invalidgrp, pensiondate, pensioncause, disorderdate, disordernmb, disdate, disreason, dismotive, disprofnmb, disprofdate, appdate, appointcur, appointfirst, appointlast, isemployee, ispersbuh, gr, csovm, disdatepr, caddnrec1, dadddate1, caddnrec2, dadddate2, waddword, cbaseprof, caddprof, cdopref1, cdopref2, cdopref3, wprizn1, wprizn2, wprizn3, ddat1, ddat2, iattr, cprizn1, cprizn2, cprizn3, wfeature1, wfeature2, ddop1, ddop2, strhost, strtabn', 'required'),
            array('lasttime, lastdate, filialno, borndate, dependants, publications, inventions, tabnmb, testperiod, pensiondate, disorderdate, disdate, disprofdate, appdate, ispersbuh, disdatepr, dadddate1, dadddate2, waddword, wprizn1, wprizn2, wprizn3, ddat1, ddat2, iattr, wfeature1, wfeature2, ddop1, ddop2', 'numerical', 'integerOnly'=>true),
            array('nrec, bornaddr, nationality, passprus, passpfor, liveaddr, passpaddr, familystate, education, department, galdep, galdephost, jobnature, complsrc, invalidgrp, pensioncause, disreason, dismotive, appointcur, appointfirst, appointlast, gr, csovm, caddnrec1, caddnrec2, cbaseprof, caddprof, cdopref1, cdopref2, cdopref3, cprizn1, cprizn2, cprizn3, strhost', 'length', 'max'=>8),
            array('lastuser, passnmb, disordernmb, disprofnmb, strtabn', 'length', 'max'=>20),
            array('fio', 'length', 'max'=>60),
            array('sex, isemployee', 'length', 'max'=>1),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nrec, lastuser, lasttime, lastdate, filialno, fio, borndate, bornaddr, dependants, sex, nationality, passprus, passpfor, liveaddr, passpaddr, familystate, education, publications, inventions, passnmb, department, galdep, galdephost, tabnmb, testperiod, jobnature, complsrc, invalidgrp, pensiondate, pensioncause, disorderdate, disordernmb, disdate, disreason, dismotive, disprofnmb, disprofdate, appdate, appointcur, appointfirst, appointlast, isemployee, ispersbuh, gr, csovm, disdatepr, caddnrec1, dadddate1, caddnrec2, dadddate2, waddword, cbaseprof, caddprof, cdopref1, cdopref2, cdopref3, wprizn1, wprizn2, wprizn3, ddat1, ddat2, iattr, cprizn1, cprizn2, cprizn3, wfeature1, wfeature2, ddop1, ddop2, strhost, strtabn', 'safe', 'on'=>'search'),
        );
    }

    public function getTableSchema() {
        $table = parent::getTableSchema();

        $table->columns['nrec']->isForeignKey = true;
        $table->foreignKeys['nrec'] = ['uStudent', 'nrec'];
        return $table;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'studentModels' => [self::HAS_MANY, 'uStudent', 'cpersons'],
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
            'fio' => 'Fio',
            'borndate' => 'Borndate',
            'bornaddr' => 'Bornaddr',
            'dependants' => 'Dependants',
            'sex' => 'Sex',
            'nationality' => 'Nationality',
            'passprus' => 'Passprus',
            'passpfor' => 'Passpfor',
            'liveaddr' => 'Liveaddr',
            'passpaddr' => 'Passpaddr',
            'familystate' => 'Familystate',
            'education' => 'Education',
            'publications' => 'Publications',
            'inventions' => 'Inventions',
            'passnmb' => 'Passnmb',
            'department' => 'Department',
            'galdep' => 'Galdep',
            'galdephost' => 'Galdephost',
            'tabnmb' => 'Tabnmb',
            'testperiod' => 'Testperiod',
            'jobnature' => 'Jobnature',
            'complsrc' => 'Complsrc',
            'invalidgrp' => 'Invalidgrp',
            'pensiondate' => 'Pensiondate',
            'pensioncause' => 'Pensioncause',
            'disorderdate' => 'Disorderdate',
            'disordernmb' => 'Disordernmb',
            'disdate' => 'Disdate',
            'disreason' => 'Disreason',
            'dismotive' => 'Dismotive',
            'disprofnmb' => 'Disprofnmb',
            'disprofdate' => 'Disprofdate',
            'appdate' => 'Appdate',
            'appointcur' => 'Appointcur',
            'appointfirst' => 'Appointfirst',
            'appointlast' => 'Appointlast',
            'isemployee' => 'Isemployee',
            'ispersbuh' => 'Ispersbuh',
            'gr' => 'Gr',
            'csovm' => 'Csovm',
            'disdatepr' => 'Disdatepr',
            'caddnrec1' => 'Caddnrec1',
            'dadddate1' => 'Dadddate1',
            'caddnrec2' => 'Caddnrec2',
            'dadddate2' => 'Dadddate2',
            'waddword' => 'Waddword',
            'cbaseprof' => 'Cbaseprof',
            'caddprof' => 'Caddprof',
            'cdopref1' => 'Cdopref1',
            'cdopref2' => 'Cdopref2',
            'cdopref3' => 'Cdopref3',
            'wprizn1' => 'Wprizn1',
            'wprizn2' => 'Wprizn2',
            'wprizn3' => 'Wprizn3',
            'ddat1' => 'Ddat1',
            'ddat2' => 'Ddat2',
            'iattr' => 'Iattr',
            'cprizn1' => 'Cprizn1',
            'cprizn2' => 'Cprizn2',
            'cprizn3' => 'Cprizn3',
            'wfeature1' => 'Wfeature1',
            'wfeature2' => 'Wfeature2',
            'ddop1' => 'Ddop1',
            'ddop2' => 'Ddop2',
            'strhost' => 'Strhost',
            'strtabn' => 'Strtabn',
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
        $criteria->compare('fio',$this->fio,true);
        $criteria->compare('borndate',$this->borndate);
        $criteria->compare('bornaddr',$this->bornaddr,true);
        $criteria->compare('dependants',$this->dependants);
        $criteria->compare('sex',$this->sex,true);
        $criteria->compare('nationality',$this->nationality,true);
        $criteria->compare('passprus',$this->passprus,true);
        $criteria->compare('passpfor',$this->passpfor,true);
        $criteria->compare('liveaddr',$this->liveaddr,true);
        $criteria->compare('passpaddr',$this->passpaddr,true);
        $criteria->compare('familystate',$this->familystate,true);
        $criteria->compare('education',$this->education,true);
        $criteria->compare('publications',$this->publications);
        $criteria->compare('inventions',$this->inventions);
        $criteria->compare('passnmb',$this->passnmb,true);
        $criteria->compare('department',$this->department,true);
        $criteria->compare('galdep',$this->galdep,true);
        $criteria->compare('galdephost',$this->galdephost,true);
        $criteria->compare('tabnmb',$this->tabnmb);
        $criteria->compare('testperiod',$this->testperiod);
        $criteria->compare('jobnature',$this->jobnature,true);
        $criteria->compare('complsrc',$this->complsrc,true);
        $criteria->compare('invalidgrp',$this->invalidgrp,true);
        $criteria->compare('pensiondate',$this->pensiondate);
        $criteria->compare('pensioncause',$this->pensioncause,true);
        $criteria->compare('disorderdate',$this->disorderdate);
        $criteria->compare('disordernmb',$this->disordernmb,true);
        $criteria->compare('disdate',$this->disdate);
        $criteria->compare('disreason',$this->disreason,true);
        $criteria->compare('dismotive',$this->dismotive,true);
        $criteria->compare('disprofnmb',$this->disprofnmb,true);
        $criteria->compare('disprofdate',$this->disprofdate);
        $criteria->compare('appdate',$this->appdate);
        $criteria->compare('appointcur',$this->appointcur,true);
        $criteria->compare('appointfirst',$this->appointfirst,true);
        $criteria->compare('appointlast',$this->appointlast,true);
        $criteria->compare('isemployee',$this->isemployee,true);
        $criteria->compare('ispersbuh',$this->ispersbuh);
        $criteria->compare('gr',$this->gr,true);
        $criteria->compare('csovm',$this->csovm,true);
        $criteria->compare('disdatepr',$this->disdatepr);
        $criteria->compare('caddnrec1',$this->caddnrec1,true);
        $criteria->compare('dadddate1',$this->dadddate1);
        $criteria->compare('caddnrec2',$this->caddnrec2,true);
        $criteria->compare('dadddate2',$this->dadddate2);
        $criteria->compare('waddword',$this->waddword);
        $criteria->compare('cbaseprof',$this->cbaseprof,true);
        $criteria->compare('caddprof',$this->caddprof,true);
        $criteria->compare('cdopref1',$this->cdopref1,true);
        $criteria->compare('cdopref2',$this->cdopref2,true);
        $criteria->compare('cdopref3',$this->cdopref3,true);
        $criteria->compare('wprizn1',$this->wprizn1);
        $criteria->compare('wprizn2',$this->wprizn2);
        $criteria->compare('wprizn3',$this->wprizn3);
        $criteria->compare('ddat1',$this->ddat1);
        $criteria->compare('ddat2',$this->ddat2);
        $criteria->compare('iattr',$this->iattr);
        $criteria->compare('cprizn1',$this->cprizn1,true);
        $criteria->compare('cprizn2',$this->cprizn2,true);
        $criteria->compare('cprizn3',$this->cprizn3,true);
        $criteria->compare('wfeature1',$this->wfeature1);
        $criteria->compare('wfeature2',$this->wfeature2);
        $criteria->compare('ddop1',$this->ddop1);
        $criteria->compare('ddop2',$this->ddop2);
        $criteria->compare('strhost',$this->strhost,true);
        $criteria->compare('strtabn',$this->strtabn,true);

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
     * @return Person the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}