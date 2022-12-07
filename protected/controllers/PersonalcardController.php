<?php

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;

class PersonalcardController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'updateFieldCheckRight', 'updateDataField', 'school', 'eduDoc', 'eduLevel',
                    'address', 'addressFull', 'gr', 'passport', 'familystate', 'print', 'choiseBook', 'printApplication', 'printApplicationPDF', 'ChangeFIO', 'SaveAttachments',
                    'SaveApplicationPDF', 'PrintFileAttachments'
                ),
                'users' => array('*'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionchoiseBook()
    {
        $galIdMassive = Yii::app()->user->getGalIdMass();
        if (null === $galIdMassive) {
            return $this->redirect(['student/nolink']);
        }

        foreach ($galIdMassive as $galid) {
            $info[] = Yii::app()->db2->createCommand()
                ->select('tp.fio F$FIO, tc.code F$CODE, tc.name F$NAME, tuc.wformed F$WFORMED, td.sfld#1# F$SFLD#1#, tus.sdepcode F$SDEPCODE, tp.nrec ID')
                ->from(uStudent::model()->tableName() . ' tus')
                ->leftJoin(uCurriculum::model()->tableName() . ' tuc', 'tus.ccurr = tuc.nrec')
                ->leftJoin(Catalog::model()->tableName() . ' tc', 'tc.nrec=tuc.cspeciality')
                ->leftJoin(Person::model()->tableName() . ' tp', 'tp.nrec=tus.cpersons')
                ->leftJoin(Dopinfo::model()->tableName() . ' td', 'td.cperson = tp.nrec')
                ->where('tp.nrec=:id', [':id' => $galid['Galid']])
                ->queryRow();
        }

        if (empty($info)) {
            return $this->redirect(['student/nolink']);
        }
        $this->layout = '//layouts/column1';
        return $this->render('choiseBook', array('infos' => $info));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function actionIndex($id = null, $message = null)
    {
        $galId = Yii::app()->user->getGalId();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if (!isset($_SESSION)) {
            session_start();
        }
        $galIdMassive = Yii::app()->user->getGalIdMass();
        if (isset($id)) {
            $prov = 0;
            foreach ($galIdMassive as $galunid) {
                if ($galunid['Galid'] == $id) {
                    $prov = 1;
                }
            }
            if ($prov == 1) {
                $galId = $id;
                $_SESSION['ID'] = $id;
            }
        } else {
            if (isset($_SESSION['ID'])) {
                $galId = $_SESSION['ID'];
            } else {
                $_SESSION['ID'] = $galId;
            }
        }

        $sql = "SELECT
CONCAT(fac.LONGNAME, ', ', tus.SDEPARTMENT, ', ', CASE WHEN bup.wformed = 0 THEN 'Очная'
 WHEN bup.wformed = 1 THEN 'Заочная'
 WHEN bup.wformed = 2 THEN 'Вечерняя'
 end) AS 'placeOfStudy',

 tp.FIO AS 'fio',
 tp.nrec AS 'pnrec',
 CONCAT(spec.code, ' ', spec.name) AS 'spec',
 ts.name AS 'fin',
td.`sfld#1#` as 'contNmb', 
td.`DFLD#1#` as 'contBegin',
cel.`SFLD#1#` AS 'entName',
tp.sex AS 'sex',
tp.borndate AS 'borndate',
 gr.name AS 'gr',
 passtype.name AS 'passVid',
 tp1.nrec as 'passNrec',
  tp1.ser AS 'pser',
  tp1.nmb AS 'pnmb',
  tp1.givenby AS 'givenby',
tp1.givendate AS 'givendate',
  tp1.todate AS 'todate',
  tp1.givenpodr AS 'givenpodr',
  edu.name AS 'eduLevel',
  tc.name as 'eduDoc',
  te.series AS 'eduSeria',
  te.DIPLOMNMB AS 'eduNmb',
  te.DIPLOMDATE as 'eduDipDate',
  tus1.SNAME AS 'eduPlace',
  fam.name as 'familystate',
-- rup.COURSE 'Курс',
 
case
when uz1.wtype = 0 then uz.SADDRESS1
when uz2.wtype = 0 then CONCAT_WS(', ', uz1.SNAME, uz.SADDRESS1)
when uz3.wtype = 0 then CONCAT_WS(', ', uz2.SNAME, uz1.SNAME , uz.SADDRESS1)
when uz4.wtype = 0 then CONCAT_WS(', ', uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
when uz5.wtype = 0 then CONCAT_WS(', ', uz4.SNAME , uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
WHEN uz6.wtype = 0 then CONCAT_WS(', ', uz5.SNAME , uz4.SNAME , uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
 END 'eduAddr',


case
when ab1.wtype = 0 then ab.SNAME
when ab2.wtype = 0 then CONCAT_WS(', ', ab1.SNAME , ab.SNAME)
when ab3.wtype = 0 then CONCAT_WS(', ', ab2.SNAME , ab1.SNAME , ab.SNAME)
when ab4.wtype = 0 then CONCAT_WS(', ', ab3.SNAME , ab2.SNAME , ab1.SNAME , ab.SNAME)
when ab5.wtype = 0 then CONCAT_WS(', ', ab4.SNAME , ab3.SNAME , ab2.SNAME , ab1.SNAME , ab.SNAME)
 END 'bornAddr',

case
when ter.wtype = 0 then pass.SADDRESS1
when ter1.wtype = 0 then CONCAT_WS(', ', ter.SNAME , pass.SADDRESS1)
when ter2.wtype = 0 then CONCAT_WS(', ', ter1.SNAME , ter.SNAME , pass.SADDRESS1)
when ter3.wtype = 0 then CONCAT_WS(', ', ter2.SNAME , ter1.SNAME , ter.SNAME , pass.SADDRESS1)
when ter4.wtype = 0 then CONCAT_WS(', ', ter3.SNAME , ter2.SNAME , ter1.SNAME , ter.SNAME , pass.SADDRESS1)
when ter41.wtype = 0 then CONCAT_WS(', ', ter4.SNAME , ter3.SNAME , ter2.SNAME , ter1.SNAME , ter.SNAME , pass.SADDRESS1)
 END 'passAddr',

case
when ter10.wtype = 0 then live.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(', ', ter10.SNAME , live.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(', ', ter11.SNAME , ter10.SNAME , live.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(', ', ter12.SNAME , ter11.SNAME , ter10.SNAME , live.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(', ', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , live.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(', ', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , live.SADDRESS1)
 END 'liveAddr', 


case
when ter20.wtype = 0 then vrem.SADDRESS1
when ter21.wtype = 0 then CONCAT_WS(', ', ter20.SNAME , vrem.SADDRESS1)
when ter22.wtype = 0 then CONCAT_WS(', ', ter21.SNAME , ter20.SNAME , vrem.SADDRESS1)
when ter23.wtype = 0 then CONCAT_WS(', ', ter22.SNAME , ter21.SNAME , ter20.SNAME , vrem.SADDRESS1)
when ter24.wtype = 0 then CONCAT_WS(', ', ter23.SNAME , ter22.SNAME , ter21.SNAME , ter20.SNAME , vrem.SADDRESS1)
when ter25.wtype = 0 then CONCAT_WS(', ', ter24.SNAME , ter23.SNAME , ter22.SNAME , ter21.SNAME , ter20.SNAME , vrem.SADDRESS1)
 END 'tempAddr',
 
 (SELECT
 GROUP_CONCAT(tel1.ADDR )
 FROM gal_COMMUNICATIONS tel1 WHERE tp.nrec = tel1.PERSON and tel1.COMTYPE in (0x800000000000022A, 0x800000000000022B, 0x80000000000003A7)) as 'phone',

 (SELECT  GROUP_CONCAT(mail.EMAIL)  FROM gal_COMMUNICATIONS mail WHERE tp.nrec = mail.PERSON and mail.COMTYPE = 0x80000000000003A6) as 'email'



FROM gal_PERSONS tp
LEFT JOIN gal_U_STUDENT tus on tus.CPERSONS = tp.nrec
LEFT JOIN gal_CATALOGS gr on gr.nrec = tp.GR
LEFT JOIN gal_PASSPORTS tp1 ON tp1.nrec = tp.passprus

LEFT JOIN gal_CATALOGS passtype ON passtype.nrec = tp1.DOCNAME
LEFT JOIN gal_CATALOGS fam ON fam.nrec = tp.familystate
LEFT JOIN gal_EDUCATION te ON te.PERSON = tp.nrec AND te.IATTR = 1
LEFT JOIN gal_U_SCHOOL tus1 on tus1.nrec = te.NAME
LEFT JOIN gal_CATALOGS edu ON edu.nrec = te.LEVEL
LEFT JOIN gal_CATALOGS tc ON te.seqnmb = tc.CODE AND tc.CPARENT = 0x80000000000002D6
LEFT JOIN gal_APPOINTMENTS ta ON ta.nrec = CASE WHEN tp.APPOINTCUR = 0x8000000000000000 THEN tp.APPOINTLAST ELSE tp.APPOINTCUR END
LEFT JOIN gal_U_STUD_FINSOURCE tusf ON tusf.nrec = ta.CREF2 -- ист.финанс
LEFT JOIN gal_SPKAU ts ON ts.nrec = tusf.CFINSOURCE -- ИФ APPOINTMENTS
LEFT JOIN gal_STAFFSTRUCT st on ta.STAFFSTR = st.nrec
LEFT JOIN gal_U_CURRICULUM bup on bup.nrec = st.CSTR
-- LEFT JOIN gal_U_CURRICULUM rup ON rup.nrec = ta.CDOPINF
LEFT JOIN gal_CATALOGS spec ON spec.nrec = bup.CSPECIALITY
LEFT JOIN gal_CATALOGS fac ON fac.nrec = bup.CFACULTY
LEFT JOIN gal_DOPINFO td ON td.nrec = (SELECT cont.nrec from gal_dopinfo cont WHERE cont.CPERSON = tp.nrec AND cont.CDOPTBL = 0x8001000000000007 ORDER BY cont.`DFLD#1#` DESC LIMIT 1)
LEFT JOIN gal_DOPINFO za4 ON za4.CPERSON = tp.nrec AND za4.CDOPTBL = 0x8001000000000003
LEFT JOIN gal_DOPINFO cel ON cel.CPERSON = tp.nrec AND cel.CDOPTBL = 0x8001000000000001
LEFT JOIN gal_CATALOGS opk ON opk.nrec = cel.`CFLD#1#` AND opk.CPARENT = 0x8001000000002697

-- Учебное заведение
LEFT JOIN gal_U_SCHOOL us ON us.nrec = te.NAME
LEFT JOIN gal_U_SETTLEMENTS adr1 ON us.CCITY = adr1.nrec -- нас пункт
LEFT JOIN gal_U_COUNTRY tuc ON us.CCOUNTRY = tuc.nrec -- страна
LEFT JOIN gal_U_COUNTRY tuc1 ON us.CREGION = tuc1.nrec -- регион
LEFT JOIN gal_CATALOGS tc1 ON tc1.CODE = te.SEQNMB AND tc1.CPARENT = 0x80000000000002D6 -- код документа об образовании

-- Адрес УЗ 2 страница карточки
LEFT JOIN gal_ADDRESSN uz ON uz.nrec = te.LEARNADDR
left join gal_sterr uz1 on uz1.nrec = uz.CSTERR
left join gal_sterr uz2 on uz2.nrec = uz1.CPARENT
left join gal_sterr uz3 on uz3.nrec = uz2.CPARENT
left join gal_sterr uz4 on uz4.nrec = uz3.CPARENT
left join gal_sterr uz5 on uz5.nrec = uz4.CPARENT
left join gal_sterr uz6 on uz6.nrec = uz5.CPARENT

-- Адреса личные
LEFT JOIN gal_ADDRESSN born ON tp.BORNADDR = born.nrec -- место рождения
left join gal_ADDRESSN live on tp.LIVEADDR = live.nrec -- место проживания
left join gal_ADDRESSN pass on tp.PASSPADDR = pass.nrec -- место прописки
left join gal_ADDRESSN vrem on vrem.CPERSON = tp.nrec and vrem.OBJTYPE = 55 -- место вр.регистрации

-- место рождения
left join gal_sterr ab on ab.nrec = born.CSTERR
left join gal_sterr ab1 on ab1.nrec = ab.CPARENT
left join gal_sterr ab2 on ab2.nrec = ab1.CPARENT
left join gal_sterr ab3 on ab3.nrec = ab2.CPARENT
left join gal_sterr ab4 on ab4.nrec = ab3.CPARENT
left join gal_sterr ab5 on ab5.nrec = ab4.CPARENT

-- прописка
left join gal_sterr ter on ter.nrec = pass.CSTERR
left join gal_sterr ter1 on ter1.nrec = ter.CPARENT
left join gal_sterr ter2 on ter2.nrec = ter1.CPARENT
left join gal_sterr ter3 on ter3.nrec = ter2.CPARENT
left join gal_sterr ter4 on ter4.nrec = ter3.CPARENT
left join gal_sterr ter41 on ter41.nrec = ter4.CPARENT

-- проживание
left join gal_sterr ter10 on ter10.nrec = live.CSTERR
left join gal_sterr ter11 on ter11.nrec = ter10.CPARENT
left join gal_sterr ter12 on ter12.nrec = ter11.CPARENT
left join gal_sterr ter13 on ter13.nrec = ter12.CPARENT
left join gal_sterr ter14 on ter14.nrec = ter13.CPARENT
left join gal_sterr ter141 on ter141.nrec = ter14.CPARENT

-- временная регистрация
left join gal_sterr ter20 on ter20.nrec = vrem.CSTERR
left join gal_sterr ter21 on ter21.nrec = ter20.CPARENT
left join gal_sterr ter22 on ter22.nrec = ter21.CPARENT
left join gal_sterr ter23 on ter23.nrec = ter22.CPARENT
left join gal_sterr ter24 on ter24.nrec = ter23.CPARENT
left join gal_sterr ter25 on ter25.nrec = ter24.CPARENT

WHERE tus.warch = 0
 AND tp.nrec = 0x" . bin2hex($galId);


        $data = Yii::app()->db2->createCommand($sql)->queryRow();

        $inn = Yii::app()->db2->createCommand()
            ->select('inn.nrec nrec, inn.nmb innNmb')
            ->from('gal_passports inn')
            ->where('inn.person = :id AND inn.docname = 0x8000000000000227', [':id' => $galId])
            ->order('inn.nrec')
            ->limit(1)
            ->queryRow();

        $snils = Yii::app()->db2->createCommand()
            ->select('snils.nrec nrec, snils.nmb snilsNmb')
            ->from('gal_passports snils')
            ->where('snils.person = :id AND snils.docname = 0x8000000000000223', [':id' => $galId])
            ->order('snils.nrec')
            ->limit(1)
            ->queryRow();


        $medPolicy = Yii::app()->db2->createCommand()
            ->select('medPolicy.nrec nrec, medPolicy.nmb medPolicyNmb,
                  medPolicy.givenby medPolicyGivenby,
                  medPolicy.givendate medPolicyGivendate,
                  medPolicy.todate medPolicyTodate')
            ->from('gal_passports medPolicy')
            ->where('medPolicy.person = :id AND medPolicy.docname = 0x8001000000002695', [':id' => $galId])
            ->order('medPolicy.nrec')
            ->limit(1)
            ->queryRow();

        $socialProtection = Yii::app()->db2->createCommand()
            ->select('socialProtection.nrec nrec, socialProtection.nmb socialProtectionNmb,
                  socialProtection.givenby socialProtectionGivenby,
                  socialProtection.givendate socialProtectionGivendate,
                  socialProtection.todate socialProtectionTodate')
            ->from('gal_passports socialProtection')
            ->where('socialProtection.person = :id AND socialProtection.docname = 0x8000000000000228', [':id' => $galId])
            ->order('socialProtection.nrec')
            ->limit(1)
            ->queryRow();

        $residence = Yii::app()->db2->createCommand()
            ->select('residence.nrec nrec, residence.nmb residenceNmb,                  
                  residence.givendate residenceGivendate,
                  residence.todate residenceTodate')
            ->from('gal_passports residence')
            ->where('residence.person = :id AND residence.docname = 0x8001000000001BA4', [':id' => $galId])
            ->order('residence.nrec')
            ->limit(1)
            ->queryRow();

        $migration = Yii::app()->db2->createCommand()
            ->select('migration.nrec nrec, migration.nmb migrationNmb,                  
                  migration.givendate migrationGivendate,
                  migration.todate migrationTodate')
            ->from('gal_passports migration')
            ->where('migration.person = :id AND migration.docname = 0x8001000000002773', [':id' => $galId])
            ->order('migration.nrec')
            ->limit(1)
            ->queryRow();


        $linkType = ($data['sex'] == 'М') ? '8001000000002C08' : '80000000000015E7';

        $husbandWife = Yii::app()->db2->createCommand()
            ->select('
                    p.nrec nrec,
                    p.rfio fio,
                    p.rborndate borndate,
                    p.str1 phone,  
                    case
when ter10.wtype = 0 then a.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(\', \', ter10.SNAME , a.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(\', \', ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(\', \', ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(\', \', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(\', \', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
 END addr                 
                  ')
            ->from('gal_psnlinks p')
            ->leftJoin('gal_catalogs c', 'c.nrec = p.linktype')
            ->leftJoin('gal_addressn a', 'p.rpassaddr = a.nrec')
            ->leftJoin('gal_sterr ter10', 'ter10.nrec = a.CSTERR')
            ->leftJoin('gal_sterr ter11', 'ter11.nrec = ter10.CPARENT')
            ->leftJoin('gal_sterr ter12', 'ter12.nrec = ter11.CPARENT')
            ->leftJoin('gal_sterr ter13', 'ter13.nrec = ter12.CPARENT')
            ->leftJoin('gal_sterr ter14', 'ter14.nrec = ter13.CPARENT')
            ->leftJoin('gal_sterr ter141', 'ter141.nrec = ter14.CPARENT')
            ->where('p.fromperson = :id AND p.linktype = :link', [':id' => $galId, ':link' => hex2bin($linkType)])
            ->order('p.nrec DESC')
            ->limit(1)
            ->queryRow();


        $mother = Yii::app()->db2->createCommand()
            ->select('p.nrec nrec, p.rfio fio,
                    p.rborndate borndate,
                    p.str1 phone,  
                    case
when ter10.wtype = 0 then a.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(\', \', ter10.SNAME , a.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(\', \', ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(\', \', ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(\', \', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(\', \', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
 END addr                 
                  ')
            ->from('gal_psnlinks p')
            ->leftJoin('gal_catalogs c', 'c.nrec = p.linktype')
            ->leftJoin('gal_addressn a', 'p.rpassaddr = a.nrec')
            ->leftJoin('gal_sterr ter10', 'ter10.nrec = a.CSTERR')
            ->leftJoin('gal_sterr ter11', 'ter11.nrec = ter10.CPARENT')
            ->leftJoin('gal_sterr ter12', 'ter12.nrec = ter11.CPARENT')
            ->leftJoin('gal_sterr ter13', 'ter13.nrec = ter12.CPARENT')
            ->leftJoin('gal_sterr ter14', 'ter14.nrec = ter13.CPARENT')
            ->leftJoin('gal_sterr ter141', 'ter141.nrec = ter14.CPARENT')
            ->where('p.fromperson = :id AND p.linktype = 0x80000000000015E5', [':id' => $galId])
            ->order('p.nrec DESC')
            ->limit(1)
            ->queryRow();


        $father = Yii::app()->db2->createCommand()
            ->select('p.nrec, p.rfio fio,
                    p.rborndate borndate,
                    p.str1 phone,  
                    case
when ter10.wtype = 0 then a.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(\', \', ter10.SNAME , a.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(\', \', ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(\', \', ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(\', \', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(\', \', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
 END addr                 
                  ')
            ->from('gal_psnlinks p')
            ->leftJoin('gal_catalogs c', 'c.nrec = p.linktype')
            ->leftJoin('gal_addressn a', 'p.rpassaddr = a.nrec')
            ->leftJoin('gal_sterr ter10', 'ter10.nrec = a.CSTERR')
            ->leftJoin('gal_sterr ter11', 'ter11.nrec = ter10.CPARENT')
            ->leftJoin('gal_sterr ter12', 'ter12.nrec = ter11.CPARENT')
            ->leftJoin('gal_sterr ter13', 'ter13.nrec = ter12.CPARENT')
            ->leftJoin('gal_sterr ter14', 'ter14.nrec = ter13.CPARENT')
            ->leftJoin('gal_sterr ter141', 'ter141.nrec = ter14.CPARENT')
            ->where('p.fromperson = :id AND p.linktype = 0x80000000000015E6', [':id' => $galId])
            ->order('p.nrec DESC')
            ->limit(1)
            ->queryRow();

        $kinder = Yii::app()->db2->createCommand()
            ->select('p.nrec, p.rfio fio,
                    p.rborndate borndate,
                    p.str1 phone,  
                    case
when ter10.wtype = 0 then a.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(\', \', ter10.SNAME , a.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(\', \', ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(\', \', ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(\', \', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(\', \', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
 END addr                
                  ')
            ->from('gal_psnlinks p')
            ->leftJoin('gal_catalogs c', 'c.nrec = p.linktype')
            ->leftJoin('gal_addressn a', 'p.rpassaddr = a.nrec')
            ->leftJoin('gal_sterr ter10', 'ter10.nrec = a.CSTERR')
            ->leftJoin('gal_sterr ter11', 'ter11.nrec = ter10.CPARENT')
            ->leftJoin('gal_sterr ter12', 'ter12.nrec = ter11.CPARENT')
            ->leftJoin('gal_sterr ter13', 'ter13.nrec = ter12.CPARENT')
            ->leftJoin('gal_sterr ter14', 'ter14.nrec = ter13.CPARENT')
            ->leftJoin('gal_sterr ter141', 'ter141.nrec = ter14.CPARENT')
            ->where('p.fromperson = :id AND p.linktype = 0x80000000000002CE', [':id' => $galId])
            ->order('p.nrec DESC')
            ->limit(3)
            ->queryAll();


        $dataForEdit = GalStudentPersonalcard::model()->find('pnrec = :pnrec', ['pnrec' => $data['pnrec']]);
        if (!$dataForEdit instanceof GalStudentPersonalcard) {
            $model = new GalStudentPersonalcard();
            $model->pnrec = $data['pnrec'];
            $model->save();
            $dataForEdit = $model;
        }
        $dataForEdit->innFromDB = $inn['nrec'];
        $dataForEdit->snilsFromDB = $snils['nrec'];
        $dataForEdit->medPolicyFromDB = $medPolicy['nrec'];
        $dataForEdit->socialProtectionFromDB = $socialProtection['nrec'];
        $dataForEdit->motherFromDB = $mother['nrec'];
        $dataForEdit->fatherFromDB = $father['nrec'];
        $dataForEdit->residenceFromDB = $residence['nrec'];
        $dataForEdit->migrationFromDB = $migration['nrec'];
        $dataForEdit->husbandWifeFromDB = $husbandWife['nrec'];
        $dataForEdit->passFromDB = $data['passNrec'];
        isset($kinder[0]) ? ($dataForEdit->kinder1FromDB = $kinder[0]['nrec']) : '';
        isset($kinder[1]) ? ($dataForEdit->kinder2FromDB = $kinder[1]['nrec']) : '';
        isset($kinder[2]) ? ($dataForEdit->kinder3FromDB = $kinder[2]['nrec']) : '';
        $dataForEdit->save();


        $dataForEdit->pnrec = bin2hex($dataForEdit->pnrec);


        foreach ($dataForEdit as $field => $value) {
            if (stripos($field, 'date') > 0 && $value) {
                $dataForEdit->$field = date('d.m.Y', strtotime($value));
            }
        }

        foreach ($dataForEdit as $field => $value) {
            try {
                if (mb_stripos($field, 'Manual', null, Yii::app()->charset) > 0 && $value) {
                    $val = bin2hex($value);


                    if (mb_stripos($val, '8000', null, Yii::app()->charset) === 0 || mb_stripos($val, '8001', null, Yii::app()->charset) === 0) {
                        $dataForEdit->$field = $val;

                    }
                }
            } catch (Exception $e) {

            }
        }

        $fnpp = Yii::app()->user->getFnpp();
        $reconciliation = Yii::app()->db2->createCommand()
            ->select('id')
            ->from('change_fio_applications')
            ->where(['and', 'fnpp = ' . $fnpp, 'order_nrec is null'])
            ->order('id desc')
            ->queryScalar();

        $gal_person = Person::model()->find('nrec = :nrec', [':nrec' => $data['pnrec']]);
        if ($gal_person instanceof Person) {
            $isConfirmed = $gal_person->ddop1;
            $dateConfirmed = CMisc::fromGalDate($gal_person->ddop2);
        } else {
            $isConfirmed = null;
            $dateConfirmed = null;
        }

        $this->layout = '//layouts/column1';
        $this->render('index', array(
            'data' => $data,
            'inn' => $inn,
            'snils' => $snils,
            'medPolicy' => $medPolicy,
            'residence' => $residence,
            'migration' => $migration,
            'husbandWife' => $husbandWife,
            'mother' => $mother,
            'father' => $father,
            'kinder' => $kinder,
            'socialProtection' => $socialProtection,
            'dataForEdit' => $dataForEdit,
            'isConfirmed' => $isConfirmed,
            'dateConfirmed' => $dateConfirmed,
            'id' => $galId,
            'message' => $message,
            'reconciliation' => $reconciliation,
            'post' => array_key_exists(0, $_GET) ? $_GET[0] : [],
        ));
    }

    public function actionUpdateFieldCheckRight()
    {
        $result = null;
        if (isset($_POST)) {
            foreach ($_POST as $key => $value) {
                $data = GalStudentPersonalcard::model()->find('pnrec = :pnrec', ['pnrec' => hex2bin($key)]);
                if ($data instanceof GalStudentPersonalcard) {
                    $fieldFromModel = $value . 'IsRight';
                    $data->$fieldFromModel = ($data->$fieldFromModel == 0) ? 1 : 0;
                    if ($data->save()) {
                        $result = $data->$fieldFromModel;
                    }
                }
            }

        }

        echo CJSON::encode(array('success' => $result));
    }

    public function actionUpdateDataField()
    {
        $result = null;

        if (isset($_POST['GalStudentPersonalcard'])) {
            $pnrec = null;
            $type = null;
            $dataField = null;
            $dataFieldName = null;
            foreach ($_POST['GalStudentPersonalcard'] as $key => $value) {
                if ($key == 'pnrec') {
                    $pnrec = $value;
                } elseif ($key == 'type') {
                    $type = $value;
                } else {
                    $dataField = $value;
                    $dataFieldName = $key;
                }
            }

            if ($pnrec) {
                $model = GalStudentPersonalcard::model()->find('pnrec = :pnrec', ['pnrec' => hex2bin($pnrec)]);
                if ($model instanceof GalStudentPersonalcard) {
                    if (!$dataField) {
                        $model->$dataFieldName = null;
                    } else {
                        if ($type == 'date') {
                            $model->$dataFieldName = date("Y-m-d", strtotime($dataField));
                        } elseif ($type == 'nrec') {
                            $model->$dataFieldName = hex2bin($dataField);
                        } else {
                            $model->$dataFieldName = $dataField;
                        }
                    }

                    $result = $model->save();
                }
            }


        }

        echo CJSON::encode(array('success' => $result));
    }

    public function actionSchool($term)
    {
        $school = Yii::app()->db2->createCommand()
            ->select('HEX(u.nrec) value,                  
                  CONCAT(u.sname, \' (\', c.name, \', \', r.name, \', \', s.name, \')\') label')
            ->from('gal_u_school u')
            ->leftJoin('gal_u_country c', 'u.ccountry = c.nrec')
            ->leftJoin('gal_u_country r', 'u.cregion = r.nrec')
            ->leftJoin('gal_u_settlements s', 'u.ccity = s.nrec')
            ->where('LENGTH(u.sname) > 4')
            ->order('u.sname')
            ->queryAll();

        $maxCnt = 30;
        $result = array();
        foreach ($school as $row) {
            if (mb_strstr(mb_strtolower($row['label'], Yii::app()->charset), mb_strtolower($term, Yii::app()->charset), false, Yii::app()->charset)) {
                $result[] = $row;
                if (--$maxCnt < 0) {
                    break;
                }
            }
        }
        echo CJSON::encode($result);
    }

    public function actionEduDoc($term)
    {
        $edu = Yii::app()->db2->createCommand()
            ->select('HEX(c.nrec) value,                  
                  c.name label')
            ->from('gal_catalogs c')
            ->where('c.mainlink = 0x80000000000002D6')
            ->queryAll();

        $maxCnt = 30;
        $result = array();
        foreach ($edu as $row) {
            if (mb_strstr(mb_strtolower($row['label'], Yii::app()->charset), mb_strtolower($term, Yii::app()->charset), false, Yii::app()->charset)) {
                $result[] = $row;
                if (--$maxCnt < 0) {
                    break;
                }
            }
        }
        echo CJSON::encode($result);
    }

    public function actionEduLevel($term)
    {
        $edu = Yii::app()->db2->createCommand()
            ->select('HEX(c.nrec) value,                  
                  c.name label')
            ->from('gal_catalogs c')
            ->where('c.cparent = 0x80000000000002E2 and c.mainlink = 0x8000000000000200')
            ->queryAll();
        foreach ($edu as $row) {
            $eduparent = Yii::app()->db2->createCommand()
                ->select('HEX(c.nrec) value,                  
                  c.name label')
                ->from('gal_catalogs c')
                ->where('c.cparent = ' . CMisc::_id($row['value']))
                ->queryAll();
            $edu = array_merge($edu, $eduparent);
        }

        $maxCnt = 30;
        $result = array();
        foreach ($edu as $row) {
            if (mb_strstr(mb_strtolower($row['label'], Yii::app()->charset), mb_strtolower($term, Yii::app()->charset), false, Yii::app()->charset)) {
                $result[] = $row;
                if (--$maxCnt < 0) {
                    break;
                }
            }
        }
        echo CJSON::encode($result);
    }

    public function actionAddress($term)
    {
        $edu = Yii::app()->db2->createCommand()
            ->select('HEX(s.nrec) value,                  
                  case
when s.wtype = 0 then s.sname
when s2.wtype = 0 then CONCAT_WS(\', \', s1.SNAME , s.SNAME)
when s3.wtype = 0 then CONCAT_WS(\', \', s2.SNAME , s1.SNAME , s.SNAME)
when s4.wtype = 0 then CONCAT_WS(\', \', s3.SNAME , s2.SNAME , s1.SNAME , s.SNAME)
when s5.wtype = 0 then CONCAT_WS(\', \', s4.SNAME , s3.SNAME , s2.SNAME , s1.SNAME , s.SNAME)
 END label')
            ->from('gal_sterr s')
            ->leftJoin('gal_sterr s1', 's.cparent = s1.nrec')
            ->leftJoin('gal_sterr s2', 's1.cparent = s2.nrec')
            ->leftJoin('gal_sterr s3', 's2.cparent = s3.nrec')
            ->leftJoin('gal_sterr s4', 's3.cparent = s4.nrec')
            ->leftJoin('gal_sterr s5', 's4.cparent = s5.nrec')
            ->where('s.wtype != 7 and s1.wtype != 7 and s2.wtype != 7 and s3.wtype != 7 and s4.wtype != 7 and s5.wtype != 7
            and s.sname LIKE \'%' . $term . '%\'  and s1.sname LIKE \'%' . $term . '%\' and s2.sname LIKE \'%' . $term . '%\' and s3.sname LIKE \'%' . $term . '%\' and s4.sname LIKE \'%' . $term . '%\' and s5.sname LIKE \'%' . $term . '%\'
             ')
            ->queryAll();


        echo CJSON::encode($edu);
    }

    public function actionGr($term)
    {
        $edu = Yii::app()->db2->createCommand()
            ->select('HEX(c.nrec) value,                  
                  c.name label')
            ->from('gal_catalogs c')
            ->where('c.mainlink = 0x80000000000001E6')
            ->queryAll();

        $maxCnt = 30;
        $result = array();
        foreach ($edu as $row) {
            if (mb_strstr(mb_strtolower($row['label'], Yii::app()->charset), mb_strtolower($term, Yii::app()->charset), false, Yii::app()->charset)) {
                $result[] = $row;
                if (--$maxCnt < 0) {
                    break;
                }
            }
        }
        echo CJSON::encode($result);
    }

    public function actionPassport($term)
    {
        $edu = Yii::app()->db2->createCommand()
            ->select('HEX(c.nrec) value,                  
                  c.name label')
            ->from('gal_catalogs c')
            ->where('c.MAINLINK = 0x8000000000000220 AND c.BMULTI = 1')
            ->queryAll();

        $maxCnt = 30;
        $result = array();
        foreach ($edu as $row) {
            if (mb_strstr(mb_strtolower($row['label'], Yii::app()->charset), mb_strtolower($term, Yii::app()->charset), false, Yii::app()->charset)) {
                $result[] = $row;
                if (--$maxCnt < 0) {
                    break;
                }
            }
        }
        echo CJSON::encode($result);
    }

    public function actionPrint($id = null)
    {
        $galId = Yii::app()->user->getGalId();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if (!isset($_SESSION)) {
            session_start();
        }
        $galIdMassive = Yii::app()->user->getGalIdMass();
        if (isset($id)) {
            $prov = 0;
            foreach ($galIdMassive as $galunid) {
                if ($galunid['Galid'] == $id) {
                    $prov = 1;
                }
            }
            if ($prov == 1) {
                $galId = $id;
                $_SESSION['ID'] = $id;
            }
        } else {
            if (isset($_SESSION['ID'])) {
                $galId = $_SESSION['ID'];
            } else {
                $_SESSION['ID'] = $galId;
            }
        }

        $sql = "SELECT
CONCAT(fac.LONGNAME, ', ', tus.SDEPARTMENT, ', ', CASE WHEN bup.wformed = 0 THEN 'Очная'
 WHEN bup.wformed = 1 THEN 'Заочная'
 WHEN bup.wformed = 2 THEN 'Вечерняя'
 end) AS 'placeOfStudy',

 tp.FIO AS 'fio',
 tp.nrec AS 'pnrec',
 CONCAT(spec.code, ' ', spec.name) AS 'spec',
 ts.name AS 'fin',
td.`sfld#1#` as 'contNmb', 
td.`DFLD#1#` as 'contBegin',
cel.`SFLD#1#` AS 'entName',
tp.sex AS 'sex',
tp.borndate AS 'borndate',
 gr.name AS 'gr',
 passtype.name AS 'passVid',
 tp1.nrec as 'passNrec',
  tp1.ser AS 'pser',
  tp1.nmb AS 'pnmb',
  tp1.givenby AS 'givenby',
tp1.givendate AS 'givendate',
  tp1.todate AS 'todate',
  tp1.givenpodr AS 'givenpodr',
  edu.name AS 'eduLevel',
  tc.name as 'eduDoc',
  te.series AS 'eduSeria',
  te.DIPLOMNMB AS 'eduNmb',
  te.DIPLOMDATE as 'eduDipDate',
  tus1.SNAME AS 'eduPlace',
  fam.name as 'familystate',
-- rup.COURSE 'Курс',
 
case
when uz1.wtype = 0 then uz.SADDRESS1
when uz2.wtype = 0 then CONCAT_WS(', ', uz1.SNAME, uz.SADDRESS1)
when uz3.wtype = 0 then CONCAT_WS(', ', uz2.SNAME, uz1.SNAME , uz.SADDRESS1)
when uz4.wtype = 0 then CONCAT_WS(', ', uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
when uz5.wtype = 0 then CONCAT_WS(', ', uz4.SNAME , uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
WHEN uz6.wtype = 0 then CONCAT_WS(', ', uz5.SNAME , uz4.SNAME , uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
 END 'eduAddr',


case
when ab1.wtype = 0 then ab.SNAME
when ab2.wtype = 0 then CONCAT_WS(', ', ab1.SNAME , ab.SNAME)
when ab3.wtype = 0 then CONCAT_WS(', ', ab2.SNAME , ab1.SNAME , ab.SNAME)
when ab4.wtype = 0 then CONCAT_WS(', ', ab3.SNAME , ab2.SNAME , ab1.SNAME , ab.SNAME)
when ab5.wtype = 0 then CONCAT_WS(', ', ab4.SNAME , ab3.SNAME , ab2.SNAME , ab1.SNAME , ab.SNAME)
 END 'bornAddr',

case
when ter.wtype = 0 then pass.SADDRESS1
when ter1.wtype = 0 then CONCAT_WS(', ', ter.SNAME , pass.SADDRESS1)
when ter2.wtype = 0 then CONCAT_WS(', ', ter1.SNAME , ter.SNAME , pass.SADDRESS1)
when ter3.wtype = 0 then CONCAT_WS(', ', ter2.SNAME , ter1.SNAME , ter.SNAME , pass.SADDRESS1)
when ter4.wtype = 0 then CONCAT_WS(', ', ter3.SNAME , ter2.SNAME , ter1.SNAME , ter.SNAME , pass.SADDRESS1)
when ter41.wtype = 0 then CONCAT_WS(', ', ter4.SNAME , ter3.SNAME , ter2.SNAME , ter1.SNAME , ter.SNAME , pass.SADDRESS1)
 END 'passAddr',

case
when ter10.wtype = 0 then live.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(', ', ter10.SNAME , live.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(', ', ter11.SNAME , ter10.SNAME , live.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(', ', ter12.SNAME , ter11.SNAME , ter10.SNAME , live.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(', ', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , live.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(', ', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , live.SADDRESS1)
 END 'liveAddr', 


case
when ter20.wtype = 0 then vrem.SADDRESS1
when ter21.wtype = 0 then CONCAT_WS(', ', ter20.SNAME , vrem.SADDRESS1)
when ter22.wtype = 0 then CONCAT_WS(', ', ter21.SNAME , ter20.SNAME , vrem.SADDRESS1)
when ter23.wtype = 0 then CONCAT_WS(', ', ter22.SNAME , ter21.SNAME , ter20.SNAME , vrem.SADDRESS1)
when ter24.wtype = 0 then CONCAT_WS(', ', ter23.SNAME , ter22.SNAME , ter21.SNAME , ter20.SNAME , vrem.SADDRESS1)
when ter25.wtype = 0 then CONCAT_WS(', ', ter24.SNAME , ter23.SNAME , ter22.SNAME , ter21.SNAME , ter20.SNAME , vrem.SADDRESS1)
 END 'tempAddr',
 
 (SELECT
 GROUP_CONCAT(tel1.ADDR )
 FROM gal_COMMUNICATIONS tel1 WHERE tp.nrec = tel1.PERSON and tel1.COMTYPE in (0x800000000000022A, 0x800000000000022B, 0x80000000000003A7)) as 'phone',

 (SELECT  GROUP_CONCAT(mail.EMAIL)  FROM gal_COMMUNICATIONS mail WHERE tp.nrec = mail.PERSON and mail.COMTYPE = 0x80000000000003A6) as 'email'



FROM gal_PERSONS tp
LEFT JOIN gal_U_STUDENT tus on tus.CPERSONS = tp.nrec
LEFT JOIN gal_CATALOGS gr on gr.nrec = tp.GR
LEFT JOIN gal_PASSPORTS tp1 ON tp1.nrec = tp.passprus
LEFT JOIN gal_CATALOGS fam ON fam.nrec = tp.familystate
LEFT JOIN gal_CATALOGS passtype ON passtype.nrec = tp1.DOCNAME
LEFT JOIN gal_EDUCATION te ON te.PERSON = tp.nrec AND te.IATTR = 1
LEFT JOIN gal_U_SCHOOL tus1 on tus1.nrec = te.NAME
LEFT JOIN gal_CATALOGS edu ON edu.nrec = te.LEVEL
LEFT JOIN gal_CATALOGS tc ON te.seqnmb = tc.CODE AND tc.CPARENT = 0x80000000000002D6
LEFT JOIN gal_APPOINTMENTS ta ON ta.nrec = CASE WHEN tp.APPOINTCUR = 0x8000000000000000 THEN tp.APPOINTLAST ELSE tp.APPOINTCUR END
LEFT JOIN gal_U_STUD_FINSOURCE tusf ON tusf.nrec = ta.CREF2 -- ист.финанс
LEFT JOIN gal_SPKAU ts ON ts.nrec = tusf.CFINSOURCE -- ИФ APPOINTMENTS
LEFT JOIN gal_STAFFSTRUCT st on ta.STAFFSTR = st.nrec
LEFT JOIN gal_U_CURRICULUM bup on bup.nrec = st.CSTR
-- LEFT JOIN gal_U_CURRICULUM rup ON rup.nrec = ta.CDOPINF
LEFT JOIN gal_CATALOGS spec ON spec.nrec = bup.CSPECIALITY
LEFT JOIN gal_CATALOGS fac ON fac.nrec = bup.CFACULTY
LEFT JOIN gal_DOPINFO td ON td.nrec = (SELECT cont.nrec from gal_dopinfo cont WHERE cont.CPERSON = tp.nrec AND cont.CDOPTBL = 0x8001000000000007 ORDER BY cont.`DFLD#1#` DESC LIMIT 1)
LEFT JOIN gal_DOPINFO za4 ON za4.CPERSON = tp.nrec AND za4.CDOPTBL = 0x8001000000000003
LEFT JOIN gal_DOPINFO cel ON cel.CPERSON = tp.nrec AND cel.CDOPTBL = 0x8001000000000001
LEFT JOIN gal_CATALOGS opk ON opk.nrec = cel.`CFLD#1#` AND opk.CPARENT = 0x8001000000002697

-- Учебное заведение
LEFT JOIN gal_U_SCHOOL us ON us.nrec = te.NAME
LEFT JOIN gal_U_SETTLEMENTS adr1 ON us.CCITY = adr1.nrec -- нас пункт
LEFT JOIN gal_U_COUNTRY tuc ON us.CCOUNTRY = tuc.nrec -- страна
LEFT JOIN gal_U_COUNTRY tuc1 ON us.CREGION = tuc1.nrec -- регион
LEFT JOIN gal_CATALOGS tc1 ON tc1.CODE = te.SEQNMB AND tc1.CPARENT = 0x80000000000002D6 -- код документа об образовании

-- Адрес УЗ 2 страница карточки
LEFT JOIN gal_ADDRESSN uz ON uz.nrec = te.LEARNADDR
left join gal_sterr uz1 on uz1.nrec = uz.CSTERR
left join gal_sterr uz2 on uz2.nrec = uz1.CPARENT
left join gal_sterr uz3 on uz3.nrec = uz2.CPARENT
left join gal_sterr uz4 on uz4.nrec = uz3.CPARENT
left join gal_sterr uz5 on uz5.nrec = uz4.CPARENT
left join gal_sterr uz6 on uz6.nrec = uz5.CPARENT

-- Адреса личные
LEFT JOIN gal_ADDRESSN born ON tp.BORNADDR = born.nrec -- место рождения
left join gal_ADDRESSN live on tp.LIVEADDR = live.nrec -- место проживания
left join gal_ADDRESSN pass on tp.PASSPADDR = pass.nrec -- место прописки
left join gal_ADDRESSN vrem on vrem.CPERSON = tp.nrec and vrem.OBJTYPE = 55 -- место вр.регистрации

-- место рождения
left join gal_sterr ab on ab.nrec = born.CSTERR
left join gal_sterr ab1 on ab1.nrec = ab.CPARENT
left join gal_sterr ab2 on ab2.nrec = ab1.CPARENT
left join gal_sterr ab3 on ab3.nrec = ab2.CPARENT
left join gal_sterr ab4 on ab4.nrec = ab3.CPARENT
left join gal_sterr ab5 on ab5.nrec = ab4.CPARENT

-- прописка
left join gal_sterr ter on ter.nrec = pass.CSTERR
left join gal_sterr ter1 on ter1.nrec = ter.CPARENT
left join gal_sterr ter2 on ter2.nrec = ter1.CPARENT
left join gal_sterr ter3 on ter3.nrec = ter2.CPARENT
left join gal_sterr ter4 on ter4.nrec = ter3.CPARENT
left join gal_sterr ter41 on ter41.nrec = ter4.CPARENT

-- проживание
left join gal_sterr ter10 on ter10.nrec = live.CSTERR
left join gal_sterr ter11 on ter11.nrec = ter10.CPARENT
left join gal_sterr ter12 on ter12.nrec = ter11.CPARENT
left join gal_sterr ter13 on ter13.nrec = ter12.CPARENT
left join gal_sterr ter14 on ter14.nrec = ter13.CPARENT
left join gal_sterr ter141 on ter141.nrec = ter14.CPARENT

-- временная регистрация
left join gal_sterr ter20 on ter20.nrec = vrem.CSTERR
left join gal_sterr ter21 on ter21.nrec = ter20.CPARENT
left join gal_sterr ter22 on ter22.nrec = ter21.CPARENT
left join gal_sterr ter23 on ter23.nrec = ter22.CPARENT
left join gal_sterr ter24 on ter24.nrec = ter23.CPARENT
left join gal_sterr ter25 on ter25.nrec = ter24.CPARENT

WHERE tus.warch = 0
 AND tp.nrec = 0x" . bin2hex($galId);

        $data = Yii::app()->db2->createCommand($sql)->queryRow();

        $inn = Yii::app()->db2->createCommand()
            ->select('inn.nmb innNmb')
            ->from('gal_passports inn')
            ->where('inn.person = :id AND inn.docname = 0x8000000000000227', [':id' => $galId])
            ->order('inn.nrec')
            ->limit(1)
            ->queryRow();

        $snils = Yii::app()->db2->createCommand()
            ->select('snils.nmb snilsNmb')
            ->from('gal_passports snils')
            ->where('snils.person = :id AND snils.docname = 0x8000000000000223', [':id' => $galId])
            ->order('snils.nrec')
            ->limit(1)
            ->queryRow();


        $medPolicy = Yii::app()->db2->createCommand()
            ->select('medPolicy.nmb medPolicyNmb,
                  medPolicy.givenby medPolicyGivenby,
                  medPolicy.givendate medPolicyGivendate,
                  medPolicy.todate medPolicyTodate')
            ->from('gal_passports medPolicy')
            ->where('medPolicy.person = :id AND medPolicy.docname = 0x8001000000002695', [':id' => $galId])
            ->order('medPolicy.nrec')
            ->limit(1)
            ->queryRow();

        $socialProtection = Yii::app()->db2->createCommand()
            ->select('socialProtection.nmb socialProtectionNmb,
                  socialProtection.givenby socialProtectionGivenby,
                  socialProtection.givendate socialProtectionGivendate,
                  socialProtection.todate socialProtectionTodate')
            ->from('gal_passports socialProtection')
            ->where('socialProtection.person = :id AND socialProtection.docname = 0x8000000000000228', [':id' => $galId])
            ->order('socialProtection.nrec')
            ->limit(1)
            ->queryRow();

        $residence = Yii::app()->db2->createCommand()
            ->select('residence.nmb residenceNmb,                  
                  residence.givendate residenceGivendate,
                  residence.todate residenceTodate')
            ->from('gal_passports residence')
            ->where('residence.person = :id AND residence.docname = 0x8001000000001BA4', [':id' => $galId])
            ->order('residence.nrec')
            ->limit(1)
            ->queryRow();

        $migration = Yii::app()->db2->createCommand()
            ->select('migration.nmb migrationNmb,                  
                  migration.givendate migrationGivendate,
                  migration.todate migrationTodate')
            ->from('gal_passports migration')
            ->where('migration.person = :id AND migration.docname = 0x8001000000002773', [':id' => $galId])
            ->order('migration.nrec')
            ->limit(1)
            ->queryRow();

        $linkType = ($data['sex'] == 'М') ? '8001000000002C08' : '80000000000015E7';

        $husbandWife = Yii::app()->db2->createCommand()
            ->select('
                    p.nrec nrec,
                    p.rfio fio,
                    p.rborndate borndate,
                    p.str1 phone,  
                    case
when ter10.wtype = 0 then a.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(\', \', ter10.SNAME , a.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(\', \', ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(\', \', ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(\', \', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(\', \', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
 END addr                 
                  ')
            ->from('gal_psnlinks p')
            ->leftJoin('gal_catalogs c', 'c.nrec = p.linktype')
            ->leftJoin('gal_addressn a', 'p.rpassaddr = a.nrec')
            ->leftJoin('gal_sterr ter10', 'ter10.nrec = a.CSTERR')
            ->leftJoin('gal_sterr ter11', 'ter11.nrec = ter10.CPARENT')
            ->leftJoin('gal_sterr ter12', 'ter12.nrec = ter11.CPARENT')
            ->leftJoin('gal_sterr ter13', 'ter13.nrec = ter12.CPARENT')
            ->leftJoin('gal_sterr ter14', 'ter14.nrec = ter13.CPARENT')
            ->leftJoin('gal_sterr ter141', 'ter141.nrec = ter14.CPARENT')
            ->where('p.fromperson = :id AND p.linktype = :link', [':id' => $galId, ':link' => hex2bin($linkType)])
            ->order('p.nrec DESC')
            ->limit(1)
            ->queryRow();

        $mother = Yii::app()->db2->createCommand()
            ->select('p.nrec nrec, p.rfio fio,
                    p.rborndate borndate,
                    p.str1 phone,  
                    case
when ter10.wtype = 0 then a.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(\', \', ter10.SNAME , a.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(\', \', ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(\', \', ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(\', \', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(\', \', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
 END addr                 
                  ')
            ->from('gal_psnlinks p')
            ->leftJoin('gal_catalogs c', 'c.nrec = p.linktype')
            ->leftJoin('gal_addressn a', 'p.rpassaddr = a.nrec')
            ->leftJoin('gal_sterr ter10', 'ter10.nrec = a.CSTERR')
            ->leftJoin('gal_sterr ter11', 'ter11.nrec = ter10.CPARENT')
            ->leftJoin('gal_sterr ter12', 'ter12.nrec = ter11.CPARENT')
            ->leftJoin('gal_sterr ter13', 'ter13.nrec = ter12.CPARENT')
            ->leftJoin('gal_sterr ter14', 'ter14.nrec = ter13.CPARENT')
            ->leftJoin('gal_sterr ter141', 'ter141.nrec = ter14.CPARENT')
            ->where('p.fromperson = :id AND p.linktype = 0x80000000000015E5', [':id' => $galId])
            ->order('p.nrec DESC')
            ->limit(1)
            ->queryRow();


        $father = Yii::app()->db2->createCommand()
            ->select('p.nrec, p.rfio fio,
                    p.rborndate borndate,
                    p.str1 phone,  
                    case
when ter10.wtype = 0 then a.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(\', \', ter10.SNAME , a.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(\', \', ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(\', \', ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(\', \', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(\', \', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
 END addr                 
                  ')
            ->from('gal_psnlinks p')
            ->leftJoin('gal_catalogs c', 'c.nrec = p.linktype')
            ->leftJoin('gal_addressn a', 'p.rpassaddr = a.nrec')
            ->leftJoin('gal_sterr ter10', 'ter10.nrec = a.CSTERR')
            ->leftJoin('gal_sterr ter11', 'ter11.nrec = ter10.CPARENT')
            ->leftJoin('gal_sterr ter12', 'ter12.nrec = ter11.CPARENT')
            ->leftJoin('gal_sterr ter13', 'ter13.nrec = ter12.CPARENT')
            ->leftJoin('gal_sterr ter14', 'ter14.nrec = ter13.CPARENT')
            ->leftJoin('gal_sterr ter141', 'ter141.nrec = ter14.CPARENT')
            ->where('p.fromperson = :id AND p.linktype = 0x80000000000015E6', [':id' => $galId])
            ->order('p.nrec DESC')
            ->limit(1)
            ->queryRow();

        $kinder = Yii::app()->db2->createCommand()
            ->select('p.nrec, p.rfio fio,
                    p.rborndate borndate,
                    p.str1 phone,  
                    case
when ter10.wtype = 0 then a.SADDRESS1
when ter11.wtype = 0 then CONCAT_WS(\', \', ter10.SNAME , a.SADDRESS1)
when ter12.wtype = 0 then CONCAT_WS(\', \', ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter13.wtype = 0 then CONCAT_WS(\', \', ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter14.wtype = 0 then CONCAT_WS(\', \', ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
when ter141.wtype = 0 then CONCAT_WS(\', \', ter14.SNAME , ter13.SNAME , ter12.SNAME , ter11.SNAME , ter10.SNAME , a.SADDRESS1)
 END addr                
                  ')
            ->from('gal_psnlinks p')
            ->leftJoin('gal_catalogs c', 'c.nrec = p.linktype')
            ->leftJoin('gal_addressn a', 'p.rpassaddr = a.nrec')
            ->leftJoin('gal_sterr ter10', 'ter10.nrec = a.CSTERR')
            ->leftJoin('gal_sterr ter11', 'ter11.nrec = ter10.CPARENT')
            ->leftJoin('gal_sterr ter12', 'ter12.nrec = ter11.CPARENT')
            ->leftJoin('gal_sterr ter13', 'ter13.nrec = ter12.CPARENT')
            ->leftJoin('gal_sterr ter14', 'ter14.nrec = ter13.CPARENT')
            ->leftJoin('gal_sterr ter141', 'ter141.nrec = ter14.CPARENT')
            ->where('p.fromperson = :id AND p.linktype = 0x80000000000002CE', [':id' => $galId])
            ->order('p.nrec DESC')
            ->limit(3)
            ->queryAll();


        $dataForEdit = GalStudentPersonalcard::model()->find('pnrec = :pnrec', ['pnrec' => $galId]);


        foreach ($dataForEdit as $field => $value) {
            if (stripos($field, 'date') > 0 && $value) {
                $dataForEdit->$field = date('d.m.Y', strtotime($value));
            }
        }

        foreach ($dataForEdit as $field => $value) {
            try {
                if (mb_stripos($field, 'Manual', null, Yii::app()->charset) > 0 && $value) {
                    $val = bin2hex($value);


                    if (mb_stripos($val, '8000', null, Yii::app()->charset) === 0 || mb_stripos($val, '8001', null, Yii::app()->charset) === 0) {
                        $dataForEdit->$field = $val;

                    }
                }
            } catch (Exception $e) {

            }
        }

        isset($kinder[0]) ? ($dataForEdit->kinder1FromDB = $kinder[0]['nrec']) : '';
        isset($kinder[1]) ? ($dataForEdit->kinder2FromDB = $kinder[1]['nrec']) : '';
        isset($kinder[2]) ? ($dataForEdit->kinder3FromDB = $kinder[2]['nrec']) : '';

//        var_dump($dataForEdit);die;
        $this->renderPartial('//pdf/personalcard', array(
            'data' => $data,
            'inn' => $inn,
            'snils' => $snils,
            'medPolicy' => $medPolicy,
            'residence' => $residence,
            'migration' => $migration,
            'husbandWife' => $husbandWife,
            'socialProtection' => $socialProtection,
            'dataForEdit' => $dataForEdit,
            'mother' => $mother,
            'father' => $father,
            'kinder' => $kinder,
        ));
    }

    public function actionDownload()
    {
        $requests = InquiriesRequests::model();
        if (!empty($_POST['InquiriesRequests'])) {
            $requests = $requests->findByPk($_POST['InquiriesRequests']['id']);
            $requests->modifiedAt = date("Y-m-d H:i:s");
            $requests->startDate = date("Y-m-d H:i:s");

            $save_path = InquiriesModule::uploadPath();
            if (!file_exists($save_path)) {
                mkdir($save_path);
            }
            if (!file_exists($save_path . $requests->id)) {
                mkdir($save_path . $requests->id);
            }

            if (!empty($_FILES)) {
                $files = $_FILES['InquiriesRequests'];
                $count_file = count($files['name']);
                $file = $files['name']['filePath'];
                $path = pathinfo($file);
                $filename = $path['filename'];
                $ext = $path['extension'];
                $temp_name = $files['tmp_name']['filePath'];
                $path_filename_ext = $save_path . $requests->id . '/' . $filename . "." . $ext;

                if (!file_exists($path_filename_ext)) {
                    move_uploaded_file($temp_name, $path_filename_ext);
                }
                $requests->filePath = str_replace($save_path, '', $path_filename_ext);
                $requests->update();
                $student = Fdata::model()->findByPk($requests->studentNpp)->getFIO();
                Yii::app()->user->setFlash('success', 'Заявка студента ' . $student . ' успешно обработана и помещена в архив.');

                $this->sendEmailFile($requests);
            }
        }

        $requests->unsetAttributes();
    }

    public function actionChangeFIO()
    {
        //все символы, кроме русских, русский считается прочим символом
        /*$filtr='/[a-z0-9\@\#\№\~\$\%\&\^\(\)\[\]\{\}\*\!\?\<\>\/\\\:\;\`\"\=\-\+\'\|]/i';
        var_dump($_POST['fio'], preg_match($filtr,$_POST['fio']));die;
        if(preg_match($filtr,$_POST['fio']) != 0){
            if (!empty($_POST)) {
                $this->redirect(array('index', $_POST));
            }
        }*/
        $fnpp = Yii::app()->user->getFnpp();
        $new_fio = $_POST['fio'];
        $reason = $_POST['reason'];

        $change_fio_applications = Yii::app()->db2->createCommand("insert into change_fio_applications (fnpp, reason_id, date_create, new_fio) values ($fnpp, $reason, now(), '$new_fio')")
            ->execute();

        $nrec = Yii::app()->user->getGalIdMass();
        $application_id = Yii::app()->db2->createCommand()
            ->select('id')
            ->from('change_fio_applications')
            ->where('fnpp =' . $fnpp)
            ->order('id desc')
            ->queryScalar();
        foreach ($nrec as $item) {
            $item_id = '0x' . bin2hex($item['Galid']);

            $change_fio_nrec = Yii::app()->db2->createCommand("insert into change_fio_nrec (person_nrec, application_id) values ( $item_id, $application_id)")
                ->execute();
            ApiKeyService::queryApi('updateChangeFio', array("nrec" => $item_id), Yii::app()->session['ApiKey'], 'PUT');
        }

        $message = self::UploadFile($application_id);
        $filename = Yii::getPathOfAlias('webroot') . '/protected/data/uploads/changefio/' . $application_id;
        self::actionPrintApplicationPDF($application_id, $filename);
        $this->redirect(array('index', 'message' => $message));
    }

    public function actionPrintApplicationPDF($id, $filename = null)
    {
        $sql = "select distinct cfr.reason,
                cfa.reason_id,
                new_fio,
                if(cfa.reason_id = 3, a.name, a2.name)                          main_doc_name,
                if(cfa.reason_id = 3, passport.doc_number, main_doc.doc_number) main_doc_number,
                if(cfa.reason_id = 3, passport.doc_date, main_doc.doc_date)     main_doc_date,
                if(cfa.reason_id = 3, passport.filename, main_doc.filename)     main_doc_file,
                if(cfa.reason_id = 3, passport.filesize, main_doc.filesize)     main_doc_filesize,
                passport.type_id 'passport_id',
                cfa.id,
                cfa.date_create,
                gp.fio,
                gus.sdepartment                                                 'group',
                fac.longname                                                    'faculty',
                gr.name                                                            'gr',      
                (SELECT tel1.ADDR FROM gal_COMMUNICATIONS tel1
                 WHERE gp.nrec = tel1.PERSON and tel1.COMTYPE in (0x800000000000022A, 0x800000000000022B, 0x80000000000003A7)
                 order by nrec desc limit 1) as                                  'phone',
                (SELECT mail.EMAIL FROM gal_COMMUNICATIONS mail
                 WHERE gp.nrec = mail.PERSON and mail.COMTYPE = 0x80000000000003A6
                 order by nrec desc limit 1) as                                   'email',
                case
                    when ter10.wtype = 0 then live.SADDRESS1
                    when ter11.wtype = 0 then CONCAT_WS(', ', ter10.SNAME, live.SADDRESS1)
                    when ter12.wtype = 0 or ter13.wtype = 0 then CONCAT_WS(', ', ter11.SNAME, ter10.SNAME, live.SADDRESS1)
                    when ter14.wtype = 0 then CONCAT_WS(', ', ter12.SNAME, ter11.SNAME, ter10.SNAME, live.SADDRESS1)
                    when ter141.wtype = 0 then CONCAT_WS(', ', ter13.SNAME, ter12.SNAME, ter11.SNAME, ter10.SNAME, live.SADDRESS1)
                    END                                                         'liveAddr'
from change_fio_applications cfa
         LEFT JOIN change_fio_reasons cfr ON cfa.reason_id = cfr.id
         left join change_fio_attachments main_doc on cfa.id = main_doc.application_id and main_doc.type_id in (2, 3, 4)
         left join change_fio_attachments passport on cfa.id = passport.application_id and passport.type_id not in (2, 3, 4)
         left join attachments_type a on a.id = passport.type_id
         left join attachments_type a2 on a2.id = main_doc.type_id
         left join change_fio_nrec cfn on cfa.id = cfn.application_id
         left join gal_persons gp on gp.nrec = cfn.person_nrec and gp.disdate = 0
         left join gal_u_student gus on gp.nrec = gus.cpersons
         left JOIN gal_CATALOGS gr on gr.nrec = gp.GR
         left JOIN gal_CATALOGS fac on fac.nrec = gus.cfaculty
         left join gal_ADDRESSN live on gp.LIVEADDR = live.nrec -- место проживания
         left join gal_sterr ter10 on ter10.nrec = live.CSTERR
         left join gal_sterr ter11 on ter11.nrec = ter10.CPARENT
         left join gal_sterr ter12 on ter12.nrec = ter11.CPARENT
         left join gal_sterr ter13 on ter13.nrec = ter12.CPARENT
         left join gal_sterr ter14 on ter14.nrec = ter13.CPARENT
         left join gal_sterr ter141 on ter141.nrec = ter14.CPARENT
where gp.disdate = 0 and cfa.id = " . $id . " order by gp.nrec desc limit 1";

        $result = Yii::app()->db2->createCommand($sql)->queryRow();
        // var_dump($result);

        $dateCreate = isset($result['date_create']) ? date("d.m.Y", strtotime($result['date_create'])) : '"____"_________ 20___ ';
        $fio = explode(' ', $result['fio']);
        $fam = $fio[0];
        $nam = $fio[1];
        unset($fio[0]);
        unset($fio[1]);
        $otc = implode(' ', $fio);
        $new_fio = explode(' ', $result['new_fio']);
        $new_fam = $new_fio[0];
        $new_nam = $new_fio[1];
        unset($new_fio[0]);
        unset($new_fio[1]);
        $new_otc = implode(' ', $new_fio);
        $gr_ar = [
            10 => '',
            1 => 'Российская Федерация',
            6 => 'Республика Узбекистан',
            7 => 'Республика Казахстан',
            8 => 'Республика Казахстан',
            5 => 'Республика Таджикистан',
        ];
        $new_gr = $result['reason_id'] == 3 ? $gr_ar[$result['passport_id']] : $result['gr'];
        $add = $result['liveAddr'];
        $str_size = 66;
        if (mb_strlen($add, Yii::app()->charset) > $str_size) {
            $space = mb_strrpos($add, ' ', $str_size - mb_strlen($add, Yii::app()->charset), Yii::app()->charset);
            $AddTwo = mb_strlen($add, Yii::app()->charset) > $str_size ? mb_substr($add, $space + 1, null, Yii::app()->charset) : '';
            $AddOne = mb_substr($add, 0, $space, Yii::app()->charset);;
        }

        $these_statements = array(
            'fam' => $fam,
            'nam' => $nam,
            'otc' => $otc,
            'new_fam' => $new_fam,
            'new_nam' => $new_nam,
            'new_otc' => $new_otc,
            'group' => $result['group'],
            'fak' => $result['faculty'],
            'gr' => $result['gr'],
            'address' => isset($AddOne) ? $AddOne : $add,
            'address2' => isset($AddTwo) ? $AddTwo : '',
            'phone' => $result['phone'],
            'mail' => $result['email'],
            'dc' => $dateCreate,
            'new_gr' => $new_gr,
            'reason' => $result['reason_id']
        );
        $this->renderPartial('//pdf/changeFio', ['these_statements' => $these_statements, 'filename' => $filename]);
    }

    public function actionSaveApplicationPDF($id)
    {
        $filename = Yii::getPathOfAlias('webroot') . '/protected/data/uploads/changefio/' . $id;
        if (!file_exists($filename)) {
            self::actionPrintApplicationPDF($id, $filename);
        }
        //ob_end_clean();
        /*header('Content-Description: File Transfer');
        header('Content-type: application/pdf');
        header('Content-Disposition: filename=' . $id . ' смена ФИО.pdf');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));
        readfile($filename);*/

        header('Content-Description: File Transfer');
        header('Content-type: application/pdf; charset=utf-8');
        header("Content-Disposition: attachment; filename=' . $id . ' смена ФИО.pdf");
        //header('Pragma: public');
        //header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));

        readfile($filename);
    }

    public static function GetReason()
    {
        $reasons = Yii::app()->db2->createCommand()
            ->select('id, reason')
            ->from('change_fio_reasons')
            ->order('id')
            ->queryAll();
        return array_column($reasons, 'reason', 'id');
    }

    public static function UploadFile($application_id)
    {
        $type = ['1' => '2', '2' => '3', '4' => '4'];
        $length = 1048576;
        foreach ($_FILES as $name => $file) {
            $err = "";
            if ($file['error'] > 0) $err .= "<b>Вы не выбрали файл</b><br>";
            if ($file['size'] > $length) $err .= "<b>Вы загружаете файл, размер которого превышает допустимый для этого поля</b><br>";
            if ($file['type'] != "application/pdf") $err .= "<b>Вы можете загрузить файл только в формате PDF</b><br>";

            if (strlen($err) == 0) {
                $tmp_name = $file['tmp_name'];

                $document_ipload_dir = Yii::getPathOfAlias('webroot') . '/protected/data/uploads/changefio/attachments/';
                //$document_ipload_dir = "./protected/runtime/changefio/";
                //$document_ipload_dir .= date('Y') . "/" . date('m') . "/";

                if (!is_dir($document_ipload_dir)) {
                    mkdir($document_ipload_dir, 0776, true);
                }

                $file_name = addcslashes($file['name'], '/');
                if ($_POST['reason'] == 3 || $name == 'upload-passport') {
                    $doc_type = $_POST['document'];
                } else {
                    $doc_type = $type[$_POST['reason']];
                }
                //$doc_type = ($_POST['reason'] == 3) ? $_POST['document'] : [$_POST['reason']];
                $doc_num = ($name == 'upload-passport') ? $_POST['num-passport'] : $_POST['num-reason'];
                $doc_date = ($name == 'upload-passport') ? $_POST['date-passport'] : $_POST['date-reason'];
                //$doc_date = date_create_from_format('d.m.Y', $doc_date)->format('Y-m-d');
                $result = Yii::app()->db2->createCommand('insert change_fio_attachments (application_id, type_id, doc_number, doc_date, filename, filesize) value (?, ?, ?, ?, ?, ?)')
                    ->execute([$application_id, $doc_type, $doc_num, $doc_date, $file_name, (int)$file['size']]);

                $id = Yii::app()->db2->createCommand()
                    ->select('id')
                    ->from('change_fio_attachments')
                    ->order('id desc')
                    ->where('application_id = ' . $application_id)
                    ->queryScalar();

                if ($id > 0) {
                    move_uploaded_file($tmp_name, $document_ipload_dir . $id);
                    if (filesize($document_ipload_dir . $id) <= 0) {
                        $err .= "Ошибка загрузки файла<br>";
                    }
                }
            }
            if ($_POST['reason'] == 3) {
                break;
            }
        }
        return strlen($err) == 0 ? true : $err;
    }


    public static function GetDocumentTypes($type = null)
    {
        $sql = Yii::app()->db2->createCommand()
            ->select('id, name')
            ->from('attachments_type at');
        if (is_null($type)) {
            return array_column($sql->queryAll(), 'name', 'id');
        }
        if ($type) {
            return array_column($sql->where('type =' . $type)->queryAll(), 'name', 'id');
        }
    }

    public function actionSaveAttachments($id)
    {
        $sql = Yii::app()->db2->createCommand()
            ->select('change_fio_attachments.id')
            ->from('change_fio_nrec')
            ->leftJoin('change_fio_attachments', 'change_fio_nrec.application_id = change_fio_attachments.application_id')
            ->where('change_fio_nrec.person_nrec = 0x' . $id)
            ->order('change_fio_attachments.application_id desc')
            ->queryScalar();
        $filename = Yii::getPathOfAlias('webroot') . '/protected/data/uploads/changefio/attachments/' . $sql;
        if (!file_exists($filename)) {
            echo "";
        }

//        ob_end_clean();
        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . $sql . '.pdf');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));

        readfile($filename);
    }

    public function actionPrintFileAttachments($id, $type)
    {

        $id_ = Yii::app()->db2->createCommand()
            ->select('id')
            ->from('change_fio_attachments')
            ->where('application_id = ' . $id)
            ->queryAll();

        $id_ = implode('', $id_[$type]);

        $filename = Yii::getPathOfAlias('webroot') . '/protected/data/uploads/changefio/attachments/' . $id_;
        if (!file_exists($filename)) {
            echo "";
        }

        header('Content-Description: File Transfer');
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename=Смена ФИО.pdf');
        header('Content-Length: ' . filesize($filename));

        readfile($filename);
    }
}