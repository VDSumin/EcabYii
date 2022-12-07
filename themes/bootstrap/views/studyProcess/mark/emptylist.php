<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 28.03.2020
 * Time: 20:09
 */
/* @var $this MarkController */

$this->pageTitle = Yii::app()->name . ' - Ошибка';
?>
<br /><br />
<?php if($code == 0):?>
    <center><h3>Сервер для работы с базой "Галактика" не отвечает.</h3></center>
<?php elseif(in_array($code, [400]) && $text['error'] == 'Для данного преподавателя нет ведомостей' && $typeOperation == 'fnppList' ):?>
    <center><h3>За вами отсутствуют закрепленные ведомости.</h3></center>
    <br/>
    <ul>
        <li type="1">В случае если это не так, попробуйте обновить страницу.</li>
        <li type="1">Если после обновления страницы не отображаются ведомости - уточните у заведующего кафедрой (исполняющего обязанности) закреплены ли за Вами ведомости.</li>
        <li type="1">В остальных случаях Вы можете обратиться на почту ias@omgtu.tech с описанием проблемы и указанием Вашего ФИО, кафедры и должности.</li>
    </ul>
<?php elseif(in_array($code, [400]) && $text['error'] == 'Для данного преподавателя нет ведомостей' && $typeOperation == 'fnppExtraList' ):?>
    <center><h3>За вами отсутствуют закрепленные направления.</h3></center>
    <br/>
    <ul>
        <li type="1">В случае если это не так, попробуйте обновить страницу.</li>
        <li type="1">Если после обновления страницы не отображаются ведомости – обратитесь в соответствующий деканат, для уточнения создано ли направление.</li>
        <li type="1">В остальных случаях Вы можете обратиться на почту ias@omgtu.tech с описанием проблемы и указанием Вашего ФИО, кафедры и должности.</li>
    </ul>
<?php elseif(in_array($code, [400,401,402,403,404,480]) ):?>
    <center><h3><?php echo $text['error']; ?></h3></center>
<?php elseif(in_array($code, [1001])): ?>
    <center><h3><?php echo $text; ?></h3></center>
<?php elseif(in_array($code, [1002])):?>
<center><h3>У Вас отсутствует доступ к ведомостям, чтобы получить доступ, заполните форму.</h3></center>
<br/>
<div class="jumbotron">
    <script src="https://yastatic.net/q/forms-frontend-ext/_/embed.js"></script>
    <iframe src="https://forms.yandex.ru/u/5e9019d2fb28050cfa7c5ecf/?iframe=1" frameborder="0"
            name="ya-form-5e9019d2fb28050cfa7c5ecf" width="650"></iframe>
</div>
<?php endif; ?>
