<?php
/* @var $this DefaultController */

$this->pageTitle = Yii::app()->name . ' - Мои уведомления';
$this->breadcrumbs = [
    'Мои уведомления'
];
?>


<div>
    <div class="alert alert-danger alert-dismissible show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="alert alert-success alert-dismissible show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
    <button onclick="showAddForm()" class="btn btn-primary mb-2" id="newNoteButton">Новое уведомление</button>

    <div id='firstPlace'></div>
    <!--    <form id="newNoteForm">
            <div class="form-group">
                <label for="titleInput">Заголовок</label>
                <input type="email" class="form-control" id="titleInput" placeholder="Деканат. Необходимо предоставить ...">
            </div>
            <div class="form-group">
                <label for="textInput">Текст уведомления</label>
                <textarea class="form-control" id="textInput" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="selectForTypeRecipient">Веберете тип получателя</label>
                <select class="form-control" id="selectForTypeRecipient">
                    <option></option>
                    <option id="opt_fio">Студенту по ФИО</option>
                    <option id="opt_group">Группе по названию</option>
                    <option id="opt_faculty">Факультету</option>
                </select>
            </div>
            <div id='result'>
            </div>


            <button type="submit" class="btn btn-primary mb-2">Отправить</button>
        </form>-->
</div>

<?php
$Form = <<< EOT
 <form id="newNoteForm">
        <div class="form-group">
            <label for="titleInput">Заголовок</label>
            <input type="email" class="form-control" id="titleInput" placeholder="Деканат. Необходимо предоставить ...">
        </div>
        <div class="form-group">
            <label for="textInput">Текст уведомления</label>
            <textarea class="form-control" id="textInput" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="selectForTypeRecipient">Веберете тип получателя</label>
            <select class="form-control" id="selectForTypeRecipient">
                <option></option>
                <option id="opt_fio">Студенту по ФИО</option>
                <option id="opt_group">Группе по названию</option>
                <option id="opt_faculty">Факультету</option>
            </select>
        </div>
        <div id='result'>
        </div>


        <button type="submit" class="btn btn-primary mb-2">Отправить</button>
    </form>
EOT;

$studentSelector = <<<EOT
<div class="form-group">
            <label for="FioInput">Введите ФИО студента</label>
            <input type="" class="form-control" id="FioInput" placeholder="Иванов Иван Иванович">
</div>
EOT;

$groupSelector = <<< EOT
        <div class="form-group">
            <label for="multiplyGroupSelector">Выберите группы</label>
            <select multiple class="form-control" id="multiplyGroupSelector">
            <option>ИВТм-203</option>
            <option>ИВТм-202</option>
            <option>ИВТм-201</option>
            </select>
        </div>
EOT;

$facultySelector = <<< EOT
        <div class="form-group">
            <label for="multiplyGroupSelector">Выберите группы</label>
            <select multiple class="form-control" id="multiplyGroupSelector">
            <option>ФЭОиМ</option>
            <option>ФИТиКС</option>
            <option>РТФ</option>
            </select>
        </div>
EOT;


?>
<script>
    let hidden = true;

    function showAddForm() {
        if (hidden) {
            hidden = false;
            $("#firstPlace").html(`<?=$Form?>`);
            applyScript();
        } else {
            console.log('123123123');
            hidden = true;
            $("#firstPlace").html(``);
        }
    }
</script>
<script>
    function applyScript() {
        $('#selectForTypeRecipient').change(function () {
            var chosen_radio = $('#selectForTypeRecipient option:selected')[0].id;
            console.log(chosen_radio);
            if (chosen_radio == "opt_fio") {
                $("#result").html(`<?=$studentSelector?>`);
            } else if (chosen_radio == "opt_group") {
                $("#result").html(`<?=$groupSelector?>`);
            } else if (chosen_radio == "opt_faculty") {
                $("#result").html(`<?=$facultySelector?>`);
            } else if (chosen_radio == "") {
                $("#result").html("<input type='hidden' name='type' value='Отдам заказ / Ищу исполнителя'>");
            }
        });
    };
</script>
<script>
    async function fetchTestData() {

        let data = {
            "note": {
                "valid_until": "2020-10-15",
                "owner": 36484,
                "title": "Привет 345345345!!!!",
                "text": "Hello World!!!!!"
            },
            "notification_lists":
                [
                    {
                        "destination": "ИВТм-203",
                        "notification_type_id": 2
                    },
                    {
                        "destination": "ИВТм-201",
                        "notification_type_id": 2
                    },
                    {
                        "destination": "ФЭОиМ",
                        "notification_type_id": 8
                    }
                ],
            "debug": {}
        }

        console.log(data);
        console.log(JSON.stringify(data))

        let response = await fetch('/index-test-main-test.php?r=notification/note/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(data),
        });

/*        if (response.ok) { // если HTTP-статус в диапазоне 200-299
            // получаем тело ответа (см. про этот метод ниже)
            let json = await response.json();
        } else {
            alert("Ошибка HTTP: " + response.status);
        }*/
    }

    async function fetchNote(note, notificationLists) {
        let data = {
            'note': note,
            'notification_lists': notificationLists
        }
        let json = JSON.stringify(data);

        let response = await fetch('/index-test-main-test.php?r=notification/note/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(data),
        });

        if (response.ok) { // если HTTP-статус в диапазоне 200-299
            // получаем тело ответа (см. про этот метод ниже)
            let json = await response.json();
        } else {
            alert("Ошибка HTTP: " + response.status);
        }
    }

</script>
