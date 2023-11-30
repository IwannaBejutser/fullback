<?php

// Подключение БД
$servername = "127.0.0.1:3306";
$username = "root";
$password = "";
$dbname = "List";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Обработка данных из формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Информация о продавце
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $telephone = isset($_POST["telephone"]) ? $_POST["telephone"] : "";
    $mail = isset($_POST["mail"]) ? $_POST["mail"] : "";
    $city = isset($_POST["city"]) ? $_POST["city"] : "";

    $marka = isset($_POST["marka"]) ? $_POST["marka"] : "";
    $model = isset($_POST["model"]) ? $_POST["model"] : "";

    // Используйте правильные имена для цветов, соответствующие вашим radio-кнопкам
    $color = isset($_POST["red"]) ? $_POST["red"] :
             (isset($_POST["blue"]) ? $_POST["blue"] :
             (isset($_POST["black"]) ? $_POST["black"] :
             (isset($_POST["orange"]) ? $_POST["orange"] :
             (isset($_POST["green"]) ? $_POST["green"] :
             (isset($_POST["brown"]) ? $_POST["brown"] : "")))));
    
    $price = isset($_POST["price"]) ? $_POST["price"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";
    $photos = isset($_FILES["photos"]) ? $_FILES["photos"] : "";

    // Обработка фотографий
    if (!empty($photos)) {
        $target_dir = "uploads/";
        $uploaded_photos = [];
        foreach ($photos["name"] as $key => $photo_name) {
            $target_file = $target_dir . basename($photo_name);
            move_uploaded_file($photos["tmp_name"][$key], $target_file);
            $uploaded_photos[] = $target_file;
        }
    }

    $photos_str = implode(",", $uploaded_photos);

    // Обработка специальных отметок (чекбоксов)
    $document = isset($_POST["document"]) ? 1 : 0;
    $repair = isset($_POST["repair"]) ? 1 : 0;

    // Проверка на уникальность номера телефона и почты
    $check_duplicate_sql = "SELECT * FROM Info WHERE telephone = '$telephone' OR mail = '$mail'";
    $result_duplicate = $conn->query($check_duplicate_sql);

    if ($result_duplicate->num_rows > 0) {
        // Вывод модального окна с ошибкой
        echo '<script>alert("Номер телефона или почта уже используются!");</script>';
    } else {
        // Вставка данных в базу данных с учетом выбранного цвета
        $sql = "INSERT INTO Info (name, price, marka, model, color, mail, telephone, city, photos, document, repair, description) 
        VALUES ('$name', '$price', '$marka', '$model', '$color', '$mail', '$telephone', '$city', '$photos_str', $document, $repair, '$description')";

        if ($conn->query($sql) === TRUE) {
            // Успешно добавлено
            header("Location: list.php");
            exit();
        } else {
            // Ошибка добавления
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Закрытие соединения с базой данных
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <title>Форма</title>
    <script src="./script.js"></script>
</head>
<body>
    <style>
        body {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }   

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: grid;
            grid-gap: 20px;
        }

        label {
            display: block;
        }

        input,
        select {
            padding: 8px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        textarea {
            resize: vertical;
        }

        .btn-reset {
            border: none;
            margin: 0;
            padding: 0;
            width: auto;
            overflow: visible;

            background: transparent;
        }

        .button__submit {
            background-color: #4caf50;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;

            transition: 0.3s ease-in-out;
        }

        .button__submit:hover {
            background-color: #45a049;
        }

        .list__link {
            margin: 10px auto;
            padding: 20px 0;
            display: block;
            text-align: center;
            color: #fff;
            background-color: purple;
            text-decoration: none;
        }

        .form-color {
            display: flex;
            gap: 20px;
        }

        label > input { /* HIDE RADIO */
            visibility: hidden;
            position: absolute;
        }

        label > input + img {
            cursor: pointer;
            border: 2px solid transparent;
        }

        label > input:checked + img {
            width: 40px;
            height: 40px;
            border: 2px solid #f00;   
        }

        ul {
            margin: 0;
        }

        li {
            margin: 10px 0;
        }

        .checkbox__special {
            margin: 0 10px 0 0;
        }

        .question {
            margin: 0;
            color: blue;
        }
    </style>
    <div class="container">
        <h2>Форма продажи автомобиля</h2>
        <form action="/" method="post" enctype="multipart/form-data">
            <!-- Информация о продавце -->
            <label for="name">Имя и фамилия:</label>
            <input type="text" id="name" name="name" placeholder="Иван Иванов" required>

            <label for="telephone">Контактный номер телефона:</label>
            <input type="tel" id="telephone" name="telephone" placeholder="+7 913 012 00 64" pattern="[0-9]{1,}" required>

            <label for="email">Адрес электронной почты:</label>
            <input type="email" id="email" name="mail" placeholder="sdfgsdf@mail.ru" required>

            <label for="city">Город:</label>
            <input list="datalistOptions" type="text" id="city" name="city" placeholder="Новосибирск" required>
            <datalist id="datalistOptions">
                <option value="Москва">Москва</option>
                <option value="Санкт-Петербург">Санкт-Петербург</option>
                <option value="Пенза">Пенза</option>
                <option value="Абакан">Абакан</option>
                <option value="Азов">Азов</option>
                <option value="Александров">Александров</option>
                <option value="Алексин">Алексин</option>
                <option value="Альметьевск">Альметьевск</option>
                <option value="Анапа">Анапа</option>
                <option value="Ангарск">Ангарск</option>
                <option value="Анжеро">Анжеро-Судженск</option>
                <option value="Апатиты">Апатиты</option>
                <option value="Арзамас">Арзамас</option>
                <option value="Армавир">Армавир</option>
                <option value="Арсеньев">Арсеньев</option>
                <option value="Артем">Артем</option>
                <option value="Архангельск">Архангельск</option>
                <option value="Асбест">Асбест</option>
                <option value="Астрахань">Астрахань</option>
                <option value="Ачинск">Ачинск</option>
                <option value="Балаково">Балаково</option>
                <option value="Балахна">Балахна</option>
                <option value="Балашиха">Балашиха</option>
                <option value="Балашов">Балашов</option>
                <option value="Барнаул">Барнаул</option>
                <option value="Батайск">Батайск</option>
                <option value="Белгород">Белгород</option>
                <option value="Белебей">Белебей</option>
                <option value="Белово">Белово</option>
                <option value="Белогорск (Амурская область)">Белогорск (Амурская область)</option>
                <option value="Белорецк">Белорецк</option>
                <option value="Белореченск">Белореченск</option>
                <option value="Бердск">Бердск</option>
                <option value="Березники">Березники</option>
                <option value="Березовский (Свердловская область)">Березовский (Свердловская область)</option>
                <option value="Бийск">Бийск</option>
                <option value="Биробиджан">Биробиджан</option>
                <option value="Благовещенск (Амурская область)">Благовещенск (Амурская область)</option>
                <option value="Бор">Бор</option>
                <option value="Борисоглебск">Борисоглебск</option>
                <option value="Боровичи">Боровичи</option>
                <option value="Братск">Братск</option>
                <option value="Брянск">Брянск</option>
                <option value="Бугульма">Бугульма</option>
                <option value="Буденновск">Буденновск</option>
                <option value="Бузулук">Бузулук</option>
                <option value="Буйнакск">Буйнакск</option>
                <option value="Великие Луки">Великие Луки</option>
                <option value="Великий Новгород">Великий Новгород</option>
                <option value="Верхняя Пышма">Верхняя Пышма</option>
                <option value="Видное">Видное</option>
                <option value="Владивосток">Владивосток</option>
                <option value="Владикавказ">Владикавказ</option>
                <option value="Владимир">Владимир</option>
                <option value="Волгоград">Волгоград</option>
                <option value="Волгодонск">Волгодонск</option>
                <option value="Волжск">Волжск</option>
                <option value="Волжский">Волжский</option>
                <option value="Вологда">Вологда</option>
                <option value="Вольск">Вольск</option>
                <option value="Воркута">Воркута</option>
                <option value="Воронеж">Воронеж</option>
                <option value="Воскресенск">Воскресенск</option>
                <option value="Воткинск">Воткинск</option>
                <option value="Всеволожск">Всеволожск</option>
                <option value="Выборг">Выборг</option>
                <option value="Выкса">Выкса</option>
                <option value="Вязьма">Вязьма</option>
                <option value="Гатчина">Гатчина</option>
                <option value="Геленджик">Геленджик</option>
                <option value="Георгиевск">Георгиевск</option>
                <option value="Глазов">Глазов</option>
                <option value="Горно-Алтайск">Горно-Алтайск</option>
                <option value="Грозный">Грозный</option>
                <option value="Губкин">Губкин</option>
                <option value="Гудермес">Гудермес</option>
                <option value="Гуково">Гуково</option>
                <option value="Гусь-Хрустальный">Гусь-Хрустальный</option>
                <option value="Дербент">Дербент</option>
                <option value="Дзержинск">Дзержинск</option>
                <option value="Димитровград">Димитровград</option>
                <option value="Дмитров">Дмитров</option>
                <option value="Долгопрудный">Долгопрудный</option>
                <option value="Домодедово">Домодедово</option>
                <option value="Донской">Донской</option>
                <option value="Дубна">Дубна</option>
                <option value="Евпатория">Евпатория</option>
                <option value="Егорьевск">Егорьевск</option>
                <option value="Ейск">Ейск</option>
                <option value="Екатеринбург">Екатеринбург</option>
                <option value="Елабуга">Елабуга</option>
                <option value="Елец">Елец</option>
                <option value="Ессентуки">Ессентуки</option>
                <option value="Железногорск (Красноярский край)">Железногорск (Красноярский край)</option>
                <option value="Железногорск (Курская область)">Железногорск (Курская область)</option>
                <option value="Жигулевск">Жигулевск</option>
                <option value="Жуковский">Жуковский</option>
                <option value="Заречный">Заречный</option>
                <option value="Зеленогорск">Зеленогорск</option>
                <option value="Зеленодольск">Зеленодольск</option>
                <option value="Златоуст">Златоуст</option>
                <option value="Иваново">Иваново</option>
                <option value="Ивантеевка">Ивантеевка</option>
                <option value="Ижевск">Ижевск</option>
                <option value="Избербаш">Избербаш</option>
                <option value="Иркутск">Иркутск</option>
                <option value="Искитим">Искитим</option>
                <option value="Ишим">Ишим</option>
                <option value="Ишимбай">Ишимбай</option>
                <option value="Йошкар-Ола">Йошкар-Ола</option>
                <option value="Казань">Казань</option>
                <option value="Калининград">Калининград</option>
                <option value="Калуга">Калуга</option>
                <option value="Каменск-Уральский">Каменск-Уральский</option>
                <option value="Каменск-Шахтинский">Каменск-Шахтинский</option>
                <option value="Камышин">Камышин</option>
                <option value="Канск">Канск</option>
                <option value="Каспийск">Каспийск</option>
                <option value="Кемерово">Кемерово</option>
                <option value="Керчь">Керчь</option>
                <option value="Кинешма">Кинешма</option>
                <option value="Кириши">Кириши</option>
                <option value="Киров (Кировская область)">Киров (Кировская область)</option>
                <option value="Кирово-Чепецк">Кирово-Чепецк</option>
                <option value="Киселевск">Киселевск</option>
                <option value="Кисловодск">Кисловодск</option>
                <option value="Клин">Клин</option>
                <option value="Клинцы">Клинцы</option>
                <option value="Ковров">Ковров</option>
                <option value="Когалым">Когалым</option>
                <option value="Коломна">Коломна</option>
                <option value="Комсомольск-на-Амуре">Комсомольск-на-Амуре</option>
                <option value="Копейск">Копейск</option>
                <option value="Королев">Королев</option>
                <option value="Кострома">Кострома</option>
                <option value="Котлас">Котлас</option>
                <option value="Красногорск">Красногорск</option>
                <option value="Краснодар">Краснодар</option>
                <option value="Краснокаменск">Краснокаменск</option>
                <option value="Краснокамск">Краснокамск</option>
                <option value="Краснотурьинск">Краснотурьинск</option>
                <option value="Красноярск">Красноярск</option>
                <option value="Кропоткин">Кропоткин</option>
                <option value="Крымск">Крымск</option>
                <option value="Кстово">Кстово</option>
                <option value="Кузнецк">Кузнецк</option>
                <option value="Кумертау">Кумертау</option>
                <option value="Кунгур">Кунгур</option>
                <option value="Курган">Курган</option>
                <option value="Курск">Курск</option>
                <option value="Кызыл">Кызыл</option>
                <option value="Лабинск">Лабинск</option>
                <option value="Лениногорск">Лениногорск</option>
                <option value="Ленинск-Кузнецкий">Ленинск-Кузнецкий</option>
                <option value="Лесосибирск">Лесосибирск</option>
                <option value="Липецк">Липецк</option>
                <option value="Лиски">Лиски</option>
                <option value="Лобня">Лобня</option>
                <option value="Лысьва">Лысьва</option>
                <option value="Лыткарино">Лыткарино</option>
                <option value="Люберцы">Люберцы</option>
                <option value="Магадан">Магадан</option>
                <option value="Магнитогорск">Магнитогорск</option>
                <option value="Майкоп">Майкоп</option>
                <option value="Махачкала">Махачкала</option>
                <option value="Междуреченск">Междуреченск</option>
                <option value="Мелеуз">Мелеуз</option>
                <option value="Миасс">Миасс</option>
                <option value="Минеральные Воды">Минеральные Воды</option>
                <option value="Минусинск">Минусинск</option>
                <option value="Михайловка">Михайловка</option>
                <option value="Михайловск (Ставропольский край)">Михайловск (Ставропольский край)</option>
                <option value="Мичуринск">Мичуринск</option>
                <option value="Мурманск">Мурманск</option>
                <option value="Муром">Муром</option>
                <option value="Мытищи">Мытищи</option>
                <option value="Набережные Челны">Набережные Челны</option>
                <option value="Назарово">Назарово</option>
                <option value="Назрань">Назрань</option>
                <option value="Нальчик">Нальчик</option>
                <option value="Наро-Фоминск">Наро-Фоминск</option>
                <option value="Находка">Находка</option>
                <option value="Невинномысск">Невинномысск</option>
                <option value="Нерюнгри">Нерюнгри</option>
                <option value="Нефтекамск">Нефтекамск</option>
                <option value="Нефтеюганск">Нефтеюганск</option>
                <option value="Нижневартовск">Нижневартовск</option>
                <option value="Нижнекамск">Нижнекамск</option>
                <option value="Нижний Новгород">Нижний Новгород</option>
                <option value="Нижний Тагил">Нижний Тагил</option>
                <option value="Новоалтайск">Новоалтайск</option>
                <option value="Новокузнецк">Новокузнецк</option>
                <option value="Новокуйбышевск">Новокуйбышевск</option>
                <option value="Новомосковск">Новомосковск</option>
                <option value="Новороссийск">Новороссийск</option>
                <option value="Новосибирск">Новосибирск</option>
                <option value="Новотроицк">Новотроицк</option>
                <option value="Новоуральск">Новоуральск</option>
                <option value="Новочебоксарск">Новочебоксарск</option>
                <option value="Новочеркасск">Новочеркасск</option>
                <option value="Новошахтинск">Новошахтинск</option>
                <option value="Новый Уренгой">Новый Уренгой</option>
                <option value="Ногинск">Ногинск</option>
                <option value="Норильск">Норильск</option>
                <option value="Ноябрьск">Ноябрьск</option>
                <option value="Нягань">Нягань</option>
                <option value="Обнинск">Обнинск</option>
                <option value="Одинцово">Одинцово</option>
                <option value="Озерск (Челябинская область)">Озерск (Челябинская область)</option>
                <option value="Октябрьский">Октябрьский</option>
                <option value="Омск">Омск</option>
                <option value="Орел">Орел</option>
                <option value="Оренбург">Оренбург</option>
                <option value="Орехово-Зуево">Орехово-Зуево</option>
                <option value="Орск">Орск</option>
                <option value="Павлово">Павлово</option>
                <option value="Павловский Посад">Павловский Посад</option>
                <option value="Первоуральск">Первоуральск</option>
                <option value="Пермь">Пермь</option>
                <option value="Петрозаводск">Петрозаводск</option>
                <option value="Петропавловск-Камчатский">Петропавловск-Камчатский</option>
                <option value="Подольск">Подольск</option>
                <option value="Полевской">Полевской</option>
                <option value="Прокопьевск">Прокопьевск</option>
                <option value="Прохладный">Прохладный</option>
                <option value="Псков">Псков</option>
                <option value="Пушкино">Пушкино</option>
                <option value="Пятигорск">Пятигорск</option>
                <option value="Раменское">Раменское</option>
                <option value="Ревда">Ревда</option>
                <option value="Реутов">Реутов</option>
                <option value="Ржев">Ржев</option>
                <option value="Рославль">Рославль</option>
                <option value="Россошь">Россошь</option>
                <option value="Ростов-на-Дону">Ростов-на-Дону</option>
                <option value="Рубцовск">Рубцовск</option>
                <option value="Рыбинск">Рыбинск</option>
                <option value="Рязань">Рязань</option>
                <option value="Салават">Салават</option>
                <option value="Сальск">Сальск</option>
                <option value="Самара">Самара</option>
                <option value="Саранск">Саранск</option>
                <option value="Сарапул">Сарапул</option>
                <option value="Саратов">Саратов</option>
                <option value="Саров">Саров</option>
                <option value="Свободный">Свободный</option>
                <option value="Севастополь">Севастополь</option>
                <option value="Северодвинск">Северодвинск</option>
                <option value="Северск">Северск</option>
                <option value="Сергиев Посад">Сергиев Посад</option>
                <option value="Серов">Серов</option>
                <option value="Серпухов">Серпухов</option>
                <option value="Сертолово">Сертолово</option>
                <option value="Сибай">Сибай</option>
                <option value="Симферополь">Симферополь</option>
                <option value="Славянск-на-Кубани">Славянск-на-Кубани</option>
                <option value="Смоленск">Смоленск</option>
                <option value="Соликамск">Соликамск</option>
                <option value="Солнечногорск">Солнечногорск</option>
                <option value="Сосновый Бор">Сосновый Бор</option>
                <option value="Сочи">Сочи</option>
                <option value="Ставрополь">Ставрополь</option>
                <option value="Старый Оскол">Старый Оскол</option>
                <option value="Стерлитамак">Стерлитамак</option>
                <option value="Ступино">Ступино</option>
                <option value="Сургут">Сургут</option>
                <option value="Сызрань">Сызрань</option>
                <option value="Сыктывкар">Сыктывкар</option>
                <option value="Таганрог">Таганрог</option>
                <option value="Тамбов">Тамбов</option>
                <option value="Тверь">Тверь</option>
                <option value="Тимашевск">Тимашевск</option>
                <option value="Тимашевск">Тихвин</option>
                <option value="Тихорецк">Тихорецк</option>
                <option value="Тобольск">Тобольск</option>
                <option value="Тольятти">Тольятти</option>
                <option value="Томск">Томск</option>
                <option value="Троицк">Троицк</option>
                <option value="Туапсе">Туапсе</option>
                <option value="Туймазы">Туймазы</option>
                <option value="Тула">Тула</option>
                <option value="Тюмень">Тюмень</option>
                <option value="Узловая">Узловая</option>
                <option value="Улан-Удэ">Улан-Удэ</option>
                <option value="Ульяновск">Ульяновск</option>
                <option value="Урус-Мартан">Урус-Мартан</option>
                <option value="Усолье-Сибирское">Усолье-Сибирское</option>
                <option value="Уссурийск">Уссурийск</option>
                <option value="Усть-Илимск">Усть-Илимск</option>
                <option value="Уфа">Уфа</option>
                <option value="Ухта">Ухта</option>
                <option value="Феодосия">Феодосия</option>
                <option value="Фрязино">Фрязино</option>
                <option value="Хабаровск">Хабаровск</option>
                <option value="Ханты-Мансийск">Ханты-Мансийск</option>
                <option value="Хасавюрт">Хасавюрт</option>
                <option value="Химки">Химки</option>
                <option value="Чайковский">Чайковский</option>
                <option value="Чапаевск">Чапаевск</option>
                <option value="Чебоксары">Чебоксары</option>
                <option value="Челябинск">Челябинск</option>
                <option value="Черемхово">Черемхово</option>
                <option value="Череповец">Череповец</option>
                <option value="Черкесск">Черкесск</option>
                <option value="Черногорск">Черногорск</option>
                <option value="Чехов">Чехов</option>
                <option value="Чистополь">Чистополь</option>
                <option value="Чита">Чита</option>
                <option value="Шадринск">Шадринск</option>
                <option value="Шали">Шали</option>
                <option value="Шахты">Шахты</option>
                <option value="Шуя">Шуя</option>
                <option value="Щекино">Щекино</option>
                <option value="Щелково">Щелково</option>
                <option value="Электросталь">Электросталь</option>
                <option value="Элиста">Элиста</option>
                <option value="Энгельс">Энгельс</option>
                <option value="Южно-Сахалинск">Южно-Сахалинск</option>
                <option value="Юрга">Юрга</option>
                <option value="Якутск">Якутск</option>
                <option value="Ялта">Ялта</option>
                <option value="Ярославль">Ярославль</option>
            </datalist>

            <!-- Характеристики автомобиля -->
            <label for="marka">Марка автомобиля</label>
            <input type="text" id="marka" name="marka" placeholder="Audi" required>

            <label for="model">Модель автомобиля</label>
            <input type="text" id="model" name="model" placeholder="TT" required>

            <label for="color">Выберите цвет</label>
            <div class="form-color">
                <!-- Красный -->
                <label>
                    <input type="radio" id="red" name="red" value="Красный"/>
                    <img src="./svg/red.png" style='width: 25px; height: 25px; border-radius: 50%;'>
                </label>
                <!-- Синий -->
                <label>
                    <input type="radio" id="blue" name="blue" value="Синий"/>
                    <img src="./svg/blue.jpg" style='width: 25px; height: 25px; border-radius: 50%;'>
                </label>
                <!-- Черный -->
                <label>
                    <input type="radio" id="black" name="black" value="Черный"/>
                    <img src="./svg/black.jpeg" style='width: 25px; height: 25px; border-radius: 50%;'>
                </label>
                <!-- Оранжевый -->
                <label>
                    <input type="radio" id="orange" name="orange" value="Оранжевый"/>
                    <img src="./svg/orange.jpg" style='width: 25px; height: 25px; border-radius: 50%;'>
                </label>
                <!-- Зеленый -->
                <label>
                    <input type="radio" id="green" name="green" value="Зеленый"/>
                    <img src="./svg/green.jpg" style='width: 25px; height: 25px; border-radius: 50%;'>
                </label>
                <!-- Коричневый -->
                <label>
                    <input type="radio" id="brown" name="brown" value="Коричневый"/>
                    <img src="./svg/brown.jpg" style='width: 25px; height: 25px; border-radius: 50%;'>
                </label>
            </div>

            <label for="special marks">Специальные отметки</label>
            <div><input class="checkbox__special" type="checkbox" id="document" name="document">Документы с проблемами или отсутствуют</div>
            <div><input class="checkbox__special" type="checkbox" id="repair" name="repair">Требуется ремонт или не на ходу</div>

            <label for="price">Цена</label>
            <input type="text" id="price" name="price" placeholder="₽" required>

            <label for="description">Описание</label>
            <textarea name="description" id="description" cols="30" rows="5" required></textarea>

            <span class="question">О чем писать</span>
            <ul>
                <li>Сколько собственников, давно ли владеете авто</li>
                <li>Состояние кузова, двигателя, подвески</li>
                <li>Был ли бит автомобиль</li>
                <li>Какие колеса, есть ли второй комплект</li>
                <li>Состояние салона</li>
                <li>Наличие тюнинга или доп. опций</li>
                <li>Возможен ли торг</li>
                <li>Где и в какое время можно посмотреть авто</li>
            </ul>

            <!-- <label for="color">Выберите цвет</label>
            <div class="radio">
            <input class="radio-red" type="radio" id="red" name="color" value="Красный">
            <label for="red"></label>

            <input class="radio-blue" type="radio" id="blue" name="color" value="Синий">
            <label for="blue"></label>

            <input class="radio-green" type="radio" id="green" name="color" value="Зеленый">
            <label for="green"></label>
            </div> -->

            <!-- Фотографии -->
            <label for="photos">Фотографии автомобиля</label>
            <input type="file" id="photos" name="photos[]" accept="image/*">

            <input type="checkbox" id="agreement" name="agreement" required>
            <label for="agreement">Я подтверждаю согласие с правилами размещения объявлений.</label>

            <!-- Кнопка отправки формы -->
            <button class="button__submit" type="submit">Опубликовать объявление</button>
        </form>
        <a href="./list.php" class="list__link">Перейти к списку объявлений</a>
    </div>
</body>
</html>
