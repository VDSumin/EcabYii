<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - Ошибка';
$this->breadcrumbs=array(
	'Ошибка',
);
?>

<?php if (in_array($code, array(404, 403))): ?>
    <h2>Ошибка №<?=$code?></h2>

    <div class="error">
        <?php echo CHtml::encode($message); ?>
    </div>
<?php else: ?>
    <h2>Технические работы</h2>

    <div class="error">
        На сайте в данном модуле ведутся технические работы, просьба выйти и повторить попытку позже.<br/>
        При повторной ошибке, пожалуйста уведомите об этом сотрудников Управления информатизации через <a href="http://omgtu.ru/adm/service/e.php">электронную приёмную Service Desk ОмГТУ</a> (Сайт ОмГТУ/Сервис/Электронная приёмная подразделений ОмГТУ/исполнитель - Управление информатизации).<br/>
        Приносим извинения за предоставленные неудобства.<br/>
        <!--<?php echo CHtml::encode($message); ?>-->
    </div>
<?php endif; ?>
