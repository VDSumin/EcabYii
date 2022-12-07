<?php

/**
 * This is the model class for table "gal_student_personalcard".
 *
 * The followings are the available columns in table 'gal_student_personalcard':
 * @property integer $id
 * @property string $pnrec
 * @property integer $placeOfStudyIsRight
 * @property integer $specIsRight
 * @property integer $finIsRight
 * @property integer $contNmbIsRight
 * @property integer $contBeginIsRight
 * @property integer $entNameIsRight
 * @property integer $sexIsRight
 * @property string $sexManual
 * @property integer $borndateIsRight
 * @property string $borndateManual
 * @property integer $grIsRight
 * @property string $grManual
 * @property integer $passVidIsRight
 * @property string $passVidManual
 * @property integer $pserIsRight
 * @property string $pserManual
 * @property integer $pnmbIsRight
 * @property string $pnmbManual
 * @property integer $givenbyIsRight
 * @property string $givenbyManual
 * @property integer $givendateIsRight
 * @property string $givendateManual
 * @property integer $todateIsRight
 * @property string $todateManual
 * @property integer $givenpodrIsRight
 * @property string $givenpodrManual
 * @property integer $eduLevelIsRight
 * @property string $eduLevelManual
 * @property integer $eduDocIsRight
 * @property string $eduDocManual
 * @property integer $eduSeriaIsRight
 * @property string $eduSeriaManual
 * @property integer $eduNmbIsRight
 * @property string $eduNmbManual
 * @property integer $eduDipDateIsRight
 * @property string $eduDipDateManual
 * @property integer $eduPlaceIsRight
 * @property string $eduPlaceManual
 * @property integer $eduAddrIsRight
 * @property string $eduAddrManual
 * @property integer $bornAddrIsRight
 * @property string $bornAddrManual
 * @property integer $passAddrIsRight
 * @property string $passAddrManual
 * @property string $passAddrManualHouse
 * @property string $passAddrManualKorp
 * @property string $passAddrManualFlat
 * @property integer $liveAddrIsRight
 * @property string $liveAddrManual
 * @property string $liveAddrManualHouse
 * @property string $liveAddrManualKorp
 * @property string $liveAddrManualFlat
 * @property integer $tempAddrIsRight
 * @property string $tempAddrManual
 * @property string $tempAddrManualHouse
 * @property string $tempAddrManualKorp
 * @property string $tempAddrManualFlat
 * @property integer $phoneIsRight
 * @property string $phoneManual
 * @property integer $emailIsRight
 * @property string $emailManual
 * @property integer $innIsRight
 * @property string $innManualNmb
 * @property integer $snilsIsRight
 * @property string $snilsManualNmb
 * @property integer $medPolicyIsRight
 * @property string $medPolicyManualNmb
 * @property string $medPolicyManualGivenby
 * @property string $medPolicyManualGivendate
 * @property string $medPolicyManualTodate
 * @property integer $socialProtectionIsRight
 * @property string $socialProtectionNmb
 * @property string $socialProtectionGivenby
 * @property string $socialProtectionGivendate
 * @property string $socialProtectionTodate
 * @property integer $residenceIsRight
 * @property string $residenceManualNmb
 * @property string $residenceManualGivendate
 * @property string $residenceManualTodate
 * @property integer $migrationIsRight
 * @property string $migrationManualNmb
 * @property string $migrationManualGivendate
 * @property string $migrationManualTodate
 * @property integer $familyStateIsRight
 * @property string $familyStateManual
 * @property integer $husbandWifeIsRight
 * @property string $husbandWifeManualFio
 * @property string $husbandWifeManualBorndate
 * @property string $husbandWifeManualPhone
 * @property string $husbandWifeManualAddr
 * @property integer $motherIsRight
 * @property string $motherManualFio
 * @property string $motherManualBorndate
 * @property string $motherManualPhone
 * @property string $motherManualAddr
 * @property integer $fatherIsRight
 * @property string $fatherManualFio
 * @property string $fatherManualBorndate
 * @property string $fatherManualPhone
 * @property string $fatherManualAddr
 * @property integer $kinder1IsRight
 * @property string $kinder1ManualFio
 * @property string $kinder1ManualBorndate
 * @property string $kinder1ManualPhone
 * @property string $kinder1ManualAddr
 * @property integer $kinder2IsRight
 * @property string $kinder2ManualFio
 * @property string $kinder2ManualBorndate
 * @property string $kinder2ManualPhone
 * @property string $kinder2ManualAddr
 * @property integer $kinder3IsRight
 * @property string $kinder3ManualFio
 * @property string $kinder3ManualBorndate
 * @property string $kinder3ManualPhone
 * @property string $kinder3ManualAddr
 * @property string $husbandWifeFromDB
 * @property string $kinder1FromDB
 * @property string $kinder2FromDB
 * @property string $kinder3FromDB
 * @property string $innFromDB
 * @property string $snilsFromDB
 * @property string $medPolicyFromDB
 * @property string $socialProtectionFromDB
 * @property string $motherFromDB
 * @property string $fatherFromDB
 * @property string $residenceFromDB
 * @property string $migrationFromDB
 * @property string $passFromDB
 */
class GalStudentPersonalcard extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'gal_student_personalcard';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pnrec', 'required'),
            array('placeOfStudyIsRight, specIsRight, finIsRight, contNmbIsRight, contBeginIsRight, entNameIsRight, sexIsRight, borndateIsRight, grIsRight, passVidIsRight, pserIsRight, pnmbIsRight, givenbyIsRight, givendateIsRight, todateIsRight, givenpodrIsRight, eduLevelIsRight, eduDocIsRight, eduSeriaIsRight, eduNmbIsRight, eduDipDateIsRight, eduPlaceIsRight, eduAddrIsRight, bornAddrIsRight, passAddrIsRight, liveAddrIsRight, tempAddrIsRight, phoneIsRight, emailIsRight, innIsRight, snilsIsRight, medPolicyIsRight, socialProtectionIsRight, residenceIsRight, migrationIsRight, familyStateIsRight, husbandWifeIsRight, motherIsRight, fatherIsRight, kinder1IsRight, kinder2IsRight, kinder3IsRight', 'numerical', 'integerOnly'=>true),
            array('pnrec, sexManual, grManual, passVidManual, eduLevelManual, eduDocManual, eduPlaceManual, familyStateManual, husbandWifeFromDB, kinder1FromDB, kinder2FromDB, kinder3FromDB, innFromDB, snilsFromDB, medPolicyFromDB, socialProtectionFromDB, motherFromDB, fatherFromDB, residenceFromDB, migrationFromDB, passFromDB', 'length', 'max'=>8),
            array('pserManual, pnmbManual, givenbyManual, givenpodrManual, eduSeriaManual, eduNmbManual, passAddrManualHouse, passAddrManualKorp, passAddrManualFlat, liveAddrManualHouse, liveAddrManualKorp, liveAddrManualFlat, tempAddrManualHouse, tempAddrManualKorp, tempAddrManualFlat, phoneManual, innManualNmb, snilsManualNmb, medPolicyManualNmb, medPolicyManualGivenby, socialProtectionNmb, socialProtectionGivenby, residenceManualNmb, migrationManualNmb, husbandWifeManualFio, husbandWifeManualPhone, motherManualFio, motherManualPhone, fatherManualFio, fatherManualPhone, kinder1ManualFio, kinder1ManualPhone, kinder2ManualFio, kinder2ManualPhone, kinder3ManualFio, kinder3ManualPhone', 'length', 'max'=>255),
            array('emailManual', 'length', 'max'=>50),
            array('borndateManual, givendateManual, todateManual, eduDipDateManual, eduAddrManual, bornAddrManual, passAddrManual, liveAddrManual, tempAddrManual, medPolicyManualGivendate, medPolicyManualTodate, socialProtectionGivendate, socialProtectionTodate, residenceManualGivendate, residenceManualTodate, migrationManualGivendate, migrationManualTodate, husbandWifeManualBorndate, husbandWifeManualAddr, motherManualBorndate, motherManualAddr, fatherManualBorndate, fatherManualAddr, kinder1ManualBorndate, kinder1ManualAddr, kinder2ManualBorndate, kinder2ManualAddr, kinder3ManualBorndate, kinder3ManualAddr', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, pnrec, placeOfStudyIsRight, specIsRight, finIsRight, contNmbIsRight, contBeginIsRight, entNameIsRight, sexIsRight, sexManual, borndateIsRight, borndateManual, grIsRight, grManual, passVidIsRight, passVidManual, pserIsRight, pserManual, pnmbIsRight, pnmbManual, givenbyIsRight, givenbyManual, givendateIsRight, givendateManual, todateIsRight, todateManual, givenpodrIsRight, givenpodrManual, eduLevelIsRight, eduLevelManual, eduDocIsRight, eduDocManual, eduSeriaIsRight, eduSeriaManual, eduNmbIsRight, eduNmbManual, eduDipDateIsRight, eduDipDateManual, eduPlaceIsRight, eduPlaceManual, eduAddrIsRight, eduAddrManual, bornAddrIsRight, bornAddrManual, passAddrIsRight, passAddrManual, passAddrManualHouse, passAddrManualKorp, passAddrManualFlat, liveAddrIsRight, liveAddrManual, liveAddrManualHouse, liveAddrManualKorp, liveAddrManualFlat, tempAddrIsRight, tempAddrManual, tempAddrManualHouse, tempAddrManualKorp, tempAddrManualFlat, phoneIsRight, phoneManual, emailIsRight, emailManual, innIsRight, innManualNmb, snilsIsRight, snilsManualNmb, medPolicyIsRight, medPolicyManualNmb, medPolicyManualGivenby, medPolicyManualGivendate, medPolicyManualTodate, socialProtectionIsRight, socialProtectionNmb, socialProtectionGivenby, socialProtectionGivendate, socialProtectionTodate, residenceIsRight, residenceManualNmb, residenceManualGivendate, residenceManualTodate, migrationIsRight, migrationManualNmb, migrationManualGivendate, migrationManualTodate, familyStateIsRight, familyStateManual, husbandWifeIsRight, husbandWifeManualFio, husbandWifeManualBorndate, husbandWifeManualPhone, husbandWifeManualAddr, motherIsRight, motherManualFio, motherManualBorndate, motherManualPhone, motherManualAddr, fatherIsRight, fatherManualFio, fatherManualBorndate, fatherManualPhone, fatherManualAddr, kinder1IsRight, kinder1ManualFio, kinder1ManualBorndate, kinder1ManualPhone, kinder1ManualAddr, kinder2IsRight, kinder2ManualFio, kinder2ManualBorndate, kinder2ManualPhone, kinder2ManualAddr, kinder3IsRight, kinder3ManualFio, kinder3ManualBorndate, kinder3ManualPhone, kinder3ManualAddr, husbandWifeFromDB, kinder1FromDB, kinder2FromDB, kinder3FromDB, innFromDB, snilsFromDB, medPolicyFromDB, socialProtectionFromDB, motherFromDB, fatherFromDB, residenceFromDB, migrationFromDB, passFromDB', 'safe', 'on'=>'search'),
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
            'id' => 'Уникальный идентификатор',
            'pnrec' => 'Nrec студента из gal_persons',
            'placeOfStudyIsRight' => 'Правильность место обучения',
            'specIsRight' => 'Правильность специальности',
            'finIsRight' => 'Правильность источника финансирования',
            'contNmbIsRight' => 'Правильность номера договора',
            'contBeginIsRight' => 'Правильность даты начала договора',
            'entNameIsRight' => 'Правильность предприятия по опк',
            'sexIsRight' => 'Правильность пола',
            'sexManual' => 'Ручной ввод пола',
            'borndateIsRight' => 'Правильность даты рождения',
            'borndateManual' => 'Ручной ввод даты рождения',
            'grIsRight' => 'Правильность гражданства',
            'grManual' => 'Ручной ввод гражданства',
            'passVidIsRight' => 'Правильность типа паспорта',
            'passVidManual' => 'Ручной ввод типа паспорта',
            'pserIsRight' => 'Правильность серии паспорта',
            'pserManual' => 'Ручной ввод серии паспорта',
            'pnmbIsRight' => 'Правильность номера паспорта',
            'pnmbManual' => 'Ручной ввод номера паспорта',
            'givenbyIsRight' => 'Правильность кем выдан паспорта',
            'givenbyManual' => 'Ручной ввод кем выдан паспорт',
            'givendateIsRight' => 'Правильность даты выдачи паспорта',
            'givendateManual' => 'Ручной ввод даты паспорта',
            'todateIsRight' => 'Правильность даты паспорта',
            'todateManual' => 'Ручной ввод даты паспорта',
            'givenpodrIsRight' => 'Правильность подразделения паспорта',
            'givenpodrManual' => 'Ручной ввод подразделения паспорта',
            'eduLevelIsRight' => 'Правильность уровень предыдущего образования',
            'eduLevelManual' => 'Ручной ввод уровень предыдущего образования',
            'eduDocIsRight' => 'Правильность документа об образовании',
            'eduDocManual' => 'Ручной ввод документа об бразовании',
            'eduSeriaIsRight' => 'Правильность серии документа об образовании',
            'eduSeriaManual' => 'Ручной ввод серии документа об образовании',
            'eduNmbIsRight' => 'Правильность номера документа об образовании',
            'eduNmbManual' => 'Ручной ввод номера документа об образовании',
            'eduDipDateIsRight' => 'Правильность даты документа об образовании',
            'eduDipDateManual' => 'Ручной ввод документа об образовании',
            'eduPlaceIsRight' => 'Правильность наименования учебного заведения',
            'eduPlaceManual' => 'Ручной ввод наименования учебного заведения',
            'eduAddrIsRight' => 'Правильность местоположения учебного заведения',
            'eduAddrManual' => 'Ручной ввод местоположения учебного заведения',
            'bornAddrIsRight' => 'Правильность мето рождения',
            'bornAddrManual' => 'Ручной ввод места рождения',
            'passAddrIsRight' => 'Правильность адреса по паспорту',
            'passAddrManual' => 'Ручной ввод адреса по паспорту',
            'passAddrManualHouse' => 'Ручной ввод адреса по паспорту - дом',
            'passAddrManualKorp' => 'Ручной ввод адреса по паспорту - корпус',
            'passAddrManualFlat' => 'Ручной ввод адреса по паспорту - квартира',
            'liveAddrIsRight' => 'Правильность адреса проживания',
            'liveAddrManual' => 'Ручной ввод адреса проживания',
            'liveAddrManualHouse' => 'Ручной ввод адреса проживания - дом',
            'liveAddrManualKorp' => 'Ручной ввод адреса проживания - корпус',
            'liveAddrManualFlat' => 'Ручной ввод адреса проживания - квартира',
            'tempAddrIsRight' => 'Правильность адреса временной регистрации',
            'tempAddrManual' => 'Ручной ввод адреса временной регистрации',
            'tempAddrManualHouse' => 'Ручной ввод адреса временной регистрации - дом',
            'tempAddrManualKorp' => 'Ручной ввод адреса временной регистрации - корпус',
            'tempAddrManualFlat' => 'Ручной ввод адреса временной регистрации - кваритра',
            'phoneIsRight' => 'Правильность телефон',
            'phoneManual' => 'Ручной ввод телефон',
            'emailIsRight' => 'Правильность email',
            'emailManual' => 'Ручной ввод email',
            'innIsRight' => 'Правильность инн',
            'innManualNmb' => 'Ручной ввод номер инн',
            'snilsIsRight' => 'Правильность снилс',
            'snilsManualNmb' => 'Ручной ввод номер снилс',
            'medPolicyIsRight' => 'Правильность страховой полис',
            'medPolicyManualNmb' => 'Ручной ввод номера страхового полиса',
            'medPolicyManualGivenby' => 'Ручной ввод кем выдан страховой полис',
            'medPolicyManualGivendate' => 'Ручной ввод дата выдачи страхового полиса',
            'medPolicyManualTodate' => 'Ручной ввод окончания полиса',
            'socialProtectionIsRight' => 'Правильность справки соц защиты',
            'socialProtectionNmb' => 'Ручной ввод номер справки соц защиты',
            'socialProtectionGivenby' => 'Ручной ввод кем выдана справка соц защиты',
            'socialProtectionGivendate' => 'Ручнйо ввод дата выдачи спраки соц защиты',
            'socialProtectionTodate' => 'Ручной ввод окончание спраки соц защиты',
            'residenceIsRight' => 'Правильность вида на жительство',
            'residenceManualNmb' => 'Ручной ввод номера вида на жительство',
            'residenceManualGivendate' => 'Ручной ввод даты выдачи вида на жительство',
            'residenceManualTodate' => 'Ручной ввод окончания вида на жительство',
            'migrationIsRight' => 'Правильность миграционной карты',
            'migrationManualNmb' => 'Ручной ввод номер миграционной карты',
            'migrationManualGivendate' => 'Ручной ввод даты выдачи миграционной карты',
            'migrationManualTodate' => 'Ручной ввод окончания миграционной карты',
            'familyStateIsRight' => 'Правильность семейное положение',
            'familyStateManual' => 'Ручной ввод семейное положение',
            'husbandWifeIsRight' => 'Husband Wife Is Right',
            'husbandWifeManualFio' => 'Husband Wife Manual Fio',
            'husbandWifeManualBorndate' => 'Husband Wife Manual Borndate',
            'husbandWifeManualPhone' => 'Husband Wife Manual Phone',
            'husbandWifeManualAddr' => 'Husband Wife Manual Addr',
            'motherIsRight' => 'Mother Is Right',
            'motherManualFio' => 'Mother Manual Fio',
            'motherManualBorndate' => 'Mother Manual Borndate',
            'motherManualPhone' => 'Mother Manual Phone',
            'motherManualAddr' => 'Mother Manual Addr',
            'fatherIsRight' => 'Father Is Right',
            'fatherManualFio' => 'Father Manual Fio',
            'fatherManualBorndate' => 'Father Manual Borndate',
            'fatherManualPhone' => 'Father Manual Phone',
            'fatherManualAddr' => 'Father Manual Addr',
            'kinder1IsRight' => 'Kinder1 Is Right',
            'kinder1ManualFio' => 'Kinder1 Manual Fio',
            'kinder1ManualBorndate' => 'Kinder1 Manual Borndate',
            'kinder1ManualPhone' => 'Kinder1 Manual Phone',
            'kinder1ManualAddr' => 'Kinder1 Manual Addr',
            'kinder2IsRight' => 'Kinder2 Is Right',
            'kinder2ManualFio' => 'Kinder2 Manual Fio',
            'kinder2ManualBorndate' => 'Kinder2 Manual Borndate',
            'kinder2ManualPhone' => 'Kinder2 Manual Phone',
            'kinder2ManualAddr' => 'Kinder2 Manual Addr',
            'kinder3IsRight' => 'Kinder3 Is Right',
            'kinder3ManualFio' => 'Kinder3 Manual Fio',
            'kinder3ManualBorndate' => 'Kinder3 Manual Borndate',
            'kinder3ManualPhone' => 'Kinder3 Manual Phone',
            'kinder3ManualAddr' => 'Kinder3 Manual Addr',
            'husbandWifeFromDB' => 'Husband Wife From Db',
            'kinder1FromDB' => 'Kinder1 From Db',
            'kinder2FromDB' => 'Kinder2 From Db',
            'kinder3FromDB' => 'Kinder3 From Db',
            'innFromDB' => 'Inn From Db',
            'snilsFromDB' => 'Snils From Db',
            'medPolicyFromDB' => 'Med Policy From Db',
            'socialProtectionFromDB' => 'Social Protection From Db',
            'motherFromDB' => 'Mother From Db',
            'fatherFromDB' => 'Father From Db',
            'residenceFromDB' => 'Residence From Db',
            'migrationFromDB' => 'Migration From Db',
            'passFromDB' => 'Pass From Db',
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
        $criteria->compare('pnrec',$this->pnrec,true);
        $criteria->compare('placeOfStudyIsRight',$this->placeOfStudyIsRight);
        $criteria->compare('specIsRight',$this->specIsRight);
        $criteria->compare('finIsRight',$this->finIsRight);
        $criteria->compare('contNmbIsRight',$this->contNmbIsRight);
        $criteria->compare('contBeginIsRight',$this->contBeginIsRight);
        $criteria->compare('entNameIsRight',$this->entNameIsRight);
        $criteria->compare('sexIsRight',$this->sexIsRight);
        $criteria->compare('sexManual',$this->sexManual,true);
        $criteria->compare('borndateIsRight',$this->borndateIsRight);
        $criteria->compare('borndateManual',$this->borndateManual,true);
        $criteria->compare('grIsRight',$this->grIsRight);
        $criteria->compare('grManual',$this->grManual,true);
        $criteria->compare('passVidIsRight',$this->passVidIsRight);
        $criteria->compare('passVidManual',$this->passVidManual,true);
        $criteria->compare('pserIsRight',$this->pserIsRight);
        $criteria->compare('pserManual',$this->pserManual,true);
        $criteria->compare('pnmbIsRight',$this->pnmbIsRight);
        $criteria->compare('pnmbManual',$this->pnmbManual,true);
        $criteria->compare('givenbyIsRight',$this->givenbyIsRight);
        $criteria->compare('givenbyManual',$this->givenbyManual,true);
        $criteria->compare('givendateIsRight',$this->givendateIsRight);
        $criteria->compare('givendateManual',$this->givendateManual,true);
        $criteria->compare('todateIsRight',$this->todateIsRight);
        $criteria->compare('todateManual',$this->todateManual,true);
        $criteria->compare('givenpodrIsRight',$this->givenpodrIsRight);
        $criteria->compare('givenpodrManual',$this->givenpodrManual,true);
        $criteria->compare('eduLevelIsRight',$this->eduLevelIsRight);
        $criteria->compare('eduLevelManual',$this->eduLevelManual,true);
        $criteria->compare('eduDocIsRight',$this->eduDocIsRight);
        $criteria->compare('eduDocManual',$this->eduDocManual,true);
        $criteria->compare('eduSeriaIsRight',$this->eduSeriaIsRight);
        $criteria->compare('eduSeriaManual',$this->eduSeriaManual,true);
        $criteria->compare('eduNmbIsRight',$this->eduNmbIsRight);
        $criteria->compare('eduNmbManual',$this->eduNmbManual,true);
        $criteria->compare('eduDipDateIsRight',$this->eduDipDateIsRight);
        $criteria->compare('eduDipDateManual',$this->eduDipDateManual,true);
        $criteria->compare('eduPlaceIsRight',$this->eduPlaceIsRight);
        $criteria->compare('eduPlaceManual',$this->eduPlaceManual,true);
        $criteria->compare('eduAddrIsRight',$this->eduAddrIsRight);
        $criteria->compare('eduAddrManual',$this->eduAddrManual,true);
        $criteria->compare('bornAddrIsRight',$this->bornAddrIsRight);
        $criteria->compare('bornAddrManual',$this->bornAddrManual,true);
        $criteria->compare('passAddrIsRight',$this->passAddrIsRight);
        $criteria->compare('passAddrManual',$this->passAddrManual,true);
        $criteria->compare('passAddrManualHouse',$this->passAddrManualHouse,true);
        $criteria->compare('passAddrManualKorp',$this->passAddrManualKorp,true);
        $criteria->compare('passAddrManualFlat',$this->passAddrManualFlat,true);
        $criteria->compare('liveAddrIsRight',$this->liveAddrIsRight);
        $criteria->compare('liveAddrManual',$this->liveAddrManual,true);
        $criteria->compare('liveAddrManualHouse',$this->liveAddrManualHouse,true);
        $criteria->compare('liveAddrManualKorp',$this->liveAddrManualKorp,true);
        $criteria->compare('liveAddrManualFlat',$this->liveAddrManualFlat,true);
        $criteria->compare('tempAddrIsRight',$this->tempAddrIsRight);
        $criteria->compare('tempAddrManual',$this->tempAddrManual,true);
        $criteria->compare('tempAddrManualHouse',$this->tempAddrManualHouse,true);
        $criteria->compare('tempAddrManualKorp',$this->tempAddrManualKorp,true);
        $criteria->compare('tempAddrManualFlat',$this->tempAddrManualFlat,true);
        $criteria->compare('phoneIsRight',$this->phoneIsRight);
        $criteria->compare('phoneManual',$this->phoneManual,true);
        $criteria->compare('emailIsRight',$this->emailIsRight);
        $criteria->compare('emailManual',$this->emailManual,true);
        $criteria->compare('innIsRight',$this->innIsRight);
        $criteria->compare('innManualNmb',$this->innManualNmb,true);
        $criteria->compare('snilsIsRight',$this->snilsIsRight);
        $criteria->compare('snilsManualNmb',$this->snilsManualNmb,true);
        $criteria->compare('medPolicyIsRight',$this->medPolicyIsRight);
        $criteria->compare('medPolicyManualNmb',$this->medPolicyManualNmb,true);
        $criteria->compare('medPolicyManualGivenby',$this->medPolicyManualGivenby,true);
        $criteria->compare('medPolicyManualGivendate',$this->medPolicyManualGivendate,true);
        $criteria->compare('medPolicyManualTodate',$this->medPolicyManualTodate,true);
        $criteria->compare('socialProtectionIsRight',$this->socialProtectionIsRight);
        $criteria->compare('socialProtectionNmb',$this->socialProtectionNmb,true);
        $criteria->compare('socialProtectionGivenby',$this->socialProtectionGivenby,true);
        $criteria->compare('socialProtectionGivendate',$this->socialProtectionGivendate,true);
        $criteria->compare('socialProtectionTodate',$this->socialProtectionTodate,true);
        $criteria->compare('residenceIsRight',$this->residenceIsRight);
        $criteria->compare('residenceManualNmb',$this->residenceManualNmb,true);
        $criteria->compare('residenceManualGivendate',$this->residenceManualGivendate,true);
        $criteria->compare('residenceManualTodate',$this->residenceManualTodate,true);
        $criteria->compare('migrationIsRight',$this->migrationIsRight);
        $criteria->compare('migrationManualNmb',$this->migrationManualNmb,true);
        $criteria->compare('migrationManualGivendate',$this->migrationManualGivendate,true);
        $criteria->compare('migrationManualTodate',$this->migrationManualTodate,true);
        $criteria->compare('familyStateIsRight',$this->familyStateIsRight);
        $criteria->compare('familyStateManual',$this->familyStateManual,true);
        $criteria->compare('husbandWifeIsRight',$this->husbandWifeIsRight);
        $criteria->compare('husbandWifeManualFio',$this->husbandWifeManualFio,true);
        $criteria->compare('husbandWifeManualBorndate',$this->husbandWifeManualBorndate,true);
        $criteria->compare('husbandWifeManualPhone',$this->husbandWifeManualPhone,true);
        $criteria->compare('husbandWifeManualAddr',$this->husbandWifeManualAddr,true);
        $criteria->compare('motherIsRight',$this->motherIsRight);
        $criteria->compare('motherManualFio',$this->motherManualFio,true);
        $criteria->compare('motherManualBorndate',$this->motherManualBorndate,true);
        $criteria->compare('motherManualPhone',$this->motherManualPhone,true);
        $criteria->compare('motherManualAddr',$this->motherManualAddr,true);
        $criteria->compare('fatherIsRight',$this->fatherIsRight);
        $criteria->compare('fatherManualFio',$this->fatherManualFio,true);
        $criteria->compare('fatherManualBorndate',$this->fatherManualBorndate,true);
        $criteria->compare('fatherManualPhone',$this->fatherManualPhone,true);
        $criteria->compare('fatherManualAddr',$this->fatherManualAddr,true);
        $criteria->compare('kinder1IsRight',$this->kinder1IsRight);
        $criteria->compare('kinder1ManualFio',$this->kinder1ManualFio,true);
        $criteria->compare('kinder1ManualBorndate',$this->kinder1ManualBorndate,true);
        $criteria->compare('kinder1ManualPhone',$this->kinder1ManualPhone,true);
        $criteria->compare('kinder1ManualAddr',$this->kinder1ManualAddr,true);
        $criteria->compare('kinder2IsRight',$this->kinder2IsRight);
        $criteria->compare('kinder2ManualFio',$this->kinder2ManualFio,true);
        $criteria->compare('kinder2ManualBorndate',$this->kinder2ManualBorndate,true);
        $criteria->compare('kinder2ManualPhone',$this->kinder2ManualPhone,true);
        $criteria->compare('kinder2ManualAddr',$this->kinder2ManualAddr,true);
        $criteria->compare('kinder3IsRight',$this->kinder3IsRight);
        $criteria->compare('kinder3ManualFio',$this->kinder3ManualFio,true);
        $criteria->compare('kinder3ManualBorndate',$this->kinder3ManualBorndate,true);
        $criteria->compare('kinder3ManualPhone',$this->kinder3ManualPhone,true);
        $criteria->compare('kinder3ManualAddr',$this->kinder3ManualAddr,true);
        $criteria->compare('husbandWifeFromDB',$this->husbandWifeFromDB,true);
        $criteria->compare('kinder1FromDB',$this->kinder1FromDB,true);
        $criteria->compare('kinder2FromDB',$this->kinder2FromDB,true);
        $criteria->compare('kinder3FromDB',$this->kinder3FromDB,true);
        $criteria->compare('innFromDB',$this->innFromDB,true);
        $criteria->compare('snilsFromDB',$this->snilsFromDB,true);
        $criteria->compare('medPolicyFromDB',$this->medPolicyFromDB,true);
        $criteria->compare('socialProtectionFromDB',$this->socialProtectionFromDB,true);
        $criteria->compare('motherFromDB',$this->motherFromDB,true);
        $criteria->compare('fatherFromDB',$this->fatherFromDB,true);
        $criteria->compare('residenceFromDB',$this->residenceFromDB,true);
        $criteria->compare('migrationFromDB',$this->migrationFromDB,true);
        $criteria->compare('passFromDB',$this->passFromDB,true);

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
     * @return GalStudentPersonalcard the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function getSchool($id){

        if ($id) {
            $school = Yii::app()->db2->createCommand()
                ->select('CONCAT(u.sname, \' (\', c.name, \', \', r.name, \', \', s.name, \')\') label')
                ->from('gal_u_school u')
                ->leftJoin('gal_u_country c', 'u.ccountry = c.nrec')
                ->leftJoin('gal_u_country r', 'u.cregion = r.nrec')
                ->leftJoin('gal_u_settlements s', 'u.ccity = s.nrec')
                ->where('u.nrec = :id', [':id' => hex2bin($id)])
                ->order('u.sname')
                ->queryScalar();

            return $school;
        }
        return '';

    }

    public static function getCatalogs($id){

        if ($id) {
            $edu = Yii::app()->db2->createCommand()
                ->select('c.name')
                ->from('gal_catalogs c')
                ->where('c.nrec = :id ', [':id' => hex2bin($id)])
                ->queryScalar();
            return $edu;

        }
        return '';
    }

    public static function getAddr($id){
        return null;
        if ($id) {
            $edu = Yii::app()->db2->createCommand()
                ->select('c.name')
                ->from('gal_catalogs c')
                ->where('c.nrec = :id ', [':id' => bin2hex($id)])
                ->queryScalar();
            return $edu;

        }
        return '';
    }

    public static function getFamilyState(){
        $sql = Yii::app()->db2->createCommand()
            ->select('HEX(c.nrec) nrec,                  
                  c.name name')
            ->from('gal_catalogs c')
            ->where('c.mainlink = 0x80000000000001FE')
            ->queryAll();

        return $sql;
    }


}