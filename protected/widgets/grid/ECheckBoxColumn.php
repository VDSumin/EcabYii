<?php

/**
 * Extended CheckBoxColumn
 */
class ECheckBoxColumn extends CCheckBoxColumn {
    
    /**
	 * Returns the header cell content.
	 * This method will render a checkbox in the header when {@link selectableRows} is greater than 1
	 * or in case {@link selectableRows} is null when {@link CGridView::selectableRows} is greater than 1.
	 * @return string the header cell content.
	 * @since 1.1.16
	 */
    public function getHeaderCellContent() {
        if (trim($this->headerTemplate) === '') {
            return $this->grid->blankDisplay;
        }

        if ($this->selectableRows === null && $this->grid->selectableRows > 1) {
            $item = CHtml::checkBox($this->id . '_all', false, array('class' => 'select-on-check-all'));
        } elseif ($this->selectableRows > 2) {
            $item = CHtml::checkBox($this->id . '_all', false);
        } else {
            $this->selectableRows = 1;
            $item = parent::getHeaderCellContent();
        }

        return strtr($this->headerTemplate, array(
            '{item}' => $item,
        ));
    }

}
