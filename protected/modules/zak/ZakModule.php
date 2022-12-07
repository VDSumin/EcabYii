<?php

class ZakModule extends CWebModule
{

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'zak.models.*',
            'zak.components.*',
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

    public static function getMyFinStruct()
    {
        $list = Yii::app()->db2->createCommand()
            ->select('sdr.npp')
            ->from('fdata f')
            ->join('wkardc_rp wr', 'f.npp = wr.fnpp')
            ->join('struct_d_rp sdr', 'sdr.npp = wr.struct')
            ->where('wr.prudal = 0
                AND f.npp = ' . Yii::app()->user->getFnpp())
            ->queryAll();
        $return = [];
        foreach ($list as $item) {
            $return[] = $item['npp'];
        }
        return $return;

    }

    public static function getMyFinStructList()
    {
        $list = Yii::app()->db2->createCommand()
            ->select('sdr.npp, sdr.name')
            ->from('fdata f')
            ->join('wkardc_rp wr', 'f.npp = wr.fnpp')
            ->join('struct_d_rp sdr', 'sdr.npp = wr.struct')
            ->where('wr.prudal = 0
                AND f.npp = ' . Yii::app()->user->getFnpp())
            ->queryAll();
        $return = [];
        foreach ($list as $item) {
            $return[] = ['npp' => $item['npp'], 'name' => $item['name']];
        }
        return $return;

    }

    public static function getShortPriviewText($text, $number = 100)
    {
        $return = '<div id="preview_text" class="shortPriview">'.mb_substr($text, 0, $number).'</div><div id="points" class="shortPriview"> ...</div>'
            .'<div  id="button_text" class="shortPriview"><br>'
            .'<u style="color: #0a6aa1; cursor: pointer" onclick="
            $(this).parents(\'td\').find(\'#points\').css({display: \'none\'});
            $(this).parents(\'td\').find(\'#button_text\').css({display: \'none\'});
            $(this).parents(\'td\').find(\'#other_text\').css({display: \'\'});
            ">Отобразить текст</u></div>';
        $return .= '<div id="other_text" class="shortPriview" style="display: none">'. mb_substr($text, $number, strlen($text)).'</div>';

        return $return;
    }


}
