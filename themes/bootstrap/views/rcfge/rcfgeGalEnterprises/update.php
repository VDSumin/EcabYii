<?php
/* @var $this RcfgeGalEnterprisesController */
?>

    <h1>���������� ����������� - "<?= $modelKat->shortname ?>"</h1>

<?php
if($addressError1 or $addressError2 or $addressError3){echo '<div class="alert alert-danger alert-dismissable fade in" style="text-align: center;">���������� �� ���� ����������, ��� ���������� ������ � ����������� ���������� ��������� ��� ������������ ����

<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>';}
?>

<?php $this->renderPartial('_form', array('modelAdr'=>$modelAdr, 'modelKat'=>$modelKat, 'addressError1' => $addressError1, 'addressError2' => $addressError2, 'addressError3' => $addressError3)); ?>