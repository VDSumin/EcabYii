<?php

class CMisc {

    const INTERNAL = 0; //Очная
    const EXTRAMURAL = 1; //Заочная
    const EVENING = 2; //Вечерняя

    private function __construct() {}

    public static function _id($id, $height = 'lower') {
        if($height == 'lower')
            return strtolower(strstr($id, '0x') ? $id : "0x{$id}");
        if($height == 'upper')
            return (strstr($id, '0x') ? "0x".strtoupper( self::_bn($id)) : "0x".strtoupper($id));
    }

    public static function _bn($id) {
        return strtolower(str_replace('0x', '', $id));
    }

    public static function toGalDate($date) {
        if ('' == $date) {
            return 0;
        }
        $time = strtotime($date);
        return date('Y', $time) * 256 * 256 + date('m', $time) * 256 + date('d', $time);
    }

    public static function fromGalDate($date, $format = 'd.m.Y г.', $empty = null) {
        if (0 == $date) {
            return $empty;
        }
        $day = $date % 256;
        $date = (int)floor($date / 256);
        $month = $date % 256;
        $date = (int)floor($date / 256);
        return date($format, mktime(0, 0, 0, $month, $day, $date));
    }

    /**
     * Returns formatted fio
     * @param string $namelong
     * @param bool $full return full FIO, or Family I.O.
     * @return string
     */
    public static function getFIO($namelong, $full = false) {
        $parts = explode(' ', $namelong);
        foreach($parts as &$part) {
            $part = mb_strtoupper(mb_substr($part, 0, 1, Yii::app()->charset), Yii::app()->charset) .
                mb_strtolower(mb_substr($part, 1, null, Yii::app()->charset), Yii::app()->charset);
        }
        $nameshort = $full ? implode(' ', $parts) : preg_replace('~^(\S+)\s+(\S)\S+\s+(\S)\S+$~u', '$1 $2.$3.', implode(' ', $parts));
        return $nameshort;
    }

    /**
     * Возвращает текстовое описание формы обучения
     *
     * @param $form
     * @return mixed|string
     */
    public static function getFromEduLabel($form){
        $forms = [
            self::INTERNAL => 'Очная',
            self::EXTRAMURAL => 'Заочная',
            self::EVENING => 'Вечерняя'
        ];

        return isset($forms[$form]) ? $forms[$form] : '';
    }

    public static function getListOfEduLabel(){
        $forms = [
            self::INTERNAL => 'Очная',
            self::EXTRAMURAL => 'Заочная',
            self::EVENING => 'Вечерняя'
        ];

        return $forms;
    }

    public static function str($str) {
        $str = iconv('utf-8', 'windows-1251//IGNORE', str_replace(array("'", '\\'), array('"',''), $str));
        return "'{$str}'";
    }
}