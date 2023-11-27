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
    grid-gap: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input,
select,
textarea {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
    margin-bottom: 10px;
}

button {
    background-color: #4caf50;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
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
    </style>
<div class="container">
    <h2>Форма продажи автомобиля</h2>
    <form action="/" method="post" enctype="multipart/form-data">
        <!-- Информация о продавце -->
        <label for="name">Имя и фамилия:</label>
        <input type="text" id="name" name="name" required>

        <label for="telephone">Контактный номер телефона:</label>
        <input type="tel" id="telephone" name="telephone" required>

        <label for="email">Адрес электронной почты:</label>
        <input type="email" id="email" name="mail" required>

        <label for="city">Город/регион:</label>
        <input type="text" id="city" name="city" required>

        <!-- Характеристики автомобиля -->
        <label for="marka">Марка автомобиля</label>
        <input type="text" id="marka" name="marka" required>

        <label for="model">Модель автомобиля</label>
        <input type="text" id="model" name="model" required>

        <label for="price">Цена</label>
        <input type="text" id="price" name="price" required>

        <label for="city">Цвет</label>
        <input type="text" id="color" name="color" required>

        <!-- Фотографии -->
        <label for="photos">Фотографии автомобиля</label>
        <input type="file" id="photos" name="photos[]" accept="image/*" multiple>

        <input type="checkbox" id="agreement" name="agreement" required>
        <label for="agreement">Я подтверждаю согласие с правилами размещения объявлений.</label>

        <!-- Кнопка отправки формы -->
        <button type="submit">Опубликовать объявление</button>
    </form>
    <a href="./list.php" class="list__link">Перейти к списку объявлений</a>
</div>

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
    $color = isset($_POST["color"]) ? $_POST["color"] : "";
    $price = isset($_POST["price"]) ? $_POST["price"] : "";
    $photos = isset($_FILES["photos"]) ? $_FILES["photos"] : "";

    // Обработка фотографий
    if (!empty($photos)) {
        $target_dir = "uploads/";
        $uploaded_photos = [];foreach ($photos["name"] as $key => $photo_name) {
            $target_file = $target_dir . basename($photo_name);
            move_uploaded_file($photos["tmp_name"][$key], $target_file);
            $uploaded_photos[] = $target_file;
        }}

    $photos_str = implode(",", $uploaded_photos);

    // Проверка на уникальность номера телефона и почты
    $check_duplicate_sql = "SELECT * FROM Info WHERE telephone = '$telephone' OR mail = '$mail'";
    $result_duplicate = $conn->query($check_duplicate_sql);

    if ($result_duplicate->num_rows > 0) {
        // Вывод модального окна с ошибкой
        echo '<script>alert("Номер телефона или почта уже используются!");</script>';
    } else {

    $sql = "INSERT INTO Info (name, price, marka, model, color, mail, telephone, city, photos) VALUES ('$name', '$price', '$marka', '$model', '$color', '$mail', '$telephone', '$city', '$photos_str')";

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

// // Перенаправление пользователя после обработки
// header("Location: list.php");
// exit();
?>

</body>
</html>