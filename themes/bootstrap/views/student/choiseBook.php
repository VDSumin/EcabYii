<?php
/* @var $info array */
$this->pageTitle = 'Выбор зачетной книжки';
?>

      <div class="row row-offcanvas row-offcanvas-right">
        <div class="col-xs-12 col-sm-9">
            <?php
            foreach ($infos as $info) {
                echo '<div class="well bs-example-bg-classes">
                <h1>' . $info['fio'] . '
                </h1><p>
                    <b>Номер книжки:</b> ' . $info['recordBook'] . '<br>
                    <b>Специальность:</b> ' . $info['spec'] . '<br>
                    <b>Группа:</b> ' . $info['studGroup'] . '<br>
                    <b>Форма обучения:</b> ' . $info['formEdu'] . '<br>
                    <b>Статус:</b> ' . (($info['status']=='Обучающиеся')?'Обучающийся':'Выпущен') . '<br>
                </p>';
                echo '<div style="text-align: right;">';
                echo CHtml::link('Перейти в зачетную книжку', ['student/index', 'id' => hex2bin(CMisc::_bn($info['nrec']) )], ['class' => 'btn btn-primary btn-md']);
                echo '</div>';
                echo '</div>';
            }
            ?>

		</div>
	  </div>
