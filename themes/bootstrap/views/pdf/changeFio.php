<?php

/* @var $this PersonalcardController */
/* @var $dataForEdit GalStudentPersonalcard*/

function getPic($field){
    if ($field){
        return '&_#_10004';
    } else {
        return 'glyphicon glyphicon-remove';
    }
}

require_once Yii::getPathOfAlias('application.vendor.mpdf60.mpdf') . '.php';

/*if (!defined('_MPDF_TTFONTPATH')) {
    define('_MPDF_TTFONTPATH', realpath('themes/bootstrap/fonts/'));
}*/

function add_custom_fonts_to_mpdf($mpdf) {

    $fontdata = [
        'courierFont' => [
            'R' => 'Courier-New.ttf',
            'I' => 'Courier-New-ital.ttf',
        ],
    ];

    foreach ($fontdata as $f => $fs) {
        // add to fontdata array
        $mpdf->fontdata[$f] = $fs;

        // add to available fonts array
        foreach (['R', 'B', 'I', 'BI'] as $style) {
            if (isset($fs[$style]) && $fs[$style]) {
                // warning: no suffix for regular style! hours wasted: 2
                $mpdf->available_unifonts[] = $f . trim($style, 'R');
            }
        }

    }

    $mpdf->default_available_fonts = $mpdf->available_unifonts;
}

//Кодировка | Формат | Размер шрифта | Шрифт
//Отступы: слева | справа | сверху | снизу | шапка | подвал

$pdf = new mPDF('utf-8', 'A4', '10', 'Times', 20, 15, 10, 15, 5, 5);
add_custom_fonts_to_mpdf($pdf);

$pdf->charset_in = 'utf-8';

$pdf->SetTextColor(0, 0, 0);


$html = "
<html><head>
<title>Смена ФИО</title>
<style>
        table {
            border-collapse: collapse;
        }
      
        strong{
            font-weight: normal;
        }
</style>
</head>
<body>
    <div style='text-align: left; font-size: 11pt;'><u><font color='white'>fff</font>УК<font color='white'>fff</font></u></div>
    <div align='right' lang='ru'>Ректору ОмГТУ<font color='white'>fffffffffffffffff</font>Д.П.Маевскому</div>
    <div>В приказ</div>
    <table align='right'>
        <tbody>
            <tr>
                <td>
                    <strong>фамилия</strong>
                </td>
                <td style='border-bottom: 1px solid black'>
                    <strong> ". $these_statements['fam'] ."</strong>
                </td>
            <tr>
                <td>
                    <strong>имя</strong>
                </td>
                <td style='border-bottom: 1px solid black'>
                    <strong>". $these_statements['nam'] ." </strong>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>отчество</strong>
                </td>
                <td style='border-bottom: 1px solid black'>
                    <strong>". $these_statements['otc'] ." </strong>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>группа</strong>
                </td>
                <td style='border-bottom: 1px solid black'> 
                    <strong>". $these_statements['group'] ." </strong>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>факультет</strong>
                </td>
                <td style='border-bottom: 1px solid black'>
                    <strong>". $these_statements['fak'] ." </strong>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>гражданство</strong>
                </td>
                <td style='border-bottom: 1px solid black'>
                    <strong>". $these_statements['gr'] ." </strong>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>проживающего</strong>
                </td>
                <td style='border-bottom: 1px solid black'>
                    <strong style='font-size: 8pt'>". $these_statements['address'] ." </strong>
                </td>
            </tr>
            <tr>
               <td>
               </td>
               <td style='border-bottom: 1px solid black; height: 16px;'>
                    <strong>". $these_statements['address2'] ." </strong>
               </td>
            </tr>
            <tr>
                <td>
                    <strong>телефон</strong>
                </td>
                <td style='border-bottom: 1px solid black'>
                    <strong>". $these_statements['phone'] ." </strong>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>e-mail</strong>
                </td>
                <td style='border-bottom: 1px solid black'>
                    <strong>". $these_statements['mail'] ." </strong>
                </td>
            </tr>
            </tbody>
        </table>";

//$pdf->AddPage();
//
$pdf->WriteHTML($html);

$html = "
<div align='center' style='padding: 20px 0'>ЗАЯВЛЕНИЕ</div>
<div>Прошу изменить мои персональные данные</div>
<table style='width: 100%'>
    <tbody>
        <tr>
            <td style='text-align: right; width: 20%'>
                <strong>фамилия</strong>
            </td>
            <td  style='border-bottom: 1px solid black'>
                <strong>". $these_statements['fam'] ." </strong>
            </td>
        </tr>
        <tr>
            <td style='text-align: right; width: 20%'>
                <strong>имя</strong>
            </td>
            <td style='border-bottom: 1px solid black'>
                <strong>". $these_statements['nam'] ." </strong>
            </td>
        </tr>
        <tr>
            <td style='text-align: right; width: 20%'>
                <strong>отчество</strong>
            </td>
            <td  style='border-bottom: 1px solid black'>
                <strong>". $these_statements['otc'] ." </strong>
            </td>
        </tr>
        <tr>
            <td style='text-align: right; width: 20%'>
                <strong>гражданство</strong>
            </td>
            <td style='border-bottom: 1px solid black'>
                <strong>". $these_statements['gr'] ." </strong>
            </td>
        </tr>
    </tbody>
</table>
<div>на</div>
<table style='width: 100%'>
    <tbody>
        <tr>
            <td style='text-align: right; width: 20%'>
                <strong>фамилия</strong>
            </td>
            <td  style='border-bottom: 1px solid black'>
                <strong>". $these_statements['new_fam'] ." </strong>
            </td>
        </tr>
        <tr>
            <td style='text-align: right; width: 20%'>
                <strong>имя</strong>
            </td>
            <td  style='border-bottom: 1px solid black;'>
                <strong>". $these_statements['new_nam'] ." </strong>
            </td>
        </tr>
        <tr>
            <td style='text-align: right; width: 20%'>
                <strong>отчество</strong>
            </td>
            <td  style='border-bottom: 1px solid black'>
                <strong>". $these_statements['new_otc'] ." </strong>
            </td>
        </tr>
        <tr>
            <td style='text-align: right; width: 20%'>
                <strong>гражданство</strong>
            </td>
            <td  style='border-bottom: 1px solid black'>
                <strong>". $these_statements['new_gr'] ." </strong>
            </td>
        </tr>
    </tbody>
</table>

<table style='margin-top: 10px'>
<tbody>
<tr>
    <td style='padding-right: 30px'>в связи с переменой</td>
    <td width='30px' height='30px' align='center' style='border: 1px solid black; font-size: 16pt; font-family: Arial'>". ((strcasecmp(mb_strtolower($these_statements['fam']), mb_strtolower($these_statements['new_fam'])) == 0) ? ' ' : '&#10003;') ."</td>
    <td style='padding-left: 20px; padding-right: 70px'>фамилии</td>
    <td width='30px' height='30px' align='center' style='border: 1px solid black; font-size: 16pt; font-family: Arial'>". ((strcasecmp(mb_strtolower($these_statements['nam']), mb_strtolower($these_statements['new_nam'])) == 0) ? ' ' : '&#10003;') ."</td>
    <td style='padding-left: 20px; padding-right: 70px'>имени</td>
    <td width='30px' height='30px' align='center' style='border: 1px solid black; font-size: 16pt; font-family: Arial'>". ((strcasecmp(mb_strtolower($these_statements['otc']), mb_strtolower($these_statements['new_otc'])) == 0) ? ' ' : '&#10003;') ."</td>
    <td style='padding-left: 20px'>отчества</td>
</tr>
</tbody>
</table>

<table>
<tbody>
<tr>
    <td></td>
    <td><font color='white'>ffffff</font></td>
</tr>
<tr>
    <td width='30px' height='30px' style='border: 1px solid black; font-size: 20px; font-family: Arial' align='center'> ". (($these_statements['reason'] == '1') ? '&#10003;' : ' ' ) ."</td>
    <td style='padding-left: 10px'><i>вступлением в брак (копия свидетельства	о заключении брака, копия паспорта с пропиской прилагается)</i></td>
</tr>
<tr>
    <td></td>
    <td><font color='white'>ffffff</font></td>
</tr>
<tr>
    <td width='30px' height='30px' style='border: 1px solid black; font-size: 20px; font-family: Arial' align='center'>" . (($these_statements['reason'] == '2') ? '&#10003;' : ' ') . "</td>
    <td style='padding-left: 10px'><i>расторжением брака (копия свидетельства	о расторжении брака, копия паспорта с пропиской прилагается)</i></td>
</tr>
<tr>
    <td></td>
    <td><font color='white'>ffffff</font></td>
</tr>
<tr>
    <td width='30px' height='30px' style='border: 1px solid black; font-size: 20px; font-family: Arial' align='center'>" . (($these_statements['reason'] == '3') ? '&#10003;' : ' ') . "</td>
    <td style='padding-left: 10px'><i>изменением гражданства (копия паспорта с пропиской прилагается)</i></td>
</tr>
<tr>
    <td></td>
    <td><font color='white'>ffffff</font></td>
</tr>
<tr>
    <td width='30px' height='30px' style='border: 1px solid black; font-size: 20px; font-family: Arial' align='center'>" . (($these_statements['reason'] == '4') ? '&#10003;' : ' ') . "</td>
    <td style='padding-left: 10px'><i>иное (подтверждающие документы, копия паспорта с пропиской прилагается)</i></td>
</tr>
<tr>
    <td></td>
    <td><font color='white'>ffffff</font></td>
</tr>
<tr>
    <td width='30px' height='30px' style='border: 1px solid black; font-size: 20px; font-family: Arial' align='center'>&#10003</td>
    <td style='padding-left: 10px; font-size: 8pt;'><i>Согласен на обработку персональных данных (в соответствии с ФЗ от <br> 27.07.2006 № 152-ФЗ)
</i></td>
</tr>
</tbody>
</table>
<div style='padding: 25px 0; margin-left: auto; margin-right: 0; width: 50%; font-size: 9pt'>Заявление сформировано на портале ОмГТУ из личного кабинета студента от " . $these_statements['dc'] . "</div>
<div style='padding-left: 20px; font-size: 11pt'>Виза управления кадров (главный корпус П-104)</div>

<div style='margin-left: auto; margin-right: 0; width: 70%; height: 30px; border-bottom: 1px solid black;'></div>
<div style='margin-left: auto; margin-right: 0; width: 70%; text-align: right; font-size: 6pt'>
подпись сотрудника принявшего заявление <font color='white'>fffffffffffffffffff</font> расшифровка <font color='white'>fffffffffffffffffff</font>дата
</div>

";

$pdf->WriteHTML($html);

$html = "</body>
</html>";

$pdf->WriteHTML($html);

if ($filename) {
    $pdf->Output($filename, 'F');
} else {
    $pdf->Output($these_statements['fam'] . $these_statements['nam'] . '.pdf', 'I');
}