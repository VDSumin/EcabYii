<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $login
 * @property string $passw
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $createdAt
 * @property string $lastVisitAt
 * @property integer $status
 * @property string $session
 *
 * @property Link[] $links Links to another systems
 */
class User extends CActiveRecord {

    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{user}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('login, passw, email, firstName, lastName, status, session', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('login', 'length', 'max' => 16),
            array('passw', 'length', 'max' => 64),
            array('firstName, lastName', 'length', 'max' => 25),
            array('session', 'length', 'max' => 32),
            array('createdAt, lastVisitAt', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, login, passw, email, firstName, lastName, createdAt, lastVisitAt, status, session', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'links' => [self::HAS_MANY, 'Link', 'user_id'],
        );
    }

    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->passw = CPasswordHelper::hashPassword($this->passw);
        }
        if (empty($this->createdAt) || ('0000-00-00 00:00:00' == $this->createdAt)) {
            $this->createdAt = date('Y-m-d H:i:s');
        }
        if (empty($this->lastVisitAt) || ('0000-00-00 00:00:00' == $this->lastVisitAt)) {
            $this->lastVisitAt = date('Y-m-d H:i:s');
        }
        if (empty($this->session) || ('' == $this->session)) {
            $this->session = '';
        }
        return parent::beforeSave();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'login' => 'Login',
            'passw' => 'Passw',
            'email' => 'Email',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'createdAt' => 'Created At',
            'lastVisitAt' => 'Last Visit At',
            'status' => 'Status',
            'session' => 'Session',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('login', $this->login, true);
        $criteria->compare('passw', $this->passw, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('firstName', $this->firstName, true);
        $criteria->compare('lastName', $this->lastName, true);
        $criteria->compare('createdAt', $this->createdAt, true);
        $criteria->compare('lastVisitAt', $this->lastVisitAt, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('session', $this->session, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function findByLink($type, $value) {
        return self::model()->with(['links' => [
            'condition' => 'links.type=:type AND links.value=:value',
            'params' => [':type' => $type, ':value' => $value]
        ]])->find();
    }

    /**
     * Creates new user by Fdata record
     * @param Fdata $user
     * @return User|null Returns model if no errors or null
     */
    public static function copyFromPriem($user, $type) {
        $model = new User;
        $model->login = "priemUser{$user->npp}";
        $model->passw = uniqid();
        $model->email = isset($user->email) ? $user->email : null;
        $model->firstName = $user->getFirstName();
        $model->lastName = $user->getLastName();
        $model->status = User::STATUS_ACTIVE;
        if ($model->save(false)) {
            $link = new Link;
            $link->user_id = $model->id;
            $link->type = Link::TYPE_NPP;
            $link->value = $user->npp;
            $link->save(false);
            $link = new Link;
            $link->user_id = $model->id;
            $link->type = Link::TYPE_PERSON_STATUS;
            $link->value = (int)$type;
            $link->save(false);
            $auth = Yii::app()->authManager;
            /* @var $auth CDbAuthManager */
            $auth->assign(WebUser::ROLE_STUDENT, $model->id);
            return $model;
        }
        return null;
    }

    /**
     * Updates user model
     * @param User $model
     * @param Fdata $user
     * @param string $personNrec
     * @return User|null Returns model if no errors or null
     */
    public static function updateFromPriem($model, $user, $type) {
        $model->email = isset($user->email) ? $user->email : null;
        $model->firstName = $user->getFirstName();
        $model->lastName = $user->getLastName();
        if ($model->save(false)) {
            $link = Link::model()->findByAttributes([
                'user_id' => $model->id,
                'type' => Link::TYPE_NPP,
                'value' => $user->npp
            ]);
            if (!($link instanceof Link)) {
                $link = new Link;
                $link->user_id = $model->id;
                $link->type = Link::TYPE_NPP;
                $link->value = $user->npp;
                $link->save(false);
            }
            $link = Link::model()->findByAttributes([
                'user_id' => $model->id,
                'type' => Link::TYPE_PERSON_STATUS,
            ]);
            if (!($link instanceof Link)) {
                $link = new Link;
                $link->user_id = $model->id;
                $link->type = Link::TYPE_PERSON_STATUS;
                $link->value = (int)$type;
                $link->save(false);
            } else {
                $link->value = (int)$type;
                //var_dump($link);die;
                $link->save(false);
            }
            return $model;
        }
        return null;
    }

    public function getRoles() {
        $auth = Yii::app()->authManager;
        /* @var $auth CDbAuthManager */
        return $auth->getAuthAssignments($this->id);
    }

}
