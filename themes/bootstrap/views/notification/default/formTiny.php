<?php
$this->widget('ext.tinymce.TinyMce', array(
    'name' => 'tinyFormInput',
    'value' => $value,
    'attribute' => 'annonce',
    //'compressorRoute' => 'tinyMce/compressor',
    'spellcheckerUrl' => 'http://speller.yandex.net/services/tinyspell',
    'htmlOptions' => array(
        'rows' => 6,
        'cols' => 60,
    ),
));