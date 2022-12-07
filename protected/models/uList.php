<?php

/**
 * This is the model class for table "gal_u_list".
 *
 * The followings are the available columns in table 'gal_u_list':
 * @property string $id
 * @property string $nrec
 * @property string $descr
 * @property string $desgr
 * @property integer $wtype
 * @property string $ccur
 * @property string $cdis
 * @property integer $datemake
 * @property integer $datedoc
 * @property integer $wperiod
 * @property string $numdoc
 * @property string $barcode
 * @property integer $wformed
 * @property string $cfac
 * @property string $cinstitut
 * @property string $cchair
 * @property string $cexaminer
 * @property string $cstgr
 * @property string $cparent
 * @property integer $wsemestr
 * @property integer $wtypediffer
 * @property integer $wstatus
 * @property integer $whours
 * @property integer $whoursaud
 * @property integer $indiplom
 * @property integer $wyeared
 * @property string $ctypework
 * @property integer $waddfld#1#
 * @property integer $waddfld#2#
 * @property integer $waddfld#3#
 * @property integer $waddfld#4#
 * @property integer $waddfld#5#
 * @property integer $waddfld#6#
 * @property integer $waddfld#7#
 * @property integer $waddfld#8#
 * @property integer $waddfld#9#
 * @property integer $waddfld#10#
 * @property string $caddfld#1#
 * @property string $caddfld#2#
 * @property string $caddfld#3#
 * @property string $caddfld#4#
 * @property string $caddfld#5#
 * @property string $caddfld#6#
 * @property string $caddfld#7#
 * @property string $caddfld#8#
 * @property string $caddfld#9#
 * @property string $caddfld#10#
 * @property string $daddfld#1#
 * @property string $daddfld#2#
 * @property string $daddfld#3#
 * @property string $daddfld#4#
 * @property string $daddfld#5#
 * @property string $daddfld#6#
 * @property string $daddfld#7#
 * @property string $daddfld#8#
 * @property string $daddfld#9#
 * @property string $daddfld#10#
 * @property string $saddfld#1#
 * @property string $saddfld#2#
 * @property string $saddfld#3#
 * @property string $saddfld#4#
 * @property string $saddfld#5#
 * @property string $saddfld#6#
 * @property string $saddfld#7#
 * @property string $saddfld#8#
 * @property string $saddfld#9#
 * @property string $saddfld#10#
 */
class uList extends CActiveRecord {

    const TYPE_LADDER = 1;
    const TYPE_EXAM = 2;
    const TYPE_KURS_WORK = 3;
    const TYPE_KURS_PROJECT = 4;
    const TYPE_DIP_WORK = 5;
    const TYPE_DIP_PROJECT = 6;
    const TYPE_PRACTICE = 9;
    const TYPE_PRACTICE_EXTRA = 109;

    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CLOSE = 2;

    const rating_Rsem = '0x8001000000000026';
    const rating_Ra = '0x8001000000000027';
    const rating_R = '0x8001000000000028';
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'gal_u_list';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nrec, descr, desgr, wtype, ccur, cdis, datemake, datedoc, wperiod, numdoc, barcode, wformed, cfac, cinstitut, cchair, cexaminer, cstgr, cparent, wsemestr, wtypediffer, wstatus, whours, whoursaud, indiplom, wyeared, ctypework, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#, daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#, saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#', 'required'),
            array('wtype, datemake, datedoc, wperiod, wformed, wsemestr, wtypediffer, wstatus, whours, whoursaud, indiplom, wyeared, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#', 'numerical', 'integerOnly' => true),
            array('nrec, ccur, cdis, cfac, cinstitut, cchair, cexaminer, cstgr, cparent, ctypework, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#', 'length', 'max' => 8),
            array('descr, numdoc', 'length', 'max' => 20),
            array('desgr', 'length', 'max' => 4),
            array('barcode', 'length', 'max' => 255),
            array('daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#', 'length', 'max' => 24),
            array('saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#', 'length', 'max' => 60),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nrec, descr, desgr, wtype, ccur, cdis, datemake, datedoc, wperiod, numdoc, barcode, wformed, cfac, cinstitut, cchair, cexaminer, cstgr, cparent, wsemestr, wtypediffer, wstatus, whours, whoursaud, indiplom, wyeared, ctypework, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#, daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#, saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'nrec' => 'Nrec',
            'descr' => 'Descr',
            'desgr' => 'Desgr',
            'wtype' => 'Wtype',
            'ccur' => 'Ccur',
            'cdis' => 'Cdis',
            'datemake' => 'Datemake',
            'datedoc' => 'Datedoc',
            'wperiod' => 'Wperiod',
            'numdoc' => 'Numdoc',
            'barcode' => 'Barcode',
            'wformed' => 'Wformed',
            'cfac' => 'Cfac',
            'cinstitut' => 'Cinstitut',
            'cchair' => 'Cchair',
            'cexaminer' => 'Cexaminer',
            'cstgr' => 'Cstgr',
            'cparent' => 'Cparent',
            'wsemestr' => 'Wsemestr',
            'wtypediffer' => 'Wtypediffer',
            'wstatus' => 'Wstatus',
            'whours' => 'Whours',
            'whoursaud' => 'Whoursaud',
            'indiplom' => 'Indiplom',
            'wyeared' => 'Wyeared',
            'ctypework' => 'Ctypework',
            'waddfld#1#' => 'Waddfld#1#',
            'waddfld#2#' => 'Waddfld#2#',
            'waddfld#3#' => 'Waddfld#3#',
            'waddfld#4#' => 'Waddfld#4#',
            'waddfld#5#' => 'Waddfld#5#',
            'waddfld#6#' => 'Waddfld#6#',
            'waddfld#7#' => 'Waddfld#7#',
            'waddfld#8#' => 'Waddfld#8#',
            'waddfld#9#' => 'Waddfld#9#',
            'waddfld#10#' => 'Waddfld#10#',
            'caddfld#1#' => 'Caddfld#1#',
            'caddfld#2#' => 'Caddfld#2#',
            'caddfld#3#' => 'Caddfld#3#',
            'caddfld#4#' => 'Caddfld#4#',
            'caddfld#5#' => 'Caddfld#5#',
            'caddfld#6#' => 'Caddfld#6#',
            'caddfld#7#' => 'Caddfld#7#',
            'caddfld#8#' => 'Caddfld#8#',
            'caddfld#9#' => 'Caddfld#9#',
            'caddfld#10#' => 'Caddfld#10#',
            'daddfld#1#' => 'Daddfld#1#',
            'daddfld#2#' => 'Daddfld#2#',
            'daddfld#3#' => 'Daddfld#3#',
            'daddfld#4#' => 'Daddfld#4#',
            'daddfld#5#' => 'Daddfld#5#',
            'daddfld#6#' => 'Daddfld#6#',
            'daddfld#7#' => 'Daddfld#7#',
            'daddfld#8#' => 'Daddfld#8#',
            'daddfld#9#' => 'Daddfld#9#',
            'daddfld#10#' => 'Daddfld#10#',
            'saddfld#1#' => 'Saddfld#1#',
            'saddfld#2#' => 'Saddfld#2#',
            'saddfld#3#' => 'Saddfld#3#',
            'saddfld#4#' => 'Saddfld#4#',
            'saddfld#5#' => 'Saddfld#5#',
            'saddfld#6#' => 'Saddfld#6#',
            'saddfld#7#' => 'Saddfld#7#',
            'saddfld#8#' => 'Saddfld#8#',
            'saddfld#9#' => 'Saddfld#9#',
            'saddfld#10#' => 'Saddfld#10#',
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
        $criteria->compare('descr', $this->descr, true);
        $criteria->compare('desgr', $this->desgr, true);
        $criteria->compare('wtype', $this->wtype);
        $criteria->compare('ccur', $this->ccur, true);
        $criteria->compare('cdis', $this->cdis, true);
        $criteria->compare('datemake', $this->datemake);
        $criteria->compare('datedoc', $this->datedoc);
        $criteria->compare('wperiod', $this->wperiod);
        $criteria->compare('numdoc', $this->numdoc, true);
        $criteria->compare('barcode', $this->barcode, true);
        $criteria->compare('wformed', $this->wformed);
        $criteria->compare('cfac', $this->cfac, true);
        $criteria->compare('cinstitut', $this->cinstitut, true);
        $criteria->compare('cchair', $this->cchair, true);
        $criteria->compare('cexaminer', $this->cexaminer, true);
        $criteria->compare('cstgr', $this->cstgr, true);
        $criteria->compare('cparent', $this->cparent, true);
        $criteria->compare('wsemestr', $this->wsemestr);
        $criteria->compare('wtypediffer', $this->wtypediffer);
        $criteria->compare('wstatus', $this->wstatus);
        $criteria->compare('whours', $this->whours);
        $criteria->compare('whoursaud', $this->whoursaud);
        $criteria->compare('indiplom', $this->indiplom);
        $criteria->compare('wyeared', $this->wyeared);
        $criteria->compare('ctypework', $this->ctypework, true);
        /* $criteria->compare('waddfld#1#',$this->waddfld#1#);
          $criteria->compare('waddfld#2#',$this->waddfld#2#);
          $criteria->compare('waddfld#3#',$this->waddfld#3#);
          $criteria->compare('waddfld#4#',$this->waddfld#4#);
          $criteria->compare('waddfld#5#',$this->waddfld#5#);
          $criteria->compare('waddfld#6#',$this->waddfld#6#);
          $criteria->compare('waddfld#7#',$this->waddfld#7#);
          $criteria->compare('waddfld#8#',$this->waddfld#8#);
          $criteria->compare('waddfld#9#',$this->waddfld#9#);
          $criteria->compare('waddfld#10#',$this->waddfld#10#);
          $criteria->compare('caddfld#1#',$this->caddfld#1#,true);
          $criteria->compare('caddfld#2#',$this->caddfld#2#,true);
          $criteria->compare('caddfld#3#',$this->caddfld#3#,true);
          $criteria->compare('caddfld#4#',$this->caddfld#4#,true);
          $criteria->compare('caddfld#5#',$this->caddfld#5#,true);
          $criteria->compare('caddfld#6#',$this->caddfld#6#,true);
          $criteria->compare('caddfld#7#',$this->caddfld#7#,true);
          $criteria->compare('caddfld#8#',$this->caddfld#8#,true);
          $criteria->compare('caddfld#9#',$this->caddfld#9#,true);
          $criteria->compare('caddfld#10#',$this->caddfld#10#,true);
          $criteria->compare('daddfld#1#',$this->daddfld#1#,true);
          $criteria->compare('daddfld#2#',$this->daddfld#2#,true);
          $criteria->compare('daddfld#3#',$this->daddfld#3#,true);
          $criteria->compare('daddfld#4#',$this->daddfld#4#,true);
          $criteria->compare('daddfld#5#',$this->daddfld#5#,true);
          $criteria->compare('daddfld#6#',$this->daddfld#6#,true);
          $criteria->compare('daddfld#7#',$this->daddfld#7#,true);
          $criteria->compare('daddfld#8#',$this->daddfld#8#,true);
          $criteria->compare('daddfld#9#',$this->daddfld#9#,true);
          $criteria->compare('daddfld#10#',$this->daddfld#10#,true);
          $criteria->compare('saddfld#1#',$this->saddfld#1#,true);
          $criteria->compare('saddfld#2#',$this->saddfld#2#,true);
          $criteria->compare('saddfld#3#',$this->saddfld#3#,true);
          $criteria->compare('saddfld#4#',$this->saddfld#4#,true);
          $criteria->compare('saddfld#5#',$this->saddfld#5#,true);
          $criteria->compare('saddfld#6#',$this->saddfld#6#,true);
          $criteria->compare('saddfld#7#',$this->saddfld#7#,true);
          $criteria->compare('saddfld#8#',$this->saddfld#8#,true);
          $criteria->compare('saddfld#9#',$this->saddfld#9#,true);
          $criteria->compare('saddfld#10#',$this->saddfld#10#,true); */

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
     * @return uList the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
