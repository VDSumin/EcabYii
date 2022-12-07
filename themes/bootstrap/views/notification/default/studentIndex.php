<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/notification/student/listItems.css">
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/notification/student/modalNote.css">

<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Мои уведомления';
$this->breadcrumbs = [
    'Мои уведомления'
];
?>

<?php
foreach ($notes as $note)
{
    $id = $note['id'];
    $title = $note['title'];
    $text = $note['text'];
    $isConfirmed = $note['confirm'];
    $blueClolor = '#d9edf7';
    $redColor = '#ffebee';
    $color = $redColor;
    if($isConfirmed) $color = $blueClolor;
    $close = <<< EOT
    <a href="" class="modal__close demo-close">
        <svg class="" viewBox="0 0 24 24"><path d="M19 6.41l-1.41-1.41-5.59 5.59-5.59-5.59-1.41 1.41 5.59 5.59-5.59 5.59 1.41 1.41 5.59-5.59 5.59 5.59 1.41-1.41-5.59-5.59z"/><path d="M0 0h24v24h-24z" fill="none"/></svg>
    </a>
EOT;
    echo "<div id='modal$id' class='modal modal__bg' role='dialog' aria-hidden='true'>";
    echo "<div class='modal__dialog'>";
    echo "<div style='background: $color' class='modal__content'>";
    echo "<h3>$title</h3>";
    echo "<p style='white-space: pre-line'>$text</p>";
    if(!$isConfirmed)
        echo "<button class='modal__close btn btn-success' onclick='confirmNote($id)' >Прочитать</button>";
    echo $close;
    echo "</div>";
    echo "</div>";
    echo "</div>";

}
?>
<!-- Modal -->
<!--<div id="modal" class="modal modal__bg" role="dialog" aria-hidden="true">
    <div class="modal__dialog">
        <div class="modal__content">
            <h1>Modal</h1>
            <p>Church-key American Apparel trust fund, cardigan mlkshk small batch Godard mustache pickled bespoke meh seitan. Wes Anderson farm-to-table vegan, kitsch Carles 8-bit gastropub paleo YOLO jean shorts health goth lo-fi. Normcore chambray locavore Banksy, YOLO meditation master cleanse readymade Bushwick.</p>


            <a href="" class="modal__close demo-close">
                <svg class="" viewBox="0 0 24 24"><path d="M19 6.41l-1.41-1.41-5.59 5.59-5.59-5.59-1.41 1.41 5.59 5.59-5.59 5.59 1.41 1.41 5.59-5.59 5.59 5.59 1.41-1.41-5.59-5.59z"/><path d="M0 0h24v24h-24z" fill="none"/></svg>
            </a>

        </div>
    </div>
</div>
<div id="modal2" class="modal modal--align-top modal__bg" role="dialog" aria-hidden="true">
    <div class="modal__dialog">
        <div class="modal__content">
            <h1>Big Modal</h1>
            <h3>This modal is pretty tall.</h3>
            <p>Selfies normcore four dollar toast four loko listicle artisan. Hoodie Marfa authentic, wayfarers church-key tofu Banksy pop-up Kickstarter Brooklyn heirloom swag synth. Echo Park cray synth mixtape. Tofu gastropub squid readymade, trust fund Wes Anderson DIY PBR 8-bit try-hard +1 Shoreditch lo-fi tote bag.</p>
            <p><img src="http://unsplash.it/600/300" alt="" /></p>
            <p>Mumblecore cred selfies fingerstache. Tousled skateboard plaid lo-fi shabby chic salvia, swag Odd Future Etsy art party Austin cronut. Crucifix whatever Pinterest food truck, pickled viral cray 90's DIY chambray keffiyeh biodiesel Vice blog. Cred meh yr tofu.</p>
            <p>Mumblecore cred selfies fingerstache. Tousled skateboard plaid lo-fi shabby chic salvia, swag Odd Future Etsy art party Austin cronut. Crucifix whatever Pinterest food truck, pickled viral cray 90's DIY chambray keffiyeh biodiesel Vice blog. Cred meh yr tofu.</p>

            <a href="" class="modal__close demo-close">
                <svg class="" viewBox="0 0 24 24"><path d="M19 6.41l-1.41-1.41-5.59 5.59-5.59-5.59-1.41 1.41 5.59 5.59-5.59 5.59 1.41 1.41 5.59-5.59 5.59 5.59 1.41-1.41-5.59-5.59z"/><path d="M0 0h24v24h-24z" fill="none"/></svg>
            </a>
        </div>
    </div>
</div>
<div id="modal3" class="modal modal__bg" role="dialog" aria-hidden="true">
    <div class="modal__dialog">
        <div class="modal__content">
            <h1>Modal 4uyy</h1>
            <p>Church-key American Apparel trust fund, cardigan mlkshk small batch Godard mustache pickled bespoke meh seitan. Wes Anderson farm-to-table vegan, kitsch Carles 8-bit gastropub paleo YOLO jean shorts health goth lo-fi.</p>


            <a href="" class="modal__close demo-close">
                <svg class="" viewBox="0 0 24 24"><path d="M19 6.41l-1.41-1.41-5.59 5.59-5.59-5.59-1.41 1.41 5.59 5.59-5.59 5.59 1.41 1.41 5.59-5.59 5.59 5.59 1.41-1.41-5.59-5.59z"/><path d="M0 0h24v24h-24z" fill="none"/></svg>
            </a>
        </div>
    </div>
</div>
<div id="modal4" class="modal modal__bg" role="dialog" aria-hidden="true">
    <div class="modal__dialog">
        <div class="modal__content">
            <h1>Modal 4545</h1>
            <p>Church-key American Apparel trust fund, cardigan mlkshk small batch Godard mustache pickled bespoke meh seitan. Wes Anderson farm-to-table vegan, kitsch Carles 8-bit gastropub paleo YOLO jean shorts health goth lo-fi.</p>


            <a href="" class="modal__close demo-close">
                <svg class="" viewBox="0 0 24 24"><path d="M19 6.41l-1.41-1.41-5.59 5.59-5.59-5.59-1.41 1.41 5.59 5.59-5.59 5.59 1.41 1.41 5.59-5.59 5.59 5.59 1.41-1.41-5.59-5.59z"/><path d="M0 0h24v24h-24z" fill="none"/></svg>
            </a>
        </div>
    </div>
</div>-->


<div class="demo-btns">
        <div class="buttons">

            <div>
                <ul class="list4a">
                    <?php
                        foreach ($notes as $note)
                        {
                            $id = $note['id'];
                            $date = $note['create_at'];
                            $date = date('d.m.Y H:m', strtotime($date));
                            $title = $note['title'];
                            $isConfirmed = $note['confirm'];
                            $blueClolor = '#d9edf7';
                            $redColor = '#ffebee';
                            $color = $redColor;
                            if($isConfirmed) $color = $blueClolor;
                            echo "<a style='background: $color' href='' data-modal='#modal$id' class='modal__trigger'> 
<li><span>$title</span></li>
<p style='color: black'>$date</p>
</a>";
                        }
                    ?>
<!--                    <a href="" data-modal="#modal" class="modal__trigger"> <li> Тестовое уведомление!!!</li></a>
                    <a href="" data-modal="#modal2" class="modal__trigger"> <li> Тестовое уведомление!!!</li></a>
                    <a href="" data-modal="#modal3" class="modal__trigger"> <li> Тестовое уведомление!!!</li></a>-->
                </ul>
            </div>
        </div>
</div>


<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/notification/modalNote.js"></script>

<script>
    function confirmNote(id) {
        fetch(`index.php?r=notification/note/confirm&id=${id}`)
            .then(() => window.location.reload());
    }
</script>