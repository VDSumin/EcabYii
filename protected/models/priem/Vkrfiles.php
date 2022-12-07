<?php

/**
 * This is the model class for table "ecab.vkrfiles".
 *
 * The followings are the available columns in table 'ecab.vkrfiles':
 * @property integer $id
 * @property string $type
 * @property string $name
 * @property string $mime
 * @property integer $fnpp
 * @property integer $size
 * @property string $text
 * @property string $vkrnrec
 * @property integer $unixdate
 * @property string $disc
 * @property string $comment
 * @property integer $semester
 * @property integer $state
 * @property integer $whostate
 * @property integer $whocomment
 * @property string $dir
 */
class Vkrfiles extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ecab.vkrfiles';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fnpp, size, unixdate, semester, state, whostate, whocomment', 'numerical', 'integerOnly'=>true),
			array('type, name, mime, text, disc, comment, dir', 'length', 'max'=>255),
			array('vkrnrec', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, name, mime, fnpp, size, text, vkrnrec, unixdate, disc, comment, semester, state, whostate, whocomment, dir', 'safe', 'on'=>'search'),
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
			'type' => 'Type',
			'name' => 'Name',
			'mime' => 'Mime',
			'fnpp' => 'Fnpp',
			'size' => 'Size',
			'text' => 'Text',
			'vkrnrec' => 'Vkrnrec',
			'unixdate' => 'Unixdate',
			'disc' => 'Disc',
			'comment' => 'Comment',
			'semester' => 'Semester',
			'state' => 'State',
			'whostate' => 'Whostate',
			'whocomment' => 'Whocomment',
			'dir' => 'Dir',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('mime',$this->mime,true);
		$criteria->compare('fnpp',$this->fnpp);
		$criteria->compare('size',$this->size);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('vkrnrec',$this->vkrnrec,true);
		$criteria->compare('unixdate',$this->unixdate);
		$criteria->compare('disc',$this->disc,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('semester',$this->semester);
		$criteria->compare('state',$this->state);
		$criteria->compare('whostate',$this->whostate);
		$criteria->compare('whocomment',$this->whocomment);
		$criteria->compare('dir',$this->dir,true);

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
	 * @return Vkrfiles the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
