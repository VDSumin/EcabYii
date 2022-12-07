<?php
/* @var $this PersonalcardController */
/* @var $dataProvider CActiveDataProvider */
/* @var $dataForEdit GalStudentPersonalcard */


Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerScriptFile(Yii::app()->clientScript->getCoreScriptUrl() . '/jquery.cookie.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl() . '/js/imask.js');

Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/jquery.maskedinput.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css');

Yii::app()->clientScript->registerCssFile(Yii::app()->theme->getBaseUrl(true) . '/css/personalcard.css');
Yii::app()->clientScript->registerScript('updateFieldCheckRightUrl', 'var updateFieldCheckRightUrl = "' . Yii::app()->createAbsoluteUrl('personalcard/updateFieldCheckRight') . '";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('schoolFindUrl', 'var schoolFindUrl = "' . Yii::app()->createAbsoluteUrl('personalcard/school') . '";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('EduDocFindUrl', 'var EduDocFindUrl = "' . Yii::app()->createAbsoluteUrl('personalcard/eduDoc') . '";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('EduLevelFindUrl', 'var EduLevelFindUrl = "' . Yii::app()->createAbsoluteUrl('personalcard/eduLevel') . '";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('AddrFindUrl', 'var AddrFindUrl = "' . Yii::app()->createAbsoluteUrl('personalcard/address') . '";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('GrFindUrl', 'var GrFindUrl = "' . Yii::app()->createAbsoluteUrl('personalcard/gr') . '";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('PassFindUrl', 'var PassFindUrl = "' . Yii::app()->createAbsoluteUrl('personalcard/passport') . '";', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->getBaseUrl(true) . '/js/personalcard.js', CClientScript::POS_END);


$this->pageTitle = 'Мои данные';
?>
<!-- Модальное Окно  -->
<div id="overlay">
    <div class="popup">
        <h2>Инструкция</h2>
        <p>
            В левой части таблицы отображается информация, хранимая в электронной базе.
        </p>
        <table>
            <tr>
                <td>Для того, чтобы подтвердить эти данные необходимо нажать кнопку рядом с полем:</td>
                <td><img src="<?= Yii::app()->theme->getBaseUrl(true) . '/images/pic1.PNG' ?>"></td>
            </tr>
        </table>

        <table>
            <tr>
                <td>Если данные были успешно подтверждены кнопка примет следющий вид:</td>
                <td><img src="<?= Yii::app()->theme->getBaseUrl(true) . '/images/pic2.PNG' ?>"></td>
            </tr>
        </table>


        <p>
            Если Ваши данные ошибочны или не заполнены, необходимо заполнить поле в правой колонке, при этом кнопку
            подтверждения данных нажимать не нужно
        </p>
        <button class="bclose" title="Закрыть"
                onclick="document.getElementById('overlay').style.display='none';"></button>
    </div>
</div>


<?= CHtml::beginForm(array('personalcard/updateDataField')); ?>
<div class="col-xs-12">


    <h3 class="text-center">Мои данные</h3>
    <h4 class="text-center">Лист сверки данных</h4>

    <?php if ($isConfirmed) : ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <strong><span class="glyphicon glyphicon-ok-sign"> </span></strong> Ваши данные подтверждены деканатом
            <strong><?= $dateConfirmed; ?></strong>
        </div>
    <?php else: ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <strong><span class="glyphicon glyphicon-remove-sign"> </span> Внимание!</strong> Ваши данные еще не
            подтверждены деканатом!
        </div>
    <?php endif; ?>

    <?= CHtml::activeHiddenField($dataForEdit, 'pnrec', ['id' => 'pnrec']) ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped  table-hover">
            <thead>
            <tr>
                <th width="20%">Наименование</th>
                <th width="40%">Хранимые данные</th>
                <th width="5%"></th>
                <th width="30%">Желаемые исправления</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td colspan="4" class="text-center">
                    <b>1. Обязательные сведения</b>
                </td>
            </tr>
            <!--  временная заглушка -->
            <tr>
                <td td colspan="4" class="text-center">
                    <!-- <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong><span class="glyphicon glyphicon-remove-sign"> </span> Внимание!</strong> Прием заявлений на смену ФИО не работает!
                    </div>-->
                </td>
            </tr>
            <!-- END временная заглушка -->
            <tr>
                <td>
                    <strong>ФИО</strong>
                </td>
                <td>
                    <strong> <?= $data['fio']; ?> </strong>
                    <?= CHtml::hiddenField('pnrec', bin2hex($data['pnrec'])) ?>
                </td>
                <td>

                </td>
                <td>
                    <div style="display: flex; align-items: center;">

                        <?php

                        echo Chtml::htmlButton('Сменить ФИО', ["class" => "myLinkModal btn btn-primary", "id" => 'btn-change-fio']);
                        if($reconciliation){
                            echo Chtml::link('Скачать', array('SaveApplicationPDF', 'id' => $reconciliation), ["class"=>"btn btn-success ", "style"=>"margin-left: 10px", "target"=>"_blank"]);
                        }
                        ?>
                        <a href="../../../../assets/Инструкция студентам смена ФИО.pdf" style="display: inline-block; margin-left: auto; margin-right: 0"  target="_blank">?</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-center">
                    <b>Сведения об обучении:</b>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Место текущего обучения</b>
                </td>
                <td>
                    <b> <?= $data['placeOfStudy']; ?></b>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->placeOfStudyIsRight) || !$dataForEdit->placeOfStudyIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'placeOfStudy'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'placeOfStudy'
                        ]);
                    } ?>

                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Специальность</b>
                </td>
                <td>
                    <?= $data['spec']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->specIsRight) || !$dataForEdit->specIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'spec'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'spec'
                        ]);
                    } ?>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>ИФ</b>
                </td>
                <td>
                    <?= $data['fin']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->finIsRight) || !$dataForEdit->finIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'fin'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'fin'
                        ]);
                    } ?>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Договор на обучение</b>
                </td>
                <td>
                    <?= $data['contNmb']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->contNmbIsRight) || !$dataForEdit->contNmbIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'contNmb'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'contNmb'
                        ]);
                    } ?>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Договор на обуч C …</b>
                </td>
                <td>
                    <?= CMisc::fromGalDate($data['contBegin']); ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->contBeginIsRight) || !$dataForEdit->contBeginIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'contBegin'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'contBegin'
                        ]);
                    } ?>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Цел. Предприятие</b>
                </td>
                <td>
                    <?= $data['entName']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->entNameIsRight) || !$dataForEdit->entNameIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'entName'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'entName'
                        ]);
                    } ?>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-center">
                    <b>Общие сведения:</b>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Пол</b>
                </td>
                <td>
                    <?= $data['sex']; ?>

                </td>
                <td>
                    <?php if (!isset($dataForEdit->sexIsRight) || !$dataForEdit->sexIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'sex'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'sex'
                        ]);
                    } ?>

                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Дата Рождения</b>
                </td>
                <td>
                    <?= CMisc::fromGalDate($data['borndate']); ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->borndateIsRight) || !$dataForEdit->borndateIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'borndate'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'borndate'
                        ]);
                    } ?>

                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'borndateManual',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата рождения'
                        ),

                    ));
                    ?>

                </td>
            </tr>
            <tr>
                <td>
                    <b>Гражданство</b>
                </td>
                <td>
                    <?= $data['gr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->grIsRight) || !$dataForEdit->grIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'gr'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'gr'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeHiddenField($dataForEdit, "grManual");
                    echo CHtml::textField('grManual', GalStudentPersonalcard::getCatalogs($dataForEdit->grManual), ["placeholder" => "Гражданство",
                        "class" => "autoGr form-group form-control"])
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Документ Удостоверяющий Личность</b>
                </td>
                <td>
                    <?= $data['passVid']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->passVidIsRight) || !$dataForEdit->passVidIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'passVid'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'passVid'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeHiddenField($dataForEdit, "passVidManual");
                    echo CHtml::textField('passVidManual', GalStudentPersonalcard::getCatalogs($dataForEdit->passVidManual), ["placeholder" => "Вид документа",
                        "class" => "autoPass form-group form-control"])
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Серия</b>
                </td>
                <td>
                    <?= $data['pser']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->pserIsRight) || !$dataForEdit->pserIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'pser'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'pser'
                        ]);
                    } ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'pserManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Серия документа']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Номер</b>
                </td>
                <td>
                    <?= $data['pnmb']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->pnmbIsRight) || !$dataForEdit->pnmbIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'pnmb'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'pnmb'
                        ]);
                    } ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'pnmbManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Номер документа']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Кем Выдан</b>
                </td>
                <td>
                    <?= $data['givenby']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->givenbyIsRight) || !$dataForEdit->givenbyIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'givenby'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'givenby'
                        ]);
                    } ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'givenbyManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Кем выдан']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Дата Выдачи</b>
                </td>
                <td>
                    <?= CMisc::fromGalDate($data['givendate']); ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->givendateIsRight) || !$dataForEdit->givendateIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'givendate'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'givendate'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'givendateManual',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата выдачи'
                        ),

                    ));
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Дата Окончания</b>
                </td>
                <td>
                    <?= $data['todate'] ? CMisc::fromGalDate($data['todate']) : '' ?>

                </td>
                <td>
                    <?php if (!isset($dataForEdit->todateIsRight) || !$dataForEdit->todateIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'todate'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'todate'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'todateManual',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата окончания'
                        ),

                    ));
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    Документ Удостов. Личность <b>Подразделение</b>
                </td>
                <td>
                    <?= $data['givenpodr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->givenpodrIsRight) || !$dataForEdit->givenpodrIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'givenpodr'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'givenpodr'
                        ]);
                    } ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'givenpodrManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Подразделение']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Адрес Рождения</b>
                </td>
                <td>
                    <?= $data['bornAddr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->bornAddrIsRight) || !$dataForEdit->bornAddrIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'bornAddr'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'bornAddr'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeTextField($dataForEdit, 'bornAddrManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес рождения']);
                    CHtml::activeHiddenField($dataForEdit, "bornAddrManual");
                    CHtml::textField('bornAddrManual', GalStudentPersonalcard::getAddr($dataForEdit->bornAddrManual), ["placeholder" => "Адрес рождения",
                        "class" => "autoAddr form-group form-control"])
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Адрес Постоянной Регистрации</b>
                </td>
                <td>
                    <?= $data['passAddr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->passAddrIsRight) || !$dataForEdit->passAddrIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'passAddr'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'passAddr'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeTextField($dataForEdit, 'passAddrManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес прописки']);

                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Адрес Проживание</b>
                </td>
                <td>
                    <?= $data['liveAddr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->liveAddrIsRight) || !$dataForEdit->liveAddrIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'liveAddr'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'liveAddr'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeTextField($dataForEdit, 'liveAddrManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес проживания']);

                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Адрес Временной Регистрации</b>
                </td>
                <td>
                    <?= $data['tempAddr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->tempAddrIsRight) || !$dataForEdit->tempAddrIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'tempAddr'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'tempAddr'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeTextField($dataForEdit, 'tempAddrManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес временной регистрации']);

                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Предыдущее Образование Уровень</b>
                </td>
                <td>
                    <?= $data['eduLevel']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->eduLevelIsRight) || !$dataForEdit->eduLevelIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduLevel'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduLevel'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeHiddenField($dataForEdit, "eduLevelManual");
                    echo CHtml::textField('eduLevelManual', GalStudentPersonalcard::getCatalogs($dataForEdit->eduLevelManual), ["placeholder" => "Уровень",
                        "class" => "autoEduLevel form-group form-control"])
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>Документ</b>
                </td>
                <td>
                    <?= $data['eduDoc']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->eduDocIsRight) || !$dataForEdit->eduDocIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduDoc'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduDoc'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeHiddenField($dataForEdit, "eduDocManual");
                    echo CHtml::textField('eduDocManual', GalStudentPersonalcard::getCatalogs($dataForEdit->eduDocManual), ["placeholder" => "Вид документа",
                        "class" => "autoEduDoc form-group form-control"])
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>Серия</b>
                </td>
                <td>
                    <?= $data['eduSeria']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->eduSeriaIsRight) || !$dataForEdit->eduSeriaIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduSeria'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduSeria'
                        ]);
                    } ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'eduSeriaManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Серия документа']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>Номер</b>
                </td>
                <td>
                    <?= $data['eduNmb']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->eduNmbIsRight) || !$dataForEdit->eduNmbIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduNmb'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduNmb'
                        ]);
                    } ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'eduNmbManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Номер документа']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>ДатаВыдачи</b>
                </td>
                <td>
                    <?= $data['eduDipDate'] ? CMisc::fromGalDate($data['eduDipDate']) : '' ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->eduDipDateIsRight) || !$dataForEdit->eduDipDateIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduDipDate'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduDipDate'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'eduDipDateManual',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата выдачи'
                        ),

                    ));
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>УчЗавед</b>
                </td>
                <td>
                    <?= $data['eduPlace']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->eduPlaceIsRight) || !$dataForEdit->eduPlaceIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduPlace'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduPlace'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeHiddenField($dataForEdit, "eduPlaceManual");
                    echo CHtml::textField('school', GalStudentPersonalcard::getSchool($dataForEdit->eduPlaceManual), ["placeholder" => "Уч.заведение",
                        "class" => "autoSchool form-group form-control"])
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    Предыд.Образование <b>Адрес уч.завед.</b>
                </td>
                <td>
                    <?= $data['eduAddr']; ?>

                </td>
                <td>
                    <?php if (!isset($dataForEdit->eduAddrIsRight) || !$dataForEdit->eduAddrIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduAddr'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'eduAddr'
                        ]);
                    } ?>
                </td>
                <td>
                    <?php
                    echo CHtml::activeTextField($dataForEdit, 'eduAddrManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес уч. заведения']);

                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Телефон</b>
                </td>
                <td>
                    <?= $data['phone']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->phoneIsRight) || !$dataForEdit->phoneIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'phone'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'phone'
                        ]);
                    } ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'phoneManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'Телефоны']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>EMail</b>
                </td>
                <td>
                    <?= $data['email']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->emailIsRight) || !$dataForEdit->emailIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'email'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'email'
                        ]);
                    } ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'emailManual', ['class' => 'form-control js-update textdata', 'placeholder' => 'E-mail']) ?>
                </td>
            </tr>
            </tbody>
        </table>

        <style>
            #myModal h5 {
                display: inline;
            }

            #myModal #reason label {
                display: inline;
            }

            #myModal .modal-dialog {
                top: 50%;
                transform: translateY(-50%);
            }

            .agreement-label p {
                display: inline;
                padding-left: 10px;
            }
        </style>
        <script>

            $(document).ready(function () {
                $('#btn-change-fio').click(function () {
                    $('#myModal').modal('toggle');
                })
                $(document).on("click", "upload-file", function () {
                    $('#InquiriesRequests_id').val($(this).attr('value'));
                    $('.inputFile').click();
                });
                $('.inputFile').change(function () {
                    var reader = new FileReader();
                    reader.onload = function (image) {
                    };
                    reader.readAsDataURL(this.files[0]);
                    $('.btn-submit').click();
                });
            });
        </script>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered table-striped  table-hover">
            <thead>
            <tr>
                <th colspan="6" class="text-center">
                    2. Дополнительные сведения:
                </th>
            </tr>
            <tr>
                <th colspan="6" class="text-center">
                    Дополнительные документы:
                </th>
            </tr>
            <tr>
                <th>Тип документа</th>
                <th>№</th>
                <th>Дата выдачи</th>
                <th>Действителен до ...</th>
                <th>Кем выдан</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <tr>

            </tr>
            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>ИНН</h4>
                </td>
                <td>
                    <?= $inn['innNmb']; ?>
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td>
                    <?php if (!isset($dataForEdit->innIsRight) || !$dataForEdit->innIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'inn'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'inn'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'innManualNmb', ['class' => 'form-control js-update textdata js-mask',
                        'data-mask' => '************', 'placeholder' => 'Номер ИНН']) ?>
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>СНИЛС</h4>
                </td>
                <td>
                    <?= $snils['snilsNmb']; ?>
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td>
                    <?php if (!isset($dataForEdit->snilsIsRight) || !$dataForEdit->snilsIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'snils'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'snils'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'snilsManualNmb', ['class' => 'form-control js-update textdata js-mask',
                        'data-mask' => '***-***-*** **',
                        'placeholder' => 'Номер СНИЛС']) ?>
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Полис обязательного мед. страхования</h4>
                </td>
                <td>
                    <?= $medPolicy['medPolicyNmb']; ?>
                </td>
                <td>
                    <?= $medPolicy['medPolicyGivendate'] ? CMisc::fromGalDate($medPolicy['medPolicyGivendate']) : ''; ?>
                </td>
                <td>
                    <?= $medPolicy['medPolicyTodate'] ? CMisc::fromGalDate($medPolicy['medPolicyTodate']) : ''; ?>
                </td>
                <td>
                    <?= $medPolicy['medPolicyGivenby']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->medPolicyIsRight) || !$dataForEdit->medPolicyIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'medPolicy'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'medPolicy'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'medPolicyManualNmb', ['class' => 'form-control js-update textdata', 'placeholder' => 'Номер полиса']) ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'medPolicyManualGivendate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата выдачи'
                        ),

                    ));
                    ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'medPolicyManualTodate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата окончания'
                        ),

                    ));
                    ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'medPolicyManualGivenby', ['class' => 'form-control js-update textdata', 'placeholder' => 'Кем выдан']) ?>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Справка из соц. защиты</h4>
                </td>
                <td>
                    <?= $socialProtection['socialProtectionNmb']; ?>
                </td>
                <td>
                    <?= $socialProtection['socialProtectionGivendate'] ? CMisc::fromGalDate($socialProtection['socialProtectionGivendate']) : ''; ?>
                </td>
                <td>
                    <?= $socialProtection['socialProtectionTodate'] ? CMisc::fromGalDate($socialProtection['socialProtectionTodate']) : ''; ?>
                </td>
                <td>
                    <?= $socialProtection['socialProtectionGivenby']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->socialProtectionIsRight) || !$dataForEdit->socialProtectionIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'socialProtection'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'socialProtection'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'socialProtectionNmb', ['class' => 'form-control js-update textdata', 'placeholder' => 'Номер справки']) ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'socialProtectionGivendate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата выдачи'
                        ),

                    ));
                    ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'socialProtectionTodate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата окончания'
                        ),

                    ));
                    ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'socialProtectionGivenby', ['class' => 'form-control js-update textdata', 'placeholder' => 'Кем выдан']) ?>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Вид на жительство</h4>
                </td>
                <td>
                    <?= $residence['residenceNmb']; ?>
                </td>
                <td>
                    <?= $residence['residenceGivendate'] ? CMisc::fromGalDate($residence['residenceGivendate']) : ''; ?>
                </td>
                <td>
                    <?= $residence['residenceTodate'] ? CMisc::fromGalDate($residence['residenceTodate']) : ''; ?>
                </td>
                <td>

                </td>
                <td>
                    <?php if (!isset($dataForEdit->residenceIsRight) || !$dataForEdit->residenceIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'residence'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'residence'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'residenceManualNmb', ['class' => 'form-control js-update textdata', 'placeholder' => 'Номер']) ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'residenceManualGivendate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата выдачи'
                        ),

                    ));
                    ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'residenceManualTodate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата окончания'
                        ),

                    ));
                    ?>
                </td>
                <td>

                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Миграционная карта</h4>
                </td>
                <td>
                    <?= $migration['migrationNmb']; ?>
                </td>
                <td>
                    <?= $migration['migrationGivendate'] ? CMisc::fromGalDate($migration['migrationGivendate']) : ''; ?>
                </td>
                <td>
                    <?= $migration['migrationTodate'] ? CMisc::fromGalDate($migration['migrationTodate']) : ''; ?>
                </td>
                <td>

                </td>
                <td>
                    <?php if (!isset($dataForEdit->migrationIsRight) || !$dataForEdit->migrationIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'migration'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'migration'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'migrationManualNmb', ['class' => 'form-control js-update textdata', 'placeholder' => 'Номер']) ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'migrationManualGivendate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата выдачи'
                        ),

                    ));
                    ?>
                </td>
                <td>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $dataForEdit,
                        'attribute' => 'migrationManualTodate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата окончания'
                        ),

                    ));
                    ?>
                </td>
                <td>

                </td>
                <td>

                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped  table-hover">
            <thead>
            <tr>
                <th colspan="6" class="text-center">
                    Сведения о родственниках:
                </th>
            </tr>
            <tr>
                <th class="text-center">
                    Семейное положение
                </th>
                <th>
                    <?= $data['familystate']; ?>
                </th>
                <th colspan="3">
                    <?php
                    echo CHtml::activeDropDownList($dataForEdit, 'familyStateManual', CHtml::listData(GalStudentPersonalcard::getFamilyState(), 'nrec', 'name'), ["empty" => '',
                        "class" => "js-update-drop nrec form-group form-control"]);
                    ?>
                </th>
                <th><?php if (!isset($dataForEdit->familyStateIsRight) || !$dataForEdit->familyStateIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'familyState'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'familyState'
                        ]);
                    } ?>
                </th>
            </tr>
            <tr>
                <th>Родственники</th>
                <th>ФИО</th>
                <th>Дата рождения</th>
                <th>Телефон</th>
                <th>Адрес проживания</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <tr>

            </tr>
            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4> <?= ($data['sex'] == 'М') ? 'Жена' : 'Муж' ?> </h4>
                </td>
                <td>
                    <?= $husbandWife['fio']; ?>
                </td>
                <td>
                    <?= $husbandWife['borndate'] ? CMisc::fromGalDate($husbandWife['borndate']) : ''; ?>
                </td>
                <td>
                    <?= $husbandWife['phone']; ?>
                </td>
                <td>
                    <?= $husbandWife['addr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->husbandWifeIsRight) || !$dataForEdit->husbandWifeIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'husbandWife'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'husbandWife'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'husbandWifeManualFio', ['class' => 'form-control js-update textdata', 'placeholder' => 'ФИО']) ?>
                </td>
                <td>
                    <?php
                    /* $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model'=>$dataForEdit,
                        'attribute'=>'husbandWifeManualBorndate',
                        'options' => array(
                            'showAnim' => 'fold',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'dateFormat' => 'dd.mm.yy',
                            'defaultDate' => date("d.m.Y"),
                        ),
                        'language' => 'ru',
                        'htmlOptions' => array(
                            'class' => 'form-control js-date-update',
                            'placeholder' => 'Дата рождения'
                        ),

                    )); */
                    ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'husbandWifeManualPhone', ['class' => 'form-control js-update textdata', 'placeholder' => 'Телефон']) ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'husbandWifeManualAddr', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес']) ?>
                </td>
                <td>

                </td>
            </tr>


            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Мать</h4>
                </td>
                <td>
                    <?= $mother['fio']; ?>
                </td>
                <td>
                    <?= $mother['borndate'] ? CMisc::fromGalDate($mother['borndate']) : ''; ?>
                </td>
                <td>
                    <?= $mother['phone']; ?>
                </td>
                <td>
                    <?= $mother['addr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->motherIsRight) || !$dataForEdit->motherIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'mother'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'mother'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'motherManualFio', ['class' => 'form-control js-update textdata', 'placeholder' => 'ФИО']) ?>
                </td>
                <td>
                    <?php
                    /*  $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                          'model'=>$dataForEdit,
                          'attribute'=>'motherManualBorndate',
                          'options' => array(
                              'showAnim' => 'fold',
                              'changeMonth' => true,
                              'changeYear' => true,
                              'dateFormat' => 'dd.mm.yy',
                              'defaultDate' => date("d.m.Y"),
                          ),
                          'language' => 'ru',
                          'htmlOptions' => array(
                              'class' => 'form-control js-date-update',
                              'placeholder' => 'Дата рождения'
                          ),

                      ));*/
                    ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'motherManualPhone', ['class' => 'form-control js-update textdata', 'placeholder' => 'Телефон']) ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'motherManualAddr', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес']) ?>
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Отец</h4>
                </td>
                <td>
                    <?= $father['fio']; ?>
                </td>
                <td>
                    <?= $father['borndate'] ? CMisc::fromGalDate($father['borndate']) : ''; ?>
                </td>
                <td>
                    <?= $father['phone']; ?>
                </td>
                <td>
                    <?= $father['addr']; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->fatherIsRight) || !$dataForEdit->fatherIsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'father'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'father'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'fatherManualFio', ['class' => 'form-control js-update textdata', 'placeholder' => 'ФИО']) ?>
                </td>
                <td>
                    <?php
                    /* $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                         'model'=>$dataForEdit,
                         'attribute'=>'fatherManualBorndate',
                         'options' => array(
                             'showAnim' => 'fold',
                             'changeMonth' => true,
                             'changeYear' => true,
                             'dateFormat' => 'dd.mm.yy',
                             'defaultDate' => date("d.m.Y"),
                         ),
                         'language' => 'ru',
                         'htmlOptions' => array(
                             'class' => 'form-control js-date-update',
                             'placeholder' => 'Дата рождения'
                         ),

                     ));*/
                    ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'fatherManualPhone', ['class' => 'form-control js-update textdata', 'placeholder' => 'Телефон']) ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'fatherManualAddr', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес']) ?>
                </td>
                <td>

                </td>
            </tr>


            <tr>
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Дети</h4> <br/>
                    <?php
                    echo CHtml::button('+', [
                        'class' => 'btn btn-default',
                        'style' => 'margin-top: 0.2em; vertical-align: top',
                        'id' => 'kinder1Btn'
                    ]);
                    ?>
                </td>
                <td>
                    <?= isset($kinder[0]) ? $kinder[0]['fio'] : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[0]) ? ($kinder[0]['borndate'] ? CMisc::fromGalDate($kinder[0]['borndate']) : '') : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[0]) ? $kinder[0]['phone'] : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[0]) ? $kinder[0]['addr'] : ''; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->kinder1IsRight) || !$dataForEdit->kinder1IsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'kinder1'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'kinder1'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder1ManualFio', ['class' => 'form-control js-update textdata', 'placeholder' => 'ФИО']) ?>
                </td>
                <td>
                    <?php
                    /*   $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                           'model'=>$dataForEdit,
                           'attribute'=>'kinder1ManualBorndate',
                           'options' => array(
                               'showAnim' => 'fold',
                               'changeMonth' => true,
                               'changeYear' => true,
                               'dateFormat' => 'dd.mm.yy',
                               'defaultDate' => date("d.m.Y"),
                           ),
                           'language' => 'ru',
                           'htmlOptions' => array(
                               'class' => 'form-control js-date-update',
                               'placeholder' => 'Дата рождения'
                           ),

                       )); */
                    ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder1ManualPhone', ['class' => 'form-control js-update textdata', 'placeholder' => 'Телефон']) ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder1ManualAddr', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес']) ?>
                </td>
                <td>

                </td>
            </tr>

            <tr class="hidcont kinder2Hid">
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Дети</h4>

                    <?php
                    echo CHtml::button('+', [
                        'class' => 'btn btn-default',
                        'style' => 'margin-top: 0.2em; vertical-align: top',
                        'id' => 'kinder2Btn'
                    ]);
                    ?>
                </td>
                <td>
                    <?= isset($kinder[1]) ? $kinder[1]['fio'] : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[1]) ? ($kinder[1]['borndate'] ? CMisc::fromGalDate($kinder[1]['borndate']) : '') : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[1]) ? $kinder[1]['phone'] : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[1]) ? $kinder[1]['addr'] : ''; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->kinder2IsRight) || !$dataForEdit->kinder2IsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'kinder2'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'kinder2'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr class="hidcont kinder2Hid">
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr class="hidcont kinder2Hid">
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder2ManualFio', ['class' => 'form-control js-update textdata', 'placeholder' => 'ФИО']) ?>
                </td>
                <td>
                    <?php
                    /*      $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                              'model'=>$dataForEdit,
                              'attribute'=>'kinder2ManualBorndate',
                              'options' => array(
                                  'showAnim' => 'fold',
                                  'changeMonth' => true,
                                  'changeYear' => true,
                                  'dateFormat' => 'dd.mm.yy',
                                  'defaultDate' => date("d.m.Y"),
                              ),
                              'language' => 'ru',
                              'htmlOptions' => array(
                                  'class' => 'form-control js-date-update',
                                  'placeholder' => 'Дата рождения'
                              ),

                          ));*/
                    ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder2ManualPhone', ['class' => 'form-control js-update textdata', 'placeholder' => 'Телефон']) ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder2ManualAddr', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес']) ?>
                </td>
                <td>

                </td>
            </tr>

            <tr class="hidcont kinder3Hid">
                <td rowspan="3" class="text-center" style="vertical-align: middle">
                    <h4>Дети</h4>
                </td>
                <td>
                    <?= isset($kinder[2]) ? $kinder[2]['fio'] : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[2]) ? ($kinder[2]['borndate'] ? CMisc::fromGalDate($kinder[2]['borndate']) : '') : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[2]) ? $kinder[2]['phone'] : ''; ?>
                </td>
                <td>
                    <?= isset($kinder[2]) ? $kinder[2]['addr'] : ''; ?>
                </td>
                <td>
                    <?php if (!isset($dataForEdit->kinder3IsRight) || !$dataForEdit->kinder3IsRight) {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-danger glyphicon glyphicon-remove fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'kinder3'
                        ]);
                    } else {
                        echo CHtml::link('', '#', [
                            'class' => 'btn btn-success glyphicon glyphicon-ok fieldCheckRight',
                            'style' => 'margin-top: 0.2em; vertical-align: top',
                            'id' => 'kinder3'
                        ]);
                    } ?>
                </td>
            </tr>
            <tr class="hidcont kinder3Hid">
                <td colspan="5" class="text-center">
                    <strong>Актуальные данные (для заполнения вручную)</strong>
                </td>
            </tr>
            <tr class="hidcont kinder3Hid">
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder3ManualFio', ['class' => 'form-control js-update textdata', 'placeholder' => 'ФИО']) ?>
                </td>
                <td>
                    <?php
                    /*  $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                          'model'=>$dataForEdit,
                          'attribute'=>'kinder3ManualBorndate',
                          'options' => array(
                              'showAnim' => 'fold',
                              'changeMonth' => true,
                              'changeYear' => true,
                              'dateFormat' => 'dd.mm.yy',
                              'defaultDate' => date("d.m.Y"),
                          ),
                          'language' => 'ru',
                          'htmlOptions' => array(
                              'class' => 'form-control js-date-update',
                              'placeholder' => 'Дата рождения'
                          ),

                      ));*/
                    ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder3ManualPhone', ['class' => 'form-control js-update textdata', 'placeholder' => 'Телефон']) ?>
                </td>
                <td>
                    <?= CHtml::activeTextField($dataForEdit, 'kinder3ManualAddr', ['class' => 'form-control js-update textdata', 'placeholder' => 'Адрес']) ?>
                </td>
                <td>

                </td>
            </tr>


            </tbody>
        </table>
    </div>
</div>

<?= CHtml::endForm() ?>


<?php echo CHtml::link('Печатать', ['personalcard/print', 'id' => $id], [
    'class' => 'btn btn-primary',
    'style' => 'margin-top: 0.2em; vertical-align: top',
    'target' => '_blank'
]); ?>

<div class="modal fade modal-dialog-centered" id="myModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Смена ФИО</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <?php
                echo CHtml::beginForm(array('Personalcard/ChangeFIO'), 'post', ["enctype"=>"multipart/form-data"]);
            ?>
            <div class="modal-body">
                <fieldset>
                    <label for="fio">Новое ФИО</label>
                    <?php
                        echo CHtml::textField('fio', array_key_exists('fio', $post) ? $post['fio'] : '', ["placeholder" => "ФИО", "class" => "form-group form-control", "required"=>"required", "maxlength" =>"120"]);
                    ?>
                        <div id="warning-fio" style="display: none; color: red; padding-bottom: 15px">ФИО не может содержать менее двух слов</div>
                        <label for="document">Документ подтверждающий личность с новым ФИО</label>
                    <?php
                        echo CHtml::dropDownList('document', array_key_exists('document', $post) ? $post['document'] : '', PersonalcardController::GetDocumentTypes(2), ["class" => "form-group form-control"]);
                    ?>
                        <label for="num-passport">Номер документа</label>
                        <?php
                        echo CHtml::textField('num-passport', array_key_exists('num-passport', $post) ? $post['num-passport'] : '', ["placeholder" => "Номер документа", "class" => "form-group form-control", "required"=>"required"]);
                        ?>
                        <label for="date-passport">Дата выдачи документа</label>
                        <?php
                        echo CHtml::dateField('date-passport', array_key_exists('date-passport', $post) ? $post['date-passport'] : '', ["placeholder" => "Дата выдачи документа", "class" => "form-group form-control", "required"=>"required"]);
                        echo CHtml::fileField('upload-passport', '', ["class"=>"btn-upload"]);
                        ?>
                        <label for="upload-passport" style="display: flex; align-items: center; margin-bottom: 15px">
                            <div class="btn btn-primary" style="margin-right: 10px">Выбрать файл</div>
                            <div class="upload-fake">Выберите файл</div>
                        </label>
                    <label for="reason">Причина смены ФИО</label>
                    <?php
                        echo CHtml::dropDownList('reason', array_key_exists('reason', $post) ? $post['reason'] : '', PersonalcardController::GetReason(), ["class" => "form-group form-control"]);
                        ?>
                    <label for="num-reason" class="label-reason">Номер документа</label>
                    <?php
                        echo CHtml::textField('num-reason', array_key_exists('num-reason', $post) ? $post['num-reason'] : '', ["placeholder" => "Номер документа", "class" => "form-group form-control", "required"=>"required"]);

                    ?>
                    <label for="date-reason" class="label-reason">Дата выдачи документа</label>
                    <?php
                        echo CHtml::dateField('date-reason', array_key_exists('date-reason', $post) ? $post['date-reason'] : '', ["placeholder" => "Дата выдачи документа", "class" => "form-group form-control", "required"=>"required"]);
                        echo CHtml::fileField('upload-reason', '', ["class"=>"btn-upload"]);
                    ?>
                    <label for="upload-reason" style="display: flex; align-items: center; margin-bottom: 15px" id="reason-label">
                        <div class="btn btn-primary" style="margin-right: 10px">Выбрать файл</div>
                        <div class="upload-fake">Выберите файл</div>
                    </label>
                </fieldset>
            </div>
            <div class="modal-footer" style="display: flex; justify-content: space-between">
                <p><a href="../../../../assets/Инструкция студентам смена ФИО.pdf" target="_blank">Инструкция по смене ФИО</a></p>
                <?php
                echo CHtml::submitButton('Отправить', ["class"=>"btn btn-success", "style" => "margin-right: 15px", 'id'=>'submit-fio']);
                ?>
            </div>
        </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var delay_popup = 1000;
    setTimeout("document.getElementById('overlay').style.display='block'", delay_popup);

    $(function () {
        // Проверяем запись в куках о посещении
        // Если запись есть - ничего не происходит
        if (!$.cookie('hideModal')) {
            // если cookie не установлено появится окно
            // с задержкой 5 секунд
            var delay_popup = 1000;
            setTimeout("document.getElementById('parent_popup').style.display='block'", delay_popup);
        }
        $.cookie('hideModal', true, {
            // Время хранения cookie в днях
            expires: 7,
            path: '/'
        });
    });


    $(document).ready(function () {
        message = '<?= $message; ?>'
        $('#reason').on('change', function () {
            let option = this.querySelector(`option[value="${this.value}"]`)
            if (option.value == 3) {
                $('#upload-reason, #num-reason, #date-reason').prop('required', false)
                $('#reason-label, #num-reason, #date-reason, .label-reason').css("display", "none");
            } else {
                $('#num-reason, #date-reason').css("display", "block");
                $('#reason-label').css("display", "flex");
                $('#upload-reason, #num-reason, #date-reason, .label-reason').prop('required', true);
            }
        })
            if ($("#reason").val() == 3) {
                $('#upload-reason, #num-reason, #date-reason').prop('required', false)
                $('#reason-label, #num-reason, #date-reason, .label-reason').css("display", "none");
            } else {
                $('#num-reason, #date-reason, .label-reason').css("display", "block");
                $('#reason-label').css("display", "flex");
                $('#upload-reason, #num-reason, #date-reason').prop('required', true);
        }
        if(message === ""){

        }
        if (message === '1') {
            $.notify({
                icon: 'glyphicon glyphicon-success-sign',
                title: 'Выполнено!',
                message: 'Заявление отправлено!',
                newest_on_top: true
            }, {
                type: 'success',
                icon_type: 'class',
                delay: 2000
            });
        } else if(message.length >= 2) {
            $.notify({
                icon: 'glyphicon glyphicon-warning-sign',
                title: 'Ошибка!',
                message: message,
                newest_on_top: true
            }, {
                type: 'danger',
                icon_type: 'class',
                delay: 2000
            });
        }

        document.getElementById('fio').onblur = function (){
            let value = this.value.trim();
            value = value.match(/\s/g);
            if(!value){
                $('#fio').css('border-color', 'red')
                $('#submit-fio').prop('disabled', true)
                $('#warning-fio').css('display', 'block')
            }
            else {
                $('#submit-fio').prop('disabled', false)
                $('#fio').css('border-color', '#ccc')
                $('#warning-fio').css('display', 'none')
            }
        }
        new IMask(document.getElementById('fio'), {
            mask: /[а-яА-ЯёЁ]/
        });

        let fields = document.querySelectorAll('.btn-upload');
        Array.prototype.forEach.call(fields, function (input) {
            let label = input.nextElementSibling,
                labelVal = label.querySelector('.upload-fake').innerText;

            input.addEventListener('change', function (e) {
                if (this.files)
                    label.querySelector('.upload-fake').innerText = 'Файл выбран';
                else
                    label.querySelector('.upload-fake').innerText = labelVal;
            });
        });
    });
</script>

<style>
    input {
        transition: 0.5s !important;
    }
    input[type=file]{
        display: none;
    }
    .modal-footer::before, .modal-footer::after{
        display: none;
    }
</style>
