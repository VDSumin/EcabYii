<?php
/* @var $info array */
$this->pageTitle = 'Выбор карточки сверки';
?>

      <div class="row row-offcanvas row-offcanvas-right">
        <div class="col-xs-12 col-sm-9">
          <p class="pull-right visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Навигация</button>
          </p>
            <?php
            foreach ($infos as $info) {
                echo '<div class="well bs-example-bg-classes">
                <h1>' . $info['F$FIO'] . '
                </h1><p>
                    <b>Номер книжки:</b> ' . $info['F$SFLD#1#'] . '<br>
                    <b>Специальность:</b> ' . $info['F$NAME'] . '<br>
                    <b>Группа:</b> ' . $info['F$SDEPCODE'] . '<br>
                    <b>Форма обучения:</b> ' . uCurriculum::formEdLabels($info['F$WFORMED'], '-') . '<br>
                </p>';
                echo CHtml::link('Перейти в сверку данных', ['personalcard/index', 'id' => $info['ID']], ['class' => 'btn btn-primary btn-md']);
                echo '</div>';
            }
            ?>

		</div>
	  </div>
