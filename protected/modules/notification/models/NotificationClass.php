<?php


class NotificationClass
{
    public static function getWknppByFnpp()
    {
        $fnpp = Yii::app()->user->getFnpp();
        $wkards = Wkardc_rp::model()->findAllByAttributes(array('fnpp' => $fnpp, 'prudal' => 0));
        $wknpps = array();
        foreach ($wkards as $elem) {
            $wknpps[] = $elem['npp'];
        }
        return $wknpps;
    }

    public static function getSknppByFnpp()
    {
        $fnpp = Yii::app()->user->getFnpp();
        $skards = Skard::model()->findAllByAttributes(array('fnpp' => $fnpp, 'prudal' => 0));
        $sknpps = array();
        foreach ($skards as $elem) {
            $sknpps[] = $elem['npp'];
        }
        return $sknpps;
    }

    private static function getFacultyBySknpp($sknpp)
    {
        $statement = "select gc.longname from skard sk
inner join gal_u_student tus on tus.nrec = sk.gal_srec
inner join gal_catalogs gc on tus.cfaculty = gc.nrec
where sk.npp = $sknpp";

        $faculty = Yii::app()->db2->createCommand($statement)->queryRow()['longname'];
        return $faculty;
    }

    private static function getGroupBySknpp($sknpp)
    {
        $statement = "select galruz.name from attendance_galruz_group galruz
inner join gal_u_student gus on galruz.name = gus.sdepcode
inner join skard sk on sk.gal_srec = gus.nrec
where sk.npp = $sknpp
and  galruz.warch = 0";

        $group = Yii::app()->db2->createCommand($statement)->queryRow()['name'];
        return $group;
    }


    public static function getEmployeeNotes()
    {
        $wknpps = self::getWknppByFnpp();
        $notes = array();

        foreach ($wknpps as $wknpp) {
            $notes = array_merge($notes, self::getNotesByWknpp($wknpp));
        }

        $oldNotes = [];
        $count = count($notes);
        for ($i = 0; $i < $count; $i++) { // Цикл косячит
            if (date($notes[$i]['valid_until']) < date('Y-m-d H:i:s')) {
                $oldNotes[] = $notes[$i];
                unset($notes[$i]);
            }
        }
        self::sortByCreateDate($notes);
        // self::sortByCreateDate($oldNotes);
        $notes = array_merge($notes, $oldNotes);
        return $notes;
    }

    private static function getNotesByWknpp($wknpp)
    {
        $notes = Yii::app()->db2->createCommand()
            ->select("note.*, group_concat( distinct ' ', nl.destination) destination, count(distinct nc.id) confirmCount")
            ->from('note')
            ->where('owner=:wknpp', array('wknpp' => $wknpp))
            ->join('notification_list nl', 'nl.note_id = note.id')
            ->leftJoin('note_confirm nc', 'nc.note_id = note.id')
            ->group('note.id')
            ->queryAll();

        return $notes;

    }

    public static function getStudentNotes()
    {
        $sknpps = NotificationClass::getSknppByFnpp();
        $notes = array();
    //    $fnppNotes = self::getNotesByFnpp(Yii::app()->user->getFnpp());
        foreach ($sknpps as $sknpp) {
            $attachment = self::getStudentAttachment($sknpp);
            $notes = array_merge($notes, self::getNotesByStudentsAttachment($attachment));
        }
        $notesForCurrentStudent = array();
        foreach ($notes as $note) {

            $noteId = $note['id'];
            $statement = "
            select nt.id from notification_list nl
inner join notification_type nt on nl.notification_type_id = nt.id
where nl.note_id = $noteId
            ";
            $results = Yii::app()->db2->createCommand($statement)->queryAll();
            $right = true;

            foreach ($results as $result) {
                if (!$note['nl' . $result['id'] . '_notification_type_id'] || $note['valid_until'] < (new DateTime())->format('Y-m-d H:i:s')) {
                    $right = false;
                }
            }
            if ($right) {
                $notesForCurrentStudent[] = $note;
            }
        }

     //   $notesForCurrentStudent = array_merge($notesForCurrentStudent, $fnppNotes);
        $notesForCurrentStudent = self::addNotesConfirm($notesForCurrentStudent, Yii::app()->user->getFnpp());
        $notesForCurrentStudent = self::sortNotesByConfirm($notesForCurrentStudent);
        $notesForCurrentStudent = array_unique($notesForCurrentStudent, SORT_REGULAR);
        return $notesForCurrentStudent;
    }

    public static function getStudentNotesCount($force = false)
    {
        if (!isset(Yii::app()->session['NotificationCount']) || $force || !isset(Yii::app()->session['NotificationCountUpdateAvaliable']) || (isset(Yii::app()->session['NotificationCountUpdateAvaliable'])
                && Yii::app()->session['NotificationCountUpdateAvaliable'] <= date('Y-m-d H:i:s'))) {
            $notes = self::getStudentNotes();
            $count = count($notes);
            for ($i = 0; $i < $count; $i++) { // Цикл косячит
                if ($notes[$i]['confirm'] !== null) {
                    unset($notes[$i]);
                }
            }
            Yii::app()->session['NotificationCount'] = count($notes);
            Yii::app()->session['NotificationCountUpdateAvaliable'] = date('Y-m-d H:i:s', strtotime("+2 minutes"));
            return array('count' => count($notes));
        } else {
            return array('count' => Yii::app()->session['NotificationCount']);
        }
    }

    public static function addNotesConfirm($notes, $fnpp)
    {
        $nppStr = $fnpp; // implode(',', $sknpps);
        for ($i = 0; $i < count($notes); $i++) {
            $confirm = NoteConfirm::model()->find('note_id = ' . $notes[$i]['id'] . ' and user_id in (' . $nppStr . ')');
            $notes[$i]['confirm'] = $confirm;
        }
        return $notes;

        /*     foreach ($notes as $note)
             {
                 NoteConfirm::model()->find('note_id = '.$note['id'].' and user_id in ('.$nppStr.')');
                 $note['text'] = 'ZOOOOO';
             }*/
    }

    private static function getStudentAttachment($sknpp)
    {
        $attachment = array
        (
            'sknpp' => $sknpp,
            'group' => null,
            'faculty' => null,
            'fnpp' => Yii::app()->user->getFnpp(),
        );
        $attachment['sknpp'] = $sknpp;
        $attachment['faculty'] = self::getFacultyBySknpp($sknpp);
        $attachment['group'] = self::getGroupBySknpp($sknpp);

        return $attachment;
    }

    private static function getNotesByStudentsAttachment($attachment)
    {

        $facutly = $attachment['faculty'];
        $group = $attachment['group'];
        $fnpp = $attachment['fnpp'];

        if ($facutly === null || $group === null || $fnpp === null)
            return array();

        $list = Yii::app()->db2->createCommand("select nt.id as id, nt.owner as owner, nt.create_at as create_at, nt.valid_until as valid_until, nt.title as title, nt.text as text,
       nl2.id as nl2_id, nl2.note_id as nl2_note_id ,nl2.destination as nl2_destination, nl2.notification_type_id as nl2_notification_type_id,
       nl8.id as nl8_id, nl8.note_id as nl8_note_id ,nl8.destination as nl8_destination, nl8.notification_type_id as nl8_notification_type_id,
       nl1.id as nl8_id, nl1.note_id as nl1_note_id ,nl1.destination as nl1_destination, nl1.notification_type_id as nl1_notification_type_id

from note nt
left join notification_list nl8 on nt.id = nl8.note_id and nl8.notification_type_id = 8 and nl8.destination = '$facutly'
left join notification_list nl2 on nt.id = nl2.note_id and nl2.notification_type_id = 2 and nl2.destination = '$group'
left join notification_list nl1 on nt.id = nl1.note_id and nl1.notification_type_id = 1 and nl1.destination='$fnpp' 
order by nt.valid_until

")->queryAll();
        /*        $list = Yii::app()->db_test->createCommand()
                    ->select('*')
                    ->from('note')
                    ->leftjoin('notification_list nl2', "note.id = nl2.note_id and nl2.destination ='$group' and nl2.notification_type_id = 2")
                    ->leftjoin('notification_list nl8', "note.id = nl8.note_id and nl8.destination ='$facutly' and nl8.notification_type_id = 8")
                    ->execute();*/
        return $list;
    }

    private static function getNotesByFnpp($fnpp)
    {
        $list = Yii::app()->db2->createCommand("select nt.id as id, nt.owner as owner, nt.create_at as create_at, nt.valid_until as valid_until, nt.title as title, nt.text as text,
       nl.id as nl_id, nl.note_id as nl_note_id ,nl.destination as nl_destination, nl.notification_type_id as nl_notification_type_id
from note nt
inner join notification_list nl on nt.id = nl.note_id and nl.destination=$fnpp and nl.notification_type_id = 1

")->queryAll();
        return $list;
    }


    private static function sortNotesByConfirm($array)
    {
        function compare($a, $b)
        {
            if (($a['confirm'] && $b['confirm']) || (!$a['confirm'] && !$b['confirm'])) return 0;
            if ($a['confirm'] && !$b['confirm']) return 1;
            if (!$a['confirm'] && $b['confirm']) return -1;
        }

        usort($array, "compare");
        return $array;
    }

    private static function sortByCreateDate(&$notes)
    {
        function compare($a, $b)
        {
            /*            if(date($a['valid_until']) < date('Y-m-d H:i:s'))
                        {
                            return 1;
                        }*/
            if (strtotime($a['create_at']) === strtotime($b['create_at'])) {
                return 0;
            } else
                return strtotime($a['create_at']) < strtotime($b['create_at']) ? 1 : -1;
        }

        usort($notes, "compare");
        // $notes = array_reverse($notes);
    }
}