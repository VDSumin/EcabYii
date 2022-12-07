<?php


/**
 * Description of FilterCurrForm
 *
 * @author user
 */
class FilterEverydayForm extends FilterForm
{
    /*
    public static function getStructuresByFnpp($fnpp)
    {
        switch ($fnpp) {
            case 796751:
                return array(388);
                break; // Исключение для Титова Д.В.
            case 750:
                return array(87, 89, 216);
                break; // Для Кузнецовой О.П.
            case 2061:
                return array(11);
                break; // Для Данюкова И.Б.
            case 596:
                return array(31);
                break; // Для Иордан А.П.
            case 779394:
                return array(15, 269, 297, 160, 177, 180, 186, 188, 194, 205, 218, 235, 239, 247, 191, 3485);
                break; // Для Платоновой Н.П.
            case 1444:
                return array(198, 32, 297);
                break; // Для Щербы Е.В.
            case 1512:
                return array(42);
                break; // Для Кальницкой Н.Г.
            case 38779:
                return array(84, 129);
                break; // Для Чебаковой Е.А.
            case 949:
                return array(85);
                break; // Для Немцовой А.Ф.
            case 758:
                return array(86);
                break; // Для Кулагиной Е.А.
            case 2169:
                return array(344, 345);
                break; // Для Гончаренко А.А.
            case 780:
                return array(185);
                break; // Для Левченко В.И.
            case 1123:
                return array(227, 277);
                break; // Для Русских Г.С.
            case 866:
                return array(120);
                break; // Для Маркечко И.В.
            case 703: // Для Корнеева С.В.
            case 795593:
                return array(127);
                break; // Для Кавыева А.М.
            case 1522:
                return array(242);
                break; // Для Голунова А.В.
            case 487:
                return array(279);
                break; // Для Деда А.В.
            case 70:
                return array(284);
                break; // Для Ложникова П.С.
            case 1432:
                return array(387);
                break; // Для Штриплинга Л.О.
            case 546:
                return array(107, 109, 111, 112);
                break; // Для Завьялова С.А.
            case 965:
                return array(396);
                break; // Для Никоновой Г.В.
            case 554:
                return array(323);
                break; // Для Захаренко В.А.
            case 1462:
                return array(300);
                break; //Для Яковлева А.Б.
            case 796427:
                return array(36);
                break; // Для Корень Е.И.
            case 1181:
                return array(238);
                break; // Для Ситникова Д.В.
            case 1953:
                return array(62);
                break; // Для Гаркуши М.Ю.
            case 704162:
                return array(42, 36);
                break; // Для Духовских Ю.А.
            case 1039:
                return array(174);
                break; // Для Пляскина М.Ю.
            case 1941:
                return array(74, 3488);
                break; // Для Сосковца А.В.
            case 852:
                return array(183);
                break; // Для Малкова О.Б.
            case 203:
                return array(195);
                break; // Для Акимкиной Т.Л.
            case 1365:
                return array(183);
                break; // Для Цыганенко Валерий Николаевич
            case 218:
                return array(183);
                break; // Для Анатольев Александр Геннадьевич
            case 417:
                return array(181);
                break; // Для Галимова Лязат Аменевна
            case 31325:
                return array(62);
                break; // Для Черкасова	Татьяна	Александровна
            case 1041:
                return array(167);
                break; // Для Погодаев Денис Викторович
            case 783606:
                return array(265);
                break; // Для Шишкина Мария Алексеевна
            case 796526:
                return array(266);
                break; // Для Конинян Кристинэ Гагиковна
            default:
                return array();
        }
    }
    */


    public function filterCurrent($id)
    {
        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('w.dolgnost,f.npp,f.fam,f.nam,f.otc, w.vpo1cat as category, Case WHEN TRIM(w.sovm) = \' \' THEN "Осн" ELSE w.sovm END as sovm,DATE_FORMAT(NOW(), \'%Y\') - DATE_FORMAT(f.rogd, \'%Y\') - (DATE_FORMAT(NOW(), \'00-%m-%d\') < DATE_FORMAT(f.rogd, \'00-%m-%d\')) AS age')
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

    public static function getDropdownCategory($fnpp)
    {
        if (self::checkOld($fnpp)) {//если 65+
            return '<button class="btn btn-sm btn-success" disabled style="width: 100%" value="' . ChiefReportsWeek::CATEGORY_OLD . '" type="button" id="dropdownMenuCategory_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                ' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_OLD) . '
                </button>';
        }
        if (self::checkRetired($fnpp)) {
            return '<button class="btn btn-sm btn-success" disabled style="width: 100%" value="' . ChiefReportsWeek::CATEGORY_RETIRED . '" type="button" id="dropdownMenuCategory_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                ' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_RETIRED) . '
                </button>';
        }

        $sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_category')
            ->where('fnpp = ' . $fnpp)
            ->order('createdAt')
            ->queryRow();
        $class = 'btn-default';
        if (Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = CURRENT_DATE')
            ->queryRow()) {
            $class = 'btn-success';
        } elseif (Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()) {
            $class = 'btn-info';
        }

        if ($sql) {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm ' . $class . ' dropdown-toggle" style="width: 100%" value="' . $sql['category'] . '" type="button" id="dropdownMenuCategory_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsWeek::getCategory($sql['category']) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuCategory_' . $fnpp . '">
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_EMPTY . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_EMPTY) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_RETIRED . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_RETIRED) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_PREGNANT . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_PREGNANT) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_CHILDREN . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_CHILDREN) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_OLD . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_OLD) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_SICK . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_SICK) . '</a></li>
    </ul>
</div>';
        } else {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm btn-default dropdown-toggle" style="width: 100%" value="' . ChiefReportsWeek::CATEGORY_EMPTY . '" type="button" id="dropdownMenuCategory_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_EMPTY) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuCategory_' . $fnpp . '">
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_EMPTY . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_EMPTY) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_RETIRED . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_RETIRED) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_PREGNANT . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_PREGNANT) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_CHILDREN . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_CHILDREN) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_OLD . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_OLD) . '</a></li>
        <li><a onclick="SetCategory(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_SICK . ')">' . ChiefReportsWeek::getCategory(ChiefReportsWeek::CATEGORY_SICK) . '</a></li>
    </ul>
</div>';
        }
    }

    public static function getDropdownFormat($fnpp)
    {
        $sqlConfirmed = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_week')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = CURRENT_DATE')
            ->queryRow();
        $vacation = self::compareVacation($sqlConfirmed, $fnpp);
        $temp_res = '<script> SetFormat('.$fnpp.', '.$vacation[1].')</script>';
        if ($vacation[0]) {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm ' . (($sqlConfirmed) ? 'btn-success' : 'btn-info') . ' dropdown-toggle" style="width: 100%" value="' . ChiefReportsWeek::FORMAT_OTHER . '" type="button" id="dropdownMenuFormat_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" value="'.ChiefReportsWeek::FORMAT_OTHER.'">
        ' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_OTHER) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuFormat_' . $fnpp . '">
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_DISTANT . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_DISTANT) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_PARTLY . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_PARTLY) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_INSIDE . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_INSIDE) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_QUARANTINE . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_QUARANTINE) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_OTHER . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_OTHER) . '</a></li>
    </ul>
</div> '.$temp_res ;
        }

        if ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_week')
            ->where('fnpp = ' . $fnpp . ' AND createdAt  = CURRENT_DATE')
            ->queryRow()
        ) {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm ' . (($sqlConfirmed) ? 'btn-success' : 'btn-info') . ' dropdown-toggle" style="width: 100%" value="' . $sql['format'] . '" type="button" id="dropdownMenuFormat_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsWeek::getFormat($sql['format']) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuFormat_' . $fnpp . '">
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_DISTANT . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_DISTANT) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_PARTLY . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_PARTLY) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_INSIDE . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_INSIDE) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_QUARANTINE . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_QUARANTINE) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_OTHER . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_OTHER) . '</a></li>
    </ul>
</div>';
        } elseif ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_week')
            ->where('fnpp = ' . $fnpp . ' AND createdAt  = subdate(current_date, 1)')
            ->queryRow()
        ) {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm btn-default dropdown-toggle" style="width: 100%" value="' . $sql['format'] . '" type="button" id="dropdownMenuFormat_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsWeek::getFormat($sql['format']) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuFormat_' . $fnpp . '">
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_DISTANT . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_DISTANT) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_PARTLY . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_PARTLY) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_INSIDE . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_INSIDE) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_QUARANTINE . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_QUARANTINE) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_OTHER . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_OTHER) . '</a></li>
    </ul>
</div>';
        } else {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm btn-default dropdown-toggle" style="width: 100%" value="' . ChiefReportsWeek::FORMAT_INSIDE . '" type="button" id="dropdownMenuFormat_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_INSIDE) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuFormat_' . $fnpp . '">
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_DISTANT . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_DISTANT) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_PARTLY . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_PARTLY) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_INSIDE . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_INSIDE) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_QUARANTINE . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_QUARANTINE) . '</a></li>
        <li><a onclick="SetFormat(' . $fnpp . ',' . ChiefReportsWeek::FORMAT_OTHER . ')">' . ChiefReportsWeek::getFormat(ChiefReportsWeek::FORMAT_OTHER) . '</a></li>
    </ul>
</div>';
        }
    }

    public static function getDropdownReasonId($fnpp)
    {
        $sqlConfirmed = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_week')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = CURRENT_DATE')
            ->queryRow();
        $sqlConfirmed['reasonId'] = self::compareVacation($sqlConfirmed, $fnpp)[0];
        $temp_res = '<script> SetReasonId('.$fnpp.', '.$sqlConfirmed['reasonId'].')</script>';
        if ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_week')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()) {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button ' . (($sql['format'] == ChiefReportsWeek::FORMAT_OTHER) ? '' : 'disabled="disabled"') . ' class="btn btn-sm ' . (($sqlConfirmed) ? 'btn-success' : 'btn-info') . ' dropdown-toggle" style="width: 100%" value="' . $sql['reasonId'] . '" type="button" id="dropdownMenuReason_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsWeek::getReasonId($sqlConfirmed['reasonId']) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuReason_' . $fnpp . '">
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_VACATION . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_VACATION) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_BABY . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_BABY) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_DISABLED . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_DISABLED) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_TRIP . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_TRIP) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_OTHER . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_OTHER) . '</a></li>
    </ul>
</div>'.$temp_res;
        } elseif ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_week')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = subdate(current_date, 1)')
            ->queryRow()
        ) {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button ' . (($sql['format'] == ChiefReportsWeek::FORMAT_OTHER) ? '' : 'disabled="disabled"') . ' class="btn btn-sm btn-default dropdown-toggle" style="width: 100%" value="' . $sqlConfirmed['reasonId'] . '" type="button" id="dropdownMenuReason_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsWeek::getReasonId($sqlConfirmed['reasonId']) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuReason_' . $fnpp . '">
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_VACATION . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_VACATION) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_BABY . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_BABY) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_DISABLED . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_DISABLED) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_TRIP . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_TRIP) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_OTHER . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_OTHER) . '</a></li>
    </ul>
</div>'.$temp_res;
        } else {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button disabled="disabled" class="btn btn-sm btn-default dropdown-toggle" style="width: 100%" value="' . ChiefReportsWeek::REASON_EMPTY . '" type="button" id="dropdownMenuReason_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsWeek::getReasonId($sqlConfirmed['reasonId']) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuReason_' . $fnpp . '">
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_VACATION . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_VACATION) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_BABY . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_BABY) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_DISABLED . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_DISABLED) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_TRIP . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_TRIP) . '</a></li>
        <li><a onclick="SetReasonId(' . $fnpp . ',' . ChiefReportsWeek::REASON_OTHER . ')">' . ChiefReportsWeek::getReasonId(ChiefReportsWeek::REASON_OTHER) . '</a></li>
    </ul>
</div>'.$temp_res;
        }
    }

    public static function getDropdownStatus($fnpp)
    {
        $sqlConfirmed = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = CURRENT_DATE')
            ->queryRow();
        if ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()
        ) {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm ' . (($sqlConfirmed) ? 'btn-success' : 'btn-info') . ' dropdown-toggle" style="width: 100%" value="' . $sql['status'] . '" type="button" id="dropdownMenuStatus_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsDay::getStatus($sql['status']) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuStatus_' . $fnpp . '">
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_IN . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_IN) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_OUT . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_OUT) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_ABROAD . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_ABROAD) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_UNKNOWN . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_UNKNOWN) . '</a></li>
    </ul>
</div>';
        } elseif ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = subdate(current_date, 1)')
            ->queryRow()
        ) {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm btn-default dropdown-toggle" style="width: 100%" value="' . $sql['status'] . '" type="button" id="dropdownMenuStatus_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsDay::getStatus($sql['status']) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuStatus_' . $fnpp . '">
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_IN . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_IN) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_OUT . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_OUT) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_ABROAD . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_ABROAD) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_UNKNOWN . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_UNKNOWN) . '</a></li>
    </ul>
</div>';
        } else {
            return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
    <button class="btn btn-sm btn-default dropdown-toggle" style="width: 100%" value="' . ChiefReportsDay::STATUS_IN . '" type="button" id="dropdownMenuStatus_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        ' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_IN) . '
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuStatus_' . $fnpp . '">
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_IN . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_IN) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_OUT . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_OUT) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_ABROAD . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_ABROAD) . '</a></li>
        <li><a onclick="SetStatus(' . $fnpp . ',' . ChiefReportsDay::STATUS_UNKNOWN . ')">' . ChiefReportsDay::getStatus(ChiefReportsDay::STATUS_UNKNOWN) . '</a></li>
    </ul>
</div>';
        }
    }

    public static function getCountry($fnpp)
    {
        $sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow();
        if ($sql) {
            if ($sql['country'] || $sql['status'] == ChiefReportsDay::STATUS_ABROAD) {
                return CHtml::textField("country_" . $fnpp, $sql['country'], array("class" => "form-control", "onChange" => 'CountryChange(' . $fnpp . ')'));
            }
        } elseif ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = subdate(current_date, 1)')
            ->queryRow()
        ) {
            if ($sql['country'] || $sql['status'] == ChiefReportsDay::STATUS_ABROAD) {
                return CHtml::textField("country_" . $fnpp, $sql['country'], array("class" => "form-control", "onChange" => 'CountryChange(' . $fnpp . ')'));
            }
        }
        return CHtml::textField("country_" . $fnpp, null, array("class" => "form-control", "disabled" => "disabled", "onChange" => "CountryChange(" . $fnpp . ")"));
    }

    public static function getBoolButtons($fnpp)
    {
        $sqlConfirmed = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = CURRENT_DATE')
            ->queryRow();
        $sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow();
        if ($sql) {
            $choose = ($sql['wasAbroad'] == 1);
            return '<div id="btn-group_' . $fnpp . '" class="btn-group btn-sm" role="group" style="display: flex; justify-content: center">
                    <button onclick="SetAbroad(' . $fnpp . ', 1)" type="button" class="btn btn-' . (($sqlConfirmed) ? 'success' : 'info') . (($choose) ? ' active' : '') . '">Да</button>
                    <button onclick="SetAbroad(' . $fnpp . ', 0)" type="button" class="btn btn-' . (($sqlConfirmed) ? 'success' : 'info') . ((!$choose) ? ' active' : '') . '">Нет</button>
                </div>';
        } elseif ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = subdate(current_date, 1)')
            ->queryRow()
        ) {
            $choose = ($sql['wasAbroad'] == 1);
            return '<div id="btn-group_' . $fnpp . '" class="btn-group btn-sm" role="group" style="display: flex; justify-content: center">
                    <button onclick="SetAbroad(' . $fnpp . ', 1)" type="button" class="btn btn-default' . (($choose) ? ' active' : '') . '">Да</button>
                    <button onclick="SetAbroad(' . $fnpp . ', 0)" type="button" class="btn btn-default' . ((!$choose) ? ' active' : '') . '">Нет</button>
                </div>';
        } else {
            return '<div id="btn-group_' . $fnpp . '" class="btn-group btn-sm" role="group" style="display: flex; justify-content: center">
                    <button onclick="SetAbroad(' . $fnpp . ', 1)" type="button" class="btn btn-default ">Да</button>
                    <button onclick="SetAbroad(' . $fnpp . ', 0)" type="button" class="btn btn-default active">Нет</button>
                </div>';
        }
    }

    public static function getCountry2($fnpp)
    {
        if ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()
        ) {
            if ($sql['country2'] || $sql['wasAbroad'] == 1) {
                return CHtml::textField("country2_" . $fnpp, $sql['country2'], array("class" => "form-control", "onChange" => 'Country2Change(' . $fnpp . ')'));
            }
        } elseif ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = subdate(current_date, 1)')
            ->queryRow()
        ) {
            if ($sql['country2'] || $sql['wasAbroad'] == 1) {
                return CHtml::textField("country2_" . $fnpp, $sql['country2'], array("class" => "form-control", "onChange" => 'Country2Change(' . $fnpp . ')'));
            }
        }
        return CHtml::textField("country2_" . $fnpp, null, array("class" => "form-control", "disabled" => "disabled", "onChange" => "Country2Change(" . $fnpp . ")"));
    }

    public static function getAdditional($fnpp)
    {
        $sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow();
        if ($sql) {
            if ($sql['additional']) {
                return CHtml::textArea("additional_" . $fnpp, $sql['additional'], array("class" => "form-control", "onChange" => 'SetAdditional(' . $fnpp . ')'));
            }
        } elseif ($sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = subdate(current_date, 1)')
            ->queryRow()
        ) {
            if ($sql['additional']) {
                return CHtml::textArea("additional_" . $fnpp, $sql['additional'], array("class" => "form-control", "onChange" => 'SetAdditional(' . $fnpp . ')'));
            }
        }
        return CHtml::textArea("additional_" . $fnpp, null, array("class" => "form-control", "onChange" => "SetAdditional(" . $fnpp . ")"));
    }

    public static function checkOld($fnpp)
    {
        $age = DateTime::createFromFormat('Y-m-d', Fdata::model()->findByPk($fnpp)->rogd)
            ->diff(new DateTime('now'))
            ->y;
        return ($age >= 65);
    }

    public static function checkRetired($fnpp)
    {
        $age = DateTime::createFromFormat('Y-m-d', Fdata::model()->findByPk($fnpp)->rogd)
            ->diff(new DateTime('now'))
            ->y;
        $month = DateTime::createFromFormat('Y-m-d', Fdata::model()->findByPk($fnpp)->rogd)
            ->diff(new DateTime('now'))
            ->m;
        if (Fdata::model()->findByPk($fnpp)->pol == 1) {
            return (($age > 60 && $age < 65) || ($age == 60 && $month >= 5));
        } elseif (Fdata::model()->findByPk($fnpp)->pol == 2) {
            return (($age > 55 && $age < 65) || ($age == 55 && $month >= 5));
        } else return false;
    }

    public static function checkFnppInDatabase($fnpp)
    {
        if (!Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()
        ) {
            Yii::app()->db->createCommand('REPLACE tbl_chief_reports_day SET fnpp = ' . $fnpp . ', createdAt = CURRENT_DATE')->query();
        }
        if (!Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_week')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()
        ) {
            Yii::app()->db->createCommand('REPLACE tbl_chief_reports_week SET fnpp = ' . $fnpp . ', createdAt = CURRENT_DATE')->query();
        }
        if (!Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_category')
            ->where('fnpp = ' . $fnpp)
            ->queryRow()) {
            if (self::checkOld($fnpp)) {
                Yii::app()->db->createCommand('REPLACE tbl_chief_reports_category SET fnpp = ' . $fnpp . ', createdAt = CURRENT_DATE, category = ' . ChiefReportsWeek::CATEGORY_OLD)->query();
            } elseif (self::checkRetired($fnpp)) {
                Yii::app()->db->createCommand('REPLACE tbl_chief_reports_category SET fnpp = ' . $fnpp . ', createdAt = CURRENT_DATE, category = ' . ChiefReportsWeek::CATEGORY_RETIRED)->query();
            } else {
                Yii::app()->db->createCommand('REPLACE tbl_chief_reports_category SET fnpp = ' . $fnpp . ', createdAt = CURRENT_DATE')->query();
            }
        }
        if (!Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_covid')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()) {
            Yii::app()->db->createCommand('REPLACE tbl_chief_reports_covid SET fnpp = ' . $fnpp . ', createdAt = CURRENT_DATE')->query();
        }
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

    public function isItMyChief($me, $chief)
    {
        $default = false;
        $departments = MonitorAccess::getDepartments($chief);
        if ($departments) {
            foreach ($departments as $department) {
                $fnpps = Yii::app()->db2->createCommand()
                    ->selectDistinct('w.fnpp')
                    ->from('wkardc_rp w')
                    ->where('w.struct in (' . $this->getSubstructures($department['npp']) . ') AND w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
                    ->queryAll();
                if ($fnpps) {
                    $arr = array();
                    foreach ($fnpps as $fnpp) {
                        array_push($arr, $fnpp['fnpp']);
                    }
                    if (in_array($me, $arr)) {
                        $default = true;
                    }
                }
            }
        }
        return $default;
    }


    public static function getDropdownCovidStatus($fnpp)
    {
        $sql = Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_covid')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->order('createdAt')
            ->queryRow();
        $class = 'btn-default';
        if (Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND confirmedAt = CURRENT_DATE')
            ->queryRow()) {
            $class = 'btn-success';
        } elseif (Yii::app()->db->createCommand()
            ->select('*')
            ->from('tbl_chief_reports_day')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->queryRow()) {
            $class = 'btn-info';
        }

        $status = $sql ? $sql['covidStatus'] : ChiefReportsWeek::CATEGORY_EMPTY;
        return '<div class="dropdown" onmouseover="$(\'.dropdown-toggle\').dropdown()">
            <button class="btn btn-sm ' . $class . ' dropdown-toggle" style="width: 100%" value="' . $status . '" type="button" id="dropdownMenuCovid_' . $fnpp . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                ' . ChiefReportsWeek::getCovidStatus($status) . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuCovid_' . $fnpp . '">
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_EMPTY . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_EMPTY) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_REVAC. ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_REVAC) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_RECOVER . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_RECOVER) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_FIRST . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_FIRST) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_SECOND . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_SECOND) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_RECUSAL . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_RECUSAL) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_GOSUSLUGI . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_GOSUSLUGI) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_OT . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_OT) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_REFUSING . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_REFUSING) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_DISMIS . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_DISMIS) . '</a></li>
                <li><a onclick="SetCovidStatus(' . $fnpp . ',' . ChiefReportsWeek::CATEGORY_COVID_ANOTHER . ')">' . ChiefReportsWeek::getCovidStatus(ChiefReportsWeek::CATEGORY_COVID_ANOTHER) . '</a></li>
            </ul>
        </div>';
    }

    public static function getCovidDate($fnpp)
    {
        $sql = Yii::app()->db->createCommand()
            ->select('date')
            ->from('tbl_chief_reports_covid')
            ->where('fnpp = ' . $fnpp . ' AND createdAt = CURRENT_DATE')
            ->order('createdAt desc')
            ->queryRow();
        $cur_date = $sql && $sql['date'] != '1000-01-01' && $sql['date'] != '1970-01-01' ? date('d.m.Y', strtotime($sql['date'])) : null;

        return CHtml::textField($fnpp, $cur_date
            , array("style" => "width: 100px; text-align:center;",
                "class" => "form-group form-control date-field datepicker", "onClose" => 'SetCovidDate(' . $fnpp . ')'));
    }

    public static function checkVacations($fnpp)
    {
        $sql = Yii::app()->db2->createCommand()
            ->selectDistinct('otp')
            ->from('wkardc_rp w')
            ->where('w.fnpp = ' . $fnpp)
            ->andWhere(' w.otp!=0')
            ->andWhere(' w.prudal = 0 AND (w.du>=CURRENT_DATE() OR w.du IS NULL)')
            ->queryRow();
        // w.struct IN (' . implode(',', MonitorAccess::getStructuresByFnpp($fnpp)) . ')
        return $sql['otp'] ?: 0;
    }

    public static function compareVacation($report, $fnpp)
    {
        $base = self::checkVacations($fnpp);
        $res = isset($report['reasonId']) ? $report['reasonId'] : $base;
        if ($res == 0) {
            $format = 2;
        } else {
            $format = isset($report['format']) ? $report['format'] : 4;
        }
        return [$res, $format];
    }

}
