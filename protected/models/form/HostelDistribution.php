<?php

class HostelDistribution
{
    public static function getSector($faculty)
    {
        //TODO: Перенести в отдельную таблицу с адресами и списком военных контрактников
        switch ($faculty):
            case 'Институт дизайна, экономики и сервиса':
            case 'Факультет экономики, сервиса и управления':
            case 'Художественно-технологический факультет':
            case 'Колледж ОмГТУ':
            case 'Колледж':
            case 'ИДЭС.ФЭСУ':
            case 'ИДЭС.ФХТ':
            case 'ИДЭС.ФК':
            case 'ХТФ':
            case 'ФХТ':
            case 'ФЭСиУ':
                return 9;
            case 'Военно-технического образования':
                return 1;
            case 'Факультет гуманитарного образования':
            case 'Нефтехимический институт':
            case 'ИНХ':
            case 'ФГ':
            case 'НХИ':
            case 'ФГО':
                return 5;
            case 'Факультет информационных технологий и компьютерных систем':
            case 'Машиностроительный институт':
            case 'Радиотехнический факультет':
            case 'ФРТ':
            case 'ИМС':
            case 'ФИ':
            case 'ФИТиКС':
            case 'РТФ':
            case 'МСИ':
                return 6;
            case 'Энергетический институт':
            case 'Факультет транспорта, нефти и газа':
            case 'ФТ':
            case 'ФТНГ':
            case 'ИЭ':
            case 'ЭНИ':
                return 7;
            default:
                return 0;
        endswitch;
    }

    public static function getHostelAddress($sektor)
    {
        switch ($sektor):
            case 1:
                return "ул. Долгирева, д. 81";
            case 3:
            case 9:
                return "ул. Красногвардейская, д. 9А";
            case 5:
                return "ул. 2-я Поселковая, д. 3";
            case 6:
                return "ул. 2-я Поселковая, д. 3/1";
            case 7:
                return "ул. Поселковая, д. 6";
            default:
                return "______________________";
        endswitch;
    }

    public static function getMinObr($fnpp)
    {
        $sql = "select distinct npp
        from fdata f
        where npp = 1338841
           or concat_ws(' ', fam, nam, otc) like 'Алтышев Артём Вадимович'
           or npp = 1338887
           or concat_ws(' ', fam, nam, otc) like 'Ведькал Андрей Александрович'
           or npp = 1338947
           or concat_ws(' ', fam, nam, otc) like 'Симонов Андрей Александрович'
           or npp = 1339023
           or concat_ws(' ', fam, nam, otc) like 'Хромов Данил Евгеньевич'
           or npp = 1339029
           or concat_ws(' ', fam, nam, otc) like 'Журавлев Дмитрий Алексеевич'
           or npp = 1339031
           or concat_ws(' ', fam, nam, otc) like 'Жильцов Артем Геннадьевич'
           or npp = 1339041
           or concat_ws(' ', fam, nam, otc) like 'Кобылянский Никита Владиславович'
           or npp = 1339063
           or concat_ws(' ', fam, nam, otc) like 'Бородихин Никита Алексеевич'
           or npp = 1339096
           or concat_ws(' ', fam, nam, otc) like 'Чулков Иван Сергеевич'
           or npp = 1339105
           or concat_ws(' ', fam, nam, otc) like 'Карташов Валентин Вадимович'
           or npp = 1339135
           or concat_ws(' ', fam, nam, otc) like 'Черномаз Виктор Иванович'
           or npp = 1339181
           or concat_ws(' ', fam, nam, otc) like 'Носов Артём Андреевич'
           or npp = 1339198
           or concat_ws(' ', fam, nam, otc) like 'Каратаев Семён Андреевич'
           or npp = 1339212
           or concat_ws(' ', fam, nam, otc) like 'Кунакбаев Дамир Асхарович'
           or npp = 1339225
           or concat_ws(' ', fam, nam, otc) like 'Позднякова Анна Александровна'
           or npp = 1339231
           or concat_ws(' ', fam, nam, otc) like 'Черемнов Михаил Алексеевич'
           or npp = 1339276
           or concat_ws(' ', fam, nam, otc) like 'Можухов Александр Владимирович'
           or npp = 1339284
           or concat_ws(' ', fam, nam, otc) like 'Демещикова Татьяна Анатольевна'
           or npp = 1339299
           or concat_ws(' ', fam, nam, otc) like 'Бережнов Даниил Александрович'
           or npp = 1339301
           or concat_ws(' ', fam, nam, otc) like 'Ходоров Иван Игоревич'
           or npp = 1339309
           or concat_ws(' ', fam, nam, otc) like 'Беспалов Александр Алексеевич'
           or npp = 1339323
           or concat_ws(' ', fam, nam, otc) like 'Муратова Камила Сериковна'
           or npp = 1339329
           or concat_ws(' ', fam, nam, otc) like 'Плотников Дмитрий Александрович'
           or npp = 1339332
           or concat_ws(' ', fam, nam, otc) like 'Лаптев Олег Александрович'
           or npp = 1339350
           or concat_ws(' ', fam, nam, otc) like 'Ценев Богдан Дмитриевич'
           or npp = 1339354
           or concat_ws(' ', fam, nam, otc) like 'Фирсова Арина Артёмовна'
           or npp = 1339379
           or concat_ws(' ', fam, nam, otc) like 'Горб Степан Александрович'
           or npp = 1339393
           or concat_ws(' ', fam, nam, otc) like 'Минасян Георгий Ваганович'
           or npp = 1339408
           or concat_ws(' ', fam, nam, otc) like 'Юркевич Тимофей Васильевич'
           or npp = 1339412
           or concat_ws(' ', fam, nam, otc) like 'Волохин Валерий Алексеевич'
           or npp = 1339422
           or concat_ws(' ', fam, nam, otc) like 'Сницарь Ольга Борисовна'
           or npp = 1339429
           or concat_ws(' ', fam, nam, otc) like 'Юркевич Владимир Васильевич'
           or npp = 1339455
           or concat_ws(' ', fam, nam, otc) like 'Александров Иван Сергеевич'
           or npp = 1339457
           or concat_ws(' ', fam, nam, otc) like 'Гусев Андрей Валерьевич'
           or npp = 1339491
           or concat_ws(' ', fam, nam, otc) like 'Афаунов Богдан Андреевич'
           or npp = 1339505
           or concat_ws(' ', fam, nam, otc) like 'Пащенко Владислав Константинович'
           or npp = 1339511
           or concat_ws(' ', fam, nam, otc) like 'Чижик Алексей Дмитриевич'
           or npp = 1339525
           or concat_ws(' ', fam, nam, otc) like 'Дроздов Илья Егорович'
           or npp = 1339602
           or concat_ws(' ', fam, nam, otc) like 'Савирская Полина Валерьевна'
           or npp = 1339645
           or concat_ws(' ', fam, nam, otc) like 'Алимов Павел Максимович'
           or npp = 1339649
           or concat_ws(' ', fam, nam, otc) like 'Самсонов Максим Константинович'
           or npp = 1339654
           or concat_ws(' ', fam, nam, otc) like 'Кихтенко Иван Алексеевич'
           or npp = 1339689
           or concat_ws(' ', fam, nam, otc) like 'Андреев Александр Дмитриевич'
           or npp = 1339702
           or concat_ws(' ', fam, nam, otc) like 'Губер Владимир Алексеевич'
           or npp = 1339731
           or concat_ws(' ', fam, nam, otc) like 'Петлин Никита Сергеевич'
           or npp = 1339743
           or concat_ws(' ', fam, nam, otc) like 'Матлак Кирилл Алексеевич'
           or npp = 1339755
           or concat_ws(' ', fam, nam, otc) like 'Садовничий Евгений Витальевич'
           or npp = 1339758
           or concat_ws(' ', fam, nam, otc) like 'Чадин Кирилл Александрович'
           or npp = 1339763
           or concat_ws(' ', fam, nam, otc) like 'Отраднова Дарья Ивановна'
           or npp = 1339770
           or concat_ws(' ', fam, nam, otc) like 'Пономаренко Дмитрий Андреевич'
           or npp = 1339786
           or concat_ws(' ', fam, nam, otc) like 'Шеков Артём Олегович'
           or npp = 1339797
           or concat_ws(' ', fam, nam, otc) like 'Матков Иван Игоревич'
           or npp = 1339830
           or concat_ws(' ', fam, nam, otc) like 'Сухоруков Кирилл Максимович'
           or npp = 1339839
           or concat_ws(' ', fam, nam, otc) like 'Савченко Павел Андреевич'
           or npp = 1339850
           or concat_ws(' ', fam, nam, otc) like 'Ваулина Елена Эдуардовна'
           or npp = 1339859
           or concat_ws(' ', fam, nam, otc) like 'Лазарев Иван Сергеевич'
           or npp = 1339865
           or concat_ws(' ', fam, nam, otc) like 'Кожахметова Салия Ильясовна'
           or npp = 1339921
           or concat_ws(' ', fam, nam, otc) like 'Куянов Иван Александрович'
           or npp = 1339937
           or concat_ws(' ', fam, nam, otc) like 'Кривицкий Антон Андреевич'
           or npp = 1340036
           or concat_ws(' ', fam, nam, otc) like 'Марков Михаил Юрьевич'
           or npp = 1340038
           or concat_ws(' ', fam, nam, otc) like 'Девятьяров Роман Вячеславович'
           or npp = 1340047
           or concat_ws(' ', fam, nam, otc) like 'Семеш Михаил Юрьевич'
           or npp = 1340057
           or concat_ws(' ', fam, nam, otc) like 'Бондарев Дмитрий Петрович'
           or npp = 1340157
           or concat_ws(' ', fam, nam, otc) like 'Османов Данила Рафикович'
           or npp = 1340233
           or concat_ws(' ', fam, nam, otc) like 'Штремель Артём Алексеевич'
           or npp = 1340272
           or concat_ws(' ', fam, nam, otc) like 'Триколе Максим Владиславович'
           or npp = 1340293
           or concat_ws(' ', fam, nam, otc) like 'Баракин Максим Евгеньевич'
           or npp = 1340312
           or concat_ws(' ', fam, nam, otc) like 'Самоздран Никита Валерьевич'
           or npp = 1340350
           or concat_ws(' ', fam, nam, otc) like 'Шипякова Александра Олеговна'
           or npp = 1340375
           or concat_ws(' ', fam, nam, otc) like 'Панченко Владислав Владимирович'
           or npp = 1340385
           or concat_ws(' ', fam, nam, otc) like 'Домрачев Степан Евгеньевич'
           or npp = 1340387
           or concat_ws(' ', fam, nam, otc) like 'Столяров Егор Сергеевич'
           or npp = 1340408
           or concat_ws(' ', fam, nam, otc) like 'Исфаилов Егор Игоревич'
           or npp = 1340425
           or concat_ws(' ', fam, nam, otc) like 'Горючкин Кирилл Александрович'
           or npp = 1340439
           or concat_ws(' ', fam, nam, otc) like 'Краснов Иван Сергеевич'
           or npp = 1340525
           or concat_ws(' ', fam, nam, otc) like 'Афанасенко Валентин Александрович'
           or npp = 1340616
           or concat_ws(' ', fam, nam, otc) like 'Сатвалдинов Арман Аманжолович'
           or npp = 1340632
           or concat_ws(' ', fam, nam, otc) like 'Мовчан Иван Николаевич'
           or npp = 1340640
           or concat_ws(' ', fam, nam, otc) like 'Каргаполова Виктория Андреевна'
           or npp = 1340687
           or concat_ws(' ', fam, nam, otc) like 'Захаров Пётр Леонидович'
           or npp = 1340688
           or concat_ws(' ', fam, nam, otc) like 'Братцева Мария Владимировна'
           or npp = 1340690
           or concat_ws(' ', fam, nam, otc) like 'Кущенко Денис Евгеньевич'
           or npp = 1340737
           or concat_ws(' ', fam, nam, otc) like 'Клюев Александр Александрович'
           or npp = 1340744
           or concat_ws(' ', fam, nam, otc) like 'ТИГАЛО ДЕНИС АЛЕКСЕЕВИЧ'
           or npp = 1340752
           or concat_ws(' ', fam, nam, otc) like 'Мартын Денис Анатольевич'
           or npp = 1340785
           or concat_ws(' ', fam, nam, otc) like 'Иванов Кирилл Вячеславович'
           or npp = 1340801
           or concat_ws(' ', fam, nam, otc) like 'Долгих Алексей Юрьевич'
           or npp = 1340824
           or concat_ws(' ', fam, nam, otc) like 'Копин Дастан Бергович'
           or npp = 1340944
           or concat_ws(' ', fam, nam, otc) like 'Старков Владислав Алексеевич'
           or npp = 1340990
           or concat_ws(' ', fam, nam, otc) like 'Таранов Николай Андреевич'
           or npp = 1341007
           or concat_ws(' ', fam, nam, otc) like 'Сосунов Алексей Дмитриевич'
           or npp = 1341057
           or concat_ws(' ', fam, nam, otc) like 'Литвиненко Глеб Геннадьевич'
           or npp = 1341066
           or concat_ws(' ', fam, nam, otc) like 'Килин Андрей Сергеевич'
           or npp = 1341079
           or concat_ws(' ', fam, nam, otc) like 'Латышев Дмитрий Валерьевич'
           or npp = 1341081
           or concat_ws(' ', fam, nam, otc) like 'Булка Владимир Евгеньевич'
           or npp = 1341187
           or concat_ws(' ', fam, nam, otc) like 'Рычков Андрей Борисович'
           or npp = 1341188
           or concat_ws(' ', fam, nam, otc) like 'Толокнов Константин Юрьевич'
           or npp = 1341363
           or concat_ws(' ', fam, nam, otc) like 'Бритван Дмитрий Олегович'
           or npp = 1341366
           or concat_ws(' ', fam, nam, otc) like 'Кабанов Николай Максимович'
           or npp = 1341417
           or concat_ws(' ', fam, nam, otc) like 'Савельев Алексей Александрович'
           or npp = 1341441
           or concat_ws(' ', fam, nam, otc) like 'Винокуров Сергей Алексеевич'
           or npp = 1341447
           or concat_ws(' ', fam, nam, otc) like 'Чередниченко Владислав Евгеньевич'
           or npp = 1341453
           or concat_ws(' ', fam, nam, otc) like 'Плотников Андрей Владимирович'
           or npp = 1341464
           or concat_ws(' ', fam, nam, otc) like 'Яловец Алексей Андреевич'
           or npp = 1341489
           or concat_ws(' ', fam, nam, otc) like 'Витютнев Вадим Алексеевич'
           or npp = 1341538
           or concat_ws(' ', fam, nam, otc) like 'Мамаев Фёдор Дмитриевич'
           or npp = 1341594
           or concat_ws(' ', fam, nam, otc) like 'Языкова Александра Валерьевна'
           or npp = 1341602
           or concat_ws(' ', fam, nam, otc) like 'Немирович-Данченко Глеб Максимович'";

        $fnpp_ar = Yii::app()->db2->createCommand($sql)->queryAll();
        if (in_array($fnpp, $fnpp_ar)) {
            return false;
        } else {
            return true;
        }
    }
}