<?php

/**
 * Remote user autchentification
 */
class RUserIdentity extends CUserIdentity {

    const ERROR_NEED_LOGOUT = 1000;
    const ERROR_TRANSFER = 1001;
    const TYPE_PPS = 1;
    const TYPE_STUDENT = 2;
    const TYPE_ZAKUP_oborud = 11;
    const TYPE_ZAKUP_expendable = 12;
    const TYPE_ZAKUP_software = 13;

    const authTime = 3; //three minutes

    protected $_code;
    protected $_type;
    protected $_id;

    public function __construct($code, $type) {
        parent::__construct(null, null);
        $this->_code = $code;
        $this->_type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate() {
        //Получаем даннные по пользователю с priem.omgtu
        //var_dump((int)$this->getType(), self::TYPE_STUDENT);die;
        if((int)$this->getType() == self::TYPE_PPS) {
            $data = Fdata::model()->with(array('keylinks', 'wkardcs', 'authcode'))->find([
                'condition' => '`authcode`.`code`=:code AND TIMESTAMPDIFF(minute, `authcode`.`ts`, CURRENT_TIMESTAMP()) < :time',
                'params' => array(
                    ':code' => $this->_code,
                    ':time' => (defined('YII_DEBUG') && YII_DEBUG) ? 10000000 : self::authTime
                ),
            ]);
        }else{
            $data = Fdata::model()->with(array('keylinks', 'skards', 'authcode'))->find([
                'condition'=>'`authcode`.`code`=:code AND TIMESTAMPDIFF(minute, `authcode`.`ts`, CURRENT_TIMESTAMP()) < :time',
                'params' => array(
                    ':code' => $this->_code,
                    ':time' => (defined('YII_DEBUG') && YII_DEBUG) ? 10000000 : self::authTime
                ),
            ]);
        }

        if ($data instanceof Fdata) {
            $model = User::model()->findByLink(Link::TYPE_NPP, $data->npp);
            $user = Yii::app()->user->getModel();                                   //Смотрим под кем залогинен

            if (!Yii::app()->user->isGuest &&
                (Yii::app()->user->checkAccess(WebUser::ROLE_ADMIN) ||              //Если под админом
                !($user instanceof User)))                                          //Или кем-то левым
            {
                Yii::app()->user->logout();                                         //То надо выйти и перелогиниться
                $this->errorCode = self::ERROR_NEED_LOGOUT;
                return !$this->errorCode;
            }
//            var_dump($user, $model, $data);die;
            if (Yii::app()->user->isGuest) {                                        //Самый простой сценарий, если на данный момент он не зашёл на портал
                if (!($model instanceof User)) {                                    //И такого пользователя нет
                    $model = User::copyFromPriem($data, $this->getType());          //Создаём нового
                } else {                                                            //А если есть
                    $model = User::updateFromPriem($model, $data, $this->getType());          //То просто обновляем данные
                }
            } else {                                                                //Если же пользователь уже залогинен
//                if (($model instanceof User) && empty($user->npp)) {                //И у него пустой npp
//                    $model = User::updateFromPriem($user, $data, $this->getType()); //То просто обновляем данные
//                }
                Yii::app()->user->logout();
                $this->errorCode = self::ERROR_NEED_LOGOUT;
                return !$this->errorCode;
            }

            if ($model instanceof User) {
                $this->_id = $model->id;
                $this->username = $model->firstName . ' ' . $model->lastName;
                $this->errorCode = self::ERROR_NONE;
            } else {
                $this->errorCode = self::ERROR_TRANSFER;
            }
        } else {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        return !$this->errorCode;
    }

//    public function authenticateWorker() {
//        //Получаем даннные по пользователю с priem.omgtu
//        $data = Fdata::model()->with(array('keylinks', 'wkards', 'authcode'))->find([
//            'condition'=>'`authcode`.`code`=:code AND TIMESTAMPDIFF(minute, `authcode`.`ts`, CURRENT_TIMESTAMP()) < :time',
//            'params' => array(
//                ':code' => $this->_code,
//                ':time' => (defined('YII_DEBUG') && YII_DEBUG) ? 10000000 : self::authTime
//            ),
//        ]);
//
//        if ($data instanceof Fdata) {
//            $model = User::model()->findByLink(Link::TYPE_NPP, $data->npp);
//            $user = Yii::app()->user->getModel();                           //Смотрим под кем залогинен
//
//            if (!Yii::app()->user->isGuest &&
//                (Yii::app()->user->checkAccess(WebUser::ROLE_ADMIN) ||      //Если под админом
//                    !($user instanceof User)))                                  //Или кем-то левым
//            {
//                Yii::app()->user->logout();                                 //То надо выйти и перелогиниться
//                $this->errorCode = self::ERROR_NEED_LOGOUT;
//                return !$this->errorCode;
//            }
//
//            if (Yii::app()->user->isGuest) {                                //Самый простой сценарий, если на данный момент он не зашёл на портал
//                if (!($model instanceof User)) {                            //И такого пользователя нет
//                    $model = User::copyFromPriem($data, $this->getType());                    //Создаём нового
//                } else {                                                    //А если есть
//                    $model = User::updateFromPriem($model, $data, $this->getType());          //То просто обновляем данные
//                }
//            } else {                                                        //Если же пользователь уже залогинен
//                if (($model instanceof User) && empty($user->npp)) {        //И у него пустой npp
//                    $model = User::updateFromPriem($user, $data, $this->getType());           //То просто обновляем данные
//                }
//                Yii::app()->user->logout();
//                $this->errorCode = self::ERROR_NEED_LOGOUT;
//                return !$this->errorCode;
//            }
//
//            if ($model instanceof User) {
//                $this->_id = $model->id;
//                $this->username = $model->firstName . ' ' . $model->lastName;
//                $this->errorCode = self::ERROR_NONE;
//            } else {
//                $this->errorCode = self::ERROR_TRANSFER;
//            }
//        } else {
//            $this->errorCode = self::ERROR_USERNAME_INVALID;
//        }
//        return !$this->errorCode;
//    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->_id;
    }

    public function getType() {
        return $this->_type;
    }

}
