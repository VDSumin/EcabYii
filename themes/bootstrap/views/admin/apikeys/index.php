<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 08.04.2020
 * Time: 19:11
 */
/* @var $this ApikeysController */
/* @var $model ApiKeys */

$this->pageTitle = 'Ключи для API';

$this->breadcrumbs = array(
    'Ключи для API',
);

?>

<center><h1>Api-Keys</h1></center>

<?= CHtml::link('Добавить запись', array('create'), array('class' => 'btn btn-success')); ?>
<?= ' '.CHtml::link('Импортироватьданные из текста', null, array('class' => 'btn btn-warning modalWin')); ?>
<?= ' '.CHtml::link('Экспортировать данные в текст', array('import'), array('class' => 'btn btn-info modalImport')); ?>
<?= ' '.CHtml::link('Удалить все записи', array('truncate'), array('class' => 'btn btn-danger', 'onclick' => 'return confirm(\'Отчистить всю таблицу?\');')); ?>


<?php $this->widget('application.widgets.grid.BGridView', array(
    'id' => 'apikeys-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'emptyText' => 'Список пуст',
    'beforeAjaxUpdate' => 'js:function() { enableLoading(); } ',
    'afterAjaxUpdate' => 'js:function() { disableLoading(); } ',
    'columns'=>array(
        'id',
        'fnpp',
        'fio',
        'glogin',
        'apikey',
        array(
            'class'=>'BButtonColumn',
            'template' => '{update} {delete}',
        ),
    )
));
echo '<div id="preloader" class="hidden">
    <div class="contpre"><span class="svg_anm"></span><br>Подождите<br><small>Информация обрабатывается</small></div>
</div>';
?>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Импорт через текст из Excel</h4>
            </div>
            <div id="modalContentFromJs" class="modal-body">
                Очень простой экспорт, просто копируешь несколько столбцов из excel по шаблону
                <br />Шаблон: <b>fio</b> \t <b>glogin</b> \t <b>key</b> \n (без пробелов)
                <?php echo CHtml::beginForm(array('import'), 'post', array("id" => "ImportModalWin", 'data-form-confirm' => "modal__confirm")); ?>
                <label for="importText" class="control-label">Вставьте текст сюда текст для импорта</label>
                <textarea id="importText" name="importText" class="form-control" rows="10" placeholder="Вставьте текст сюда"
                          style="resize: vertical; background-color: white;" autofocus></textarea>
                <br />
                <button class="btn btn-primary" onclick="$('#myModal').modal('toggle');enableLoading()">Импорть</button>
                <?php echo CHtml::endForm(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    function enableLoading() {
        var $preloader = $('#preloader');
        $preloader.removeClass('hidden');
    }
    function disableLoading() {
        var $preloader = $('#preloader');
        $preloader.addClass('hidden');
    }
    $(document).on('click', '.modalWin', function(e) {
        e.preventDefault();
        var $select = $(this);
        $('#myModal').modal('toggle');
    });
</script>

<div class="modal fade" id="myModalImport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Экспорт данных</h4>
            </div>
            <div id="modalImportContentFromJs" class="modal-body">

            </div>
        </div>
    </div>
</div>


<script>
    $(document).on('click', '.modalImport', function(e) {
        e.preventDefault();

        var $select = $(this);
        $.ajax({
            'url': "<?= Yii::app()->createAbsoluteUrl('admin/apikeys/export'); ?>",
            'type': 'post',
            'dataType': 'json',
            'data': 'import=true',
            'success' : function(responce) {
                if (responce.success) {
                    $('#modalImportContentFromJs').html(responce.success);
                    $('#myModalImport').modal('toggle');
                } else {
                }
            }
        });
    });

    function CopyData () {
        var table = document.getElementById('importdata');
        var range = document.createRange();
        range.selectNode(table);
        window.getSelection().addRange(range);
        var successful = document.execCommand('copy');
        if(successful) {
            $.notify({
                title: "<center><strong><h4>" + "Текст скопирован" + "</h4></strong></center>",
                message: "<center>" + "Вся таблица скопирована в ваш буфер обмена" + "</center>",
            }, {
                type: 'success',
                delay: 5000,
                placement: {
                    from: 'bottom',
                    align: 'center'
                },
                offset: {
                    y: 80
                }
            });
        }
    }
</script>

