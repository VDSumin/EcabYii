<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="en">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/navbar-fixed-top.css?1">
    <link rel="stylesheet" type="text/css"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/css/sticky-footer-navbar.css?1">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/extra.css?2">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/navbar-left.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/table-ulist.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/last.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/infoBlock.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/form.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/css/notification/notificationBell.css">

    <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(61180627, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/61180627" style="position:absolute; left:-9999px;" alt=""/></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->

</head>

<body cz-shortcut-listen="true">

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand"><?php echo CHtml::encode(Yii::app()->name); ?></div>
            <?         // Notification Bell
            require_once('protected/modules/notification/models/EmployeePermissions.php');
            if (((!Yii::app()->user->isGuest and !Yii::app()->user->getPerStatus()) || (EmployeePermissions::isAnyPermissions()))): ?>
                <div style="display: flex; max-width: max-content">
                    <ul class="nav navbar-nav">
                        <li>
                            <a id="notificationLink" href="index.php?r=notification/">
                                <img style="width: 20px" src=" /themes/bootstrap/images/notify-bell.svg">
                            </a>
                        </li>
                    </ul>
                </div>
            <? endif; // Notification Bell ?>
        </div><!-- header -->
        <div id="navbar" class="navbar-collapse collapse">
            <?php
            $this->widget('zii.widgets.CMenu', array(
                'htmlOptions' => array('class' => 'nav navbar-nav'),
                'encodeLabel' => false,
                'items' => Controller::getCMenuItem(),
            )); ?>
        </div><!-- mainmenu -->
    </div>
</nav>

<div class="container">
    <?php if (!empty($this->breadcrumbs)): ?>
        <ol class="breadcrumb">
            <?php $this->widget('zii.widgets.CBreadcrumbs', array(
                'links' => $this->breadcrumbs,
            )); ?><!-- breadcrumbs -->
        </ol>
    <?php endif ?>

    <?php echo $content; ?>
</div>

<footer class="footer">
    <script rel="javascript" type="text/javascript"
            src="<?php echo Yii::app()->theme->baseUrl; ?>/js/extra.js"></script>
    <script rel="javascript" type="text/javascript" src="<?php echo Yii::app()->baseUrl; ?>/js/tooltip.js"></script>
    <script rel="javascript" type="text/javascript"
            src="<?php echo Yii::app()->theme->baseUrl; ?>/js/bootstrap-notify.min.js"></script>
    <div class="container">
        <?php if (!Yii::app()->user->isGuest): ?>
            <div id="timer"><?= gmdate('i:s', Yii::app()->user->authTimeout) ?></div>
            Copyright &copy; <?php echo date('Y'); ?> by Omgtu.
            All Rights Reserved.<br/>
        <?php else: ?>
            Copyright &copy; <?php echo date('Y'); ?> by Omgtu.<br/>
            All Rights Reserved.<br/>
        <?php endif; ?>
    </div><!-- footer -->
</footer>
<!-- page -->

<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/bootstrap.min.js">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <
    script;
    src = "<?= Yii::app()->theme->baseUrl ?>/js/studbook/ie10-viewport-bug-workaround.js" ></script>
<?php if (!Yii::app()->user->getPerStatus()): ?>
<!--/*Notification-bell-changer*/-->
<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/notification/notificationBellSwitch.js"></script>
<? endif; ?>
</body>
<?php if (!Yii::app()->user->isGuest): ?>
    <script>
        var loginTimeOut = <?=Yii::app()->user->authTimeout ? Yii::app()->user->authTimeout : 0?>;
        var time = loginTimeOut;
        var ajaxRedy = true;

        function updateSession() {
            if (ajaxRedy) {
                ajaxRedy = false;
                $.ajax({url: "<?=Yii::app()->controller->createUrl('/site/ajax');?>"});
                setTimeout(function () {
                    ajaxRedy = true;
                }, 3000);
            }
            time = loginTimeOut;
        }

        $(document).on('keydown', updateSession);
        $(document).on('mousemove', updateSession);

        $(function () {
            var inter = setInterval(function () {
                if (time <= 0) {
                    clearInterval(inter);
                    if (loginTimeOut) {
                        $.notify({
                            // options
                            icon: 'glyphicon glyphicon-warning-sign',
                            message: 'ВНИМАНИЕ! ВЫ АВТОМАТИЧЕСКИ ВЫШЛИ, НАЖМИТЕ СЮДА, ЧТОБЫ ВОЙТИ ЕЩЁ РАЗ.',
                            <?php $loginUrl = Yii::app()->user->loginUrl; ?>
                            url: '<?= is_array($loginUrl) ? Yii::app()->controller->createUrl(array_shift($loginUrl), $loginUrl) : $loginUrl; ?>',
                            target: '_blank'
                        }, {
                            // settings
                            type: 'danger',
                            icon_type: 'class',
                            delay: 0
                        });
                    }
                    return;
                }
                --time;
                var minutes = Math.floor(time / 60);
                var seconds = time - 60 * minutes;

                $('#timer').text(minutes + ':' + (seconds > 9 ? seconds : ('0' + seconds)));
            }, 1000);
        });
    </script>
<?php endif; ?>
</html>
