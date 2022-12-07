<?php

/**
 * This is the model class for table "gal_u_curriculum".
 *
 * The followings are the available columns in table 'gal_u_curriculum':
 * @property string $id
 * @property string $nrec
 * @property string $name
 * @property integer $wtype
 * @property string $cparent
 * @property string $cmodel
 * @property string $cedstd
 * @property string $descr
 * @property string $desgr
 * @property string $cnote
 * @property integer $status
 * @property integer $wformed
 * @property double $term
 * @property integer $dateapp
 * @property integer $dateend
 * @property string $regnum
 * @property string $specialitycode
 * @property integer $yeared
 * @property integer $course
 * @property string $cqualification
 * @property string $cspeciality
 * @property string $cspecialization
 * @property string $cfaculty
 * @property string $cinstitut
 * @property string $cchair
 * @property string $cbaseeducation
 * @property integer $numstud
 * @property integer $numstudload
 * @property integer $hourgosplan
 * @property double $dcreditgosplan
 * @property integer $hourcurplan
 * @property double $dcreditcurplan
 * @property integer $hourlecroom
 * @property integer $hourstudown
 * @property integer $hourcontown
 * @property integer $hourexam
 * @property integer $numexam
 * @property integer $numtest
 * @property double $credlecroom
 * @property double $credstudown
 * @property double $credcontown
 * @property double $credexam
 * @property double $credtest
 * @property double $credprj
 * @property double $credwrk
 * @property double $credctr
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
 * @property integer $wgos
 * @property integer $wdegree
 * @property double $dcredequival
 * @property double $dcredcapacity
 * @property integer $wshot
 * @property integer $reattsize
 * @property double $reattcredsize
 * @property integer $wmodulemode
 * @property integer $wproperties
 * @property integer $wtypeedsched
 * @property integer $wmonth
 * @property integer $hourcontwrk
 * @property double $credcontwrk
 * @property integer $info
 */
class uCurriculum extends CActiveRecord {

    const INTERNAL = 0; //Очная
    const EXTERMURAL = 1; //Заочная
    const EVENING = 2; //Вечерняя

    const DEG_SPECIALIST = 0;
    const DEG_BACHELOR = 1;
    const DEG_MASTER = 2;
    const DEG_GRADUATE = 5;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'gal_u_curriculum';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nrec, name, wtype, cparent, cmodel, cedstd, descr, desgr, cnote, status, wformed, term, dateapp, dateend, regnum, specialitycode, yeared, course, cqualification, cspeciality, cspecialization, cfaculty, cinstitut, cchair, cbaseeducation, numstud, numstudload, hourgosplan, dcreditgosplan, hourcurplan, dcreditcurplan, hourlecroom, hourstudown, hourcontown, hourexam, numexam, numtest, credlecroom, credstudown, credcontown, credexam, credtest, credprj, credwrk, credctr, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#, daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#, saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#, wgos, wdegree, dcredequival, dcredcapacity, wshot, reattsize, reattcredsize, wmodulemode, wproperties, wtypeedsched, wmonth, hourcontwrk, credcontwrk, info', 'required'),
            array('wtype, status, wformed, dateapp, dateend, yeared, course, numstud, numstudload, hourgosplan, hourcurplan, hourlecroom, hourstudown, hourcontown, hourexam, numexam, numtest, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#, wgos, wdegree, wshot, reattsize, wmodulemode, wproperties, wtypeedsched, wmonth, hourcontwrk, info', 'numerical', 'integerOnly' => true),
            array('term, dcreditgosplan, dcreditcurplan, credlecroom, credstudown, credcontown, credexam, credtest, credprj, credwrk, credctr, dcredequival, dcredcapacity, reattcredsize, credcontwrk', 'numerical'),
            array('nrec, cparent, cmodel, cedstd, cnote, cqualification, cspeciality, cspecialization, cfaculty, cinstitut, cchair, cbaseeducation, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#', 'length', 'max' => 8),
            array('name', 'length', 'max' => 250),
            array('descr, regnum, specialitycode', 'length', 'max' => 20),
            array('desgr', 'length', 'max' => 4),
            array('daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#', 'length', 'max' => 24),
            array('saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#', 'length', 'max' => 60),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nrec, name, wtype, cparent, cmodel, cedstd, descr, desgr, cnote, status, wformed, term, dateapp, dateend, regnum, specialitycode, yeared, course, cqualification, cspeciality, cspecialization, cfaculty, cinstitut, cchair, cbaseeducation, numstud, numstudload, hourgosplan, dcreditgosplan, hourcurplan, dcreditcurplan, hourlecroom, hourstudown, hourcontown, hourexam, numexam, numtest, credlecroom, credstudown, credcontown, credexam, credtest, credprj, credwrk, credctr, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#, daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#, saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#, wgos, wdegree, dcredequival, dcredcapacity, wshot, reattsize, reattcredsize, wmodulemode, wproperties, wtypeedsched, wmonth, hourcontwrk, credcontwrk, info', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
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
            'name' => 'Name',
            'wtype' => 'Wtype',
            'cparent' => 'Cparent',
            'cmodel' => 'Cmodel',
            'cedstd' => 'Cedstd',
            'descr' => 'Descr',
            'desgr' => 'Desgr',
            'cnote' => 'Cnote',
            'status' => 'Status',
            'wformed' => 'Wformed',
            'term' => 'Term',
            'dateapp' => 'Dateapp',
            'dateend' => 'Dateend',
            'regnum' => 'Regnum',
            'specialitycode' => 'Specialitycode',
            'yeared' => 'Yeared',
            'course' => 'Course',
            'cqualification' => 'Cqualification',
            'cspeciality' => 'Cspeciality',
            'cspecialization' => 'Cspecialization',
            'cfaculty' => 'Cfaculty',
            'cinstitut' => 'Cinstitut',
            'cchair' => 'Cchair',
            'cbaseeducation' => 'Cbaseeducation',
            'numstud' => 'Numstud',
            'numstudload' => 'Numstudload',
            'hourgosplan' => 'Hourgosplan',
            'dcreditgosplan' => 'Dcreditgosplan',
            'hourcurplan' => 'Hourcurplan',
            'dcreditcurplan' => 'Dcreditcurplan',
            'hourlecroom' => 'Hourlecroom',
            'hourstudown' => 'Hourstudown',
            'hourcontown' => 'Hourcontown',
            'hourexam' => 'Hourexam',
            'numexam' => 'Numexam',
            'numtest' => 'Numtest',
            'credlecroom' => 'Credlecroom',
            'credstudown' => 'Credstudown',
            'credcontown' => 'Credcontown',
            'credexam' => 'Credexam',
            'credtest' => 'Credtest',
            'credprj' => 'Credprj',
            'credwrk' => 'Credwrk',
            'credctr' => 'Credctr',
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
            'wgos' => 'Wgos',
            'wdegree' => 'Wdegree',
            'dcredequival' => 'Dcredequival',
            'dcredcapacity' => 'Dcredcapacity',
            'wshot' => 'Wshot',
            'reattsize' => 'Reattsize',
            'reattcredsize' => 'Reattcredsize',
            'wmodulemode' => 'Wmodulemode',
            'wproperties' => 'Wproperties',
            'wtypeedsched' => 'Wtypeedsched',
            'wmonth' => 'Wmonth',
            'hourcontwrk' => 'Hourcontwrk',
            'credcontwrk' => 'Credcontwrk',
            'info' => 'Info',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('wtype', $this->wtype);
        $criteria->compare('cparent', $this->cparent, true);
        $criteria->compare('cmodel', $this->cmodel, true);
        $criteria->compare('cedstd', $this->cedstd, true);
        $criteria->compare('descr', $this->descr, true);
        $criteria->compare('desgr', $this->desgr, true);
        $criteria->compare('cnote', $this->cnote, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('wformed', $this->wformed);
        $criteria->compare('term', $this->term);
        $criteria->compare('dateapp', $this->dateapp);
        $criteria->compare('dateend', $this->dateend);
        $criteria->compare('regnum', $this->regnum, true);
        $criteria->compare('specialitycode', $this->specialitycode, true);
        $criteria->compare('yeared', $this->yeared);
        $criteria->compare('course', $this->course);
        $criteria->compare('cqualification', $this->cqualification, true);
        $criteria->compare('cspeciality', $this->cspeciality, true);
        $criteria->compare('cspecialization', $this->cspecialization, true);
        $criteria->compare('cfaculty', $this->cfaculty, true);
        $criteria->compare('cinstitut', $this->cinstitut, true);
        $criteria->compare('cchair', $this->cchair, true);
        $criteria->compare('cbaseeducation', $this->cbaseeducation, true);
        $criteria->compare('numstud', $this->numstud);
        $criteria->compare('numstudload', $this->numstudload);
        $criteria->compare('hourgosplan', $this->hourgosplan);
        $criteria->compare('dcreditgosplan', $this->dcreditgosplan);
        $criteria->compare('hourcurplan', $this->hourcurplan);
        $criteria->compare('dcreditcurplan', $this->dcreditcurplan);
        $criteria->compare('hourlecroom', $this->hourlecroom);
        $criteria->compare('hourstudown', $this->hourstudown);
        $criteria->compare('hourcontown', $this->hourcontown);
        $criteria->compare('hourexam', $this->hourexam);
        $criteria->compare('numexam', $this->numexam);
        $criteria->compare('numtest', $this->numtest);
        $criteria->compare('credlecroom', $this->credlecroom);
        $criteria->compare('credstudown', $this->credstudown);
        $criteria->compare('credcontown', $this->credcontown);
        $criteria->compare('credexam', $this->credexam);
        $criteria->compare('credtest', $this->credtest);
        $criteria->compare('credprj', $this->credprj);
        $criteria->compare('credwrk', $this->credwrk);
        $criteria->compare('credctr', $this->credctr);
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
        $criteria->compare('wgos', $this->wgos);
        $criteria->compare('wdegree', $this->wdegree);
        $criteria->compare('dcredequival', $this->dcredequival);
        $criteria->compare('dcredcapacity', $this->dcredcapacity);
        $criteria->compare('wshot', $this->wshot);
        $criteria->compare('reattsize', $this->reattsize);
        $criteria->compare('reattcredsize', $this->reattcredsize);
        $criteria->compare('wmodulemode', $this->wmodulemode);
        $criteria->compare('wproperties', $this->wproperties);
        $criteria->compare('wtypeedsched', $this->wtypeedsched);
        $criteria->compare('wmonth', $this->wmonth);
        $criteria->compare('hourcontwrk', $this->hourcontwrk);
        $criteria->compare('credcontwrk', $this->credcontwrk);
        $criteria->compare('info', $this->info);

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
     * @return uCurriculum the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function formEdLabels($value = false, $default = null) {
        $labels = array(
            self::INTERNAL => 'Очная',
            self::EXTERMURAL => 'Заочная',
            self::EVENING => 'Вечерняя',
        );

        if ((true === $value) && isset($this->wformed)) {
            return isset($labels[$this->wformed]) ? $labels[$this->wformed] : $default;
        } elseif (false !== $value) {
            return isset($labels[$value]) ? $labels[$value] : $default;
        }
        return $labels;
    }

}
