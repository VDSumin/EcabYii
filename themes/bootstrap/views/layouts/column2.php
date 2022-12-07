<?php /* @var $this Controller */

?>

        
<?php $this->beginContent('//layouts/main'); ?>
<div class="col-12 col-md-6 col-sm-12 col-lg-9 col-md-push-3" role="main">
    <div class="bs-docs-section">
        <?php echo $content; ?>
        <!-- content -->
    </div></div>

<div class="col-12 col-sm-12 col-md-3 col-lg-3 col-md-pull-9" role="complementary">
    <nav class="bs-docs-sidebar hidden-print affix-top">
        <?php
        $this->widget('zii.widgets.CMenu', array(
            'htmlOptions' => array('class' => 'nav '),
            'activateParents'=>true,
            'encodeLabel' => false,
            'linkLabelWrapper' => 'span',
            'items' => $this->menu,
        ));
        $this->endWidget();
        ?>



        <!-- sidebar -->
    </nav>
</div>

