<?php

/**
 * Description of PortfolioController
 *
 * @author user
 */
class PortfolioController extends Controller {

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('nolink'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('index','Foto'),
                'users' => array('@'),
                //'roles' => array(WebUser::ROLE_STUDENT),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {
        $galId = Yii::app()->user->getGalId();
        if (null === $galId) {
            return $this->redirect(['student/nolink']);
        }
        if(!isset($_SESSION)){
            session_start();
        }
        $galIdMassive = Yii::app()->user->getGalIdMass();
        if(isset($id)){
            $prov = 0;
            foreach ($galIdMassive as $galunid){
                if($galunid['Galid'] == $id){$prov = 1;}
            }
            if($prov == 1) {
                $galId = $id;
                $_SESSION['ID'] = $id;
            }
        }else{
            if(isset($_SESSION['ID'])) {
                $galId = $_SESSION['ID'];
            }else{
                $_SESSION['ID'] = $galId;
            }
        }
        $model = Yii::app()->user->getModel();
        $npp = null;
        if ($model instanceof User) {
            foreach ($model->links as $link) {
                if (Link::TYPE_NPP == $link->type) {
                    $npp = $link->value;
                    break;
                }
            }
        }
        $info = Yii::app()->db2->createCommand("SELECT gus.fio AS 'fio', CASE WHEN gus.wdegree = 0 THEN 'Специалист'
  WHEN gus.wdegree = 1 THEN 'Бакалавр'
  WHEN gus.wdegree = 2 THEN 'Магистр'
  WHEN gus.wdegree = 5 THEN 'Аспирант'
  END AS 'degree',
  CONCAT(gus.codeprof,' - ',gus.spost) as 'post',
  gus.profname AS 'profil',
  gus.sdepartment AS 'group',
  (SELECT CONCAT(ga.appointdate) FROM gal_appointments ga LEFT JOIN gal_apphist gap ON gap.cappoint = ga.nrec WHERE ga.person = gus.cpersons AND gap.codoper IN (30001,30052) ORDER BY gap.nrec LIMIT 1) AS 'firstdate',
  (SELECT guc.dateapp FROM gal_persons gp 
  LEFT JOIN gal_appointments ga on ga.nrec = (CASE WHEN gp.appointcur != 0x8000000000000000 THEN gp.appointcur ELSE gp.appointlast END) 
  LEFT JOIN gal_staffstruct gs ON gs.nrec = ga.staffstr
  LEFT JOIN gal_u_curriculum guc ON guc.nrec = gs.cstr
  WHERE gp.nrec = gus.cpersons) as 'termstart',
  (SELECT guc.dateend FROM gal_persons gp 
  LEFT JOIN gal_appointments ga on ga.nrec = (CASE WHEN gp.appointcur != 0x8000000000000000 THEN gp.appointcur ELSE gp.appointlast END) 
  LEFT JOIN gal_staffstruct gs ON gs.nrec = ga.staffstr
  LEFT JOIN gal_u_curriculum guc ON guc.nrec = gs.cstr
  WHERE gp.nrec = gus.cpersons) 'termend',
  (SELECT GROUP_CONCAT( DISTINCT tel1.ADDR  SEPARATOR ', ')
 FROM gal_COMMUNICATIONS tel1 WHERE gus.cpersons = tel1.PERSON and tel1.COMTYPE in (0x800000000000022A, 0x800000000000022B, 0x80000000000003A7)) as 'phone',
 (SELECT  GROUP_CONCAT( DISTINCT mail.EMAIL  SEPARATOR ', ')  FROM gal_COMMUNICATIONS mail WHERE gus.cpersons = mail.PERSON and mail.COMTYPE = 0x80000000000003A6) as 'email'
  FROM gal_u_student gus
  WHERE gus.cpersons = 0x".bin2hex($galId))->queryRow();
        $education = Yii::app()->db2->createCommand("SELECT gc.name, ge.series, ge.diplomnmb, ge.diplomdate, qua.name qua, school.sname, spec.code spcode, spec.name spname, edu.name level,
  CASE
  WHEN uz1.wtype = 0 THEN uz.SADDRESS1
  WHEN uz2.wtype = 0 THEN CONCAT_WS(', ', uz1.SNAME, uz.SADDRESS1)
  WHEN uz3.wtype = 0 THEN CONCAT_WS(', ', uz2.SNAME, uz1.SNAME , uz.SADDRESS1)
  WHEN uz4.wtype = 0 THEN CONCAT_WS(', ', uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
  WHEN uz5.wtype = 0 THEN CONCAT_WS(', ', uz4.SNAME , uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
  WHEN uz6.wtype = 0 THEN CONCAT_WS(', ', uz5.SNAME , uz4.SNAME , uz3.SNAME , uz2.SNAME , uz1.SNAME , uz.SADDRESS1)
  END 'eduAddr'
  FROM fdata f
  LEFT JOIN skard s on s.fnpp = f.npp
  LEFT JOIN gal_u_student gus on gus.nrec = s.gal_srec
  LEFT JOIN gal_education ge ON ge.person = gus.cpersons
  LEFT JOIN gal_catalogs gc ON gc.code = CONVERT( ge.seqnmb, CHAR) AND gc.cparent= 0x80000000000002D6
  LEFT JOIN gal_catalogs qua ON qua.nrec = ge.qualification
  LEFT JOIN gal_catalogs edu ON edu.nrec = ge.level
  LEFT JOIN gal_catalogs spec ON spec.nrec = ge.speciality
  LEFT JOIN gal_u_school school ON school.nrec =ge.name

  LEFT JOIN gal_ADDRESSN uz ON uz.nrec = ge.LEARNADDR
  LEFT JOIN gal_sterr uz1 ON uz1.nrec = uz.CSTERR
  LEFT JOIN gal_sterr uz2 ON uz2.nrec = uz1.CPARENT
  LEFT JOIN gal_sterr uz3 ON uz3.nrec = uz2.CPARENT
  LEFT JOIN gal_sterr uz4 ON uz4.nrec = uz3.CPARENT
  LEFT JOIN gal_sterr uz5 ON uz5.nrec = uz4.CPARENT
  LEFT JOIN gal_sterr uz6 ON uz6.nrec = uz5.CPARENT
  WHERE f.npp = ".$npp." AND ge.iattr = 1
  GROUP BY gc.name, ge.series, ge.diplomnmb, ge.diplomdate, qua.name, school.sname")->queryAll();
        $numbersemestr = Yii::app()->db2->createCommand("SELECT  gucs . semester , SUM(gum.wmark) 'summ', 
  COUNT(gum.wmark) 'count',
  SUM(gum.wmark)/COUNT(gum.wmark) 'average',
  gucs.dbeg 'sbegin',
  gucs.dend 'send'
  FROM  gal_u_student   gus  
  LEFT JOIN  gal_persons   gp  ON gus.cpersons = gp.nrec 
  JOIN  gal_appointments   ga  ON ga.nrec = (CASE WHEN gp.appointcur != 0x8000000000000000 THEN gp.appointcur ELSE gp.appointlast END) 
  LEFT JOIN  gal_catalogs   fac  ON ga.privpension = fac.nrec 
  LEFT JOIN  gal_staffstruct   gs  ON ga.staffstr = gs.nrec 
  LEFT JOIN  gal_u_curriculum   cur  ON gs.cstr = cur.nrec 
  LEFT JOIN  gal_catalogs   chair  ON cur.cchair = chair.nrec 
  LEFT JOIN  gal_catalogs   sp  ON cur.cspeciality = sp.nrec 
  LEFT JOIN  gal_u_curriculum   cur2  ON cur2.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curriculum   cur4  ON cur4.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.nrec != cur2.nrec AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curr_discontent   gucd  ON cur.nrec = gucd.ccurr 
  JOIN  gal_u_curr_dis   curDis  ON (gucd.ccurr_dis = curDis.nrec AND (curDis.`daddfld#1#`  > 0)) 
  LEFT JOIN  gal_u_discipline   gud  ON curDis.cdis = gud.nrec 
  JOIN  gal_u_typework   gut  ON (gut.nrec = gucd.ctypework AND gut.nrec IN (0x8000000000000006, 0x8001000000000002, 0x8000000000000005, 0x800000000000000A, 
  0x8000000000000009, 0x8001000000000023, 0x800100000000003B, 0x800100000000002E, 0x800100000000002D, 0x8001000000000020, 0x8001000000000048, 0x800100000000003C)) 
  LEFT JOIN  gal_u_curr_semester   gucs  ON gucs.nrec = gucd.csemester 
  LEFT JOIN  gal_u_list   gul  ON gul.nrec = (SELECT gul1.nrec FROM gal_u_list gul1 INNER JOIN gal_u_marks gum1 ON gum1.clist = gul1.nrec 
  WHERE gul1.cdis = gud.nrec AND gul1.ctypework = gut.nrec AND gul1.wsemestr = gucs.semester AND gul1.ccur IN (SELECT gulcur.nrec FROM gal_u_curriculum gulcur 
  WHERE gulcur.cparent IN (cur.nrec, cur2.nrec, cur4.nrec)) AND gum1.cpersons = gp.nrec AND gum1.wendres IN (1,2,3) 
  ORDER BY gum1.datemark DESC, CASE gul1.wtypediffer WHEN 0 THEN gum1.wmark END DESC, CASE WHEN gul1.wtypediffer = 1 THEN gum1.wmark END ASC LIMIT 1) 
  LEFT JOIN  gal_u_marks   gum  ON (gul.nrec = gum.clist AND gum.cpersons = gp.nrec AND gum.wendres IN (1,2,3)) 
  LEFT JOIN  gal_persons   examPers  ON examPers.nrec = (CASE WHEN gum.cperexam IS NOT NULL THEN gum.cperexam ELSE gul.cexaminer END) 
  LEFT JOIN  gal_up_wrating   guw  ON guw.cmarks = gum.nrec LEFT JOIN  gal_up_wrating_hours   guwh  ON guwh.clist = gul.nrec 
  LEFT JOIN  gal_catalogs   gc  ON gc.nrec = gum.cmark 
  WHERE gp.nrec = 0x".bin2hex($galId)." AND gut.code != '08'
  GROUP BY  gucs . semester , gucs.dbeg, gucs.dend")->queryAll();
        foreach ($numbersemestr as $key => $semeser){
            $date1 = CMisc::fromGalDate($semeser['sbegin'],'Y-m-d 00:00:00');
            $date2 = CMisc::fromGalDate($semeser['send'],'Y-m-d 23:59:59');
            $date2 = (($date2 > date('Y-m-d 23:59:59'))?date('Y-m-d 23:59:59'):$date2);
            $numbersemestr[$key]['procent'] = $this->Statistics($date1, $date2, $galId);
        }
        $procentRaiting = Yii::app()->db2->createCommand("SELECT gucs.semester 'semester', SUM(atr.vdouble)/COUNT(atr.vdouble) 'proc'
  FROM  gal_u_student   gus  
  LEFT JOIN  gal_persons   gp  ON gus.cpersons = gp.nrec 
  JOIN  gal_appointments   ga  ON ga.nrec = (CASE WHEN gp.appointcur != 0x8000000000000000 THEN gp.appointcur ELSE gp.appointlast END) 
  LEFT JOIN  gal_catalogs   fac  ON ga.privpension = fac.nrec 
  LEFT JOIN  gal_staffstruct   gs  ON ga.staffstr = gs.nrec 
  LEFT JOIN  gal_u_curriculum   cur  ON gs.cstr = cur.nrec 
  LEFT JOIN  gal_catalogs   chair  ON cur.cchair = chair.nrec 
  LEFT JOIN  gal_catalogs   sp  ON cur.cspeciality = sp.nrec 
  LEFT JOIN  gal_u_curriculum   cur2  ON cur2.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curriculum   cur4  ON cur4.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.nrec != cur2.nrec AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curr_discontent   gucd  ON cur.nrec = gucd.ccurr 
  JOIN  gal_u_curr_dis   curDis  ON (gucd.ccurr_dis = curDis.nrec AND (curDis.`daddfld#1#`  > 0)) 
  LEFT JOIN  gal_u_discipline   gud  ON curDis.cdis = gud.nrec 
  JOIN  gal_u_typework   gut  ON (gut.nrec = gucd.ctypework AND gut.nrec IN (0x8000000000000006, 0x8001000000000002, 0x8000000000000005, 0x800000000000000A, 
  0x8000000000000009, 0x8001000000000023, 0x800100000000003B, 0x800100000000002E, 0x800100000000002D, 0x8001000000000020, 0x8001000000000048, 0x800100000000003C)) 
  LEFT JOIN  gal_u_curr_semester   gucs  ON gucs.nrec = gucd.csemester 
  LEFT JOIN  gal_u_list   gul  ON gul.nrec = (SELECT gul1.nrec FROM gal_u_list gul1 INNER JOIN gal_u_marks gum1 ON gum1.clist = gul1.nrec 
  WHERE gul1.cdis = gud.nrec AND gul1.ctypework = gut.nrec AND gul1.wsemestr = gucs.semester AND gul1.ccur IN (SELECT gulcur.nrec FROM gal_u_curriculum gulcur 
  WHERE gulcur.cparent IN (cur.nrec, cur2.nrec, cur4.nrec)) AND gum1.cpersons = gp.nrec AND gum1.wendres IN (1,2,3) 
  ORDER BY gum1.datemark DESC, CASE gul1.wtypediffer WHEN 0 THEN gum1.wmark END DESC, CASE WHEN gul1.wtypediffer = 1 THEN gum1.wmark END ASC LIMIT 1) 
  LEFT JOIN  gal_u_marks   gum  ON (gul.nrec = gum.clist AND gum.cpersons = gp.nrec AND gum.wendres IN (1,2,3)) 
  LEFT JOIN  gal_persons   examPers  ON examPers.nrec = (CASE WHEN gum.cperexam IS NOT NULL THEN gum.cperexam ELSE gul.cexaminer END) 
  LEFT JOIN  gal_up_wrating   guw  ON guw.cmarks = gum.nrec LEFT JOIN  gal_up_wrating_hours   guwh  ON guwh.clist = gul.nrec 
  LEFT JOIN  gal_catalogs   gc  ON gc.nrec = gum.cmark 
  LEFT JOIN gal_attrval atr ON  atr.crec = gum.nrec
  WHERE gp.nrec = 0x".bin2hex($galId)." AND atr.cattrnam = 0x8001000000000028 GROUP BY gucs.semester")->queryAll();
        if(!empty($procentRaiting)) {
            foreach ($procentRaiting as $mark) {
                $raiting[$mark['semester']] = $mark['proc'];
            }
        }
        foreach ($numbersemestr as $key => $semeser) {
            if (isset($raiting[$semeser['semester']])) {
                $numbersemestr[$key]['raiting'] = $raiting[$semeser['semester']];
            } else {
                $numbersemestr[$key]['raiting'] = '';
            }
        }
        $procentUchPlan = Yii::app()->db2->createCommand("SELECT gum.wmark as 'mark', COUNT(*) as 'count'
  FROM  gal_u_student   gus  
  LEFT JOIN  gal_persons   gp  ON gus.cpersons = gp.nrec 
  JOIN  gal_appointments   ga  ON ga.nrec = (CASE WHEN gp.appointcur != 0x8000000000000000 THEN gp.appointcur ELSE gp.appointlast END) 
  LEFT JOIN  gal_catalogs   fac  ON ga.privpension = fac.nrec 
  LEFT JOIN  gal_staffstruct   gs  ON ga.staffstr = gs.nrec 
  LEFT JOIN  gal_u_curriculum   cur  ON gs.cstr = cur.nrec 
  LEFT JOIN  gal_catalogs   chair  ON cur.cchair = chair.nrec 
  LEFT JOIN  gal_catalogs   sp  ON cur.cspeciality = sp.nrec 
  LEFT JOIN  gal_u_curriculum   cur2  ON cur2.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curriculum   cur4  ON cur4.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.nrec != cur2.nrec AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curr_discontent   gucd  ON cur.nrec = gucd.ccurr 
  JOIN  gal_u_curr_dis   curDis  ON (gucd.ccurr_dis = curDis.nrec AND (curDis.`daddfld#1#`  > 0)) 
  LEFT JOIN  gal_u_discipline   gud  ON curDis.cdis = gud.nrec 
  JOIN  gal_u_typework   gut  ON (gut.nrec = gucd.ctypework AND gut.nrec IN (0x8000000000000006, 0x8001000000000002, 0x8000000000000005, 0x800000000000000A, 
  0x8000000000000009, 0x8001000000000023, 0x800100000000003B, 0x800100000000002E, 0x800100000000002D, 0x8001000000000020, 0x8001000000000048, 0x800100000000003C)) 
  LEFT JOIN  gal_u_curr_semester   gucs  ON gucs.nrec = gucd.csemester 
  LEFT JOIN  gal_u_list   gul  ON gul.nrec = (SELECT gul1.nrec FROM gal_u_list gul1 INNER JOIN gal_u_marks gum1 ON gum1.clist = gul1.nrec 
  WHERE gul1.cdis = gud.nrec AND gul1.ctypework = gut.nrec AND gul1.wsemestr = gucs.semester AND gul1.ccur IN (SELECT gulcur.nrec FROM gal_u_curriculum gulcur 
  WHERE gulcur.cparent IN (cur.nrec, cur2.nrec, cur4.nrec)) AND gum1.cpersons = gp.nrec AND gum1.wendres IN (1,2,3) 
  ORDER BY gum1.datemark DESC, CASE gul1.wtypediffer WHEN 0 THEN gum1.wmark END DESC, CASE WHEN gul1.wtypediffer = 1 THEN gum1.wmark END ASC LIMIT 1) 
  LEFT JOIN  gal_u_marks   gum  ON (gul.nrec = gum.clist AND gum.cpersons = gp.nrec AND gum.wendres IN (1,2,3)) 
  LEFT JOIN  gal_persons   examPers  ON examPers.nrec = (CASE WHEN gum.cperexam IS NOT NULL THEN gum.cperexam ELSE gul.cexaminer END) 
  LEFT JOIN  gal_up_wrating   guw  ON guw.cmarks = gum.nrec LEFT JOIN  gal_up_wrating_hours   guwh  ON guwh.clist = gul.nrec 
  LEFT JOIN  gal_catalogs   gc  ON gc.nrec = gum.cmark 
  WHERE gp.nrec = 0x".bin2hex($galId)." AND gut.code != '08' AND gum.wmark IN (3,4,5) GROUP BY gum.wmark")->queryAll();
        foreach ($procentUchPlan as $mark){
            $UchPlan[$mark['mark']] = $mark['count'];
        }
        if(count($procentUchPlan) == 0){$UchPlan[3] = 0;$UchPlan[4] = 0;$UchPlan[5] = 0; }
        $krkp = Yii::app()->db2->createCommand("SELECT gucs.semester 'semester', gud.name 'name', gc.name 'mark', gv.title 'title', 
  (SELECT v.id FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'work' ORDER BY v.ID DESC LIMIT 1) 'work',
  (SELECT v.id FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'worktask' ORDER BY v.ID DESC LIMIT 1) 'worktask',
  (SELECT v.name FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'worktask' ORDER BY v.ID DESC LIMIT 1) 'worktaskfile',
  (SELECT v.id FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'add_1' ORDER BY v.ID DESC LIMIT 1) 'dop1',
  (SELECT v.name FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'add_1' ORDER BY v.ID DESC LIMIT 1) 'dop1file',
  (SELECT v.id FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'add_2' ORDER BY v.ID DESC LIMIT 1) 'dop2',
  (SELECT v.name FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'add_2' ORDER BY v.ID DESC LIMIT 1) 'dop2file'
  FROM  gal_u_student   gus  
  LEFT JOIN  gal_persons   gp  ON gus.cpersons = gp.nrec 
  JOIN  gal_appointments   ga  ON ga.nrec = (CASE WHEN gp.appointcur != 0x8000000000000000 THEN gp.appointcur ELSE gp.appointlast END) 
  LEFT JOIN  gal_catalogs   fac  ON ga.privpension = fac.nrec 
  LEFT JOIN  gal_staffstruct   gs  ON ga.staffstr = gs.nrec 
  LEFT JOIN  gal_u_curriculum   cur  ON gs.cstr = cur.nrec 
  LEFT JOIN  gal_catalogs   chair  ON cur.cchair = chair.nrec 
  LEFT JOIN  gal_catalogs   sp  ON cur.cspeciality = sp.nrec 
  LEFT JOIN  gal_u_curriculum   cur2  ON cur2.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curriculum   cur4  ON cur4.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.nrec != cur2.nrec AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curr_discontent   gucd  ON cur.nrec = gucd.ccurr 
  JOIN  gal_u_curr_dis   curDis  ON (gucd.ccurr_dis = curDis.nrec AND (curDis.`daddfld#1#`  > 0)) 
  LEFT JOIN  gal_u_discipline   gud  ON curDis.cdis = gud.nrec 
  JOIN  gal_u_typework   gut  ON (gut.nrec = gucd.ctypework AND gut.nrec IN (0x800000000000000A, 0x8000000000000009)) 
  LEFT JOIN  gal_u_curr_semester   gucs  ON gucs.nrec = gucd.csemester 
  LEFT JOIN  gal_u_list   gul  ON gul.nrec = (SELECT gul1.nrec FROM gal_u_list gul1 INNER JOIN gal_u_marks gum1 ON gum1.clist = gul1.nrec 
  WHERE gul1.cdis = gud.nrec AND gul1.ctypework = gut.nrec AND gul1.wsemestr = gucs.semester AND gul1.ccur IN (SELECT gulcur.nrec FROM gal_u_curriculum gulcur 
  WHERE gulcur.cparent IN (cur.nrec, cur2.nrec, cur4.nrec)) AND gum1.cpersons = gp.nrec AND gum1.wendres IN (1,2,3) 
  ORDER BY gum1.datemark DESC, CASE gul1.wtypediffer WHEN 0 THEN gum1.wmark END DESC, CASE WHEN gul1.wtypediffer = 1 THEN gum1.wmark END ASC LIMIT 1) 
  LEFT JOIN  gal_u_marks   gum  ON (gul.nrec = gum.clist AND gum.cpersons = gp.nrec AND gum.wendres IN (1,2,3)) 
  LEFT JOIN  gal_persons   examPers  ON examPers.nrec = (CASE WHEN gum.cperexam IS NOT NULL THEN gum.cperexam ELSE gul.cexaminer END) 
  LEFT JOIN  gal_up_wrating   guw  ON guw.cmarks = gum.nrec LEFT JOIN  gal_up_wrating_hours   guwh  ON guwh.clist = gul.nrec 
  LEFT JOIN  gal_catalogs   gc  ON gc.nrec = gum.cmark 
  LEFT JOIN gal_vkr gv ON gv.nrec = gum.cdb_dip
  WHERE gp.nrec = 0x".bin2hex($galId)." AND gc.name IS NOT NULL ORDER BY gucs.semester")->queryAll();
        $practic = Yii::app()->db2->createCommand("SELECT gul.nrec 'nreclist', gud.name 'discipline', gucs.semester 'semester', gul.wyeared 'yeared', gc.name 'mark', gurp.sbegin, gurp.send, gk.name 'PredprName',
  CASE
  WHEN gs1.wtype = 0 THEN gaddr.SADDRESS1
  WHEN gs2.wtype = 0 THEN CONCAT_WS(', ', gs1.SNAME, gaddr.SADDRESS1)
  WHEN gs3.wtype = 0 THEN CONCAT_WS(', ', gs2.SNAME, gs1.SNAME , gaddr.SADDRESS1)
  WHEN gs4.wtype = 0 THEN CONCAT_WS(', ', gs3.SNAME , gs2.SNAME , gs1.SNAME , gaddr.SADDRESS1)
  WHEN gs5.wtype = 0 THEN CONCAT_WS(', ', gs4.SNAME , gs3.SNAME , gs2.SNAME , gs1.SNAME , gaddr.SADDRESS1)
  WHEN gs6.wtype = 0 THEN CONCAT_WS(', ', gs5.SNAME , gs4.SNAME , gs3.SNAME , gs2.SNAME , gs1.SNAME , gaddr.SADDRESS1)
  END 'PredprAddr',
  v.id, v.name, v.text, v.comment
  FROM  gal_u_student   gus  
  LEFT JOIN  gal_persons   gp  ON gus.cpersons = gp.nrec 
  JOIN  gal_appointments   ga  ON ga.nrec = (CASE WHEN gp.appointcur != 0x8000000000000000 THEN gp.appointcur ELSE gp.appointlast END) 
  LEFT JOIN  gal_catalogs   fac  ON ga.privpension = fac.nrec 
  LEFT JOIN  gal_staffstruct   gs  ON ga.staffstr = gs.nrec 
  LEFT JOIN  gal_u_curriculum   cur  ON gs.cstr = cur.nrec 
  LEFT JOIN  gal_catalogs   chair  ON cur.cchair = chair.nrec 
  LEFT JOIN  gal_catalogs   sp  ON cur.cspeciality = sp.nrec 
  LEFT JOIN  gal_u_curriculum   cur2  ON cur2.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curriculum   cur4  ON cur4.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.nrec != cur2.nrec AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curr_discontent   gucd  ON cur.nrec = gucd.ccurr 
  JOIN  gal_u_curr_dis   curDis  ON (gucd.ccurr_dis = curDis.nrec AND (curDis.`daddfld#1#`  > 0)) 
  LEFT JOIN  gal_u_discipline   gud  ON curDis.cdis = gud.nrec 
  JOIN  gal_u_typework   gut  ON (gut.nrec = gucd.ctypework AND gut.nrec IN (0x8001000000000023, 0x800100000000002C)) 
  LEFT JOIN  gal_u_curr_semester   gucs  ON gucs.nrec = gucd.csemester 
  LEFT JOIN  gal_u_list   gul  ON gul.nrec = (SELECT gul1.nrec FROM gal_u_list gul1 INNER JOIN gal_u_marks gum1 ON gum1.clist = gul1.nrec 
  WHERE gul1.cdis = gud.nrec AND gul1.ctypework = gut.nrec AND gul1.wsemestr = gucs.semester AND gul1.ccur IN (SELECT gulcur.nrec FROM gal_u_curriculum gulcur 
  WHERE gulcur.cparent IN (cur.nrec, cur2.nrec, cur4.nrec)) AND gum1.cpersons = gp.nrec AND gum1.wendres IN (1,2,3) 
  ORDER BY gum1.datemark DESC, CASE gul1.wtypediffer WHEN 0 THEN gum1.wmark END DESC, CASE WHEN gul1.wtypediffer = 1 THEN gum1.wmark END ASC LIMIT 1) 
  LEFT JOIN  gal_u_marks   gum  ON (gul.nrec = gum.clist AND gum.cpersons = gp.nrec AND gum.wendres IN (1,2,3)) 
  LEFT JOIN  gal_persons   examPers  ON examPers.nrec = (CASE WHEN gum.cperexam IS NOT NULL THEN gum.cperexam ELSE gul.cexaminer END) 
  LEFT JOIN  gal_up_wrating   guw  ON guw.cmarks = gum.nrec LEFT JOIN  gal_up_wrating_hours   guwh  ON guwh.clist = gul.nrec 
  LEFT JOIN  gal_catalogs   gc  ON gc.nrec = gum.cmark 
  LEFT JOIN gal_up_register_practices gurp ON gurp.clist = gul.nrec AND gurp.cperson = gp.nrec
  LEFT JOIN gal_katorg gk ON gk.nrec = gurp.ccompany
  LEFT JOIN gal_addressn gaddr ON gaddr.nrec = gk.cjuridicaladdr
  LEFT JOIN gal_sterr gs1 ON gs1.nrec = gaddr.csterr
  LEFT JOIN gal_sterr gs2 ON gs2.nrec = gs1.cparent
  LEFT JOIN gal_sterr gs3 ON gs3.nrec = gs2.cparent
  LEFT JOIN gal_sterr gs4 ON gs4.nrec = gs3.cparent
  LEFT JOIN gal_sterr gs5 ON gs5.nrec = gs4.cparent
  LEFT JOIN gal_sterr gs6 ON gs6.nrec = gs5.cparent
  LEFT JOIN skard s on s.gal_srec = gus.nrec
  LEFT JOIN ecab.vkrfiles v ON v.disc = HEX(gud.nrec) AND v.semester = gucs.semester AND s.fnpp = v.fnpp
  WHERE gum.nrec is not null and gp.nrec = 0x".bin2hex($galId))->queryAll();
        $otherWork = Yii::app()->db2->createCommand("SELECT *, (SELECT COUNT(*) FROM ecab.vkrfiles v1 WHERE tt.fnpp = v1.fnpp AND tt.disc = v1.disc) countdisc
  FROM (SELECT v.id, gud.name 'discipline', v.semester, v.name, v.text, v.comment,v.fnpp,v.disc
  FROM  gal_u_student   gus  
  LEFT JOIN  gal_persons   gp  ON gus.cpersons = gp.nrec 
  JOIN  gal_appointments   ga  ON ga.nrec = (CASE WHEN gp.appointcur != 0x8000000000000000 THEN gp.appointcur ELSE gp.appointlast END) 
  LEFT JOIN  gal_catalogs   fac  ON ga.privpension = fac.nrec 
  LEFT JOIN  gal_staffstruct   gs  ON ga.staffstr = gs.nrec 
  LEFT JOIN  gal_u_curriculum   cur  ON gs.cstr = cur.nrec 
  LEFT JOIN  gal_catalogs   chair  ON cur.cchair = chair.nrec 
  LEFT JOIN  gal_catalogs   sp  ON cur.cspeciality = sp.nrec 
  LEFT JOIN  gal_u_curriculum   cur2  ON cur2.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1) 
  LEFT JOIN  gal_u_curriculum   cur4  ON cur4.nrec = (SELECT cur3.nrec FROM gal_appointments ga2 LEFT JOIN gal_staffstruct gs2 ON ga2.staffstr = gs2.nrec 
  INNER JOIN gal_u_curriculum cur3 ON cur3.nrec = gs2.cstr WHERE ga2.person = ga.person AND cur3.nrec != cur.nrec 
  AND cur3.nrec != cur2.nrec AND cur3.cspeciality = cur.cspeciality AND cur3.wformed = cur.wformed AND cur3.yeared = cur.yeared ORDER BY ga2.nrec DESC LIMIT 1)
  LEFT JOIN  gal_u_curr_discontent   gucd  ON cur.nrec = gucd.ccurr 
  JOIN  gal_u_curr_dis   curDis  ON (gucd.ccurr_dis = curDis.nrec AND (curDis.`daddfld#1#`  > 0)) 
  LEFT JOIN  gal_u_discipline   gud  ON curDis.cdis = gud.nrec 
  JOIN  gal_u_typework   gut  ON (gut.nrec = gucd.ctypework AND gut.nrec IN (0x8000000000000006, 0x8001000000000002, 0x8000000000000005, 0x800000000000000A, 0x8000000000000009, 
      0x8001000000000020, 0x8001000000000021, 0x8001000000000022, 0x8001000000000034, 0x8001000000000035))
  LEFT JOIN  gal_u_curr_semester   gucs  ON gucs.nrec = gucd.csemester 
  LEFT JOIN  gal_u_list   gul  ON gul.nrec = (SELECT gul1.nrec FROM gal_u_list gul1 INNER JOIN gal_u_marks gum1 ON gum1.clist = gul1.nrec 
  WHERE gul1.cdis = gud.nrec AND gul1.ctypework = gut.nrec AND gul1.wsemestr = gucs.semester AND gul1.ccur IN (SELECT gulcur.nrec FROM gal_u_curriculum gulcur 
  WHERE gulcur.cparent IN (cur.nrec, cur2.nrec, cur4.nrec)) AND gum1.cpersons = gp.nrec AND gum1.wendres IN (1,2,3) 
  ORDER BY gum1.datemark DESC, CASE gul1.wtypediffer WHEN 0 THEN gum1.wmark END DESC, CASE WHEN gul1.wtypediffer = 1 THEN gum1.wmark END ASC LIMIT 1)
  LEFT JOIN  gal_u_marks   gum  ON (gul.nrec = gum.clist AND gum.cpersons = gp.nrec AND gum.wendres IN (1,2,3)) 
  LEFT JOIN  gal_persons   examPers  ON examPers.nrec = (CASE WHEN gum.cperexam IS NOT NULL THEN gum.cperexam ELSE gul.cexaminer END) 
  LEFT JOIN  gal_up_wrating   guw  ON guw.cmarks = gum.nrec LEFT JOIN  gal_up_wrating_hours   guwh  ON guwh.clist = gul.nrec 
  LEFT JOIN  gal_catalogs   gc  ON gc.nrec = gum.cmark 
  LEFT JOIN gal_up_register_practices gurp ON gurp.clist = gul.nrec AND gurp.cperson = gp.nrec
  LEFT JOIN gal_katorg gk ON gk.nrec = gurp.ccompany
  LEFT JOIN gal_addressn gaddr ON gaddr.nrec = gk.cjuridicaladdr
  LEFT JOIN gal_sterr gs1 ON gs1.nrec = gaddr.csterr
  LEFT JOIN gal_sterr gs2 ON gs2.nrec = gs1.cparent
  LEFT JOIN gal_sterr gs3 ON gs3.nrec = gs2.cparent
  LEFT JOIN gal_sterr gs4 ON gs4.nrec = gs3.cparent
  LEFT JOIN gal_sterr gs5 ON gs5.nrec = gs4.cparent
  LEFT JOIN gal_sterr gs6 ON gs6.nrec = gs5.cparent
  LEFT JOIN skard s on s.gal_srec = gus.nrec
  INNER JOIN ecab.vkrfiles v ON s.fnpp = v.fnpp AND v.disc = HEX(gud.nrec) AND v.semester = gucs.semester
  WHERE 1=1
  -- and gum.nrec is not null 
  and gp.nrec = 0x".bin2hex($galId)."
  UNION
  SELECT v.id, CASE WHEN v.disc LIKE '800%' THEN gud.name ELSE v.disc END 'discipline', v.semester, v.name, v.text, v.comment, v.fnpp,v.disc
    FROM studbase.fdata f
    LEFT JOIN ecab.vkrfiles v ON v.fnpp = f.npp
    LEFT JOIN studbase.gal_u_discipline gud ON gud.nrec = UNHEX(v.disc)
    WHERE f.npp = ".$npp." AND v.type LIKE 'other' AND v.semester is null
  ) as tt
  ORDER BY tt.discipline, tt.semester, tt.text")->queryAll();
        $appoint = Yii::app()->db2->createCommand("SELECT ga.nrec
          FROM gal_appointments ga
          WHERE ga.person = 0x".bin2hex($galId)."
          ORDER BY ga.nrec DESC
          LIMIT 1")->queryScalar();
        $theme = Yii::app()->db2->createCommand("SELECT gv.title, gp.fio, gv.post, gc.name chair, gc1.name mark, gul.datedoc, vv.id work, vv.name workname,
  (SELECT v.id FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'worktit' ORDER BY v.ID DESC LIMIT 1) 'worktit',
  (SELECT v.name FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'worktit' ORDER BY v.ID DESC LIMIT 1) 'worktitname',
  (SELECT v.id FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'review' ORDER BY v.ID DESC LIMIT 1) 'review',
  (SELECT v.name FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'review' ORDER BY v.ID DESC LIMIT 1) 'reviewname',
  (SELECT v.id FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'rev' ORDER BY v.ID DESC LIMIT 1) 'rev',
  (SELECT v.name FROM ecab.vkrfiles v WHERE v.vkrnrec = gv.nrec AND v.type LIKE 'rev' ORDER BY v.ID DESC LIMIT 1) 'revname',
  CASE WHEN guc.wdegree=0 THEN 'специалиста' 
  WHEN guc.wdegree=1 THEN 'бакалавра'
  WHEN guc.wdegree=7 THEN 'бакалавра' 
  WHEN guc.wdegree=2 THEN 'магистра' 
  WHEN guc.wdegree=5 THEN 'аспиранта' ELSE '' END degree, gv.date
  FROM gal_u_student gus
  LEFT JOIN gal_vkr gv ON (gv.author = gus.cpersons AND gv.type IN (5,6,7))
  LEFT JOIN gal_persons gp ON gp.nrec = gv.supervisor
  LEFT JOIN gal_appointments ga on ga.person = gus.cpersons
  LEFT JOIN gal_staffstruct gs ON gs.nrec = ga.staffstr
  LEFT JOIN gal_u_curriculum guc on guc.nrec = gs.cstr
  LEFT JOIN gal_catalogs gc on gc.nrec = guc.cchair
  LEFT JOIN gal_u_list gul ON (gul.ccur IN (SELECT guc1.nrec FROM gal_u_curriculum guc1 WHERE guc1.cparent = guc.nrec) 
  AND gul.ctypework IN (0x8001000000000034,0x8001000000000035,0x8001000000000027,0x8001000000000022,0x8001000000000021))
  INNER JOIN gal_u_marks gum ON (gum.cpersons = gus.cpersons AND gum.clist = gul.nrec AND gum.wendres != 0)
  LEFT JOIN gal_catalogs gc1 ON gc1.nrec = gum.cmark
  LEFT JOIN ecab.vkrfiles vv ON vv.vkrnrec = gv.nrec AND vv.type like 'work'
  LEFT JOIN ecab.vkr_apl va ON va.vkrnrec = gv.nrec AND va.prdel = 0
  LEFT JOIN skard s on s.gal_srec = gus.nrec
          WHERE s.fnpp = ".$npp."
          AND ga.nrec = (SELECT ga.nrec
          FROM gal_appointments ga
          WHERE ga.person = gus.cpersons
          ORDER BY ga.nrec DESC
          LIMIT 1)")->queryAll();
        $fotocheck = Yii::app()->db2->createCommand("SELECT COUNT(*) 
              FROM afiles a
              WHERE 1=1
              AND a.fnpp = ".$npp."
              AND a.doctype like 'fdataphoto'")->queryScalar();

        $stud=Yii::app()->db2->createCommand("SELECT COUNT(*) FROM skard s WHERE s.fnpp = ".$npp." AND s.prudal = 0")->queryScalar();
        $nauka= Yii::app()->db2->createCommand("SELECT a.npp, a.tnpp, a.fnpp as afnpp, a.description, a.link, t.sort 
          ,YEAR(a.dc) 'year', a.part, a.dc
          from studbase.uchet_activity a 
          left join studbase.uchet_types t on a.tnpp = t.npp 
          left join studbase.uchet_cowriters c on a.npp = c.anpp 
          where (a.fnpp=".$npp." OR c.pnpp=".$npp.")".(($stud>0)?" ":" and a.istatus=2 ")
            ."GROUP BY a.npp
          ORDER BY a.dc DESC")->queryAll();
        $np = array(array(1.05, 1.10), array(1.14, 1.19), array(2.17, 2.19));
        $umr = array(array(1.01, 1.03), array(2.03, 2.03));
        $ois = array(array(1.11, 1.13), array(2.04, 2.05));
        foreach ($nauka as $key=>$value) {
            $otv = "";
            foreach($np as $ch){ if($value['sort']>=$ch[0] && $value['sort']<=$ch[1]) $otv = "np"; }
            foreach($umr as $ch){ if($value['sort']>=$ch[0] && $value['sort']<=$ch[1]) $otv = "umr"; }
            foreach($ois as $ch){ if($value['sort']>=$ch[0] && $value['sort']<=$ch[1]) $otv = "ois"; }
            if($otv == ""){
                unset($nauka[$key]);
                continue;
            }else{
                $nauka[$key]['type'] = $otv;
            }
            $req = "";
            $row = Yii::app()->db2->createCommand("SELECT tt.name, t.typ FROM studbase.uchet_types t left join studbase.uchet_typtypes tt on t.typ = tt.npp where t.npp=".$value['tnpp'])->queryRow();
            if($row['typ'] == 2 || $row['typ'] == 3){
                $req .= $row['name'].": ".$value['part'];
            }
            if($row['typ'] == 1 || $row['typ'] == 4){
                $req .= $row['name'].": ".$value['part'];
            }
            $nauka[$key]['req'] = $req;
        }

        $spr=array(
            '101' => 'Титульный лист индивидуального учебного плана и аттестации аспиранта и объяснительная записка к выбору темы диссертации',
            '102' => 'Общий план научных исследований аспиранта',
            '103' => 'Выписка из протокола заседания кафедры об утверждении темы научно-квалификационной работы и индивидуального учебного плана аспиранта',
            '104' => 'Выписка из протокола заседания совета факультета об утверждении темы научно-квалификационной работы и индивидуального учебного плана аспиранта',
            '105' => 'Рабочий план 1 полугодия',
            '106' => 'Рабочий план 2 полугодия',
            '107' => 'Отчет о выполнении научных исследований за 1 полугодие',
            '108' => 'Отчет о выполнении научных исследований за 2 полугодие',
            '109' => 'Выписка из протокола заседания кафедры о результатах прохождения промежуточной аттестации',
            '110' => 'Индивидуальный план научно-исследовательской практики (1 курс 2 семестр)',
            '111' => 'Отчет о прохождении научно-исследовательской практики',
            '112' => 'Заключение о прохождении научно-исследовательской практики',
            '113' => 'Индивидуальный план педагогической практики',
            '114' => 'Отчет о прохождении педагогической практики',
            '115' => 'Заключение о прохождении педагогической практики',
        );
        $aspirant= Yii::app()->db2->createCommand("select id, yy, mime, size, fnpp, type, checkasp, name, dateload from ecab.repfiles where fnpp='".$npp."' order by id DESC")->queryAll();
        foreach ($aspirant as $key =>$item) {
            if(!empty($spr[$item['type']])){
                $aspirant[$key]['Name'] = $spr[$item['type']];
            }else{
                $aspirant[$key]['Name'] = "тип отчёта упразднён";
            }
        }
//        var_dump($info,$education);die;
//        var_dump($npp, bin2hex($galId),bin2hex($appoint));die;
//        var_dump($numbersemestr);die;
//        var_dump($UchPlan);die;
//        var_dump($krkp);die;
//        var_dump($practic);die;
//        var_dump($otherWork);die;
//        var_dump($theme, bin2hex($galId),bin2hex($appoint));die;
//        var_dump($nauka);die;
        //var_dump($aspirant);die;
        $this->render('index',array(
            'galId' => $galId,
            'npp' => $npp,
            'info' => $info,
            'education' => $education,
            'numbersemestr' => $numbersemestr,
            'procentUchPlan' => $UchPlan,
            'krkp' => $krkp,
            'practic' => $practic,
            'otherWork' => $otherWork,
            'themeAll' => $theme,
            'fotocheck' => $fotocheck,
            'nauka' => $nauka,
            'aspirant' => $aspirant,
        ));
    }

//    public static function actionFoto($npp){
//        $data = Yii::app()->db2->createCommand("select b.scan scan, a.mime mime
//              from studbase.afiles a
//              left join studbase.afiles_blob b on a.id = b.id
//              where a.fnpp = ".$npp."
//              and a.doctype = 'fdataphoto'")->queryRow();
//        header('Content-type: '.$data['mime']);
//        return $data['scan'];
//    }

    public function Statistics($date1, $date2, $galId) {
        $discipline = $this->getdisciplinelist($date1, $date2, $galId);
        $i=0;
        $data[1]['disciplineNrec'] = '';
        $data[1]['discipline'] = '';
        $data[1]['kindOfWorkId'] = '';
        $data[1]['Kind'] = '';
        $data[1]['teacherFio'] = '';
        $data[1]['teacherFnpp'] = '';
        $data[1]['Amount'] = 0;
        $data[1]['studGroupName'] = '';
        foreach($discipline as $dis){
            $test = 1;
            foreach ($data as $dat){
                if($dis['disciplineNrec'] == $dat['disciplineNrec']) {
                    if($dis['teacherFnpp'] == $dat['teacherFnpp']) {
                        if($dis['kindOfWorkId'] == $dat['kindOfWorkId']) {
                            $test = 0;
                        }elseif ($dis['kindOfWorkId'] == '2' and $dis['studGroupName'] != $dat['studGroupName']){
                            $test = 1;
                        }
                    }
                }
            }
            if($test){//если похожих записей не было найдено, то создаем новую запись
                $i++;
                $data[$i]['discipline'] = $dis['discipline'];
                $data[$i]['disciplineNrec'] = $dis['disciplineNrec'];
                $data[$i]['kindOfWorkId'] = $dis['kindOfWorkId'];
                $data[$i]['Kind'] = $dis['Kind'];
                $data[$i]['teacherFio'] = $dis['teacherFio'];
                $data[$i]['teacherFnpp'] = $dis['teacherFnpp'];
                $data[$i]['Amount'] = 0;
                $data[$i]['studGroupName'] = $dis['studGroupName'];
                foreach ($discipline as $dis4et) {
                    if(($data[$i]['disciplineNrec'] == $dis4et['disciplineNrec'])
                        and ($data[$i]['teacherFnpp'] == $dis4et['teacherFnpp'])
                        and ($data[$i]['kindOfWorkId'] == $dis4et['kindOfWorkId'])
                        and ($data[$i]['studGroupName'] == $dis4et['studGroupName'])) {
                        $data[$i]['Amount']++;
                    }
                }
            }else{continue;}
        }
        sort($data);

        $list = $this->getmydata($galId);
        $i=0;
        foreach ($list as $li) {
            $proc[0][$i] = 0;
            foreach ($data as $dat) {
                if ((($dat['kindOfWorkId'] == 2) and (substr($dat['studGroupName'], -2) == '/1')) or ($dat['kindOfWorkId'] != 2)) {
                    $proc[0][$i] += (2 * $dat['Amount']);
                }
            }
            $i++;
        }
        $i=0;
        foreach ($list as $li){
            $j=0;
            foreach ($data as $dat){
                $liststeach[$i][$j] = 0;
                $j++;
            }$i++;
        }
        $i=0;
        foreach ($list as $li){
            $proc[2][$i] = 0;
            foreach ($discipline as $dis){
                $test = 0;
                if( in_array($this->markteach($li['fnpp'],$dis['id']), [1, 6]) ){
                    $test = 1;
                }
                $j=0;
                foreach ($data as $dat){
                    if($dis['disciplineNrec'] == $dat['disciplineNrec']
                        and $dis['teacherFnpp'] == $dat['teacherFnpp']
                        and $dis['kindOfWorkId'] == $dat['kindOfWorkId']
                        and $dis['studGroupName'] == $dat['studGroupName']) {
                        if ($test) {
                            $liststeach[$i][$j] = $liststeach[$i][$j]+2;
                            $proc[2][$i] += 2;
                        }
                    }$j++;
                }
            }$i++;
        }
        $i=0;
        foreach ($list as $li) {
            if ($proc[2][$i] > $proc[0][$i]) {
                $proc[2][$i] = $proc[0][$i];
            }
            $i++;
        }
        $procent = ($proc[0][0] != 0)?100*round(($proc[2][0]/$proc[0][0]),4):0;
        return $procent;
    }

    //определяет дисциплины для статистике по дате (8)---
    public function getdisciplinelist($date1, $date2, $galId) {
        $group = $this->getgroupid($galId);
        $result = Yii::app()->db2->createCommand()
            ->select('ats.studGroupId, ats.id, ats.discipline, ats.disciplineNrec, ats.kindOfWorkId, ats.teacherFio, ats.teacherFnpp, 
                    ak.name AS \'Kind\', ats.studGroupName, atw.name AS \'Type\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%H:%i\') AS \'time\', 
                    DATE_FORMAT(ats.dateTimeStartOfClasses, \'%d.%m.%Y\') AS \'dat\'')
            ->from('attendance_schedule ats')
            ->join('attendance_kindofwork ak', 'ak.id = ats.kindOfWorkId')
            ->join('attendance_typeofwork atw', 'atw.id = ats.typeOfWorkId')
            ->where(array('AND',
                'ats.studGroupId='.$group,
                'ats.dateTimeStartOfClasses > \''.$date1.'\'',
                'ats.dateTimeStartOfClasses < \''.$date2.'\'',
            ))
            ->order('ats.dateTimeStartOfClasses')
            ->queryAll();

        return $result;
    }

    //определяет группу студента id (1.1)+
    public function getgroupid($galId) {
        $result = Yii::app()->db2->createCommand()
            ->select('agg.id')
            ->from('gal_u_student gus')
            ->join('attendance_galruz_group agg', 'agg.gal_nrec = gus.cstgr')
            ->where('gus.cpersons = :galid',array(':galid' => $galId))
            ->queryScalar();
        //$fnpp = $this->myfnpp($galId);var_dump($result,$galId,$fnpp);die;
        return $result;
    }

    //определяет список в котором только один студент(3.1)+
    public function getmydata($galId) {
        $result = Yii::app()->db2->createCommand()
            ->select('s.fnpp, gus.cpersons, gus.fio')
            ->from('gal_u_student gus')
            ->join('skard s', 'gus.nrec = s.gal_srec')
            ->where('gus.cpersons=:galid',array(':galid' => $galId))
            ->queryAll();

        return $result;
    }

    //получаем из журналу оценку (5)+
    public function markteach($fnpp,$schid) {
        $result = Yii::app()->db2->createCommand()
            ->select('aj.teacherMarkId')
            ->from('attendance_journal aj')
            ->where(array('AND',
                'aj.studentFnpp='.$fnpp,
                'aj.scheduleId='.$schid))
            ->queryScalar();

        return $result;
    }

    public static function getapp($anpp) {
        $row = Yii::app()->db2->createCommand("select concat_ws(' ', f.fam, f.nam, f.otc) as fio, a.part 
from studbase.uchet_activity a left join studbase.fdata f on a.fnpp=f.npp where a.npp=".$anpp)->queryRow();
        $req = "<div style=\"font-size: 90%; font-style: italic; text-align: left;\">автор</div><div style=\"border-bottom: 2px solid #fff; font-weigth: bold; margin-bottom: 5px;\">".$row['fio'];
        if($row['part']>0) $req .= "(".(1*$row['part']).")";
        $req .= "</div>";

        return $req;
    }

    public static function getcoau($anpp) {
        $aulist = "";
        $rows = Yii::app()->db2->createCommand("select concat_ws(' ', f.fam, f.nam, f.otc) as fio, c.part 
from studbase.uchet_cowriters c left join studbase.fdata f on c.pnpp=f.npp where c.anpp=".$anpp)->queryAll();
        foreach ($rows as $row) {
            $aulist .= "<div style=\"font-size: 90%; font-style: italic; text-align: left;\">соавтор</div><div style=\"border-bottom: 2px solid #fff; margin-bottom: 5px;\">".$row['fio'];
            if(round($row['part'], 3)!=0) $aulist .= "&nbsp;(".round($row['part'], 3).")";
            $aulist .= "</div>";
        }

        return $aulist;
    }

    public static function yearandyear($text){
        if("общий док." == $text){return $text;}
        $newtext = "";
        $years = explode("/",$text);
        if($years[0]<2015){
            $newtext.=($years[0]+1)."/".($years[1]+1);
        }else{
            $newtext=$text;
        }

        return $newtext;
    }

}
