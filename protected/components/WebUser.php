<?php

class WebUser extends CWebUser {

    /* Types */
    const TYPE_LOCAL = 0;
    const TYPE_REMOTE = 1;

    /* Roles */
    const ROLE_ADMIN = 'admin';
    const ROLE_SECRET = 'secret';
    const ROLE_HR = 'hr'; //(Human resources department) отдел кадров
    const ROLE_AMP = 'AMP'; //административно-хозяйственная часть

    const ROLE_DEAN = 'dean';
    const ROLE_ACTING_DEAN = 'acting_dean';

    const ROLE_CHIEF = 'chief';
    const ROLE_ACTING_CHIEF = 'acting_chief';

    const ROLE_PPS = 'pps';
    const ROLE_USER = 'user';
    const ROLE_GUEST = 'guest';

    const ROLE_STUDENT = 'student';
    const ROLE_STEWARD = 'steward'; //Староста

    private $_model = null;

    /**
     * @inheritdoc
     */
    protected function beforeLogout() {
        if (self::TYPE_REMOTE == $this->getState('type')) {
            //TODO remove session, but not necessary
        }
        return parent::beforeLogout();
    }

    /**
     * {@inheritdoc}
     */
    protected function afterLogin($fromCookie) {
        parent::afterLogin($fromCookie);
        $user = $this->getModel();
        if ($user instanceof User) {
            $user->lastVisitAt = date('Y-m-d H:i:s');
            $user->save(false);
        }
    }

    /**
     * Check has user admin role o not
     * @return bool
     */
    public function isAdmin() {
        return $this->checkAccess(self::ROLE_ADMIN);
    }

    /**
	 * {@inheritdoc}
     * White list
	 * @return boolean whether the operations can be performed by this user.
	 */
    public function checkAccess($operation, $params = array(), $allowCaching = true) {
        if (is_array($operation)) {
            foreach($operation as &$one) {
                if (parent::checkAccess($one, $params, $allowCaching)) {
                    return true;
                }
            }
            return false;
        }
        return parent::checkAccess($operation, $params, $allowCaching);
    }

    /**
     * Returns array that contains roles and theirs descritpions
     * @return array
     */
    public static function roleLabels() {
        $auth = Yii::app()->authManager;
        /* @var $auth CDbAuthManager */
        $roles = $auth->getAuthItems(CAuthItem::TYPE_ROLE);

        $labels = array();
        foreach($roles as $role) {
            $labels[$role->name] = $role->description;
        }

        return $labels;
    }

    /**
     * Returns user model
     * @return User
     */
    //определение galunid для студента
    public function getModel() {
        if (null === $this->_model) {
            $this->_model = User::model()->findByPk($this->id);
        }
        return $this->_model;
    }

    public function getFnpp() {
        $model = $this->getModel();
        if ($model instanceof User) {
            $npp = null;
            foreach ($model->links as $link) {
                if (Link::TYPE_NPP == $link->type) {
                    $npp = $link->value;
                    break;
                }
            }
            if (null !== $npp) {
                return $npp;
            }else{
                return false;
            }
        }
    }

    public function getGalId() {
        $model = $this->getModel();
        if ($model instanceof User) {
            $npp = null;
            foreach ($model->links as $link) {
                if (Link::TYPE_NPP == $link->type) {
                    $npp = $link->value;
                    break;
                }
            }
            if (null !== $npp) {
                return Yii::app()->db2->createCommand()
                    ->select('COALESCE(gs.cpersons, k.gal_unid)')
                    ->from(Fdata::model()->tableName() . ' f')
                    ->leftJoin(Skard::model()->tableName() . ' s', 's.fnpp = f.npp')
                    ->join('gal_u_student gs', 'gs.nrec = s.gal_srec')
                    ->leftJoin(Keylinks::model()->tableName() . ' k', 'k.fnpp = f.npp')
                    ->where('f.npp = :npp AND COALESCE(gs.cpersons, k.gal_unid) IS NOT NULL', [':npp' => $npp])
                    ->order('gs.warch, gs.appdate')
                    ->queryScalar();
            }
        }
    }
    //определение для студента всех galunid's и вывод их в массиве
    public function getGalIdMass() {
        $model = $this->getModel();
        if ($model instanceof User) {
            $npp = null;
            foreach ($model->links as $link) {
                if (Link::TYPE_NPP == $link->type) {
                    $npp = $link->value;
                    break;
                }
            }
            if (null !== $npp) {
                return Yii::app()->db2->createCommand()
                    ->selectDistinct('COALESCE(gs.cpersons, k.gal_unid) as Galid')
                    ->from(Fdata::model()->tableName() . ' f')
                    ->leftJoin(Skard::model()->tableName() . ' s', 's.fnpp = f.npp')
                    ->join('gal_u_student gs', 'gs.nrec = s.gal_srec')
                    ->leftJoin(Keylinks::model()->tableName() . ' k', 'k.fnpp = f.npp')
                    ->where('f.npp = :npp AND COALESCE(gs.cpersons, k.gal_unid) IS NOT NULL AND gs.warch = 0', [':npp' => $npp])
                    ->order('gs.warch, gs.appdate')
                    ->queryAll();
            }
        }
    }
    //определение galunid для преподавателей
    public function getGalIdT() {
        $model = $this->getModel();
        if ($model instanceof User) {
            $npp = null;
            foreach ($model->links as $link) {
                if (Link::TYPE_NPP == $link->type) {
                    $npp = $link->value;
                    break;
                }
            }
            if (null !== $npp) {
                return Yii::app()->db2->createCommand()
                    ->select('gp.nrec')
                    ->from(Fdata::model()->tableName() . ' f')
                    ->leftJoin(Keylinks::model()->tableName() . ' k', 'k.fnpp = f.npp')
                    ->leftJoin('gal_persons gp', 'gp.nrec = k.gal_unid')
                    ->leftJoin('gal_u_student gs', 'gs.cpersons = gp.nrec and gs.warch = 1')
                    ->where('f.npp = :npp AND gp.nrec IS NOT NULL', [':npp' => $npp])
                    ->andWhere('gp.disdate = 0')
                    ->order('gp.appdate DESC')
                    ->queryScalar();
            }
        }
    }

    public function getPerStatus() {
        $model = $this->getModel();
        if ($model instanceof User) {
            $pps = null;
            foreach ($model->links as $link) {
                if (Link::TYPE_PERSON_STATUS == $link->type) {
                    $pps = $link->value;
                    break;
                }
            }
            if (RUserIdentity::TYPE_PPS == (int)$pps) {
                return true;
            }else{
                return false;
            }
        }

    }

    /**
     * compares two roles if role1 > role2 then returns 1, if equal 0, else -1
     * if roles can't be compared then returns false
     * @param mixed $role1
     * @param mixed $role2
     * @return int|bool
     */
    public static function cmpRole($role1, $role2) {
        if ($role1 === $role2) return 0;

        $roles = array(
            self::ROLE_ADMIN => array('r' => 9999, 'c' => '*'),
            self::ROLE_HR => array('r' => 4, 'c' => 'h'),
            self::ROLE_DEAN => array('r' => 4, 'c' => 'u'),
            self::ROLE_ACTING_DEAN => array('r' => 4, 'c' => 'u'),
            self::ROLE_CHIEF => array('r' => 3, 'c' => 'u'),
            self::ROLE_ACTING_CHIEF => array('r' => 3, 'c' => 'u'),
            self::ROLE_PPS => array('r' => 2, 'c' => 'u'),

            self::ROLE_STEWARD => array('r' => 3, 'c' => 's'),
            self::ROLE_STUDENT => array('r' => 2, 'c' => 's'),
            self::ROLE_SECRET => array('r' => 2, 'c' => 's'),

            self::ROLE_USER => array('r' => 1, 'c' => '*'),
            self::ROLE_GUEST => array('r' => 0, 'c' => '*'),
        );

        if (!isset($roles[$role1]) || !isset($roles[$role2])) return false;

        $c1 = $roles[$role1]['c'];
        $c2 = $roles[$role2]['c'];

        if (($c1 === $c2) || in_array('*', array($c1, $c2))) {
            $r1 = $roles[$role1]['r'];
            $r2 = $roles[$role2]['r'];
            if ($r1 > $r2) {
                return 1;
            } elseif ($r1 < $r2) {
                return -1;
            } else {
                return  0;
            }
        }

        return false;
    }

    /**
     * Returns default role
     * @return string
     */
    public static function getDefaultRole() {
        return WebUser::ROLE_USER;
    }

    //получаем студенческие карточки из галактики

    /**
     * @param $controller
     * @param int $status
     * @return array
     */
    public function getStudentCards($status = 0, $hard = false ) {
        if(isset(Yii::app()->session['student_cards'])){
            if(!$hard) {
                return Yii::app()->session['student_cards'];
            }
        }
        if(!isset(Yii::app()->session['ApiKey'])) {
            Yii::app()->session['ApiKey'] = 'c80d479b-4468-4065-a0a5-75e254b22a64';//mobile
        }
        $fnpp = $this->getFnpp();
        $data = ApiKeyService::queryApi('getAllStudents', array("id" => $fnpp), Yii::app()->session['ApiKey'], 'GET');
//        var_dump($data);die;
        if($data['code'] == 0){
            return [];
        }
        if($status == 0){
            $return = [];
            foreach ($data['json_data']['students'] as $row){
                if(in_array($row['status'], ['Обучающиеся', 'Академический отпуск'])) {
                    $return[] = $row;
                }
            }
        }else {
            $return = $data['json_data']['students'];
        }
        Yii::app()->session['student_cards'] = $return;

        return Yii::app()->session['student_cards'];
    }

}
