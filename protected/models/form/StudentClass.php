<?php
/**
 * Created by PhpStorm.
 * User: Evgeny Trapeznikov
 * Date: 20.04.2018
 * Time: 11:50
 */

class StudentClass
{
    public static function getTotle($cur, $sem){
        $galId = Yii::app()->user->getGalId();
        if (null === $galId) {
            return false;
        }

        $info = Yii::app()->db2->createCommand()
            ->select('tut.wresultes')
            ->from('gal_u_tolerancesession tut')
            ->where('tut.cstudent=:id AND tut.wsemester=:sem and tut.cplan = :cur', [':id' => $galId, ':sem'=>$sem, ':cur'=>hex2bin($cur)])
            ->order('tut.nrec DESC')
            ->limit(1)
            ->queryScalar();

        return $info ? true : false;
    }

}