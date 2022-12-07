<?php

Yii::import('zii.widgets.CDetailView');

class BDetailView extends CDetailView {
    public function init() {
        $this->cssFile = false;
        if (empty($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = 'table table-striped _table-ulist';
        } else {
            $this->htmlOptions['class'] .= ' table table-striped _table-ulist';
        }
        parent::init();
    }
}

