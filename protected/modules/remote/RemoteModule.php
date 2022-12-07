<?php

class RemoteModule extends CWebModule
{

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'remote.models.*',
            'remote.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else {
            return false;
        }
    }

    public static function startDate()
    {
        return '2022-07-01';
    }

    public static function startIZODate()
    {
        return '2022-02-01';
    }

    public static function uploadPath($id = false)
    {
        if (!file_exists('protected/data/uploads/')) {
            mkdir('protected/data/uploads/');
        }
        if ($id) {
            $path = 'protected/data/uploads/' . 'remote_' . floor($id / 10000) . '/';
            if (!file_exists($path)) {
                mkdir($path);
            }
            return $path;
        } else {
            return 'protected/data/uploads/';
        }
    }

    public static function checkMeIn($form)
    {
        if (empty(Yii::app()->user->getFnpp())) {
            $form->redirect(array('/site'));
        }
    }

    public static function checkMyGroupDiscipline($form, $group, $discipline)
    {
        RemoteModule::checkMeIn($form);
        $check = Yii::app()->db2->createCommand()
            ->select('COUNT(*)')
            ->from('attendance_schedule ats')
            ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
            ->where(array('and',
                'ats.teacherFnpp = ' . Yii::app()->user->getFnpp(),
                'ats.dateTimeStartOfClasses > \'' . date(RemoteModule::startDate()) . '\'',
                array('in', 'agg.wformed', array(0, 2)),
                'agg.warch = 0',
                'HEX(ats.disciplineNrec) = \'' . $discipline . '\'',
                'agg.id = ' . $group
            ))
            ->orWhere(array('and',
                'ats.teacherFnpp = ' . Yii::app()->user->getFnpp(),
                'agg.wformed = 1',
                'agg.warch = 0',
                'ats.dateTimeStartOfClasses > \'' . date(RemoteModule::startIZODate()) . '\'',
//                'ats.dateTimeStartOfClasses > \''.date('2020-08-31').'\'',
                /*  'ats.dateTimeStartOfClasses < \''.date('2020-09-05').'\'',*/
                'HEX(ats.disciplineNrec) = \'' . $discipline . '\'',
                'agg.id = ' . $group
            ))
            ->queryScalar();
        if ($check == 0) {
            $form->redirect(array('index'));
        }
    }

    public static function getAllGroup($group, $discipline, $action)
    {
        $groupModel = AttendanceGalruzGroup::model()->findByPk($group);
        $list = Yii::app()->db2->createCommand()
            ->selectDistinct('agg.id, agg.name')
            ->from('attendance_schedule ats')
            ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
            ->where(array('and',
                'ats.teacherFnpp = ' . Yii::app()->user->getFnpp(),
                'ats.dateTimeStartOfClasses > \'' . date(RemoteModule::startDate()) . '\'',
                array('in', 'agg.wformed', array(0, 2)),
                'agg.warch = 0',
                'HEX(ats.disciplineNrec) = \'' . $discipline . '\'',
                'agg.id != ' . $group,
                'agg.course = ' . $groupModel->course,
                'agg.wformed = ' . $groupModel->wformed
            ))
            ->orWhere(array('and',
                'ats.teacherFnpp = ' . Yii::app()->user->getFnpp(),
                'agg.wformed = 1',
                'agg.warch = 0',
                'ats.dateTimeStartOfClasses > \'' . date(RemoteModule::startIZODate()) . '\'',
//                'ats.dateTimeStartOfClasses > \''.date('2020-08-31').'\'',
                /*  'ats.dateTimeStartOfClasses < \''.date('2020-09-05').'\'',*/
                'HEX(ats.disciplineNrec) = \'' . $discipline . '\'',
                'agg.id != ' . $group,
                'agg.course = ' . $groupModel->course,
                'agg.wformed = ' . $groupModel->wformed
            ))->queryAll();
        if ($action == 'count') {
            return count($list);
        }
        if ($action == 'list') {
            $return_string = '';
            foreach ($list as $item) {
                $return_string .= $item['name'] . ', ';
            }
            return trim($return_string, ', ');
        }
        if ($action == 'array') {
            return $list;
        }
    }

    public static function getAllGroupExtra($group, $discipline, $action)
    {
        $groupModel = AttendanceGalruzGroup::model()->findByPk($group);
        $list = Yii::app()->db2->createCommand()
            ->selectDistinct('agg.id, agg.name')
            ->from('attendance_galruz_group agg')
            ->join('remote_extra_task r', 'r.`group` = agg.id and HEX(r.discipline) = \'' . $discipline . '\'')
            ->where(array('and',
                'r.teacher = ' . Yii::app()->user->getFnpp(),
                'agg.warch = 0',
                'agg.id != ' . $group,
                'agg.course = ' . $groupModel->course,
            ))
            ->queryAll();
        if ($action == 'count') {
            return count($list);
        }
        if ($action == 'list') {
            $return_string = '';
            foreach ($list as $item) {
                $return_string .= $item['name'] . ', ';
            }
            return trim($return_string, ', ');
        }
        if ($action == 'array') {
            return $list;
        }
    }

    public static function getStream($group, $discipline, $action)
    {
        $groupModel = AttendanceGalruzGroup::model()->findByPk($group);
        $start = Yii::app()->db2->createCommand()->select('ats.dateTimeStartOfClasses')
            ->from('attendance_schedule ats')
            ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
            ->where(array('and',
                'ats.teacherFnpp = ' . Yii::app()->user->getFnpp(),
                'ats.dateTimeStartOfClasses > \'' . date(RemoteModule::startDate()) . '\'',
                array('in', 'agg.wformed', array(0, 2)),
                'agg.warch = 0',
                'HEX(ats.disciplineNrec) = \'' . $discipline . '\'',
                'agg.id != ' . $group,
                'agg.course = ' . $groupModel->course,
                'agg.wformed = ' . $groupModel->wformed,
                'ats.typeOfWorkId = 3'
            ))
            ->orWhere(array('and',
                'ats.teacherFnpp = ' . Yii::app()->user->getFnpp(),
                'agg.wformed = 1',
                'agg.warch = 0',
                'ats.dateTimeStartOfClasses > \'' . date(RemoteModule::startIZODate()) . '\'',
//                'ats.dateTimeStartOfClasses > \''.date('2020-08-31').'\'',
                /*  'ats.dateTimeStartOfClasses < \''.date('2020-09-05').'\'',*/
                'HEX(ats.disciplineNrec) = \'' . $discipline . '\'',
                'agg.id != ' . $group,
                'agg.course = ' . $groupModel->course,
                'agg.wformed = ' . $groupModel->wformed,
                'ats.typeOfWorkId = 3'
            ))->limit(1)->queryScalar();
        $list = Yii::app()->db2->createCommand()
            ->selectDistinct('agg.id, agg.name')->from('attendance_schedule ats')
            ->join('attendance_galruz_group agg', 'agg.id = ats.studGroupId')
            ->where(array('and',
                'ats.teacherFnpp = ' . Yii::app()->user->getFnpp(),
                'agg.warch = 0',
                'HEX(ats.disciplineNrec) = \'' . $discipline . '\'',
                'agg.id != ' . $group,
                'agg.course = ' . $groupModel->course,
                'agg.wformed = ' . $groupModel->wformed,
                'ats.dateTimeStartOfClasses = \'' . $start . '\''
            ))->queryAll();
        if ($action == 'list') {
            $return_string = '';
            foreach ($list as $item) {
                $return_string .= $item['name'] . ', ';
            }
            return trim($return_string, ', ');
        }
        if ($action == 'array') {
            return $list;
        }
    }

    public static function listfile($id, $action = 'icon')
    {
        $model = RemoteTaskList::model()->findByPk($id);
        if ($model->file_from != 0) {
            $id = $model->file_from;
        }
        $save_path = RemoteModule::uploadPath($id) . $id . '/';
        if (!file_exists($save_path)) {
            mkdir($save_path);
        }
        $list = [];
        if ($handle = opendir($save_path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    $list[] = array('name' => $entry);
                }
            }
            closedir($handle);
        }
        $string_return = '';
        if ($action == 'list') {
            $string_return .= 'Текущие файлы:<br/>';
            if ($id != $model->id && RemoteTaskList::model()->findByPk($id)) {
                $string_return .= '<b>Файлы этого занятия привязаны к занятию группы ' .
                    CHtml::link(
                        AttendanceGalruzGroup::model()->findByPk(RemoteTaskList::model()->findByPk($id)->group)->name,
                        ['/remote/default/editTask', 'id' => $id])
                    . ' по текущей дисциплине</b><br/>';
            }
        }
        $formail = [];
        foreach ($list as $row) {
            if ($action == 'icon') {
                $string_return .= CHtml::link('<h3 style="width: min-content; margin: auto;">
            <center><span rel=\'tooltip\' data-toggle=\'tooltip\' title=\'' . $row['name'] . '\' class=\'glyphicon glyphicon-file\' style="color: black"></center></h3>
            <h5 style="width: min-content; margin: auto;">' . $row['name'] . ' </h5>',
                    ['/remote/default/downloadFile', 'id' => $id, 'name' => $row['name']], ['target' => '_blank']);
            }
            if ($action == 'list') {
                $string_return .= '<div class="row alert alert-success" ><div class="col-xs-11"> ' . CHtml::link('<h4 style="margin: 0px">' . $row['name'] . ' </h4>',
                        ['/remote/default/downloadFile', 'id' => $id, 'name' => $row['name']], ['target' => '_blank']) . ' </div>
            <div class="col-xs-1">' . CHtml::link('удалить',
                        ['/remote/default/deleteFile', 'id' => $id, 'name' => $row['name']], ['target' => '_blank', 'style' => 'color:red']) . '</div>
            </div>';
            }
            if ($action == 'mail') {
                $formail[] = ['tmp_name' => $save_path . $row['name'], 'name' => $row['name']];
            }

        }
        if ($action == 'mail') {
            return $formail;
        }
        return $string_return;
    }


    public static function printComment($row)
    {
        $str = $row;
        $reg_exUrl = "/(http|https|ftp|ftps):\/\/[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

        if (preg_match_all($reg_exUrl, $str, $url)) {
            $new_url = array_map(function ($x){return sprintf("<a target='_blank' style='display: inline; word-wrap: break-word' href= %s >%s </a> ", $x, $x);}, $url[0]);
            foreach ($url[0] as $i =>$value){
                $str = str_replace($value, $new_url[$i], $str);
            }
        }
        return nl2br($str);

    }

}
