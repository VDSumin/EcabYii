<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 25.06.2020
 * Time: 22:10
 */

class ZakClass
{

    public static function changeCart($id, $block = false) {
        $model = ZakOborTovar::model()->findAllByAttributes(array('oborud' => $id));
        $list = ZakTovar::model()->findAll();
        $return = '';
        foreach ($model as $item) {
            $return .= ','. $item->tovar;
        }
        $return = explode(',', $return);
        if($block) {
            return CHtml::dropDownList('list', $return, CHtml::listData($list, 'npp', 'name'), [
                'class' => 'form-control js-cart-change',
                'empty' => 'Не назначена',
                'multiple' => 'multiple',
                'disabled' => 'disabled',
                'data-placeholder' => "Картриджы отсутствуют"
            ]);
        }else{
            return CHtml::dropDownList('list', $return, CHtml::listData($list, 'npp', 'name'), [
                'class' => 'form-control js-cart-change',
                'empty' => 'Не назначена',
                'multiple' => 'multiple',
                'data-placeholder' => "Картриджы отсутствуют"
            ]);
        }
    }

    public static function changePrint($id, $block = false) {
        $model = ZakOborTovar::model()->findAllByAttributes(array('tovar' => $id));
        $list = ZakOborud::model()->findAll();
        $return = '';
        foreach ($model as $item) {
            $return .= ','. $item->oborud;
        }
        $return = explode(',', $return);
        if($block) {
            return CHtml::dropDownList('list', $return, CHtml::listData($list, 'npp', 'name'), [
                'class' => 'form-control js-cart-change',
                'empty' => 'Не назначена',
                'multiple' => 'multiple',
                'disabled' => 'disabled',
                'data-placeholder' => "Принтеры отсутствуют"
            ]);
        }else{
            return CHtml::dropDownList('list', $return, CHtml::listData($list, 'npp', 'name'), [
                'class' => 'form-control js-cart-change',
                'empty' => 'Не назначена',
                'multiple' => 'multiple',
                'data-placeholder' => "Принтеры отсутствуют"
            ]);
        }
    }

    public static function finsourse($id) {
        $arrayFin = [''=>'', '1' => 'из средств подразделения', '2' => 'ЦФ в/б'];
        if(isset($arrayFin[$id])) {
            $return = $arrayFin[$id];
        }else{
            $return = "";
        }
        return $return;
    }

    public static function getActionStatus($id) {
        $return = ZakAuction::model()->findByPk($id)->state;
        if($return == 1){$return = true;} else{$return = false;}
            return $return;
    }

}