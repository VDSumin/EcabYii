<?php

/**
 * Description of FilterCurrForm
 *
 * @author user
 */
/*class FilterEveryweekForm extends FilterForm
{



    public static function checkFnppInDatabase($fnpp)
    {
        if (!
        Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_week')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()
        ) {
            Yii::app()->db->createCommand('REPLACE tbl_chief_reports_week SET fnpp = ' . $fnpp . ', createdAt = CURRENT_TIMESTAMP')->query();
        }
    }

    public function filterCurrent($id)
    {
        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('Case WHEN TRIM(w.sovm) = \' \' THEN "Осн" ELSE w.sovm END as sovm, w.vpo1cat as category, w.dolgnost,f.npp,f.fam,f.nam,f.otc,DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) AS age')
            ->from('wkardc_rp w')
            ->join('fdata f', 'f.npp=w.fnpp')
            ->where('w.struct in (' . $this->getSubstructures($id) . ') AND w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
            ->order('f.fam')
            ->group('f.npp');

        if (!empty($this->filters['dolgnost'])) {
            $sql->andWhere(array('like', 'w.dolgnost', '%' . $this->filters['dolgnost'] . '%'));
        }
        if (!empty($this->filters['fio'])) {
            $sql->andWhere(array('like', 'CONCAT(f.fam,\' \',f.nam,\' \',f.otc)', '%' . $this->filters['fio'] . '%'));
        }

        return new CArrayDataProvider($sql->queryAll(), array('pagination' => false, 'keyField' => 'npp'));
    }

    public function getSubstructures($id)
    {
        $l1 = Yii::app()->db2->createCommand()
            ->selectDistinct('s.l')
            ->from('struct_d_rp s')
            ->where('s.npp = ' . $id)
            ->queryScalar();
        $u = Yii::app()->db2->createCommand()
            ->selectDistinct('s.u')
            ->from('struct_d_rp s')
            ->where('s.npp = ' . $id)
            ->queryScalar();
        $l2 = Yii::app()->db2->createCommand()
            ->selectDistinct('MIN(s.l)')
            ->from('struct_d_rp s')
            ->where('s.l > ' . $l1 . ' AND s.u = ' . $u)
            ->queryScalar();
        if (!$l2) $l2 = $l1;
        $npps = Yii::app()->db2->createCommand()
            ->selectDistinct('s.npp')
            ->from('struct_d_rp s')
            ->where('s.l >= ' . $l1 . ' AND s.l <= ' . $l2 . ' AND s.u > ' . $u)
            ->queryAll();
        $arr = array($id);
        foreach ($npps as $npp) {
            array_push($arr, $npp['npp']);
        }
        return (implode(',', $arr));
    }

}
