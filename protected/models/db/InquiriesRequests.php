<?php

class InquiriesRequests extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RemoteTaskList the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{inquiries_requests}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id, groupNpp, studentNpp, typeId, takePickUp', 'numerical', 'integerOnly' => true),
            array('id, groupNpp, studentNpp, typeId, startDate, finishDate, filePath, modifiedAt, createdAt, takePickUp', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'groupNpp' => 'Группа',
            'studentNpp' => 'Студент',
            'typeId' => 'Тип заявки',
            'filePath' => 'Статус',
            'startDate' => 'С',
            'finishDate' => 'По',
            'createdAt' => 'Дата подачи заявки',
            'takePickUp' => 'Способ получения'
        );
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db;
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

        $criteria->compare('id', $this->id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function searchResponsible()
    {
        $criteria = new CDbCriteria;

        $criteria->addCondition('FALSE');
        foreach (InquiriesResponsibles::getResponsibleTypes() as $type) {
            if (!in_array($type, array(
                InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::HOSTEL))->id,
            ))) {
                $criteria->addCondition('typeId = ' . $type, 'OR');
            }
        }
        if ($faculties = InquiriesResponsibles::getDeanFaculties()) {
            $criteria->addCondition('typeId IN (' .
                InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::PFR))->id .
                ', ' .
                InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::PLACE_OF_STUDY))->id .
                ') AND facultyNrec IN (' . implode(', ', $faculties) . ')', 'OR');
        }
        $criteria->addCondition('filePath = ""');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function searchResponsibleHostel()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->addCondition('filePath = ""');
        $criteria->addCondition('typeId = ' . InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::HOSTEL))->id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getUploadResponsibleHostel($id)
    {
        return '<center>' .
            CHtml::link(CHtml::tag("span", array(
                "class" => "glyphicon glyphicon-ok",
                "rel" => "tooltip",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                "title" => "Подтвердить готовность",
                "style" => "color: green;"
            )), array('accept', 'id' => $id)) .
            ' ' .
            CHtml::link(CHtml::tag("span", array(
                "class" => "glyphicon glyphicon-remove",
                "rel" => "tooltip",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                "title" => "Отклонить заявку",
                "style" => "color: red;"
            )), array('decline', 'id' => $id)) .
            ' ' .
            CHtml::link(CHtml::tag("span", array(
                "class" => "glyphicon glyphicon-trash",
                "rel" => "tooltip",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                "title" => "Удалить заявку",
                "style" => "color: black;"
            )), array('delete', 'id' => $id)) .
            '</center>';
    }

    public function searchResponsibleArchive()
    {
        $criteria = new CDbCriteria;

        $criteria->addCondition('FALSE');
        foreach (InquiriesResponsibles::getResponsibleTypes() as $type) {
            $criteria->addCondition('typeId = ' . $type, 'OR');
        }
        if ($faculties = InquiriesResponsibles::getDeanFaculties()) {
            $criteria->addCondition('typeId IN (' .
                InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::PFR))->id .
                ', ' .
                InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::PLACE_OF_STUDY))->id .
                ') AND facultyNrec IN (' . implode(', ', $faculties) . ')', 'OR');
        }
        $criteria->addCondition('filePath != ""');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function searchStudent()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('studentNpp', Yii::app()->user->getFnpp());

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array('defaultOrder' => array('createdAt' => SORT_DESC)),
        ));
    }

    public static function getGroups()
    {
        return ($result = Yii::app()->db2->createCommand()
            ->selectDistinct('s.npp, s.gruppa')
            ->from('skard s')
            ->where('s.fnpp = ' . Yii::app()->user->getFnpp())
            ->andWhere('s.prudal = 0')
            ->andWhere('s.gruppa NOT LIKE \'ABIT\'')
            ->andWhere('s.gruppa NOT LIKE \'%+%\'')
            ->group('s.gruppa')
            ->queryAll()) ? $result : array(array('npp' => -1));
    }

    public static function getUploadResponsible($id)
    {
        return '<center>' .
            CHtml::link(CHtml::tag("span", array(
                "class" => "glyphicon glyphicon-cloud-upload",
                "rel" => "tooltip",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                "title" => "Загрузить файл",
            )), null, array("class" => "add_file_input", "value" => $id)) .
            ' ' .
            CHtml::link(CHtml::tag("span", array(
                "class" => "glyphicon glyphicon-ok",
                "rel" => "tooltip",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                "title" => "Подтвердить готовность",
                "style" => "color: green;"
            )), array('accept', 'id' => $id)) .
            ' ' .
            CHtml::tag("a", array(
                "class" => "glyphicon glyphicon-remove",
                "rel" => "tooltip",
                "data-toggle" => "tooltip",
                "data-placement" => "top",
                "title" => "Отклонить заявку",
                "style" => "color: red; text-decoration:none",
                "value" => $id
            )) .
            '</center>';
    }

    public static function getUploadAdmin($path, $id)
    {
        if ($path == '') {
            return '<center>' .
                CHtml::link(CHtml::tag("span", array(
                    "class" => "glyphicon glyphicon-cloud-upload",
                    "rel" => "tooltip",
                    "data-toggle" => "tooltip",
                    "data-placement" => "top",
                    "title" => "Загрузить файл",
                )), null, array("class" => "add_file_input", "value" => $id)) .
                ' ' .
                CHtml::link(CHtml::tag("span", array(
                    "class" => "glyphicon glyphicon-ok",
                    "rel" => "tooltip",
                    "data-toggle" => "tooltip",
                    "data-placement" => "top",
                    "title" => "Подтвердить готовность",
                    "style" => "color: green;"
                )), array('accept', 'id' => $id)) .
                ' ' .
                CHtml::tag("a", array(
                    "class" => "glyphicon glyphicon-remove",
                    "rel" => "tooltip",
                    "data-toggle" => "tooltip",
                    "data-placement" => "top",
                    "title" => "Отклонить заявку",
                    "style" => "color: red; text-decoration:none",
                    "value" => $id
                )) .
                ' ' .
                CHtml::link(CHtml::tag("span", array(
                    "class" => "glyphicon glyphicon-trash",
                    "rel" => "tooltip",
                    "data-toggle" => "tooltip",
                    "data-placement" => "top",
                    "title" => "Удалить заявку",
                    "style" => "color: black;"
                )), array('delete', 'id' => $id)) .
                '</center>';
        } elseif (strpos($path, '!declined') === 0) {
            $comment = substr($path, 9, strlen($path) - 9);
            if (strlen($comment) > 0) {
                $comment = '<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Комментарий: ' . $comment . '\' class=\'glyphicon glyphicon-comment\' style="color: black"></span> ';
            }
            return '<center><span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Заявка отклонена\' class=\'glyphicon glyphicon-alert\' style="color: red"></span> '
                . $comment .
                CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Удалить заявку\' class=\'glyphicon glyphicon-trash\' style="color: black"></span>',
                    array('delete', 'id' => $id)) . '</center>';
        } elseif ($path == '!accepted') {
            return '<center><span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Заявка принята\' class=\'glyphicon glyphicon-ok\' style="color: green"></span> ' . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Удалить заявку\' class=\'glyphicon glyphicon-trash\' style="color: black"></span>',
                    array('delete', 'id' => $id)) . '</center>';
        } else {
            return '<center>' . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Скачать\' class=\'glyphicon glyphicon-download-alt\' style="color: green"></span>',
                    array('downloadFile', 'id' => $id), array('target' => '_blank')) . ' ' . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Удалить заявку\' class=\'glyphicon glyphicon-trash\' style="color: black"></span>',
                    array('delete', 'id' => $id)) . '</center>';
        }
    }

    public static function getUploadStudent($path, $id)
    {
        if ($path == '') {
            return '<center><span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Заявка в процессе рассмотрения\' class=\'glyphicon glyphicon-hourglass\' style="color: red"></span> ' . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Удалить заявку\' class=\'glyphicon glyphicon-trash\' style="color: black"></span>',
                    array('delete', 'id' => $id)) . '</center>';
        } elseif (strpos($path, '!declined') === 0) {
            $comment = substr($path, 9, strlen($path) - 9);
            if (strlen($comment) > 0) {
                $comment = ' <span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Комментарий: ' . $comment . '\' class=\'glyphicon glyphicon-comment\' style="color: black"></span>';
            }
            return '<center><span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Заявка отклонена\' class=\'glyphicon glyphicon-alert\' style="color: red"></span>' . $comment . '</center>';
        } elseif ($path == '!accepted') {
            return '<center><span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Заявка принята\' class=\'glyphicon glyphicon-ok\' style="color: green"></span></center>';
        } else {
            return '<center>' . CHtml::link('<span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'Скачать\' class=\'glyphicon glyphicon-download-alt\' style="color: green"></span>',
                    array('downloadFile', 'id' => $id), array('target' => '_blank')) . '</center>';
        }
    }

    public static function getMonthes($type = 0)
    {
        if ($type == 0) {
            return array(
                1 => 'Январь',
                2 => 'Февраль',
                3 => 'Март',
                4 => 'Апрель',
                5 => 'Май',
                6 => 'Июнь',
                7 => 'Июль',
                8 => 'Август',
                9 => 'Сентябрь',
                10 => 'Октябрь',
                11 => 'Ноябрь',
                12 => 'Декабрь',
            );
        } elseif ($type == 1) {
            return array(
                1 => 'Января',
                2 => 'Февраля',
                3 => 'Марта',
                4 => 'Апреля',
                5 => 'Мая',
                6 => 'Июня',
                7 => 'Июля',
                8 => 'Августа',
                9 => 'Сентября',
                10 => 'Октября',
                11 => 'Ноября',
                12 => 'Декабря',
            );
        }
    }

    public static function getDate($str, $type, $typeId)
    {
        if (in_array(InquiriesTypes::getTypeString($typeId), array(InquiriesTypes::PLACE_OF_STUDY, InquiriesTypes::PFR))) {
            return '-';
        } elseif (in_array(InquiriesTypes::getTypeString($typeId), array(InquiriesTypes::HOSTEL))) {
            return date('d.m.Y', strtotime($str));
        } else {
            return self::getMonthes($type)[(int)date('m', strtotime($str))] . ' ' . date('Y', strtotime($str));
        }
    }

    public static function getHostelContract($data)
    {
        return HostelContract::model()->findByPk(explode('_', $data)[0])->contNumber;
    }

    public static function getHostel($data)
    {
        return HostelHousing::model()->findByPk(HostelContract::model()->findByPk(explode('_', $data)[0])->housingId)->hostel;
    }

    public static function getHostelReason($data)
    {
        return InquiriesTypes::getHostelReasonString((int)explode('_', $data)[1]);
    }

    public static function getHostelNumberAndDate($data)
    {
        $contract = HostelContract::model()->findByPk(explode('_', $data)[0]);
        return $contract->contNumber . ' ' . date('d.m.Y', strtotime($contract->contDate));
    }

    public static function getHostelAddress($data)
    {
        $hostel = HostelHousing::model()->findByPk(HostelContract::model()->findByPk(explode('_', $data)[0])->housingId);
        $str = $hostel->hostel == 9 ? '3' : $hostel->hostel;
        $addr = Yii::app()->db2->createCommand()
            ->selectDistinct('value')
            ->from('hostel_settings')
            ->where('name = \'h' . $hostel->hostel . 'FullAdd\'')
            ->queryScalar();
        return 'Общежитие № ' . $str . ' ' . $hostel->block . ' блок, по адресу: ' . $addr . ' (комната ' . $hostel->flat . ')';
    }

    public static function getContractDates($request)
    {
        $contract = HostelContract::model()->findByPk(explode('_', $request->additional)[0]);
        return date('d.m.Y', strtotime($contract->contBegin)) . ' – ' .
            date('d.m.Y', strtotime($contract->contEnd)) . ' / ' .
            date('d.m.Y', strtotime($request->startDate)) . ' – ' .
            date('d.m.Y', strtotime($request->finishDate));
    }

    public static function getMinDate()
    {
        $date = date('d.m.Y');
        if (
        $tmp = self::model()->findByAttributes(array(
            'typeId' => InquiriesTypes::model()->findByAttributes(array('name' => InquiriesTypes::HOSTEL))->id,
        ), array(
            'order' => 'createdAt',
        ))
        ) $date = date('d.m.Y', strtotime($tmp->createdAt));
        return $date;
    }

    public static function getFacultyNrec($groupNpp)
    {
        $return = Yii::app()->db2->createCommand()
            ->selectDistinct('gs.cfaculty')
            ->from(Fdata::model()->tableName() . ' f')
            ->leftJoin(Skard::model()->tableName() . ' s', 's.fnpp = f.npp')
            ->join('gal_u_student gs', 'gs.nrec = s.gal_srec')
            ->where('s.npp = ' . $groupNpp . ' AND gs.cfaculty IS NOT NULL')
            ->queryScalar();
        if($return) {return $return;}

        $return = Yii::app()->db2->createCommand()
            ->selectDistinct('gs.cfaculty')
            ->from(Fdata::model()->tableName() . ' f')
            ->leftJoin(Skard::model()->tableName() . ' s', 's.fnpp = f.npp')
            ->join('gal_u_student gs', 'gs.nrec = s.gal_srec')
            ->where('f.npp = ' . Yii::app()->user->getFnpp() . ' AND gs.cfaculty IS NOT NULL')
            ->order('gs.warch, gs.appdate')
            ->queryScalar();
        return $return;
    }

    public static function getFacultyEmail($facultyNrec)
    {
        return (Yii::app()->db2->createCommand()
            ->selectDistinct('gm.address')
            ->from('gal_mailing gm')
            ->where('gm.gal_unid LIKE \'0x' . bin2hex($facultyNrec) . '\'')
            ->queryScalar());
    }
}