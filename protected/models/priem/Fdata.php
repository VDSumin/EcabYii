<?php

/**
 * This is the model class for table "fdata".
 *
 * The followings are the available columns in table 'fdata':
 * @property integer $npp
 * @property string $fam
 * @property string $nam
 * @property string $otc
 * @property integer $pol
 * @property string $rogd
 * @property integer $pvid
 * @property string $pser
 * @property string $pnomer
 * @property string $pdat
 * @property string $pkem
 * @property string $stob
 * @property string $stsp
 * @property integer $godok
 * @property string $inostr
 * @property integer $gragd
 * @property string $nacion
 * @property string $kur
 * @property integer $kkor
 * @property string $user
 * @property double $cball
 * @property integer $mestogos
 * @property string $mestokladr
 * @property string $mestoadr
 * @property string $mestoindex
 * @property string $svjaz
 * @property string $phones
 * @property integer $obrdoc
 * @property integer $obrotl
 * @property integer $obrazdoctype
 * @property string $obraznomdoc
 * @property string $obrazserdoc
 * @property string $obrazdatdoc
 * @property string $obrazkemdoc
 * @property integer $obrazgos
 * @property string $obrazkladr
 * @property string $obrazadr
 * @property string $obrazindex
 * @property integer $uchzav
 * @property string $prof
 * @property string $zakr
 * @property string $dkor
 * @property string $sotel
 * @property string $dolgn
 * @property string $email
 * @property string $nomshk
 * @property string $serdoc
 * @property string $nomdoc
 * @property string $gorod
 * @property string $raion
 * @property integer $npp_i
 * @property string $fotocheck
 * @property string $webpwd
 * @property string $obshaga
 * @property integer $bgos
 * @property string $bkladr
 * @property string $badr
 * @property string $bindex
 * @property string $photo
 * @property string $ppodr
 * @property integer $faktgos
 * @property string $faktkladr
 * @property string $faktadr
 * @property string $faktindex
 * @property integer $webstep
 * @property string $mestodom
 * @property string $mestokorp
 * @property string $mestokvart
 * @property string $bdom
 * @property string $bkorp
 * @property string $bkvart
 * @property string $faktdom
 * @property string $faktkorp
 * @property string $faktkvart
 * @property string $obrazdom
 * @property string $obrazkorp
 * @property string $obrazkvart
 * @property integer $vozvrat
 * @property string $oldpser
 * @property string $oldpnomer
 * @property string $dr
 * @property string $gal_nation
 *
 * The followings are the available model relations:
 * @property Contract[] $contracts
 * @property Documents[] $documents
 * @property Kodinostr $inostr0
 * @property Kodprof $prof0
 * @property Vozvrat $vozvrat0
 * @property Kard[] $kards
 * @property Kard[] $wkardcs
 * @property Keylinks $keylinks
 * @property Wkard[] $wkards
 */
class Fdata extends CActiveRecord {

    public $fio;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'fdata';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pol, pvid, godok, gragd, kkor, mestogos, obrdoc, obrotl, obrazdoctype, obrazgos, uchzav, npp_i, bgos, faktgos, webstep, vozvrat', 'numerical', 'integerOnly' => true),
            array('cball', 'numerical'),
            array('fam, nam, otc', 'length', 'max' => 40),
            array('pser, mestoindex, obrazserdoc, obrazindex, gorod, bindex, ppodr, faktindex, oldpser', 'length', 'max' => 6),
            array('pnomer, raion, mestodom, mestokorp, mestokvart, bdom, bkorp, bkvart, faktdom, faktkorp, faktkvart, obrazdom, obrazkorp, obrazkvart, oldpnomer', 'length', 'max' => 10),
            array('stob, stsp', 'length', 'max' => 3),
            array('inostr, kur, prof, zakr, fotocheck, obshaga', 'length', 'max' => 1),
            array('nacion', 'length', 'max' => 2),
            array('user, gal_nation', 'length', 'max' => 30),
            array('mestokladr, obrazkladr, bkladr, faktkladr', 'length', 'max' => 19),
            array('mestoadr, badr', 'length', 'max' => 100),
            array('svjaz', 'length', 'max' => 70),
            array('obraznomdoc, nomdoc', 'length', 'max' => 16),
            array('obrazadr', 'length', 'max' => 200),
            array('sotel', 'length', 'max' => 12),
            array('email, webpwd', 'length', 'max' => 250),
            array('nomshk', 'length', 'max' => 4),
            array('serdoc', 'length', 'max' => 8),
            array('faktadr', 'length', 'max' => 255),
            array('rogd, pdat, pkem, phones, obrazdatdoc, obrazkemdoc, dkor, dolgn, photo, dr', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('npp, fio, fam, nam, otc, pol, rogd, pvid, pser, pnomer, pdat, pkem, stob, stsp, godok, inostr, gragd, nacion, kur, kkor, user, cball, mestogos, mestokladr, mestoadr, mestoindex, svjaz, phones, obrdoc, obrotl, obrazdoctype, obraznomdoc, obrazserdoc, obrazdatdoc, obrazkemdoc, obrazgos, obrazkladr, obrazadr, obrazindex, uchzav, prof, zakr, dkor, sotel, dolgn, email, nomshk, serdoc, nomdoc, gorod, raion, npp_i, fotocheck, webpwd, obshaga, bgos, bkladr, badr, bindex, photo, ppodr, faktgos, faktkladr, faktadr, faktindex, webstep, mestodom, mestokorp, mestokvart, bdom, bkorp, bkvart, faktdom, faktkorp, faktkvart, obrazdom, obrazkorp, obrazkvart, vozvrat, oldpser, oldpnomer, dr, gal_nation', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'skards' => array(self::HAS_MANY, 'Skard', 'fnpp', 'joinType' => 'INNER JOIN'),
            'wkards' => array(self::HAS_MANY, 'Wkard', 'fnpp', 'joinType' => 'INNER JOIN'),
            'wkardcs' => array(self::HAS_MANY, 'Wkardc_rp', 'fnpp', 'joinType' => 'INNER JOIN'),
            'keylinks' => array(self::HAS_MANY, 'Keylinks', 'fnpp', 'joinType' => 'INNER JOIN', 'together' => true),
            'authcode' => array(self::HAS_ONE, 'Authcodes', 'fnpp'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'npp' => '№',
            'fam' => 'Фамилия',
            'nam' => 'Имя',
            'otc' => 'Отчество',
            'pol' => 'Пол',
            'rogd' => 'Дата рождения',
            'pvid' => 'Pvid',
            'pser' => 'Pser',
            'pnomer' => 'Pnomer',
            'pdat' => 'Pdat',
            'pkem' => 'Pkem',
            'stob' => 'Stob',
            'stsp' => 'Stsp',
            'godok' => 'Godok',
            'inostr' => 'Inostr',
            'gragd' => 'Gragd',
            'nacion' => 'Nacion',
            'kur' => 'Kur',
            'kkor' => 'Kkor',
            'user' => 'User',
            'cball' => 'Cball',
            'mestogos' => 'Mestogos',
            'mestokladr' => 'Mestokladr',
            'mestoadr' => 'Mestoadr',
            'mestoindex' => 'Mestoindex',
            'svjaz' => 'Svjaz',
            'phones' => 'Phones',
            'obrdoc' => 'Obrdoc',
            'obrotl' => 'Obrotl',
            'obrazdoctype' => 'Obrazdoctype',
            'obraznomdoc' => 'Obraznomdoc',
            'obrazserdoc' => 'Obrazserdoc',
            'obrazdatdoc' => 'Obrazdatdoc',
            'obrazkemdoc' => 'Obrazkemdoc',
            'obrazgos' => 'Obrazgos',
            'obrazkladr' => 'Obrazkladr',
            'obrazadr' => 'Obrazadr',
            'obrazindex' => 'Obrazindex',
            'uchzav' => 'Uchzav',
            'prof' => 'Prof',
            'zakr' => 'Zakr',
            'dkor' => 'Dkor',
            'sotel' => 'Sotel',
            'dolgn' => 'Dolgn',
            'email' => 'Email',
            'nomshk' => 'Nomshk',
            'serdoc' => 'Serdoc',
            'nomdoc' => 'Nomdoc',
            'gorod' => 'Gorod',
            'raion' => 'Raion',
            'npp_i' => 'Npp I',
            'fotocheck' => 'Fotocheck',
            'webpwd' => 'Webpwd',
            'obshaga' => 'Obshaga',
            'bgos' => 'Bgos',
            'bkladr' => 'Bkladr',
            'badr' => 'Badr',
            'bindex' => 'Bindex',
            'photo' => 'Photo',
            'ppodr' => 'Ppodr',
            'faktgos' => 'Faktgos',
            'faktkladr' => 'Faktkladr',
            'faktadr' => 'Faktadr',
            'faktindex' => 'Faktindex',
            'webstep' => 'Webstep',
            'mestodom' => 'Mestodom',
            'mestokorp' => 'Mestokorp',
            'mestokvart' => 'Mestokvart',
            'bdom' => 'Bdom',
            'bkorp' => 'Bkorp',
            'bkvart' => 'Bkvart',
            'faktdom' => 'Faktdom',
            'faktkorp' => 'Faktkorp',
            'faktkvart' => 'Faktkvart',
            'obrazdom' => 'Obrazdom',
            'obrazkorp' => 'Obrazkorp',
            'obrazkvart' => 'Obrazkvart',
            'vozvrat' => 'Vozvrat',
            'oldpser' => 'Oldpser',
            'oldpnomer' => 'Oldpnomer',
            'dr' => 'Dr',
            'gal_nation' => 'Gal Nation',
        );
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
     * @return Fdata the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * Retuns first name with father name
     * @return string
     */
    public function getFirstName() {
        return trim($this->nam) . ' ' . trim($this->otc);
    }

    /**
     * Returns last name
     * @return string
     */
    public function getLastName() {
        return trim($this->fam);
    }

    public function getFIO() {
        return trim($this->fam.' '.$this->nam.' '.$this->otc);
    }

}
