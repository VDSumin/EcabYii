<?php
$html = '
<table border="1">
    <tr>
        <td>Русский текст</td>
        <td>Русский текст</td>
        <td>Русский текст</td>
        <td>Русский текст</td>
    </tr>
    <tr>
        <td>Русский текст</td>
        <td>Русский текст</td>
        <td>Русский текст</td>
        <td><a href="http://mpdf.bpm1.com/" title="mPDF">mPDF</a></td>
    </tr>
</table>';

include("mpdf.php");
//Кодировка | Формат | Размер шрифта | Шрифт
//Отступы: слева | справа | сверху | снизу | шапка | подвал
$mpdf = new mPDF('utf-8', 'A4', '10', 'Times', 0, 0, 5, 5, 5, 5);
$mpdf->charset_in = 'utf-8';

$stylesheet = 'table {
                    text-align: center;
                    width: 320px;
                    color: black;
                    margin: 0;
                    float: left;
               }
               td {
                    width: 80px;
               }';

//Записываем стили
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->list_indent_first_level = 0;
//Записываем html
$mpdf->WriteHTML($html, 2);
$mpdf->Output('mpdf.pdf', 'I');