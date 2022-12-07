<?php

class DefaultController extends Controller {

    public $layout = '//layouts/column1';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index',
                    'giveTheTask',
                    'taskList',
                    'studentContacts',
                    'reportFiles'
                ),
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
        if(!ReadClass::checkpps()){
            $this->redirect(array('/remote/read'));
        }
        return true;
    }

    public function actionIndex() {
        RemoteModule::checkMeIn($this);
        $list = Yii::app()->db2->createCommand()
            ->selectDistinct('ats.discipline, agg.name \'group\', agg.id groupId, HEX(ats.disciplineNrec) disciplineId
            , CASE agg.wformed WHEN 0 THEN \'Очная\' WHEN 1 THEN \'Заочная\' WHEN 2 THEN \'Вечерняя\' END formed
            , (SELECT COUNT(*) FROM remote_task_list rtl WHERE rtl.`group` = ats.studGroupId AND rtl.discipline = ats.disciplineNrec) \'count\'
            , \'\' extraText
            , (SELECT COUNT(*) FROM fdata f LEFT JOIN skard s ON s.fnpp = f.npp
            LEFT JOIN ecab.vkrfiles v ON v.fnpp = f.npp
            WHERE agg.name = s.gruppa AND v.disc = HEX(ats.disciplineNrec) AND v.state NOT IN (1, 2) and prudal = 0) newWorks'
            )
            ->from('attendance_schedule ats')
            ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
            ->where(array('and',
                'ats.teacherFnpp = '.Yii::app()->user->getFnpp(),
                'ats.dateTimeStartOfClasses > \''.date(RemoteModule::startDate()).'\'',
                array('in','agg.wformed',array(0,2)),
                'agg.warch = 0'
            ))
            ->orWhere(array('and',
                'ats.teacherFnpp = '.Yii::app()->user->getFnpp(),
                'agg.wformed = 1',
                'agg.warch = 0',
                'ats.dateTimeStartOfClasses > \''.date(RemoteModule::startIZODate()).'\''
            ))
            ->queryAll();

        $listExtra = Yii::app()->db2->createCommand()
            ->selectDistinct('gud.name discipline, agg.name \'group\', agg.id groupId, HEX(ret.discipline) disciplineId
            , CASE agg.wformed WHEN 0 THEN \'Очная\' WHEN 1 THEN \'Заочная\' WHEN 2 THEN \'Вечерняя\' END formed
            , (SELECT COUNT(*) FROM remote_task_list rtl WHERE rtl.`group` = ret.group AND rtl.discipline = ret.discipline) \'count\'
            ,  \'(Дополнительное задание)\' extraText
            , (SELECT COUNT(*) FROM fdata f LEFT JOIN skard s ON s.fnpp = f.npp
            LEFT JOIN ecab.vkrfiles v ON v.fnpp = f.npp
            WHERE agg.name = s.gruppa AND v.disc = HEX(gud.nrec) AND v.state NOT IN (1, 2)) newWorks'
            )
            ->from('remote_extra_task ret')
            ->join('attendance_galruz_group agg', 'agg.id = ret.group')
            ->join('gal_u_discipline gud', 'gud.nrec = ret.discipline')
            ->where(array('and',
                'ret.teacher = '.Yii::app()->user->getFnpp(), 'agg.warch = 0',
            ))
            ->queryAll();
        $list = array_merge($list, $listExtra);

        $this->layout='//layouts/column1';
        $this->render('index', ['list' => $list]);
    }

    public function actionGiveTheTask($group, $discipline) {
//        RemoteModule::checkMyGroupDiscipline($this, $group, $discipline);
        $model=new RemoteTaskList();
        $model->discipline = hex2bin(CMisc::_bn($discipline));
        $model->group = $group;
        if(!empty($_POST['RemoteTaskList']))
        {
            $model->comment = $_POST['RemoteTaskList']['comment'];
            if(!empty(Yii::app()->user->getFnpp())){$fnpp = Yii::app()->user->getFnpp();}else{$fnpp = null;}
            $model->author_fnpp = $fnpp;
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
                }elseif (isset($_POST['checkboxStream'])){
                    foreach (RemoteModule::getStream($group, $discipline, 'array') as $item){
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

                $this->redirect(array('taskList', 'group' => $group, 'discipline' => $discipline));
            }
        }
        $this->layout='//layouts/column1';
        $this->render('giveTheTask', ['model'=>$model]);
    }

    public function actionEditTask($id) {
        $model=RemoteTaskList::model()->findByPk($id);
//        RemoteModule::checkMyGroupDiscipline($this, $model->group, bin2hex($model->discipline));

        if(!empty($_POST['RemoteTaskList']))
        {
            $model->comment = $_POST['RemoteTaskList']['comment'];
            if(!empty(Yii::app()->user->getFnpp())){$fnpp = Yii::app()->user->getFnpp();}else{$fnpp = null;}
            $model->author_fnpp = $fnpp;
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

                $this->redirect(array('taskList', 'group' => $model->group, 'discipline' => bin2hex($model->discipline)));
            }
        }
        $this->layout='//layouts/column1';
        $this->render('editTask', ['model'=>$model]);
    }

    public function actionTaskList($group, $discipline) {
//        RemoteModule::checkMyGroupDiscipline($this, $group, $discipline);
        $list = RemoteTaskList::model()->findAllByAttributes(array('group' => $group, 'discipline' => hex2bin(CMisc::_bn($discipline)))
            , array('order' => 'create_date DESC'));

        $this->layout='//layouts/column1';
        $this->render('taskList', ['list' => $list, 'group' => $group, 'discipline' => $discipline]);
    }

    public function actionDownloadFile($id, $name) {
        $save_path = RemoteModule::uploadPath($id);
        $file = $save_path.$id.'/'.$name;
        if(file_exists($file)){
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"" . $name . "\"");
            readfile($file);
        }
    }

    public function actionDeleteFile($id, $name) {
        $save_path = RemoteModule::uploadPath($id);
        $file = $save_path.$id.'/'.$name;
        if(file_exists($file)){
            unlink($file);
        }
    }

    public function actionMailGroup($id) {
        $model = RemoteTaskList::model()->findByPk($id);
        if($model->send_mail){$this->redirect(array('taskList', 'group' => $model->group, 'discipline' => bin2hex($model->discipline))); }
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

        //if(count($good_email) > 0) {
            $model->send_mail = 1;
            $model->save();
        //}

        $group = AttendanceGalruzGroup::model()->findByPk($model->group);

        $this->render('mailGroup', ['id' => $id, 'good_email' => $good_email, 'bad_email' => $bad_email, 'group' => $group, 'discipline' => $discipline]);
    }

    public function actionDeleteTask($id) {
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

        $this->redirect(array('taskList', 'group' => $group, 'discipline' => $discipline));
    }

    public function actionStudentContacts()
    {
        $return = '';
        if($_POST['group']){
            $group = $_POST['group'];
            $sql = 'SELECT DISTINCT f.email, CONCAT(f.fam,\' \',f.nam,\' \',f.otc) \'fio\'
              FROM fdata f 
              LEFT JOIN skard s ON s.fnpp = f.npp
              LEFT JOIN attendance_galruz_group agg ON agg.name = s.gruppa
              INNER JOIN gal_u_student gus ON gus.nrec = s.gal_srec AND gus.fio = CONCAT(f.fam, \' \', f.nam,\' \',f.otc)
              WHERE agg.id = '.$group.'
              AND s.prudal = 0
              GROUP BY f.npp
              ORDER BY CONCAT(f.fam,\' \',f.nam,\' \',f.otc)';
            $list_email = Yii::app()->db2->createCommand($sql)->queryAll();

            $return .= '<div class="jumbotron" style="height: 500px;">';
            $return .= '<div style="overflow-y: scroll; height: 100%; border: 1px #9e9e9e solid">';
            $return .= '<table class="table table-striped _table-ulist static table-hover" style="margin-bottom: 0px;">';
            $return .= '<tr><td><center><b>ФИО</b></center></td><td><center><b>Email</b></center></td></tr>';
            foreach ($list_email as $item) {
                $return .= '<tr>';
                $return .= '<td>'.$item['fio'].'</td>';
                $return .= '<td>'.$item['email'].'</td>';
                $return .= '</tr>';
            }
            $return .= '</table>';
            $return .= '</div>';
            $return .= '</div>';

        }

        echo CJSON::encode(array('success' => $return));
    }


    public function actionReportFiles()
    {
        $return = '';
        if($_POST['group'] and $_POST['discipline']){
            //var_dump($_POST);die;
            $group = $_POST['group'];
            $discipline = $_POST['discipline'];
            $find_semester = $_POST['semester'];
            $modelGroup = AttendanceGalruzGroup::model()->findByPk($group);
            $modelDiscipline = uDiscipline::model()->findByAttributes(array('nrec' => hex2bin($discipline) ) );
            $return .= '<center><h2>Группа: '.$modelGroup->name.'</h2></center>'.CHtml::hiddenField('groupId', $modelGroup->id);
            $return .= '<center><h4>Дисциплина: '.$modelDiscipline->name.'</h4></center>'.CHtml::hiddenField('disciplineId', $discipline);

            $semesters = ReadClass::getAllDisciplineSemesterFromGroup($modelGroup->id, $modelDiscipline->nrec);
            //var_dump($semesters);die;
            foreach ($semesters as $s) {
                if ($find_semester == 'undefined') {
                    if ($s['checkWorks'] != $s['allWorks']) {
                        $find_semester = $s['sem'];
                    }
                }
            }
            if ($find_semester == 'undefined') {
                $find_semester = $semesters[0]['sem'];
            }
            $return .= '<div class="btn-group btn-group-justified" role="group">';
            //var_dump($return);die;
            foreach ($semesters as $s){
                $return .= '<div class="btn-group" role="group">
                            <button type="button" class="btn btn-default '.(($s['sem'] == $find_semester)?'active':'').' reportFiles">семестр '.$s['sem'].' ('.(($s['checkWorks']!=$s['allWorks'])?'<b>'.$s['checkWorks'].'/'.$s['allWorks'].'</b>':$s['checkWorks'].'/'.$s['allWorks'])
                    .')</button>'.CHtml::hiddenField('semester', $s['sem']).'
                        </div>';
            }
            $return .= '</div>';

            $return .= '<div class="jumbotron" style="height: 500px; padding-right: 0px; padding-left: 0px;">';
            $return .= '<div style="overflow-y: scroll; height: 100%; border: 1px #9e9e9e solid">';
            //var_dump($return);die;
            $sql = 'SELECT DISTINCT f.npp, v.id, from_unixtime(v.unixdate,\'%d.%m.%Y\') dtime, COUNT(*) OVER (PARTITION BY f.npp) \'countFilesToMe\'
              FROM fdata f 
              LEFT JOIN skard s ON s.fnpp = f.npp
              LEFT JOIN attendance_galruz_group agg ON agg.name = s.gruppa
              INNER JOIN gal_u_student gus ON gus.nrec = s.gal_srec AND gus.fio = CONCAT(f.fam, \' \', f.nam,\' \',f.otc)
              LEFT JOIN ecab.vkrfiles v ON v.fnpp = f.npp AND v.disc = \''.$discipline.'\' AND v.semester = '.$find_semester.'
              WHERE agg.id = '.$group.'
              AND s.prudal = 0
              and (f.webpwd is not null and f.webpwd not in (\'\', \'not from web\'))
              GROUP BY f.npp, v.id
              ORDER BY CONCAT(f.fam,\' \',f.nam,\' \',f.otc), v.id desc';
            $files = Yii::app()->db2->createCommand($sql)->queryAll();
            //var_dump($find_semester, $files);die;
            $return .= '<table class="table table-striped _table-ulist static table-hover" style="margin-bottom: 0px;">';
            $return .= '<tr><th style="width: 20%" ><center>ФИО</center></th><th style="width: 20%"><center>Файла</center></th>
            <th style="width: 20%"><center>Операции</center></th> <th style="width: 40%"><center>Комментарий (250 символов)</center></th></tr>';
            $prevFnpp = 0;
            //var_dump($files);die;

            foreach ($files as $file) {
                $return .= '<tr style="border-top: 2px black solid;">';
                if($file['npp'] != $prevFnpp) {
                    $return .= '<td rowspan="' . $file['countFilesToMe'] . '" style="width:" >' . Fdata::model()->findByPk($file['npp'])->getFIO() . '</td>';
                    $prevFnpp = $file['npp'];
                }
                $vkrfile = Vkrfiles::model()->findByPk($file['id']);
                if(!($vkrfile instanceof Vkrfiles)){
                    $return .= '<td class="warning" colspan="3"><center>Работа не загружена</center></td>';
                    continue;
                }
                if ($vkrfile->state == 1){
                    $classBtn = 'btn-success';
                    $classFont = 'success';
                    $tooltip = "Работа принята";
                } elseif ($vkrfile->state == 2) {
                    $classBtn = 'btn-danger';
                    $classFont = 'danger';
                    $tooltip = "Работа отклонена";
                } else {
                    $classBtn = 'btn-info';
                    $classFont = 'info';
                    $tooltip = "Работа не проверена";
                }

                $return .= '<td class="'. $classFont .'">';
                $return .= '<span id="tooltipId" rel="tooltip" title= "'. $tooltip.'" data-toggle="tooltip" >' . '<div class="dtime">'.
                    CHtml::link($vkrfile->name .' ('. round(($vkrfile->size)/1024/1024,2) .' Мбайт, ' .$file['dtime'] .')</div>' , array('/studyProcess/mark/downLoadWorkFile','id' => $vkrfile->id), [
                        'class' => ' btn btn-mine ' .$classBtn ,
                        'style' => 'white-space: normal',
                        'target' => '_blank'
                    ]) .  '</span>';
                $return .= '</td>';

                $return .= '<td style="text-align: center" class="'. $classFont .'">';
                $return .= '<span rel="tooltip" title= "Принять работу" data-toggle="tooltip" >' . CHtml::link('', '#', [
                        'class' => 'btn btn-success glyphicon glyphicon-ok',
                        'style' => 'vertical-align: top',
                        'id' => 'stateOfFile'
                    ]). '</span>';
                $return .=  '<span rel="tooltip" title= "Отклонить работу" data-toggle="tooltip" >' . CHtml::link('', '#', [
                        'class' => 'btn btn-danger glyphicon glyphicon-remove',
                        'style' => 'vertical-align: top',
                        'id' => 'stateOfFile'
                    ]). '</span>';
                $return .=   '<span rel="tooltip" title= "Снять статус с работы" data-toggle="tooltip" >' . CHtml::link('', '#', [
                        'class' => 'btn btn-info glyphicon glyphicon-ban-circle',
                        'style' => 'vertical-align: top',
                        'id' => 'stateOfFile'
                    ]). '</span>';

                $return .= '</td>';

                $return .= '<td class="'. $classFont .'">';
                $return .= CHtml::textArea('commentFieldText', $vkrfile->comment, ['class' => 'form-control', 'maxlength'=>254, 'rows' => 5, 'style'=>'resize: vertical;']).
                    CHtml::hiddenField('commentFieldId', $vkrfile->id);
                $return .= '</td>';

                $return .= '</tr>';
            }
            $return .= '</table>';
            $return .= '</div>';
            $return .= '</div>';
        }

        echo CJSON::encode(array('success' => $return));
    }

}
