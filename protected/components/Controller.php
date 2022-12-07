<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

    const  ADMIN_FNPP =  ['768843', '38779', '768369', '671035', '778432', '772391'];

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column1';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    /**
     * Is params like nrec (varbinary[8])
     * @param array $params
     * @return boolean
     * @throws CHttpException
     */
    public static function checkNrec($params = array('id')) {
        foreach ($params as $param) {
            $value = Yii::app()->request->getParam($param);
            if (!preg_match('/^(0x)?[A-Z0-9]{16}$/i', $value)) {
                throw new CHttpException('500', "Некорректный $param " . CHtml::encode($value));
            }
        }
        return true;
    }

    /**
     * @param array $params
     * @return bool
     * @throws CHttpException
     */
    public static function checkfnpp() {
        if (!isset(Yii::app()->session['fnpp'])){
            //throw new CHttpException('501', "Ошибка авторизации");
            return Yii::app()->controller->redirect(['/site/index']);
        }

        return true;
    }

    public static function getCMenuItem() {
        if(Yii::app()->user->getPerStatus()){
            $pps = true;
        }else{
            $pps = false;
        }
        /*Блок проверок существования таблиц*/
        MonitorAccess::checkTables();
        /*end*/
        $Fnpp = Yii::app()->user->getFnpp();
        $KillThem = [0];
        if(in_array($Fnpp, $KillThem)){ $vis = false; }else{ $vis = true; }

        $cMenu = [];/*Массив меню*/

        $cMenu[] = array('label' => 'Главная', 'url' => array('/site/index'));

        if (InquiriesResponsibles::amIResponsible()) {
            $cMenu[] = array('label' => 'Заявки', 'url' => array('/inquiries/responsible'));
        }

        if(!Yii::app()->user->isGuest) {
            if ($pps) {
                /*надо потом сделать так что бы если массив из одного элемента передается то он сразу и отдавался*/
                if (!empty($Fnpp) and !empty(MonitorAccess::getDepartments($Fnpp))) {
                    $cMenu[] = array('label' => 'Мониторинг', 'url' => array('/chiefs'));
                }
                if ((in_array($Fnpp, self::ADMIN_FNPP))) {
                    if (in_array($Fnpp, self::ADMIN_FNPP)) {
                        $cMenu[] = self::setSubMenu([
                            array('label' => 'Новости', 'url' => array('/news')),
                            array('label' => 'Ключи для API', 'url' => array('/admin/apikeys')),
                            array('label' => 'Конфигурация "Заявки"', 'url' => array('/inquiries/admin')),
                            array('label' => 'Права доступа', 'url' => array('/admin/access')),
                            array('label' => 'SQL-запросы', 'url' => array('/admin/querys')),
                            array('label' => 'Доступ к прошлой КР', 'url' => array('/admin/remote')),
                        ], '#', 'Админ', (!Yii::app()->user->isGuest and in_array($Fnpp, self::ADMIN_FNPP)));
                    } else {
                        $cMenu[] = array('label' => 'Новости', 'url' => array('/news'));
                    }
                }
                /*$wkard220 = Wkardc_rp::model()->findByAttributes(array('fnpp' => $Fnpp, 'prudal' => 0, 'struct' => 220));
                if ($wkard220 instanceof Wkardc_rp) {
                    $wkard220 = true;
                } else {
                    $wkard220 = false;
                }*/
                if (in_array($Fnpp, Controller::ADMIN_FNPP)) {
                    $cMenu[] = array('label' => 'Рабочие программы', 'url' => array('/cases'));
                }
/*                $cMenu[] = self::setSubMenu([
//                array('label' => 'Заявки на закупку и заправку картриджей', 'url' => array('/zak'), 'visible'=>(!empty($Fnpp) and !empty(self::getFinanciallyResponsible($Fnpp)))),
                    array('label' => 'Реестр аукционов', 'url' => array('/zak/auctions'), 'visible' => (in_array($Fnpp, ['1556', '39788', '683679', '295', '1531']))),
                    array('label' => 'Заявки на закупку оборудования', 'url' => array('/zak/oborud')),
                    array('label' => 'Заявки на закупку расходных материалов', 'url' => array('/zak/expendable')),
                    array('label' => 'Заявки на закупку программного обеспечения', 'url' => array('/zak/software')),
                ], '#', 'Заявки на закупки', (!Yii::app()->user->isGuest and $pps and !empty($Fnpp) and !empty(self::getFinanciallyResponsible($Fnpp))));*/
                $cMenu[] = array('label' => 'Контактная работа', 'url' => array('/remote'), 'visible' => (!Yii::app()->user->isGuest and $pps));
                $cMenu[] = self::setSubMenu([
                    array('label' => 'Журнал посещаемости', 'url' => array('/attendance/index'), 'visible' => (!Yii::app()->user->isGuest and $pps)),
                    array('label' => 'Ведомости', 'url' => array('/studyProcess/mark'), 'visible' => (!Yii::app()->user->isGuest and $pps)),
                    array('label' => 'Направления', 'url' => array('/studyProcess/extramark'), 'visible' => (!Yii::app()->user->isGuest and $pps)),
                ], '#', 'Учебный процесс', (!Yii::app()->user->isGuest and $pps));
                $cMenu[] = array('label' => 'Индивидуальный план', 'url' => array('/individualplan'), 'visible' => (!Yii::app()->user->isGuest and $pps));
            } else {
//            $galIdCount = count(Yii::app()->user->getGalIdMass());
                $galIdCount = count(Yii::app()->user->getStudentCards());
//            $galIdCount = 0;
                $cMenu[] = array('label' => 'Контактная работа', 'url' => array('/remote/read'), 'visible' => (!Yii::app()->user->isGuest and !$pps));
                $cMenu[] = array('label' => 'Портфолио', 'url' => array('/portfolio/index'), 'visible' => (!Yii::app()->user->isGuest and !$pps));
                $cMenu[] = array('label' => 'Мои данные', 'url' => array('/personalcard/index'), 'visible' => (!Yii::app()->user->isGuest and ($galIdCount <= 1) and !$pps and $vis));
                $cMenu[] = array('label' => 'Мои данные', 'url' => array('/personalcard/choiseBook'), 'visible' => (!Yii::app()->user->isGuest and ($galIdCount > 1) and !$pps and $vis));
                $cMenu[] = self::setSubMenu([
                    array('label' => 'Зачетная книжка', 'url' => array('/student/index'), 'visible' => (!Yii::app()->user->isGuest and ($galIdCount <= 1) and !$pps)),
                    array('label' => 'Зачетные книжки', 'url' => array('/student/choiseBook'), 'visible' => (!Yii::app()->user->isGuest and ($galIdCount > 1) and !$pps)),
                    array('label' => 'Журнал посещаемости', 'url' => array('/journal/index'), 'visible' => (!Yii::app()->user->isGuest and !$pps)),
                    array('label' => 'Ваши направления на пересдачу', 'url' => array('/educationWork/extralist'), 'visible' => (!Yii::app()->user->isGuest and !$pps)),
                ], '#', 'Учебный процесс', (!Yii::app()->user->isGuest and !$pps));

                $cMenu[] = array('label' => 'Мои заявки', 'url' => array('/inquiries'), 'visible' => (!Yii::app()->user->isGuest and !$pps));
            }
        }
        $cMenu[] = array('label'=>'Вход', 'url'=> Yii::app()->user->loginUrl, 'visible'=>Yii::app()->user->isGuest);
        //$cMenu[] = array('label'=>'Запасной вход', 'url'=>array('/site/login') , 'visible'=>Yii::app()->user->isGuest);

        if (!Yii::app()->user->isGuest) {
            $arr = explode(' ', Yii::app()->user->model->attributes['firstName']);
            $name = mb_substr($arr[0], 0, 1) . '.';
            $secondName = (isset($arr[1])) ? mb_substr($arr[1], 0, 1) . '.' : '';
            $fio = Yii::app()->user->model->attributes['lastName'] . ' ' . $name . $secondName;
        } else {
            $fio = '';
        }
        $cMenu[] = array('label' => 'Выход (' . $fio . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest);

        return $cMenu;
    }

    public static function setSubMenu($items, $url, $label, $vis) {
        $submenu = ['submenuOptions' => array('class' => 'dropdown-menu'),
            'linkOptions' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'button',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false'
            ),
            'url'=>$url,
            'label' => $label . ' <span class="caret"></span>',
            'items' => $items,
            'visible'=>$vis,
            ];

        return $submenu;

    }


    public static function getFinanciallyResponsible($Fnpp){
        if (in_array($Fnpp, Controller::ADMIN_FNPP)) {
            return true;
        }
        $return = Yii::app()->db2->createCommand()
            ->select('sdr.npp')
            ->from('fdata f')
            ->join('wkardc_rp wr', 'f.npp = wr.fnpp')
            ->join('struct_d_rp sdr', 'sdr.npp = wr.struct')
            ->where('wr.МатОтв = 1
                AND wr.prudal = 0
                AND f.npp = ' . $Fnpp)
            ->queryAll();
        return $return;
    }


}
