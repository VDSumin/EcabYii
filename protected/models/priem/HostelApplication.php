<?php
/**
 * This is the model class for table "hostel_application".
 *
 * The followings are the available columns in table 'hostel_application':
 * @property integer $id
 * @property integer $fnpp
 * @property datetime $dateCreate
 * @property datetime $dateConfirm
 * @property datetime $dateBegin
 * @property datetime $dateEnd
 * @property string $phone
 * @property string $phone2
 * @property string $speciality
 * @property boolean $lgot
 * @property string $edu_faculty
 * @property string $filename
 * @property string $sector
 * @property integer $status
 * @property string $comment
 * @property string $real_faculty
 * @property string $fio_pr
 **/
class HostelApplication extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'hostel_application';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('fnpp', 'required'),
            array('fnpp', 'numerical', 'integerOnly' => true),
            array('filename', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, fnpp, filename', 'safe', 'on' => 'search'),
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
            'fdata' => array(self::BELONGS_TO, 'Fdata', 'fnpp'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Óíèêàëüíûé êëş÷',
            'fnpp' => 'ñâÿçü ñ òàáëèöåé fdata',
            'filename' => 'Õıø èìåíè ôàéëà',
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
        $criteria = new CDbCriteria;
        $criteria->with = array('fdata' => array(
            'condition' => 'CONCAT_WS(" ", TRIM(fam), TRIM(nam), TRIM(otc)) like :fam',
            'params' => array(':fam' => '%' . $this->fio . '%')
        ),);
        $criteria->compare('id', $this->id);
        $criteria->compare('fnpp', $this->fnpp);
        $criteria->compare('filename', $this->filename, true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
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
     * @return HostelContract the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function recently($limit = 1)
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => 'id DESC',
            'limit' => $limit,
        ));
        return $this;
    }

}