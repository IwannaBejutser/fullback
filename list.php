<?php
// Подключение к базе данных
$servername = "127.0.0.1:3306";
$username = "root";
$password = "";
$dbname = "List";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Фильтр по марке
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="list.css"> -->
    <title>Список автомобилей</title>
</head>
<body>
    <style>
        body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p {
        margin: 0;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    button {
        padding: 0;
        border: none;
        font: inherit;
        color: inherit;
        background-color: transparent;
        cursor: pointer;
    }

    .list__title {
        padding: 25px 0;
        text-align: center;
        color: #fff;
        background-color: #000;
    }

    .container {
        margin: 0 auto;
        padding: 0 15px;
        max-width: 600px;
    }

    .article__card {
        margin: 25px 0;
        padding: 20px;
        display: flex;
        gap: 50px;
        border-radius: 20px;
        background-color: #fff;
        transition: 0.3s ease-in-out;
    }

    .article__card:hover {
        scale: 1.07;
    }

    .form__link {
        margin: 20px auto;
        padding: 20px 0;
        display: block;
        text-align: center;
        color: #fff;
        background-color: purple;
    }
    </style>
    <h2 class="list__title">Список автомобилей</h2>

    <div class="container">

    <form method="get" action="list.php">
        <label for="filter">Фильтр по марке:</label>
        <select name="filter" id="filter">
        <option value="">Все марки</option>
        <?php
        // Получите уникальные марки из базы данных
        $sql_brands = "SELECT DISTINCT marka FROM Info";
        $result_brands = $conn->query($sql_brands);

        // Выводите каждую марку как опцию для фильтра
        while ($row_brand = $result_brands->fetch_assoc()) {
            $brand = $row_brand["marka"];
            $selected = ($brand == $filter) ? 'selected' : ''; // Добавляем selected, если марка соответствует фильтру
            echo "<option value=\"$brand\" $selected>$brand</option>";
        }
        ?>
        </select>
        <button type="submit">Применить фильтр</button>
    </form>

    <a href="./index.php" class="form__link">
        Разместить объявление
    </a>
</div>

<?php
// Запрос к базе данных
$sql = "SELECT * FROM Info";
if (!empty($filter)) {
    $sql .= " WHERE marka = '$filter'";
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Вывод данных
    echo "<div class='container'>";
    while ($row = $result->fetch_assoc()) {
        echo "<a href='product.php?id=" . $row["id"] . "'>";
        echo "<article class='article__card'>";
        // Вывод фотографий
        $photos = explode(",", $row["photos"]);
        // echo "<p><strong>Фотографии:</strong></p>";
        foreach ($photos as $photo) {
            echo "<img src='$photo' alt='Car Photo' style='width: 300px; max-height: 200px;'>";
        }

        echo "<div class='article__text'>";
        // echo "<p><strong>Имя:</strong> " . $row["name"] . "</p>";
        // echo "<p><strong>Телефон:</strong> " . $row["telephone"] . "</p>";
        // echo "<p><strong>Email:</strong> " . $row["mail"] . "</p>";
        echo "<p><strong>Марка:</strong> " . $row["marka"] . "</p>";
        echo "<p><strong>Модель:</strong> " . $row["model"] . "</p>";
        echo "<p><strong>Город:</strong> " . $row["city"] . "</p>";

        // echo "<p><strong>Цвет:</strong> " . $row["color"] . "</p>";
        echo "<p><strong>Цена:</strong> " . number_format($row["price"], 0, '', ' ') . " ₽</p>";

        echo "</div>";
        
        echo "</article>";
        echo "</a>";
    }
    echo "</div>";
} else {
    echo "0 результатов";
}

// Закрытие соединения с базой данных
$conn->close();
?>

</body>
</html>