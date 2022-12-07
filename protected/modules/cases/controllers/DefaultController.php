<?php

class DefaultController extends Controller
{
    public function actionIndex()
    {

        if (!Yii::app()->user->isGuest && Yii::app()->user->getPerStatus()) {
            $form = new CaseForm();
            $data = $form->GetDisciplines();
            $filter = new FilterForm;
            if (isset($_GET['FilterForm'])) {
                $filter->filters = $_GET['FilterForm'];
            }
            $pager = new CPagination();
            $pager->pageSize = 15;
            $dataProvider = new CArrayDataProvider($filter->arrayFilter($data), array(
                'pagination' => $pager,
                'keyField' => 'nrec',
                'sort' => array('attributes' => array('discipline', 'chair'))));

            $this->render('index', array(
                'dataProvider' => $dataProvider,
                'filter' => $filter
            ));
        } else {
            Yii::app()->controller->redirect(['/site/index']);
        }
    }

    public function actionCreate($nrec = null, $chair = null)
    {
        if (is_null($nrec) || is_null($chair) || Yii::app()->user->isGuest || !Yii::app()->user->getPerStatus()) {
            Yii::app()->controller->redirect(['/site/index']);
        } else {
            $form = new CaseForm();
            $chairs = $form->GetChairs((new WebUser)->getFnpp());
            $in_chair = $chairs && in_array('0x' . strtolower($chair), $chairs);

            if (!in_array((new WebUser)->getFnpp(), self::ADMIN_FNPP) && !$in_chair) {
                Yii::app()->controller->redirect(['/site/index']);
            } else {
                /*var_dump($form->GetDiscipline($nrec, $chair)['name']);
                var_dump($form->GetSpecialities($nrec, $chair));
                var_dump($chair);
                var_dump($nrec);*/
                $specialities = $form->GetSpecialities($nrec, $chair);
                $newSpecialities = array();
//                var_dump($specialities);
                foreach ($specialities as $spec) {
                    $names = $spec['nameConcat'];
                    $groups = explode(',', $names);
                    $groupMasks = array();
                    if (count($groups) > 1) {
                        foreach ($groups as $group) {
                            $group = trim($group);
                            $parts = explode('-', $group);
                            if (count($parts[1]) > 3) {
                                $groupMasks[] = $group;
                            } else {
                                $mask = substr($group, 0, -1) . '*';
                                if (!in_array($mask, $groupMasks)) {
                                    $groupMasks[] = $mask;
                                } else {
                                    continue;
                                }
                            }
                        }
                    } else {
                        $groupMasks[] = $groups[0];
                    }
                    $groupMasks = implode(', ', $groupMasks);
                    //var_dump($groupMasks);
                    $spec['codeName'] = $spec['codeName'] . '(' . $groupMasks . ')';
                    //    var_dump($spec['codeName']);
                    $newSpecialities[] = $spec;
                }
                $this->render('create', array(
                    'disciplineName' => $form->GetDiscipline($nrec, $chair)['name'],
                    'specialities' => $newSpecialities,
                    'chair' => $chair,
                    'discipline' => $nrec
                ));
            }
        }
    }

    public function actionDownload($curr, $dis, $chair)
    {
        ini_set('upload_tmp_dir', '/data/www/up/tmp');
        if (is_null($curr) || is_null($dis) || Yii::app()->user->isGuest || !Yii::app()->user->getPerStatus()) {
            Yii::app()->controller->redirect(['/site/index']);
        } else {
            $form = new CaseForm();
            $curdis = $form->GetCurDis($curr, $dis);
            CaseForm::GetDocument(array(
                'disName' => $curdis['name'],
                'chair' => $chair,
                'chairName' => $curdis['chair'],
                'curdis' => $curdis['curdis']
            ));
        }
    }


    protected function beforeAction($action)
    {
        $wkard220 = Wkardc_rp::model()->findByAttributes(array('fnpp' => Yii::app()->user->getFnpp(), 'prudal' => 0, 'struct' => 220));
        $wkard220 = $wkard220 instanceof Wkardc_rp;

        if (!in_array(Yii::app()->user->getFnpp(), Controller::ADMIN_FNPP)) {
            if (!$wkard220) {
                $this->redirect(array('/site'));
            }
            if (!isset(Yii::app()->session['ApiKey'])) {
                $key = ApiKeys::model()->findByAttributes(array('fnpp' => Yii::app()->user->getFnpp()));
                if ($key instanceof ApiKeys) {
                    Yii::app()->session['ApiKey'] = $key->apikey;
                } else {
                    $this->render('//admin/apikeys/apiKeyError', ['code' => 1002, 'text' => 'Отсутствует API-key']);
                    die;
                }
            };
        }
        return true;
    }
}