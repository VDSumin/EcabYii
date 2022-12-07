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
//Кодировка | Формат | Размер шрифта | Шрифт
//Отступы: слева | справа | сверху | снизу | шапка | подвал
$pdf = new mPDF('utf-8', 'A4', '10', 'Times', 10, 10, 10, 15, 5, 5);
$pdf->charset_in = 'utf-8';

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('times', '', 14, '', true);

$pdf->setFooter('Сведения сдаются студентом в деканат с копией документа, удостоверяющего личность (все страницы, имеющие запись), 
ИНН и СНИЛС в срок до 15 сентября для очной и очно-заочной форм обучения; для студентов заочной формы обучения - по мере выхода на установочную сессию
в соответствии с графиком учебного процесса.');

//--------------table1
$html = "
<html><head>
<title>Печатная форма - Мои данные</title>
<style>
        table {
            border-collapse: collapse;
        }
</style>
</head>
<body>
    <div style='text-align: center;'><h3>Лист сверки данных обучающихся</h3></div>
    <table border='1'>
            <thead>
            <tr>
                <th width='20%'>Наименование</th>
                <th width='37%'>Хранимые данные</th>
                <th width='6%'>+/-</th>
                <th width='37%'>Желаемые исправления</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td colspan='4' class='text-center'>
                    <b>1. Обязательные сведения</b>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>ФИО</strong>
                </td>
                <td>
                    <strong>". $data['fio'] ." </strong>
                </td>
                <td>

                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan='4' class='text-center'>
                    <b>Сведения об обучении:</b>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Место текущего обучения</b>
                </td>
                <td>
                   <b>". $data['placeOfStudy'] ."</b>
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->placeOfStudyIsRight) || !$dataForEdit->placeOfStudyIsRight)?'-':'+')."
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                   <b>Специальность</b>
                </td>
                <td>
                    ". $data['spec']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->specIsRight) || !$dataForEdit->specIsRight)?'-':'+')."
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>ИФ</b>
                </td>
                <td>
                    ". $data['fin']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->finIsRight) || !$dataForEdit->finIsRight)?'-':'+')."
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Договор на обучение</b>
                </td>
                <td>
                    ". $data['contNmb']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->contNmbIsRight) || !$dataForEdit->contNmbIsRight)?'-':'+')."
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Договор на обуч C …</b>
                </td>
                <td>
                    ". ($data['contBegin'] ? CMisc::fromGalDate($data['contBegin']) : '') ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->contBeginIsRight) || !$dataForEdit->contBeginIsRight)?'-':'+')."
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Цел. Предприятие</b>
                </td>
                <td>
                    ". $data['entName']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->entNameIsRight) || !$dataForEdit->entNameIsRight)?'-':'+')."
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan='4' class='text-center'>
                    <b>Общие сведения:</b>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Пол</b>
                </td>
                <td>
                    ". $data['sex']."

                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->sexIsRight) || !$dataForEdit->sexIsRight)?'-':'+')."
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Дата Рождения</b>
                </td>
                <td>
                    ". CMisc::fromGalDate($data['borndate']) ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->borndateIsRight) || !$dataForEdit->borndateIsRight)?'-':'+')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'borndateManual')? CHtml::value($dataForEdit, 'borndateManual'):'')."
                </td>
            </tr>
            <tr>
                <td>
                    <b>Гражданство</b>
                </td>
                <td>
                    ". $data['gr']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->grIsRight) || !$dataForEdit->grIsRight)?'-':'+')."
                </td>
                <td>
                    ". GalStudentPersonalcard::getCatalogs($dataForEdit->grManual) ."
                </td>
            </tr>
            <tr>
                <td>
                    <b>Документ Удостоверяющий Личность</b>
                </td>
                <td>
                    ". $data['passVid']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->passVidIsRight) || !$dataForEdit->passVidIsRight)?'-':'+')."
                </td>
                <td>
                    ". GalStudentPersonalcard::getCatalogs($dataForEdit->passVidManual) ."
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Серия</b>
                </td>
                <td>
                    ". $data['pser']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->pserIsRight) || !$dataForEdit->pserIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'pserManual') ."
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Номер</b>
                </td>
                <td>
                    ". $data['pnmb']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->pnmbIsRight) || !$dataForEdit->pnmbIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'pnmbManual')."
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Кем Выдан</b>
                </td>
                <td>
                    ". $data['givenby']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->givenbyIsRight) || !$dataForEdit->givenbyIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'givenbyManual')."
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Дата Выдачи</b>
                </td>
                <td>
                    ". ($data['givendate'] ? CMisc::fromGalDate($data['givendate']) : '') ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->givendateIsRight) || !$dataForEdit->givendateIsRight)?'-':'+')."
                </td>
                <td>
                     ".(CHtml::value($dataForEdit, 'givendateManual')? CHtml::value($dataForEdit, 'givendateManual'):'')."
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Дата Окончания</b>
                </td>
                <td>
                    ". ($data['todate'] ? CMisc::fromGalDate($data['todate']) : '') ."

                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->todateIsRight) || !$dataForEdit->todateIsRight)?'-':'+')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'todateManual')? CHtml::value($dataForEdit, 'todateManual'):'')."
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Подразделение</b>
                </td>
                <td>
                    ". $data['givenpodr']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->givenpodrIsRight) || !$dataForEdit->givenpodrIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'givenpodrManual')."
                </td>
            </tr>
            <tr>
                <td>
                    <b>Адрес Рождения</b>
                </td>
                <td>
                    ". $data['bornAddr']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->bornAddrIsRight) || !$dataForEdit->bornAddrIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'bornAddrManual')."
                </td>
            </tr>
            <tr>
                <td>
                    <b>Адрес Постоянной Регистрации</b>
                </td>
                <td>
                    ". $data['passAddr'] ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->passAddrIsRight) || !$dataForEdit->passAddrIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'passAddrManual')."
                </td>
            </tr>
            <tr>
                <td>
                    <b>Адрес Проживание</b>
                </td>
                <td>
                    ". $data['liveAddr']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->liveAddrIsRight) || !$dataForEdit->liveAddrIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'liveAddrManual')."
                </td>
            </tr>
            <tr>
                <td>
                    <b>Адрес Временной Регистрации</b>
                </td>
                <td>
                    ". $data['tempAddr']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->tempAddrIsRight) || !$dataForEdit->tempAddrIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'tempAddrManual')."
                </td>
            </tr>
            <tr>
                <td>
                    <b>Предыдущее Образование Уровень</b>
                </td>
                <td>
                    ". $data['eduLevel']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->eduLevelIsRight) || !$dataForEdit->eduLevelIsRight)?'-':'+')."
                </td>
                <td>
                    ". GalStudentPersonalcard::getCatalogs($dataForEdit->eduLevelManual)."
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>Документ</b>
                </td>
                <td>
                    ". $data['eduDoc'] ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->eduDocIsRight) || !$dataForEdit->eduDocIsRight)?'-':'+')."
                </td>
                <td>
                    ". GalStudentPersonalcard::getCatalogs($dataForEdit->eduDocManual)."
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>Серия</b>
                </td>
                <td>
                    ". $data['eduSeria']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->eduSeriaIsRight) || !$dataForEdit->eduSeriaIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'eduSeriaManual')."
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>Номер</b>
                </td>
                <td>
                    ". $data['eduNmb'] ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->eduNmbIsRight) || !$dataForEdit->eduNmbIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'eduNmbManual')."
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>ДатаВыдачи</b>
                </td>
                <td>
                    ". ($data['eduDipDate'] ? CMisc::fromGalDate($data['eduDipDate']) : '') ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->eduDipDateIsRight) || !$dataForEdit->eduDipDateIsRight)?'-':'+')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'eduDipDateManual')? CHtml::value($dataForEdit, 'eduDipDateManual'):'')."
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>УчЗавед</b>
                </td>
                <td>
                    ". $data['eduPlace'] ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->eduPlaceIsRight) || !$dataForEdit->eduPlaceIsRight)?'-':'+')."
                </td>
                <td>
                    ". GalStudentPersonalcard::getSchool($dataForEdit->eduPlaceManual)."
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>Адрес уч.завед.</b>
                </td>
                <td>
                    ". $data['eduAddr']."

                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->eduAddrIsRight) || !$dataForEdit->eduAddrIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'eduAddrManual')."
                </td>
            </tr>
            <tr>
                <td>
                    <b>Телефон</b>
                </td>
                <td>
                    ". $data['phone'] ."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->phoneIsRight) || !$dataForEdit->phoneIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'phoneManual')."
                </td>
            </tr>
            <tr>
                <td>
                    <b>EMail</b>
                </td>
                <td>
                    ". $data['email']."
                </td>
                <td style='text-align: center;'>
                    ".((!isset($dataForEdit->emailIsRight) || !$dataForEdit->emailIsRight)?'-':'+')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'emailManual')."
                </td>
            </tr>
            </tbody>
        </table>";

$pdf->AddPage();

$pdf->WriteHTML($html);

/*$html = "<table border='0' style='width: 100%;'>
<tr>
<td style='height: 4mm;' colspan='2'></td>
</tr>
<tr>
<td style='font-size: 4mm;' colspan='2'>Достоверность представленных сведений подтверждаю.</td>
</tr>
<tr>
<td style='height: 8mm; border-bottom: 0.3mm solid black;' colspan='2'></td>
</tr>
<tr>
<td style='height: 2mm; text-align: center;'>(подпись / дата)</td><td style='height: 2mm; text-align: center;'>ФИО студента</td>
</tr>
</table>";

$pdf->WriteHTML($html);

$html = "<table border='0' style='width: 100%;'>
<tr>
<td style='height: 8mm;' colspan='2'></td>
</tr>
<tr>
<td style='font-size: 4mm;' colspan='2'>Сведения проверил:</td>
</tr>
<tr>
<td style='height: 8mm; border-bottom: 0.3mm solid black;' colspan='2'></td>
</tr>
<tr>
<td style='height: 2mm; text-align: center;'>(подпись / дата)</td><td style='height: 2mm; text-align: center;'>ФИО сотрудника</td>
</tr>
</table>";

$pdf->WriteHTML($html);*/

//------------------table2
       $html = "
<table border='1' width='100%'>
            <thead>
            <tr>
                <th colspan='6' class='text-center'>
                    2. Дополнительные сведения:
                </th>
            </tr>
            <tr>
                <th colspan='6' class='text-center'>
                    Дополнительные документы:
                </th>
            </tr>
            <tr>
                <th>Тип документа</th>
                <th>№</th>
                <th>Дата выдачи</th>
                <th>Действителен до ...</th>
                <th>Кем выдан</th>
                <th width='5%'></th>
            </tr>
            </thead>

            <tbody>
            <tr>

            </tr>
            <tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>ИНН</h4>
                </td>
                <td>
                    ". $inn['innNmb']."
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td>
                    ".((!isset($dataForEdit->innIsRight) || !$dataForEdit->innIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'innManualNmb')."
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>
            <tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>СНИЛС</h4>
                </td>
                <td>
                    ". $snils['snilsNmb']."
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td>
                    ".((!isset($dataForEdit->snilsIsRight) || !$dataForEdit->snilsIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'snilsManualNmb')."
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>
            <tr>
                <td  rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Полис обязательного мед. страхования</h4>
                </td>
                <td>
                    ". ($medPolicy['medPolicyNmb'])."
                </td>
                <td>
                    ". ($medPolicy['medPolicyGivendate'] ? CMisc::fromGalDate($medPolicy['medPolicyGivendate']) : '')."
                </td>
                <td>
                    ". ($medPolicy['medPolicyTodate'] ? CMisc::fromGalDate($medPolicy['medPolicyTodate']) : '')."
                </td>
                <td>
                    ". ($medPolicy['medPolicyGivenby'])."
                </td>
                <td>
                    ".((!isset($dataForEdit->medPolicyIsRight) || !$dataForEdit->medPolicyIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'medPolicyManualNmb')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'medPolicyManualGivendate')? CHtml::value($dataForEdit, 'medPolicyManualGivendate'):'')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'medPolicyManualTodate')? CHtml::value($dataForEdit, 'medPolicyManualTodate'):'')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'medPolicyManualGivenby')."
                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>
            <tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Справка из соц. защиты</h4>
                </td>
                <td>
                    ". $socialProtection['socialProtectionNmb']."
                </td>
                <td>
                    ". ($socialProtection['socialProtectionGivendate'] ? CMisc::fromGalDate($socialProtection['socialProtectionGivendate']) : '')."
                </td>
                <td>
                    ". ($socialProtection['socialProtectionTodate'] ? CMisc::fromGalDate($socialProtection['socialProtectionTodate']) : '')."
                </td>
                <td>
                    ". ($socialProtection['socialProtectionGivenby'])."
                </td>
                <td>
                    ".((!isset($dataForEdit->socialProtectionIsRight) || !$dataForEdit->socialProtectionIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'socialProtectionNmb')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'socialProtectionGivendate')? CHtml::value($dataForEdit, 'socialProtectionGivendate'):'')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'socialProtectionTodate')? CHtml::value($dataForEdit, 'socialProtectionTodate'):'')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'socialProtectionGivenby')."
                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>
            <tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Вид на жительство</h4>
                </td>
                <td>
                    ". $residence['residenceNmb'] ."
                </td>
                <td>
                    ". ($residence['residenceGivendate'] ? CMisc::fromGalDate($residence['residenceGivendate']) : '')."
                </td>
                <td>
                    ". ($residence['residenceTodate'] ? CMisc::fromGalDate($residence['residenceTodate']) : '')."
                </td>
                <td>

                </td>
                <td>
                    ".((!isset($dataForEdit->residenceIsRight) || !$dataForEdit->residenceIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'residenceManualNmb')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'residenceManualGivendate')? CHtml::value($dataForEdit, 'residenceManualGivendate'):'')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'residenceManualTodate')? CHtml::value($dataForEdit, 'residenceManualTodate'):'')."
                </td>
                <td>

                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>
            <tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Миграционная карта</h4>
                </td>
                <td>
                    ". $migration['migrationNmb']."
                </td>
                <td>
                    ". ($migration['migrationGivendate'] ? CMisc::fromGalDate($migration['migrationGivendate']) : '')."
                </td>
                <td>
                    ". ($migration['migrationTodate'] ? CMisc::fromGalDate($migration['migrationTodate']) : '')."
                </td>
                <td>

                </td>
                <td>
                    ".((!isset($dataForEdit->migrationIsRight) || !$dataForEdit->migrationIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'migrationManualNmb')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'migrationManualGivendate')? CHtml::value($dataForEdit, 'migrationManualGivendate'):'')."
                </td>
                <td>
                    ".(CHtml::value($dataForEdit, 'migrationManualTodate')? CHtml::value($dataForEdit, 'migrationManualTodate'):'')."
                </td>
                <td>

                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>
            </tbody>
        </table>";

//-----------------------table3
       $html .= "<table border='1' width='100%'>
            <thead>
            <tr>
                <th colspan='6' class='text-center'>
                    Сведения о родственниках:
                </th>
            </tr>
            <tr>
                <th class='text-center'>
                    Семейное положение
                </th>
                <th>
                ". $data['familystate'] ."
                </th>
                <th colspan='3'>

                    ". GalStudentPersonalcard::getCatalogs($dataForEdit->familyStateManual) ."

                </th>
                <th>
                    ".((!isset($dataForEdit->familyStateIsRight) || !$dataForEdit->familyStateIsRight)?'-':'+')."
                </th>
            </tr>
            <tr>
                <th>Родственники</th>
                <th>ФИО</th>
                <th>Дата рождения</th>
                <th>Телефон</th>
                <th>Адрес проживания</th>
                <th width='5%'></th>
            </tr>
            </thead>

            <tbody>
            <tr>

            </tr>
            <tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                  <h4> ". (($data['sex'] == 'М') ? 'Жена' : 'Муж') ." </h4>
                </td>
                <td>
                    ". $husbandWife['fio']."
                </td>
                <td>
                    ". ($husbandWife['borndate'] ? CMisc::fromGalDate($husbandWife['borndate']) : '') ."
                </td>
                <td>
                    ". $husbandWife['phone']."
                </td>
                <td>
                    ". $husbandWife['addr']."
                </td>
                <td>
                    ". ((!isset($dataForEdit->husbandWifeIsRight) || !$dataForEdit->husbandWifeIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'husbandWifeManualFio') ."
                </td>
                <td>
                ". (CHtml::value($dataForEdit, 'husbandWifeManualBorndate')?CHtml::value($dataForEdit, 'husbandWifeManualBorndate'):'') ."
                    
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'husbandWifeManualPhone')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'husbandWifeManualAddr')."
                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>


            <tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Мать</h4>
                </td>
                <td>
                    ". $mother['fio'] ."
                </td>
                <td>
                    ". ($mother['borndate'] ? CMisc::fromGalDate($mother['borndate']) : '') ."
                </td>
                <td>
                    ". $mother['phone'] ."
                </td>
                <td>
                    ". $mother['addr'] ."
                </td>
                <td>
                    ". ((!isset($dataForEdit->motherIsRight) || !$dataForEdit->motherIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'motherManualFio')."
                </td>
                <td>
                    ". (CHtml::value($dataForEdit, 'motherManualBorndate')?CHtml::value($dataForEdit, 'motherManualBorndate'):'') ."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'motherManualPhone')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'motherManualAddr')."
                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>
            <tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Отец</h4>
                </td>
                <td>
                    ". $father['fio'] ."
                </td>
                <td>
                    ". ($father['borndate'] ? CMisc::fromGalDate($father['borndate']) : '') ."
                </td>
                <td>
                    ". $father['phone'] ."
                </td>
                <td>
                    ". $father['addr'] ."
                </td>
                <td>
                    ". ((!isset($dataForEdit->fatherIsRight) || !$dataForEdit->fatherIsRight)?'-':'+')."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'fatherManualFio') ."
                </td>
                <td>
                    ". (CHtml::value($dataForEdit, 'fatherManualBorndate')?CHtml::value($dataForEdit, 'fatherManualBorndate'):'') ."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'fatherManualPhone')."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'fatherManualAddr')."
                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>";

if(CHtml::value($dataForEdit, 'kinder1IsRight') or !is_null(CHtml::value($dataForEdit, 'kinder1ManualFio'))  or isset($kinder[0]['fio'])) {
    $html .= "<tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Дети</h4>
                </td>
                <td>
                    " . (isset($kinder[0]) ? $kinder[0]['fio'] : '') . "
                </td>
                <td>
                    " . (isset($kinder[0]) ? ($kinder[0]['borndate'] ? CMisc::fromGalDate($kinder[0]['borndate']) : '') : '') . "
                </td>
                <td>
                    " . (isset($kinder[0]) ? $kinder[0]['phone'] : '') . "
                </td>
                <td>
                    " . (isset($kinder[0]) ? $kinder[0]['addr'] : '') . "
                </td>
                <td>
                    " . ((!isset($dataForEdit->kinder1IsRight) || !$dataForEdit->kinder1IsRight) ? '-' : '+') . "
                </td>
            </tr>
            <tr>
                <td>
                    " . CHtml::value($dataForEdit, 'kinder1ManualFio') . "
                </td>
                <td>
                    " . (CHtml::value($dataForEdit, 'kinder1ManualBorndate') ? CHtml::value($dataForEdit, 'kinder1ManualBorndate') : '') . "
                </td>
                <td>
                    " . CHtml::value($dataForEdit, 'kinder1ManualPhone') . "
                </td>
                <td>
                    " . CHtml::value($dataForEdit, 'kinder1ManualAddr') . "
                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>";
}

if(CHtml::value($dataForEdit, 'kinder2IsRight') or !is_null(CHtml::value($dataForEdit, 'kinder2ManualFio'))  or isset($kinder[1]['fio'])){
    $html .= "<tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Дети</h4>
                </td>
                <td>
                    ". (isset($kinder[1]) ? $kinder[1]['fio'] : '')."
                </td>
                <td>
                    ". (isset($kinder[1]) ? ($kinder[1]['borndate'] ? CMisc::fromGalDate($kinder[1]['borndate']) : '') : '') ."
                </td>
                <td>
                    ". (isset($kinder[1]) ? $kinder[1]['phone'] : '') ."
                </td>
                <td>
                    ". (isset($kinder[1]) ? $kinder[1]['addr'] : '') ."
                </td>
                <td>
                    ". ((!isset($dataForEdit->kinder2IsRight) || !$dataForEdit->kinder2IsRight)?'-':'+') ."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'kinder2ManualFio') ."
                </td>
                <td>
                    ". (CHtml::value($dataForEdit, 'kinder2ManualBorndate')?CHtml::value($dataForEdit, 'kinder2ManualBorndate'):'') ."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'kinder2ManualPhone') ."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'kinder2ManualAddr') ."
                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>";
}

if(CHtml::value($dataForEdit, 'kinder3IsRight') or !is_null(CHtml::value($dataForEdit, 'kinder3ManualFio'))  or isset($kinder[2]['fio'])){
    $html .= "<tr>
                <td rowspan='2' class='text-center' style='vertical-align: middle'>
                    <h4>Дети</h4>
                </td>
                <td>
                    ". (isset($kinder[2]) ? $kinder[2]['fio'] : '') ."
                </td>
                <td>
                    ". (isset($kinder[2]) ? ($kinder[2]['borndate'] ? CMisc::fromGalDate($kinder[2]['borndate']) : '') : '') ."
                </td>
                <td>
                    ". (isset($kinder[2]) ? $kinder[2]['phone'] : '') ."
                </td>
                <td>
                    ". (isset($kinder[2]) ? $kinder[2]['addr'] : '') ."
                </td>
                <td>
                    ". ((!isset($dataForEdit->kinder3IsRight) || !$dataForEdit->kinder3IsRight)?'-':'+') ."
                </td>
            </tr>
            <tr>
                <td>
                    ". CHtml::value($dataForEdit, 'kinder3ManualFio') ."
                </td>
                <td>
                    ". (CHtml::value($dataForEdit, 'kinder3ManualBorndate')?CHtml::value($dataForEdit, 'kinder3ManualBorndate'):'') ."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'kinder3ManualPhone') ."
                </td>
                <td>
                    ". CHtml::value($dataForEdit, 'kinder3ManualAddr') ."
                </td>
                <td style='color: white;'>
                *
                </td>
            </tr>";
}
$html .= "</tbody>
        </table>";


$pdf->WriteHTML($html);

$html = "<table border='0' style='width: 100%;'>
<tr>
<td style='height: 4mm;' colspan='2'></td>
</tr>
<tr>
<td style='font-size: 4mm;' colspan='2'>Достоверность представленных сведений подтверждаю.</td>
</tr>
<tr>
<td style='height: 8mm; border-bottom: 0.3mm solid black;' colspan='2'></td>
</tr>
<tr>
<td style='height: 2mm; text-align: center;'>(подпись / дата)</td><td style='height: 2mm; text-align: center;'>ФИО студента</td>
</tr>
<tr>
<td style='height: 8mm;' colspan='2'></td>
</tr>
<tr>
<td style='font-size: 4mm;' colspan='2'>Сведения проверил:</td>
</tr>
<tr>
<td style='height: 8mm; border-bottom: 0.3mm solid black;' colspan='2'></td>
</tr>
<tr>
<td style='height: 2mm; text-align: center;'>(подпись / дата)</td><td style='height: 2mm; text-align: center;'>ФИО сотрудника</td>
</tr>
</table>";

$pdf->WriteHTML($html);


$html = "</body>
</html>";

$pdf->WriteHTML($html);








$pdf->Output($data['fio'] . '.pdf', 'I');
