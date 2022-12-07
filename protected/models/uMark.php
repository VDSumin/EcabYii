<?php

/**
 * This is the model class for table "gal_u_mark".
 *
 * The followings are the available columns in table 'gal_u_mark':
 * @property string $id
 * @property string $nrec
 * @property string $cpersons
 * @property string $sfio
 * @property string $cmark
 * @property integer $wmark
 * @property integer $wstatus
 * @property string $clistpar
 * @property string $clist
 * @property integer $datemark
 * @property integer $wendres
 * @property string $cdb_dip
 * @property string $cperexam
 * @property integer $wrating
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
class uMark extends CActiveRecord
{

    const TM_FINAL = 1; //окончательная оценка
    const TM_TRANSFER = 2; //оценка со статусом перевод
    const TM_RECERTIFICATION = 3; //оценка со статусом переатестация
    const TM_CURRENT = 0; //оценка со статусом текущая

    public static function markStatusLabels($value = null) {
        $labels = array(
            self::TM_FINAL => 'окончательная оценка',
            self::TM_TRANSFER => 'перевод',
            self::TM_RECERTIFICATION => 'переаттестация',
            self::TM_CURRENT => 'текущая оценка',
        );

        if (isset($labels[$value])) {
            return $labels[$value];
        }
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'gal_u_marks';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nrec, cpersons, sfio, cmark, wmark, wstatus, clistpar, clist, datemark, wendres, cdb_dip, cperexam, wrating, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#, daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#, saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#', 'required'),
            array('wmark, wstatus, datemark, wendres, wrating, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#', 'numerical', 'integerOnly'=>true),
            array('nrec, cpersons, cmark, clistpar, clist, cdb_dip, cperexam, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#', 'length', 'max'=>8),
            array('sfio', 'length', 'max'=>40),
            array('daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#', 'length', 'max'=>24),
            array('saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#', 'length', 'max'=>60),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nrec, cpersons, sfio, cmark, wmark, wstatus, clistpar, clist, datemark, wendres, cdb_dip, cperexam, wrating, waddfld#1#, waddfld#2#, waddfld#3#, waddfld#4#, waddfld#5#, waddfld#6#, waddfld#7#, waddfld#8#, waddfld#9#, waddfld#10#, caddfld#1#, caddfld#2#, caddfld#3#, caddfld#4#, caddfld#5#, caddfld#6#, caddfld#7#, caddfld#8#, caddfld#9#, caddfld#10#, daddfld#1#, daddfld#2#, daddfld#3#, daddfld#4#, daddfld#5#, daddfld#6#, daddfld#7#, daddfld#8#, daddfld#9#, daddfld#10#, saddfld#1#, saddfld#2#, saddfld#3#, saddfld#4#, saddfld#5#, saddfld#6#, saddfld#7#, saddfld#8#, saddfld#9#, saddfld#10#', 'safe', 'on'=>'search'),
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
            'cpersons' => 'Cpersons',
            'sfio' => 'Sfio',
            'cmark' => 'Cmark',
            'wmark' => 'Wmark',
            'wstatus' => 'Wstatus',
            'clistpar' => 'Clistpar',
            'clist' => 'Clist',
            'datemark' => 'Datemark',
            'wendres' => 'Wendres',
            'cdb_dip' => 'Cdb Dip',
            'cperexam' => 'Cperexam',
            'wrating' => 'Wrating',
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
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('nrec',$this->nrec,true);
        $criteria->compare('cpersons',$this->cpersons,true);
        $criteria->compare('sfio',$this->sfio,true);
        $criteria->compare('cmark',$this->cmark,true);
        $criteria->compare('wmark',$this->wmark);
        $criteria->compare('wstatus',$this->wstatus);
        $criteria->compare('clistpar',$this->clistpar,true);
        $criteria->compare('clist',$this->clist,true);
        $criteria->compare('datemark',$this->datemark);
        $criteria->compare('wendres',$this->wendres);
        $criteria->compare('cdb_dip',$this->cdb_dip,true);
        $criteria->compare('cperexam',$this->cperexam,true);
        $criteria->compare('wrating',$this->wrating);
        /*$criteria->compare('waddfld#1#',$this->waddfld#1#);
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
        $criteria->compare('saddfld#10#',$this->saddfld#10#,true);*/

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
     * @return uMark the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    public static function getRating($type, $flag, $rating) {
        //Exam
        if ((in_array($type, array(uList::TYPE_EXAM, uList::TYPE_PRACTICE))) && ($flag == '0') && ($rating =='R')) {
            return array(array(
                'mark' =>  array('name' => 'Отлично', 'nrec' => '0x8000000000000295'),
                'ra_min' => 35,
                'ra_max' => 40,
                'rmin' => 90,
                'rmax' => 100,
                'readonly' => false
            ),
                array(
                    'mark' =>  array('name' => 'Отлично', 'nrec' => '0x8000000000000295'),
                    'ra_min' => 25,
                    'ra_max' => 34,
                    'rmin' => 90,
                    'rmax' => 94,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Хорошо', 'nrec' => '0x8000000000000296'),
                    'ra_min' => 35,
                    'ra_max' => 40,
                    'rmin' => 75,
                    'rmax' => 89,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Хорошо', 'nrec' => '0x8000000000000296'),
                    'ra_min' => 25,
                    'ra_max' => 34,
                    'rmin' => 75,
                    'rmax' => 89,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Хорошо', 'nrec' => '0x8000000000000296'),
                    'ra_min' => 15,
                    'ra_max' => 24,
                    'rmin' => 75,
                    'rmax' => 84,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Удовлетворительно', 'nrec' => '0x8000000000000297'),
                    'ra_min' => 25,
                    'ra_max' => 34,
                    'rmin' => 65,
                    'rmax' => 74,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Удовлетворительно', 'nrec' => '0x8000000000000297'),
                    'ra_min' => 15,
                    'ra_max' => 24,
                    'rmin' => 60,
                    'rmax' => 74,
                ),
                array(
                    'mark' =>  array('name' => 'Удовлетворительно', 'nrec' => '0x8000000000000297'),
                    'ra_min' => 0,
                    'ra_max' => 14,
                    'rmin' => 60,
                    'rmax' => 74,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Неудовлетворительно', 'nrec' => '0x8000000000000298'),
                    'ra_min' => 15,
                    'ra_max' => 24,
                    'rmin' => 55,
                    'rmax' => 59,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Неудовлетворительно', 'nrec' => '0x8000000000000298'),
                    'ra_min' => 0,
                    'ra_max' => 14,
                    'rmin' => 40,
                    'rmax' => 59,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Неудовлетворительно', 'nrec' => '0x8000000000000298'),
                    'ra_min' => 0,
                    'ra_max' => 0,
                    'rmin' => 0,
                    'rmax' => 39,
                    'readonly' => true
                ),
                array(
                    'mark' =>  array('name' => 'Неявка', 'nrec' => '0x800100000000242C'),
                    'ra_min' => -1,
                    'ra_max' => -1,
                    'rmin' => 0,
                    'rmax' => 60,
                    'readonly' => false
                )
            );
        } elseif ((in_array($type, array(uList::TYPE_EXAM, uList::TYPE_PRACTICE))) && ($flag == '0') && ($rating == 'Rsem')) {
            return array(array(
                'error' => 'Рейтинг за семестр должен находиться в пределе от 0 до 60',
                'rmin' => 0,
                'rmax' => 60
            )
            );
        } elseif ((in_array($type, array(uList::TYPE_EXAM, uList::TYPE_PRACTICE))) && ($flag == '0') && ($rating == 'Ra')) {
            return array(array(
                'error' => 'Рейтинг за аттестацию должен находиться в пределе от -1 до 40',
                'rmin' => -1,
                'rmax' => 40
            )
            );
        } elseif (($type == uList::TYPE_LADDER) && ($flag == '1') && ($rating == 'R')) { //Lader R
            return array(
                array(
                    'mark' =>  array('name' => 'Зачтено', 'nrec' => '0x800000000000029C'),
                    'ra_min' => 0,
                    'ra_max' => 40,
                    'rmin' => 60,
                    'rmax' => 100,
                    'readonly' => false
                ),
                array(
                    'mark' =>  array('name' => 'Незачтено', 'nrec' => '0x800000000000029D'),
                    'ra_min' => 0,
                    'ra_max' => 0,
                    'rmin' => 0,
                    'rmax' => 39,
                    'readonly' => true,
                    'rbExist' => false
                ),
                array(
                    'mark' =>  array('name' => 'Незачтено', 'nrec' => '0x800000000000029D'),
                    'ra_min' => -1,
                    'ra_max' => -1,
                    'rmin' => 59,
                    'rmax' => 59,
                    'readonly' => false,
                    'rbExist' => false
                ),
                array(
                    'mark' =>  array('name' => 'Незачтено', 'nrec' => '0x800000000000029D'),
                    'ra_min' => 0,
                    'ra_max' => 19,
                    'rmin' => 40,
                    'rmax' => 59,
                    'readonly' => false,
                    'rbExist' => false
                )
            );
        } elseif (($type == uList::TYPE_LADDER) && ($flag == '1') && ($rating == 'Rsem')) { //Lader Rsem
            return array(
                array(
                    'error' => 'Рейтинг за семестр должен находиться в пределе от 0 до 60',
                    'rmin' => 0,
                    'rmax' => 60
                )
            );

        } elseif (($type == uList::TYPE_LADDER) && ($flag == '1') && ($rating == 'Ra')) { //Lader Ra
            return array(
                array(
                    'error' => 'Рейтинг за аттестацию должен находиться в пределе от -1 до 40',
                    'rmin' => -1,
                    'rmax' => 40
                )
            );
        } elseif ((in_array($type, array(uList::TYPE_LADDER, uList::TYPE_KURS_PROJECT, uList::TYPE_KURS_WORK))) && ($flag == '0') && ($rating =='R')) {
            return array(array(
                'mark' =>  array('name' => 'Отлично', 'nrec' => '0x8000000000000295'),
                'ra_min' => 35,
                'ra_max' => 40,
                'rmin' => 90,
                'rmax' => 100,
            ),
                array(
                    'mark' =>  array('name' => 'Отлично', 'nrec' => '0x8000000000000295'),
                    'ra_min' => 25,
                    'ra_max' => 34,
                    'rmin' => 90,
                    'rmax' => 94,
                ),
                array(
                    'mark' =>  array('name' => 'Хорошо', 'nrec' => '0x8000000000000296'),
                    'ra_min' => 35,
                    'ra_max' => 40,
                    'rmin' => 75,
                    'rmax' => 89,
                ),
                array(
                    'mark' =>  array('name' => 'Хорошо', 'nrec' => '0x8000000000000296'),
                    'ra_min' => 25,
                    'ra_max' => 34,
                    'rmin' => 75,
                    'rmax' => 89,
                ),
                array(
                    'mark' =>  array('name' => 'Хорошо', 'nrec' => '0x8000000000000296'),
                    'ra_min' => 15,
                    'ra_max' => 24,
                    'rmin' => 75,
                    'rmax' => 84,
                ),
                array(
                    'mark' =>  array('name' => 'Удовлетворительно', 'nrec' => '0x8000000000000297'),
                    'ra_min' => 25,
                    'ra_max' => 34,
                    'rmin' => 65,
                    'rmax' => 74,
                ),
                array(
                    'mark' =>  array('name' => 'Удовлетворительно', 'nrec' => '0x8000000000000297'),
                    'ra_min' => 15,
                    'ra_max' => 24,
                    'rmin' => 60,
                    'rmax' => 74,
                ),
                array(
                    'mark' =>  array('name' => 'Удовлетворительно', 'nrec' => '0x8000000000000297'),
                    'ra_min' => 0,
                    'ra_max' => 14,
                    'rmin' => 60,
                    'rmax' => 74,
                ),
                array(
                    'mark' =>  array('name' => 'Неудовлетворительно', 'nrec' => '0x8000000000000298'),
                    'ra_min' => 15,
                    'ra_max' => 24,
                    'rmin' => 55,
                    'rmax' => 59,
                    'rbExist' => false
                ),
                array(
                    'mark' =>  array('name' => 'Неудовлетворительно', 'nrec' => '0x8000000000000298'),
                    'ra_min' => 0,
                    'ra_max' => 14,
                    'rmin' => 0,
                    'rmax' => 59,
                    'rbExist' => false
                ),
                array(
                    'mark' =>  array('name' => 'Неудовлетворительно', 'nrec' => '0x8000000000000298'),
                    'ra_min' => 0,
                    'ra_max' => 0,
                    'rmin' => 0,
                    'rmax' => 39,
                    'rbExist' => false
                )
            );
        } elseif ((in_array ($type, array(uList::TYPE_LADDER, uList::TYPE_KURS_PROJECT, uList::TYPE_KURS_WORK))) && ($flag == '0') && ($rating == 'Rsem')) {
            return array(array(
                'error' => 'Рейтинг за семестр должен находиться в пределе от 0 до 60',
                'rmin' => 0,
                'rmax' => 60
            )
            );
        } elseif ((in_array ($type, array(uList::TYPE_LADDER, uList::TYPE_KURS_PROJECT, uList::TYPE_KURS_WORK))) && ($rating == 'Ra')) {
            return array(array(
                'error' => 'Рейтинг за аттестацию должен находиться в пределе от 0 до 40',
                'rmin' => 0,
                'rmax' => 40
            )
            );
        }

    }


}