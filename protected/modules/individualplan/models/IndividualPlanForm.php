<?php

/**
 * IndividualPlanForm for individual plan module
 */
class IndividualPlanForm extends CFormModel {

    public $fnpp;


    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {
        return array(
            array('fnpp', 'on' => 'dbload'),
            array('fnpp', 'safe'),
         //   array('theme', 'filter', 'filter' => array($this, 'sanitizeThemes')),
         //   array('theme', 'length', 'max' => 250 , 'tooLong' => 'Тема слишком длинная, пожалуйста, переформулируйте (макс. 250 символов)'),
         //   array('post', 'length', 'max' => 60 , 'tooLong' => 'Должность слишком длинная, пожалуйста, будьте проще (макс. 60 символов)'),
         //   array('dateProtect', 'date', 'message' => 'Должна быть указана дата защиты', 'format' => 'dd.MM.yyyy', 'allowEmpty' => false),
         //   array('protocol', 'required', 'message' => 'Должен быть указан номер протокола'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'Fnpp' => 'Идентификатор преподавателя',
        );
    }

    public function __construct($scenario = '')
    {
        $this->fnpp =  $this->getUserFnpp();
        parent::__construct($scenario);
    }


    /**
     * This method get userModel
     *
     * @return mixed
     */
    public function getUserModel(){
        return Yii::app()->user->getModel();
    }

    public function getUserFnpp(){
        $model = $this->getUserModel();
        $npp = null;
        if ($model instanceof User) {
            foreach ($model->links as $link) {
                if (Link::TYPE_NPP == $link->type) {
                    $npp = $link->value;
                    break;
                }
            }
        }

        Yii::app()->session['fnpp'] = $npp;

        return $npp;
    }

    public function getAppointmentsForMenu(){
        $menu = [];
        if ($this->fnpp){
            $sql = Yii::app()->db2->createCommand()
                ->selectDistinct('structd_rp.npp, structd_rp.name')
                ->from(Wkardc_rp::model()->tableName() . ' t')
                ->leftJoin(StructD_rp::model()->tableName() . ' structd_rp', 'structd_rp.npp = t.struct')
                ->where('t.fnpp = :fnpp 
                AND (t.dolgnost IN (\'ассистент\',\'доцент\',\'профессор\',\'заведующий кафедрой\') OR t.dolgnost LIKE \'%преподават%\')
                AND (t.prudal=0 OR t.du > NOW()) ',
                    [':fnpp' => $this->fnpp])
                ->queryAll();

            if ($sql){
                foreach ($sql as $one){
                    $menu[] = [
                        'label' => $one['name'],
                        'url' => ['struct', 'chair'=>$one['npp']],
                    ];
                }
            }

        }

        if ($menu){
            return $menu;
        } else {
            $menu[] =
                [
                    'label' => 'Информации о работе на кафедре не найдено!',
                    'url' => ['#'],
                ];
            return $menu;
        }

    }

    public function getYearFromLoad(){
        $criteria = new CDbCriteria();
        $criteria->compare('fnpp', Yii::app()->session['fnpp']);
        $criteria->distinct = true;
        $criteria->select = 'yearOfLoad';
        $models = WorkloadPlanActual::model()->findAll($criteria);
        $yearList = [];
        if ($models){
            foreach ($models as $model){
                $yearList[$model->yearOfLoad] = $model->yearOfLoad;
            }
        }
        return $yearList;
    }

    public static function getInfoForChart(){
        $dis = AttendanceSchedule::getDisActualByPersonAndYear();
        $kindOfWorks = AttendanceKindofwork::model()->findAll();
        if ($dis){
            $result = [];

            foreach ($dis as $oneDis){
                foreach ($kindOfWorks as $kindOfWork){
                    $criteria = new CDbCriteria();
                    $criteria->select = 'dateTimeStartOfClasses';
                    $criteria->distinct = true;
                    $criteria->compare('teacherFnpp', Yii::app()->session['fnpp']);
                    $criteria->compare('discipline', $oneDis->discipline);
                    $criteria->compare('kindOfWorkId', $kindOfWork->id);
                    $criteria->compare('yearOfEducation', Yii::app()->session['yearEdu']);


                    $model=AttendanceSchedule::model()->findAll($criteria);

                    $result[$oneDis->discipline]['kindOfWork'][$kindOfWork->name] = count($model) * 2;
                }

            }

            return $result;

        }

        return null;
    }


}
