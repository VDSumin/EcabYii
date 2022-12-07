<?php

class BButtonColumn extends CButtonColumn {

    public $viewButtonLabel = '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Просмотр" class="glyphicon glyphicon-search"/>';
    public $viewButtonImageUrl = false;

    public $updateButtonLabel = '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Редактировать" class="glyphicon glyphicon-pencil"/>';
    public $updateButtonImageUrl = false;

    public $deleteButtonLabel = '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Удалить" class="glyphicon glyphicon-remove"/>';
    public $deleteButtonImageUrl = false;

    /**
     * Renders a link button.
     * @param string $id the ID of the button
     * @param array $button the button configuration which may contain 'label', 'url', 'imageUrl' and 'options' elements.
     * See {@link buttons} for more details.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data object associated with the row
     */
    protected function renderButton($id,$button,$row,$data)
    {
        if (isset($button['visible']) && !$this->evaluateExpression($button['visible'],array('row'=>$row,'data'=>$data)))
            return;
        $label=isset($button['label']) ? $button['label'] : $id;
        $url=isset($button['url']) ? $this->evaluateExpression($button['url'],array('data'=>$data,'row'=>$row)) : '#';
        $options=isset($button['options']) ? $button['options'] : array();
        if(!isset($options['title']))
            $options['title']=$label;
        if (isset($button['imageUrl']) && is_string($button['imageUrl'])) {
            echo CHtml::link(CHtml::image($button['imageUrl'], $label), $url, $options);
        } elseif (!empty($button['glyphicon'])) {
            echo CHtml::link(CHtml::tag('span', [
                'class' => 'glyphicon glyphicon-' . $button['glyphicon'],
                'rel' => 'tooltip',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => $label,
            ]), $url, $options);
        } else {
            echo CHtml::link($label, $url, $options);
        }
    }
}