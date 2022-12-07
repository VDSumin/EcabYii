<?php

class ChairController extends Controller {

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index',
                    'giveTheTask',
                    'taskList',
                    'extraTask', 'addExtraTask', 'saveExtraTask', 'editExtraTask', 'deleteExtraTask', 'fullListGroup'),
                'users' => array('*'),
                'expression' => 'Controller::checkfnpp()',
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    protected function beforeAction($action) {
        if(Yii::app()->user->getFnpp() == null){
            $this->redirect(array('/site'));
        }
        if(ChairClass::getMyStruct(false) == null){
            $this->redirect(array('/remote'));
        }
        return true;
    }


    public function actionIndex() {
        $chair = ChairClass::getMyStruct(false);
        $list = ChairClass::getChairList($chair);
        $actingList = ChairClass::getActing($list, $chair);
        $this->render('index', ['list' => $list, 'chair' => $chair, 'actingList' => $actingList]);
    }

    public function actionActing() {
        $chair = ChairClass::getMyStruct(false);
        $list = ChairClass::getChairList($chair);
        $actingList = ChairClass::getActing($list, $chair);
        $this->render('acting', ['list' => $list, 'chair' => $chair, 'actingList' => $actingList]);
    }

    public function actionPersonList($id) {
        $list = Yii::app()->db2->createCommand()
            ->selectDistinct('ats.discipline, HEX(ats.disciplineNrec) disciplineId, agg.name \'group\', agg.id groupId
            , (SELECT REPLACE(GROUP_CONCAT(DISTINCT ats1.teacherFio),\',\',\', \') FROM attendance_schedule ats1 
            WHERE ats1.studGroupId = agg.id AND ats1.disciplineNrec = ats.disciplineNrec) examiners
            , CASE agg.wformed WHEN 0 THEN \'Очная\' WHEN 1 THEN \'Заочная\' WHEN 2 THEN \'Вечерняя\' END formed
            , (SELECT COUNT(*) FROM remote_task_list rtl WHERE rtl.`group` = ats.studGroupId AND rtl.discipline = ats.disciplineNrec) \'count\' ' )
            ->from('attendance_schedule ats')
            ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
            ->where(array('and',
                'ats.teacherFnpp = '.$id,
                'ats.dateTimeStartOfClasses > \''.date(RemoteModule::startDate()).'\'',
                array('in','agg.wformed',array(0,2)),
                'agg.warch = 0'
            ))
            ->orWhere(array('and',
                'ats.teacherFnpp = '.$id,
                'agg.wformed = 1',
                'agg.warch = 0',
                'ats.semesterStartDate != CONVERT(\'2020-09-01\', datetime)',
                'ats.yearOfEducation = 2019'
            ))
            ->orWhere(array('and',
                'ats.teacherFnpp = '.$id,
                'agg.wformed = 1',
                'agg.warch = 0',
                'ats.dateTimeStartOfClasses > \''.date('2020-08-31').'\'',
            ))
            ->group('ats.discipline, ats.disciplineNrec, agg.id')
            ->queryAll();
        $listExtra = Yii::app()->db2->createCommand()
            ->selectDistinct('gud.name discipline, HEX(ret.discipline) disciplineId, agg.name \'group\', agg.id groupId
            , (SELECT DISTINCT REPLACE(GROUP_CONCAT(DISTINCT UPPER(CONCAT_WS(\' \', ft.fam, LEFT(ft.nam,1), LEFT(ft.otc,1)))),\',\',\', \') FROM remote_extra_task rett 
            LEFT JOIN fdata ft ON ft.npp = rett.teacher WHERE rett.discipline = ret.discipline AND rett.`group` = ret.`group`) examiners
            , CASE agg.wformed WHEN 0 THEN \'Очная\' WHEN 1 THEN \'Заочная\' WHEN 2 THEN \'Вечерняя\' END formed
            , (SELECT COUNT(*) FROM remote_task_list rtl WHERE rtl.`group` = ret.group AND rtl.discipline = ret.discipline) \'count\''
            )
            ->from('remote_extra_task ret')
            ->join('attendance_galruz_group agg', 'agg.id = ret.group')
            ->join('gal_u_discipline gud', 'gud.nrec = ret.discipline')
            ->join('fdata f', 'f.npp = ret.teacher')
            ->where(array('and', 'ret.teacher = '.$id,))
            ->group('ret.discipline')
            ->queryAll();
        $list = array_merge($list, $listExtra);

        $this->layout='//layouts/column1';
        $this->render('personList', ['list' => $list, 'person' => $id]);
    }

    public function actionTaskList($fnpp, $group, $discipline) {
        $list = RemoteTaskList::model()->findAllByAttributes(array('group' => $group, 'discipline' => hex2bin(CMisc::_bn($discipline)))
            , array('order' => 'create_date DESC'));

        $this->layout='//layouts/column1';
        $this->render('taskList', ['list' => $list, 'group' => $group, 'discipline' => $discipline, 'person' => $fnpp]);
    }

    public function actionActing_chiefPerson($fnpp) {
        $wkards = Wkardc_rp::model()->findAllByAttributes(array('fnpp' => $fnpp, 'prudal' => 0));
        foreach ($wkards as $row){
            if(in_array($row->structD->npp, ChairClass::getMyStruct(false))) {
                $struct = $row->structD->npp;
                break;
            }
        }
        $right = RemoteRights::model()->findByAttributes(array('fnpp'=>$fnpp, 'struct' => $struct, 'role' => 1));
        if($right instanceof RemoteRights){
            $right->delete();
        }else{
            $right = new RemoteRights();
            $right->fnpp = $fnpp;
            $right->struct = $struct;
            $right->role = 1;
            $right->author = Yii::app()->user->getFnpp();
            $right->date= date("Y-m-d H:i:s");
            $right->save();
        }
        $this->redirect(array('/remote/chair/acting'));
    }

    public function actionGiveTheTask($fnpp, $group, $discipline) {
        $model=new RemoteTaskList();
        $model->discipline = hex2bin(CMisc::_bn($discipline));
        $model->group = $group;
        if(!empty($_POST['RemoteTaskList']))
        {
            $model->comment = $_POST['RemoteTaskList']['comment'];
            $model->author_fnpp = (!empty(Yii::app()->user->getFnpp()))?Yii::app()->user->getFnpp():$fnpp;
            $model->create_date = date("Y-m-d H:i:s");

            if($model->save()){
                $save_path = RemoteModule::uploadPath($model->id);
                if (!file_exists($save_path.$model->id)) { mkdir($save_path.$model->id);}

                if(!empty($_FILES)) {
                    $files = $_FILES['RemoteFiles'];
                    $count_file = count($files['name']);
                    for ($i = 0; $i < $count_file; $i++) {
                        $file = $files['name'][$i];
                        $size = $files['size'][$i];
                        if($file != '' && $size < 10485760) {
                            $path = pathinfo($file);
                            $filename = $path['filename'];
                            $ext = $path['extension'];
                            $temp_name = $files['tmp_name'][$i];
                            $path_filename_ext = $save_path . $model->id . '/' . $filename . "." . $ext;

                            if (!file_exists($path_filename_ext)) {
                                move_uploaded_file($temp_name, $path_filename_ext);
                            }
                        }
                    }
                }

                if(isset($_POST['checkboxAll'])){
                    foreach (RemoteModule::getAllGroup($group, $discipline, 'array') as $item){
                        $modelOther = new RemoteTaskList();
                        $modelOther->discipline = $model->discipline;
                        $modelOther->group = $item['id'];
                        $modelOther->comment = $model->comment;
                        $modelOther->author_fnpp = $model->author_fnpp;
                        $modelOther->create_date = $model->create_date;
                        $modelOther->file_from = $model->id;
                        if($modelOther->save()){
                            if (!file_exists($save_path.$modelOther->id)) { mkdir($save_path.$modelOther->id);}
                        }
                    }
                }

                $this->redirect(array('taskList', 'fnpp' => $fnpp, 'group' => $group, 'discipline' => $discipline));
            }
        }
        $this->layout='//layouts/column1';
        $this->render('giveTheTask', ['model'=>$model, 'person' => $fnpp]);
    }

    public function actionEditTask($fnpp, $id) {
        $model=RemoteTaskList::model()->findByPk($id);

        if(!empty($_POST['RemoteTaskList']))
        {
            $model->comment = $_POST['RemoteTaskList']['comment'];
            $model->author_fnpp = (!empty(Yii::app()->user->getFnpp()))?Yii::app()->user->getFnpp():$fnpp;
            $model->create_date = date("Y-m-d H:i:s");
            if($model->save()){
                $id_from = $model->id;
                if($model->file_from != 0){ $id_from = $model->file_from; }
                $save_path = RemoteModule::uploadPath($id_from);
                if (!file_exists($save_path.$id_from)) { mkdir($save_path.$id_from);}

                if(!empty($_FILES)) {
                    $files = $_FILES['RemoteFiles'];
                    $count_file = count($files['name']);
                    for ($i = 0; $i < $count_file; $i++) {
                        $file = $files['name'][$i];
                        $size = $files['size'][$i];
                        if($file != '' && $size < 10485760) {
                            $path = pathinfo($file);
                            $filename = $path['filename'];
                            $ext = $path['extension'];
                            $temp_name = $files['tmp_name'][$i];
                            $path_filename_ext = $save_path . $id_from . '/' . $filename . "." . $ext;

                            if (!file_exists($path_filename_ext)) {
                                move_uploaded_file($temp_name, $path_filename_ext);
                            }
                        }
                    }
                }

                $this->redirect(array('taskList', 'fnpp' => $fnpp, 'group' => $model->group, 'discipline' => bin2hex($model->discipline)));
            }
        }
        $this->layout='//layouts/column1';
        $this->render('editTask', ['model'=>$model, 'person' => $fnpp]);
    }

    public function actionMailGroup($fnpp, $id) {
        $model = RemoteTaskList::model()->findByPk($id);
        if($model->send_mail){$this->redirect(array('taskList', 'fnpp' => $fnpp, 'group' => $model->group, 'discipline' => bin2hex($model->discipline))); }
        $discipline = uDiscipline::model()->findByPk($model->discipline);
        $teacher = Fdata::model()->findByPk($model->author_fnpp);
        $sql = 'SELECT DISTINCT f.email, CONCAT(f.fam,\' \',f.nam,\' \',f.otc) \'fio\'
      FROM fdata f 
      LEFT JOIN skard s ON s.fnpp = f.npp
      LEFT JOIN attendance_galruz_group agg ON agg.name = s.gruppa
      INNER JOIN gal_u_student gus ON gus.nrec = s.gal_srec AND gus.fio = CONCAT(f.fam, \' \', f.nam,\' \',f.otc)
      WHERE agg.id = '.$model->group.'
      AND s.prudal = 0
      GROUP BY f.npp';
        $list_email = Yii::app()->db2->createCommand($sql)->queryAll();

        $text = '<b>Дисциплина:</b> ' . $discipline->name . ' <br /> '.
            '<b>Преподаватель:</b> ' . $teacher->getFIO() . ' <br /> '.
            '<b>Задание:</b> <br/>' . $model->comment;

        $text .= '<br /> <hr />'.'Чтобы просмотреть выданные задания необходимо с главной страницы ОмГТУ перейти в "Сервисы -> Электронный кабинет -> Студенческий портал -> Контактная работа"';

        $bad_email = [];
        $good_email = [];
        foreach ($list_email as $one_email) {
            $mail = new YiiMailer();
            $mail->setFrom('edu.noreply@omgtu.tech', 'ОмГТУ. Рассылка учебного портала');
            $mail->setTo($one_email['email']);
            $mail->setSubject('Новое задание на учебном портале ОмГТУ');
            foreach (RemoteModule::listfile($id, 'mail') as $one){
                $mail->addAttachment($one['tmp_name'],$one['name']);
            }
            $mail->setBody($text);

            if (!$mail->send()) {
                $bad_email[] = ['email' => $one_email['email'], 'fio' => $one_email['fio'],  'error' => $mail->getError()] ;
            }else{
                $good_email[] = ['email' => $one_email['email'], 'fio' => $one_email['fio']] ;
            }
            sleep(1);
        }

        if(count($good_email) > 0) {
            $model->send_mail = 1;
            $model->save();
        }

        $group = AttendanceGalruzGroup::model()->findByPk($model->group);

        $this->render('mailGroup', ['id' => $id, 'good_email' => $good_email, 'bad_email' => $bad_email, 'group' => $group, 'discipline' => $discipline, 'person' => $fnpp]);
    }

    public function actionDeleteTask($fnpp, $id) {
        $save_path = RemoteModule::uploadPath($id).$id;
        $files = RemoteModule::listfile($id, 'mail');
        $model = RemoteTaskList::model()->findByPk($id);
        $modelOther = RemoteTaskList::model()->findAllByAttributes(array('file_from' => $id));
        foreach ($modelOther as $modelItem){
            $modelItem->file_from = 0;
            $modelItem->save();
        }
        $group = $model->group;
        $discipline = bin2hex($model->discipline);
        foreach ($files as $f){
            unlink($f['tmp_name']);
        }
        rmdir($save_path);

        $model->delete();

        $this->redirect(array('taskList', 'fnpp' => $fnpp, 'group' => $group, 'discipline' => $discipline));
    }

    public function actionExtraTask() {
        $chair = ChairClass::getMyStruct(false);
        $list = RemoteExtraTask::model()->findAllByAttributes(array('chair' => $chair));

        $listTask = [];
        foreach ($list as $row){
            $listTask[] = [
                'id' => $row['id'],
                'discipline' => uDiscipline::model()->findByPk($row["discipline"])->name,
                'group' => AttendanceGalruzGroup::model()->findByPk($row["group"])->name,
                'teacher' => Fdata::model()->findByPk($row["teacher"])->getFIO(),
                'author' => Fdata::model()->findByPk($row["author_fnpp"])->getFIO(),
                'create_date' => $row['create_date']
            ];
        }

        $filter = new FilterForm;
        if (isset($_GET['FilterForm'])) {
            $filter->filters = $_GET['FilterForm'];
        }

        $pager = new CPagination();
        $pageSize = 15;
        $pager->pageSize = $pageSize;
        $dataProvider = new CArrayDataProvider(
            $filter->arrayFilter($listTask),
            array('pagination' => $pager, 'keyField' => 'id')
        );

        $this->render('extraTask', ['dataProvider' => $dataProvider,
            'filter' => $filter,
            'chair' => $chair
        ]);
    }

    public function actionAddExtraTask() {
        $chair = ChairClass::getMyStruct(false);
        $group = isset($_POST['group'])?$_POST['group']:'';

        $return = '<h3>Введите название группы</h3><br/>';
        $return .= CHtml::hiddenField("group", $group, array("class" => "group-id"));
        if($group != '') {
            $modelGroup = AttendanceGalruzGroup::model()->findByPk($group);
            $return .= CHtml::textField('groupName', $modelGroup->name, array('style' => 'width: 200px;', 'class' => 'form-group form-control chooseGroup'));
        }else{
            $return .= CHtml::textField('groupName', '', array('style' => 'width: 200px;', 'class' => 'form-group form-control chooseGroup'));
        }
        $return .= '<br/>';

        $return .= '<script> 
        $( ".chooseGroup" ).autocomplete({
            source: "' . Yii::app()->createAbsoluteUrl('/remote/chair/fullListGroup') .'",
            focus: function( event, ui ) {
              $(this).val(ui.item.label);
              return false;
            },
            select: function( event, ui ) {
            var $select = $(this);
            var groupHidden = $select.parents().find(\'.group-id\');
            var valueGroup = ui.item.value;
            $(this).val(ui.item.label);
            groupHidden.val(ui.item.value);
              $.ajax({
                \'url\' : \''. Yii::app()->createAbsoluteUrl('remote/chair/addExtraTask').'\',
                \'type\': \'post\',
                \'dataType\': \'json\',
                \'data\': \'group=\' + valueGroup,
                \'success\' : function(responce) {
                        if (responce.success) {
                            $(\'#modalExtraTask #modalExtraTaskContentFromJs\').html(responce.text);
                        }   
                    }
              });
            return false;
            },
            change: function( event, ui ) {
                var hidden = $(this).parents("td:first").find("input[type=hidden]");
                $(this).val(hidden.val() ? hidden.data(\'label\') : \'\');
                $.ajax({
                \'url\' : \''. Yii::app()->createAbsoluteUrl('remote/chair/addExtraTask').'\',
                \'type\': \'post\',
                \'dataType\': \'json\',
                \'success\' : function(responce) {
                        if (responce.success) {
                            $(\'#modalExtraTask #modalExtraTaskContentFromJs\').html(responce.text);
                        }   
                    }
              });
            }
        });
        </script>';

        if($group != '') {
            $return .= '<hr/>';
            $return .= '<h3>Выберите дисциплину</h3><br/>';
            $return .= CHtml::dropDownList('discipline', '',
                CHtml::listData(ChairClass::getAllDisciplineFromGroup($group), "id", "name")
                , array(
                    'prompt' => 'Дисциплины из планна группы',
                    'class' => 'selectDiscipline form-control'
                ));
            $return .= '<br/><hr/>';

            $list = ChairClass::getChairList($chair);
            $listTeacher = [];
            foreach ($list as $row){$listTeacher[$row['npp']] = $row->getFIO();}

            $return .= '<h3>Выберите преподователя</h3><br/>';
            $return .= CHtml::dropDownList('teacher', '',
                $listTeacher
                , array(
                    'prompt' => 'Преподователи кафедры',
                    'class' => 'selectTeacher form-control'
                ));
            $return .= '<br/><hr/>';
            $return .= CHtml::link('Сохранить', '', array('class' => 'isaveExtraTask btn btn-primary'));

        }

        echo CJSON::encode(array('success' => true, 'text' => $return));
    }

    public function actionFullListGroup($term, $force = false) {
        $sql = 'SELECT agg.id value, agg.name label FROM attendance_galruz_group agg WHERE agg.warch = 0 AND agg.name LIKE \''.$term.'%\'';
        $list = Yii::app()->db2->createCommand($sql)->queryAll();

        $maxCnt = 30;
        $result = array();
        foreach($list as $row) {
            $result[] = $row;
            if (--$maxCnt < 0) { break; }
        }
        echo CJSON::encode($result);
    }

    public function actionEditExtraTask() {

        if($_POST['id']){
            $model = RemoteExtraTask::model()->findByPk($_POST['id']);
            $return = '<h3>Изменить дополнительное задание для группы '.AttendanceGalruzGroup::model()->findByPk($model->group)->name.'</h3><br/>';

            $return .= '<hr/>';
            $return .= '<h3>Выберите дисциплину</h3><br/>';
            $return .= CHtml::dropDownList('discipline', strtoupper(bin2hex($model->discipline)),
                CHtml::listData(ChairClass::getAllDisciplineFromGroup($model->group), "id", "name")
                , array(
                    'prompt' => 'Дисциплины из планна группы',
                    'class' => 'selectDiscipline form-control'
                ));
            $return .= '<br/><hr/>';

            $list = ChairClass::getChairList($model->chair);
            $listTeacher = [];
            foreach ($list as $row){$listTeacher[$row['npp']] = $row->getFIO();}

            $return .= '<h3>Выберите преподователя</h3><br/>';
            $return .= CHtml::dropDownList('teacher', $model->teacher,
                $listTeacher
                , array(
                    'prompt' => 'Преподователи вашей кафедры',
                    'class' => 'selectTeacher form-control'
                ));
            $return .= '<br/><hr/>';
            $return .= CHtml::link('Сохранить', '', array('class' => 'usaveExtraTask btn btn-primary'));

        }

        echo CJSON::encode(array('success' => true, 'text' => $return));
    }

    public function actionSaveExtraTask() {
        $result = false;
        if(isset($_POST)) {
            if(isset($_POST['id'])){
                $model = RemoteExtraTask::model()->findByPk($_POST['id']);
                $model->discipline = hex2bin($_POST['discipline']);
                $model->group = $_POST['group'];
                $model->teacher = $_POST['teacher'];
                $model->create_date = date("Y-m-d H:i:s");
                $model->author_fnpp = Yii::app()->user->getFnpp();
                $result = $model->save();
            }else {
                $model = new RemoteExtraTask();
                $model->discipline = hex2bin($_POST['discipline']);
                $model->group = $_POST['group'];
                $model->teacher = $_POST['teacher'];
                $model->chair = ((ChairClass::getMyStruct(false)[0]) ? ChairClass::getMyStruct(false)[0] : null);
                $model->create_date = date("Y-m-d H:i:s");
                $model->author_fnpp = Yii::app()->user->getFnpp();
                $model->send_mail_date = null;
                $result = $model->save();
            }
        }
        echo CJSON::encode(array('success' => $result));
    }

    public function actionDeleteExtraTask() {
        $result = false;
        if(isset($_POST['id'])) {
            $model = RemoteExtraTask::model()->findByPk($_POST['id']);
            $result = $model->delete();
        }
        echo CJSON::encode(array('success' => $result));
    }
}
