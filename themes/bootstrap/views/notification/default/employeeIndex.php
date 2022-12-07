<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Мои уведомления';
$this->breadcrumbs = [
    'Мои уведомления'
];
/**
 * загрузка виджета без рендеринга.
 */
ob_start();
$this->renderPartial('formTiny', array('value' => ''));
$content = ob_get_contents();
ob_end_clean();
ob_start();
echo $content;
$content = ob_get_contents();
ob_end_clean();
//echo $content;
?>

<style>
    #destinationTable .table td {
        width: 50px;
        height: 1em;
        border: 1px solid #5992ff;
        font-family: sans-serif;
        font-size: .9em;
        transition: background-color .5s;
    }

    .new {
        background-color: #b3ffb3;
    }

    .table_list {
        display: flex;
        flex-direction: column;
    }

    li {
        list-style-type: none;
    }

    ul {
        margin: 0;
        padding: 0;
    }

    .add_btn {
        height: 25px
    }

</style>

<div>
    <button onclick="showAddForm()" class="btn btn-primary mb-2" id="newNoteButton">Новое уведомление</button>
    <div id='firstPlace'></div>
</div>

<?php

$Form = <<< EOT

 <form  id="newNoteForm">
        <div style="margin-top: 1em" class="form-group">
            <label for="titleInput">Заголовок</label>
            <input id="titleInput" autocomplete="off"  type="text" class="form-control" placeholder="Заголовок">
        </div>
        <div class="form-group">
            <label for="textInput">Текст уведомления</label>
            <!--<textarea class="form-control" id="textInput" rows="3"></textarea>-->
            {$content}
        </div>
        <div id='targetGroup'>
        
        <div class="dropdown form-group">
    <h3>Определите группы получателей</h3>
    <!--   <button onClick='addCollumn()' type="button" class="add_btn" data-dismiss="alert" aria-label="Close">-->
    <div>
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            Добавить условие
            <span aria-hidden="true">+</span>
        </button>
        <ul id='ulDropdownMenuCondition' class="dropdown-menu" aria-labelledby="dropdownMenu">
            <li><a onclick="addCollumn2('Факультетам')">Факультет</a></li>
            <li><a onclick="addCollumn2('Группам')">Группа</a></li>
           <!-- <li><a onclick="addCollumn2('Студентам')">Студент</a></li>-->
        </ul>
    </div>
</div>

<div style='display:flex'>
    <table id='destinationTable' class="table">
        <tr></tr>
        <tr></tr>
    </table>

</div>
        </div>
        
        <div id = 'dateGroup' class="form-group">
            <label for="datePicker">Действительно до</label>
            <input style="padding: 0 12px;" class="form-control" id="datePicker" type="date">
        </div>

    </form>
   
        <button onclick="sendFetch()" type="submit" class="btn btn-primary mb-2">Отправить</button>
EOT;

function drawDateWidget($controller)
{
    $currentDate = date('d.m.yy');
    $controller->widget('zii.widgets.jui.CJuiDatePicker', array(
        'name' => 'publishDate',
        // 'flat'=> true,
        //   'model' => New HostelHousingController(),
        'id' => 'kalendar',
        'value' => $currentDate,
        'attribute' => 'created',
        //    'skin' => false,
        // additional javascript options for the date picker plugin
        'options' => array(
            // 'dateFormat' => 'yy-mm-dd',
            'dateFormat' => 'dd.mm.yy',
            'changeMonth' => 'true',
            'changeYear' => 'true',
            'showAnim' => 'slideDown',
            'showButtonPanel' => 'true',
            //  'constrainInput' => 'true'

        ),
        'htmlOptions' => array(
            'style' => 'margin-right: 10px;',
            'class' => 'form-control',
            'name' => 'date',
            'autocomplete' => 'off'
        ),
        // 'flat' => true,
        'language' => 'ru'
    ));

}

?>


<script>
    function formatDate(date) {

        var dd = date.getDate();
        if (dd < 10) dd = '0' + dd;

        var mm = date.getMonth() + 1;
        if (mm < 10) mm = '0' + mm;

        var yy = date.getFullYear();
        if (yy < 10) yy = '0' + yy;

        return yy + '-' + mm + '-' + dd;
    }

    let currentDate = formatDate(new Date());

    let hidden = false;
    let removed = true;

    function showAddForm() {
        if (removed) {
            hidden = false;
            $("#firstPlace").html(`<?=$Form?>`);
            loadTiny();
            document.getElementById('datePicker').value = currentDate;
            removed = false;
        } else if (!hidden) {
            $("#firstPlace").addClass(`hidden`);
            hidden = true;
        } else {
            $("#firstPlace").removeClass(`hidden`);
            hidden = false;
        }
    }
</script>

<style>
    .group-dropdown {
        list-style-type: none;
        width: 100%;
        flex-wrap: wrap;

    }

    .open > .dropdown-menu {
        display: flex;
    }

    .group-dropdown li {
        flex: 0 0 33%;
        border: 1px solid grey;
        display: flex;
        align-items: flex-end;
    }

    .group-dropdown li a {
        width: 100%;
        height: 100%;
        padding: 1em;
    }

    /*    @media (max-width: 768px) {
            .group-dropdown li
            {
                width: 20%;
            }*/

    @media (max-width: 1200px) {
        .group-dropdown li {
            /*   width: 30%;*/
        }

    }
</style>

<script>
    var table = document.querySelector('table.table');
    /// TODO ADD TargetGroups
    let dictionary =
        {
            'faculty': 8,
            'groups': 2,
            'students': 1
        };
    let permissionsObj = JSON.parse('<?php echo json_encode($permissions)?>')
    if (permissionsObj['students'] === false) permissionsObj['students'] = [];
    if (permissionsObj['groups'] === false) permissionsObj['groups'] = [];


    if (!(permissionsObj['groups'] instanceof Array)) permissionsObj['groups'] = Object.values(permissionsObj['groups'])
    if (!(permissionsObj['faculty'] instanceof Array)) permissionsObj['faculty'] = Object.values(permissionsObj['faculty'])

    studentsArrFio = [];
    studentsArrIds = [];
    permissionsObj['students'].forEach((elem) => {
        studentsArrFio.push(elem['fio']);
        studentsArrIds.push(elem['fnpp']);
    });

    let testArr = {
        <?php if($permissions['faculty']): ?>
        'Факультетам': {
            'faculty': permissionsObj['faculty']
        },
        <?php endif; ?>
        <?php if($permissions['groups']): ?>
        'Группам': {
            'groups': permissionsObj['groups']
        },
        <?php endif; ?>
        <?php if($permissions['students']): ?>
        'Студентам': {
            'students': studentsArrFio
        },
        <?php endif; ?>

        /*        'Академистам': {
                    'Academ': [false]
                },
                'Старостам': {
                    'Starost': [false]
                }*/
    }
    let targetArr = {};


    let viewArr = Object.keys(testArr);

    function addCollumn2(columnName, update = '') {
        let head = columnName;
        if (viewArr.indexOf(columnName) !== -1 && update === '') {
            viewArr.splice(viewArr.indexOf(columnName), 1);
            // console.log(viewArr);
        } else if (update === '') throw new Error('Значение колонки не определено')

        if (update === '') {
            updateTable();
        } else {
            updateCell(update);
        }


        function addAnimation(newCell) {
            newCell.className = 'new';
            (function (c) {
                setTimeout(function () {
                    c.classList.remove('new')
                }, 500);
            })(newCell);
        }

        function updateTable() {
            var rows = destinationTable.tBodies[0].rows;
            for (var i = 0, l = 2; i < l; i++) {
                var newCell = rows[i].insertCell(-1);
                console.log(newCell);
                if (i === 0) {
                    newCell.outerHTML = `<th>${head}</th>`
                    addAnimation(newCell);
                } else if (i === 1) {
                    updateCell(newCell)
                    addAnimation(newCell);
                }
            }
        }

        function updateCell(newCell) {
            if (Object.keys(testArr[head])[0]) {
                let name = Object.keys(testArr[head])[0];
                if (name === 'groups' || name === 'students') {
                    let listItems = `
<div id='searchParent${name}' >
    <div  class="col-lg-6">
        <div  class="row input-group">
            <input id="search${name}" type="text" class="form-control" placeholder="Введите название группы" autocomplete="off">
            <span class="input-group-btn">
                   <button onclick="addListItemFromButton('ul${Object.keys(testArr[head])[0]}', 'search${name}', 'ulDropdownMenu${Object.keys(testArr[head])[0]}')" class="btn btn-default" type="button">Добавить</button>
                   </span>
        </div>
${generateDropDownMenu(Object.keys(testArr[head])[0])}
                             <ul id = 'ul${Object.keys(testArr[head])[0]}' >
                             </ul>
    </div>
</div>

`

                    newCell.innerHTML = `${listItems}`;

                } else {
                    newCell.innerHTML =
                        `<div class = 'dropdown form-group'>
                             <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu${Object.keys(testArr[head])[0]}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                              Добавить
                             <span class="caret"></span>
                             </button>
                             ${generateDropDownMenu(Object.keys(testArr[head])[0])}
                             </div>
                             <ul id = 'ul${Object.keys(testArr[head])[0]}' >
                             </ul>`
                }
            }

            function generateDropDownMenu(name) {
                //   debugger;
                let listItems = '';
                let items = testArr[head][name];
                if (name === 'groups' || name === 'students') {
                    for (let i = 0; i < items.length; i++) {
                        listItems += `<li><a onclick="setInputText(document.getElementById('search${name}'), '${items[i]}', 'ulDropdownMenu${name}')" class="dropdown-item">${items[i]}</a></li>`
                    }
                } else {
                    for (let i = 0; i < items.length; i++) {
                        listItems += `<li style="display:flex;flex-grow: 1;"><a onclick="addListItem(document.getElementById('ul${Object.keys(testArr[head])[0]}'),'${items[i]}')" class="dropdown-item">${items[i]}</a></li>`
                    }
                }
                return `<ul id='ulDropdownMenu${name}' class="dropdown-menu group-dropdown" aria-labelledby="dropdownMenu">${listItems}</ul>`;
            }

            if (Object.keys(testArr[head])[0] === 'groups')
                autocomplete(document.getElementById("searchgroups"), testArr[head][Object.keys(testArr[head])[0]], document.getElementById('ulDropdownMenugroups'));
            if (Object.keys(testArr[head])[0] === 'students')
                autocomplete(document.getElementById("searchstudents"), testArr[head][Object.keys(testArr[head])[0]], document.getElementById('ulDropdownMenustudents'));
        }

    }

    let notification_lists = [];

    function deleteListItem(ul, txt) {
        let index = undefined;
        for (let i = 0; i < notification_lists.length; i++) {
            if (notification_lists[i]['notification_type_id'] !== 1 && notification_lists[i]['destination'] === txt) {
                index = i;
                break;
            } else if (notification_lists[i]['notification_type_id'] === 1
                && notification_lists[i]['destination'] === permissionsObj['students'].filter((elem) => {
                    return elem['fio'].toUpperCase() === txt.toUpperCase();
                })[0]['fnpp']) {
                index = i;
                break;
            }
        }
        if (index !== undefined) {
            notification_lists.splice(index, 1);
        }
        //   console.log(notification_lists)
    }

    function addListItem(ul, txt) {
        let unique = false;
        notification_lists.find(list => list.destination === txt) !== undefined ? unique = false : unique = true;

        if (unique) {
            Object.keys(dictionary).map(function (key, index) {
                if ((ul.id).includes(key)) {
                    if (dictionary[key] !== 1) {
                        notification_lists.push({
                            'destination': txt,
                            'notification_type_id': dictionary[key]
                        })
                    } else {
                        notification_lists.push({
                            'destination': permissionsObj['students'].filter((elem) => {
                                return elem['fio'].toUpperCase() === txt.toUpperCase();
                            })[0]['fnpp'],
                            'notification_type_id': dictionary[key]
                        })
                    }
                }
            })

            //   notification_lists.push({destination: txt, notification_type_id: })
            ul.innerHTML += `<li>
<div class="alert" style='display: inline-flex '>
<label>${txt}</label>
        <button onclick="deleteListItem(${ul.id}, '${txt}')" style="padding: 0 0 5px 10px;" type="button" class="close " data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
</div>
</li>`
        }

    }

    function addListItemFromButton(ulId, inputId, ulDropdownId) {
        let ul = document.getElementById(ulId);
        let inp = document.getElementById(inputId);
        let text = inp.value.trim();
        let nodes = document.getElementById(ulDropdownId).childNodes;

        for (let i = 0; i < nodes.length; i++) {
            if (nodes[i].childNodes[0].text.toUpperCase() === text.toUpperCase()) {
                addListItem(ul, nodes[i].childNodes[0].text);
                inp.value = '';
            }
        }

        if (ulId === 'ulgroups') {
            filterStudentBySelectedGroupsAutoMode();
        }
    }

    function setInputText(inp, text, ulId) {
        inp.value = text;

    }


    async function sendFetch() {
        let title = document.getElementById('titleInput').value;
        //  let text = document.getElementById('textInput').value;
        let text = window.parent.tinymce.get('tinyFormInput').save();
        if (text.length === 0 || title.length === 0) {
            throw new Error('Текст и заголовок не могут быть пустыми')
        } else if (notification_lists.length === 0) {
            throw new Error('Не были выбраны группы получателей')
        } else if (document.getElementById('datePicker').value === '' || new Date(document.getElementById('datePicker').value) <= new Date()) {
            throw new Error('Неверно выбрана дата')
        }
        let data = {
            "note": {
                "valid_until": document.getElementById('datePicker').value,
                "owner": <?= $wknpp ?>,
                "title": title,
                "text": text
            },
            "notification_lists": notification_lists,
            "debug": {}
        }

        /*        // console.log(data);
                console.log(JSON.stringify(data))*/

        let response = await fetch('/index-test-main-test.php?r=notification/note/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(data),
        })
            .then(() => window.location.reload());
    }

</script>
<div style="display: flex; flex-direction: column; margin-top: 20px">
    <h2 style="align-self: center">Отправленные уведомления</h2>
    <div class="">
        <?php
        $this->widget('application.widgets.grid.BGridView', array(
            'id' => 'requests-table',
            'beforeAjaxUpdate' => 'js:function() { $("#requests-table").addClass("loading");}',
            'afterAjaxUpdate' => 'js:function() { $("#requests-table").removeClass("loading");
        $(\'[rel="tooltip"]\').tooltip();}',
            'dataProvider' => $dataProvider,
            //  'filter' => $requests,
            'columns' => array(
                /*            'id' => array(
                                  //  'header' => 'Номер',
                                    'name' => 'Номер',
                                    'value' => '$data[\'id\']'),*/
                'title' => array(
                    //  'header' => 'Номер',
                    'name' => 'Заголовок',
                    'value' => '$data[\'title\']',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                ),
                'text' => array(
                    //  'header' => 'Номер',
                    'name' => 'Текст',
                    'value' => '$data[\'text\']',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                ),
                'destination' => array(
                    //  'header' => 'Номер',
                    'name' => 'Получатели',
                    //'value' => '$data[\'destination\']',
                    'value' => function ($data) {
                        $isNpp = true;
                        $nppArr = explode(',', $data['destination']);
                        $fioArr = array();
                        $otherArr = array();
                        foreach ($nppArr as $elem) {
                            if (!is_numeric($elem)) {
                                $isNpp = false;
                                $otherArr[] = $elem;
                            } else {
                                $fioArr[] = Fdata::model()->findByPk((int)$elem)->getFIO();
                            }
                        }
                        echo implode(', ', array_merge($fioArr, $otherArr));
                    },
                    'htmlOptions' => array('style' => 'text-align:center;'),
                ),
                'confirmCount' => array(
                    //  'header' => 'Номер',
                    'name' => 'Прочитано',
                    'value' => '$data[\'confirmCount\']',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                ),
                'create_at' => array(
                    //  'header' => 'Номер',
                    'name' => 'Дата создания',
                    'value' => 'date(\'d.m.Y H:i:s\',strtotime($data[\'create_at\']))',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                ),
                'valid_until' => array(
                    //  'header' => 'Номер',
                    'name' => 'Действительно до',
                    'value' => 'date(\'d.m.Y\',strtotime($data[\'valid_until\']))',
                    'htmlOptions' => array('style' => 'text-align:center;'),
                ),
                array(
                    'value' => function ($data) {
                        //      var_dump(date('Y-m-d H:i:s'));
                        //     var_dump(date($data['valid_until']));
                        if (date($data['valid_until']) > date('Y-m-d H:i:s')) {
                            echo <<< EOT
<a class="delete" title="<span rel=&quot;tooltip&quot; data-toggle=&quot;tooltip&quot; data-placement=&quot;top&quot; title=&quot;Удалить&quot; class=&quot;glyphicon glyphicon-remove&quot;/>"">
EOT;

                            echo "<span onclick='remove({$data['id']})' style='display: inherit;' rel='tooltip' data-toggle='tooltip' data-placement='top' title='' class='glyphicon glyphicon-remove' data-original-title='Удалить'></span>";
                            echo "</a>";
                        } else {
                            echo "<span style='color: brown'>Удалено</span>";
                        }

                    },
                    'htmlOptions' => array('align' => 'center'),
                ),

                array(
                    'class' => 'BButtonColumn',
                    'htmlOptions' => array('style' => 'text-align:center'),
                    'header' => 'Отчёт',
                    'template' => '{print}',
                    'buttons' => array(
                        'print' => array(
                            'label' => '<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Печать" class="glyphicon glyphicon-print"/>',
                            'url' => 'Yii::app()->createUrl("notification/note/print", array("id" => CHtml::value($data, "id")))',
                            //   'visible' => 'uList::visiblePrintBtn(CHtml::value($data, "wtype"), CHtml::value($data, "status"), CHtml::value($data, "dopStatusList"), "main")',
                            'options' => array('target' => '_blank'),
                        ),
                    )
                ),
            ),
        ));
        ?>
    </div>

    <script>
        function remove(id) {
            fetch(`index.php?r=notification/note/remove&id=${id}`)
                .then(() => window.location.reload());
        }
    </script>

    <script>
        let isLoaded = false;

        function loadTiny() {
            jQuery(function ($) {
                $('#tinyFormInput').tinymce({
                    'language': 'ru',
                    'plugins': ['advlist autolink lists link image charmap print preview hr anchor pagebreak', 'searchreplace visualblocks visualchars code fullscreen', 'insertdatetime media nonbreaking save table contextmenu directionality', 'template paste textcolor', 'spellchecker'],
                    'toolbar': 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor',
                    'toolbar_items_size': 'small',
                    'image_advtab': true,
                    'relative_urls': false,
                    'spellchecker_languages': '+Русский=ru',
                    'spellchecker_rpc_url': 'http://speller.yandex.net/services/tinyspell'
                });
                jQuery('#requests-table').yiiGridView({
                    'ajaxUpdate': ['requests-table'],
                    'ajaxVar': 'ajax',
                    'pagerClass': 'pagercustom-pager',
                    'loadingClass': 'grid-view-loading',
                    'filterClass': 'filters',
                    'tableClass': 'items table table-striped _table-ulist',
                    'selectableRows': 1,
                    'enableHistory': false,
                    'updateSelector': '{page}, {sort}',
                    'filterSelector': '{filter}',
                    'beforeAjaxUpdate': function () {
                        $("#requests-table").addClass("loading");
                    },
                    'afterAjaxUpdate': function () {
                        $("#requests-table").removeClass("loading");
                        $('[rel="tooltip"]').tooltip();
                    }
                });
            });
        }

        function autocomplete(inp, arr, targetUl) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var firstFocus = true;

            function reset() {
                try {
                    var nodes = targetUl.childNodes
                    for (let i = 0; i < nodes.length; i++) {
                        nodes[i].setAttribute('style', 'display:none')
                    }
                } catch (e) {
                    console.log(e.message);
                }
            }

            function showAll() {
                try {
                    var nodes = targetUl.childNodes
                    for (let i = 0; i < nodes.length; i++) {
                        nodes[i].setAttribute('style', 'display:flex;flex-grow: 1;')
                    }
                } catch (e) {
                    console.log(e.message);
                }
            }

            reset();

            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function (e) {
                let val = this.value;
                let listItems = [];
                reset();
                targetUl.setAttribute('style', 'display:flex');
                var nodes = targetUl.childNodes;
                listItems = Array.prototype.slice.call(nodes).filter((item) => {
                    return item.childNodes[0].text.substr(0, val.length).toUpperCase() == val.toUpperCase()
                })
                listItems.forEach((elem) => {
                    elem.setAttribute('style', 'display:flex;flex-grow: 1;')
                })
            });

            /*execute a function when someone clicks in the document:*/
            inp.addEventListener("blur", function (e) {
                setTimeout(() => {
                    reset();
                    targetUl.setAttribute('style', 'display:none;');
                }, 200)

            });

            inp.addEventListener("focus", function (e) {
                targetUl.setAttribute('style', 'display:flex;');
                if (firstFocus && inp.value === '') {
                    showAll();
                }
            });
        }

        let studentsArrBackup = [];

        function filterStudentBySelectedGroupsAutoMode() {
            if (studentsArrBackup.length === 0)
                studentsArrBackup = [...testArr['Студентам']['students']];

            let groups = notification_lists.filter((elem) => elem['notification_type_id'] === 2);
            let groupNames = [];
            groups.forEach((elem) => {
                groupNames.push(elem['destination']);
            })
            filterStudentBySelectedGroups(groupNames);

            notification_lists = notification_lists.filter((elem) => {
                return elem['notification_type_id'] !== 1;
            })
        }

        function filterStudentBySelectedGroups(groups) {
            if (studentsArrBackup.length === 0)
                studentsArrBackup = [...testArr['Студентам']['students']];

            testArr['Студентам']['students'] = [];
            groups.forEach((group) => {
                testArr['Студентам']['students'] = testArr['Студентам']['students'].concat(studentsArrBackup.filter((elem) => {
                    return elem.includes(group)
                }))
            })

            if (document.getElementById("searchstudents")) {
                autocomplete(document.getElementById("searchstudents"), testArr['Студентам'][Object.keys(testArr['Студентам'])[0]], document.getElementById('ulDropdownMenustudents'));
                addCollumn2('Студентам', document.getElementById('searchParentstudents'));
            }
        }

    </script>
