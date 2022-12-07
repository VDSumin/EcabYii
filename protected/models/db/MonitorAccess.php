<?php

/**
 * This is the model class for table "{{monitor_access}}".
 *
 * The followings are the available columns in table '{{monitor_access}}':
 * @property integer $id
 * @property integer $fnpp
 * @property integer $struct
 * @property integer $createdBy
 * @property string $createDate
 */
class MonitorAccess extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MonitorAccess the static model class
     */
    public static function model($className = __CLASS__)
    {
        self::checkTables();
        return parent::model($className);
    }

    public static function checkTables()
    {
        if (!(Yii::app()->db->createCommand('SHOW TABLES LIKE \'tbl_monitor_access\'')->queryAll())) {
            Yii::app()->db->createCommand('CREATE TABLE tbl_monitor_access (
                                              id int(11) NOT NULL AUTO_INCREMENT,
                                              fnpp int(11) DEFAULT NULL,
                                              struct int(11) DEFAULT NULL,
                                              createdBy int(11) DEFAULT NULL,
                                              createDate datetime DEFAULT NULL,
                                              PRIMARY KEY (id)
                                            )
                                            ENGINE = INNODB,
                                            CHARACTER SET utf8,
                                            COLLATE utf8_general_ci;')->query();
        }
    }
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{monitor_access}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fnpp, struct, createdBy', 'numerical', 'integerOnly'=>true),
			array('createDate', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fnpp, struct, createdBy, createDate', 'safe', 'on'=>'search'),
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
			'fnpp' => 'Сотрудник (id)',
			'struct' => 'Подразделение (struct)',
			'createdBy' => 'Кто дал права',
			'createDate' => 'Дата создания/изменения',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('fnpp',$this->fnpp);
		$criteria->compare('struct',$this->struct);
		$criteria->compare('createdBy',$this->createdBy);
		$criteria->compare('createDate',$this->createDate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public static function getStructuresByFnpp($fnpp)
    {
        $data = Yii::app()->db->createCommand()
            ->selectDistinct('tma.struct npp')
            ->from('tbl_monitor_access tma')
            ->where('tma.fnpp = '.$fnpp)
            ->queryAll();
        $return = [];
        if($data){
            foreach ($data as $item) {
                $return[] = $item['npp'];
            }
        }else{
            return [];
        }
        return $return;
    }

    public static function getDepartments($fnpp)
    {
        if (sizeof(MonitorAccess::getStructuresByFnpp($fnpp)) > 0) {
            return (Yii::app()->db2->createCommand()
                ->selectDistinct('w.struct npp, struct_getpath2_rp(w.struct) department')
                ->from('wkardc_rp w')
                ->where('w.prudal=\'0\' AND
                (w.vpo1cat LIKE \'рп\'  OR
                (w.vpo1cat LIKE \'итп\' AND w.dolgnost LIKE \'%начальник%\') OR                 
                (w.vpo1cat LIKE \'офицеры\' AND w.dolgnost LIKE \'%начальник%\') OR             
                (w.vpo1cat LIKE \'НР\' AND (w.dolgnost LIKE \'%начальник%\' OR w.dolgnost LIKE \'старший научный сотрудник\' OR w.dolgnost LIKE \'главный научный сотрудник\')) OR
                w.dolgnost LIKE \'%заведующий%\' OR 
                w.dolgnost LIKE \'%декан%\'  OR 
                w.dolgnost LIKE \'%директор%\'  OR 
                w.dolgnost LIKE \'%начальник%\') AND 
                fnpp = ' . $fnpp . ' OR
                w.struct IN (' . implode(',', MonitorAccess::getStructuresByFnpp($fnpp)) . ')')
                ->queryAll());
        }  else {
            return (Yii::app()->db2->createCommand()
                ->selectDistinct('w.struct npp, struct_getpath2_rp(w.struct) department')
                ->from('wkardc_rp w')
                ->where('w.prudal=\'0\' AND
                (w.vpo1cat LIKE \'рп\'  OR
                (w.vpo1cat LIKE \'итп\' AND w.dolgnost LIKE \'%начальник%\') OR                 
                (w.vpo1cat LIKE \'офицеры\' AND w.dolgnost LIKE \'%начальник%\') OR             
                (w.vpo1cat LIKE \'НР\' AND (w.dolgnost LIKE \'%начальник%\' OR w.dolgnost LIKE \'старший научный сотрудник\' OR w.dolgnost LIKE \'главный научный сотрудник\')) OR
                w.dolgnost LIKE \'%заведующий%\' OR 
                w.dolgnost LIKE \'%декан%\'  OR 
                w.dolgnost LIKE \'%директор%\'  OR 
                w.dolgnost LIKE \'%начальник%\') AND 
                fnpp = ' . $fnpp)
                ->queryAll());
        }
    }

}
