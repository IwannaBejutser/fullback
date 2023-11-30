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

// Фильтры
$filter_marka = isset($_GET['filter_marka']) ? $_GET['filter_marka'] : '';
$filter_city = isset($_GET['filter_city']) ? $_GET['filter_city'] : '';
$filter_color = isset($_GET['filter_color']) ? $_GET['filter_color'] : '';
$filter_document = isset($_GET['filter_document']) ? $_GET['filter_document'] : '';
$filter_repair = isset($_GET['filter_repair']) ? $_GET['filter_repair'] : '';
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

    .form__filter {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    }

    .form__filter label {
        margin: 10px 0;
    }

    .filter {
        width: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    #filter_marka,
    #filter_city,
    #filter_color,
    .filter__button {
        padding: 8px;
        border-radius: 4px;
    }

    .filter__button {
        max-width: 200px;
        margin: 10px 0;
        background-color: black;
        color: #fff;
        transition: background-color 0.3s ease-in-out;
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

    .checkboxes {
        display: flex;
    }
    </style>
    <h2 class="list__title">Список автомобилей</h2>

    <div class="container">
        <div class="form__filter">
            <form class="filter" method="get" action="list.php">
                <label for="filter_marka">Марка:</label>
                <select name="filter_marka" id="filter_marka">
                    <option value="">Все марки</option>
                    <?php
                    $sql_brands = "SELECT DISTINCT marka FROM Info";
                    $result_brands = $conn->query($sql_brands);
                    while ($row_brand = $result_brands->fetch_assoc()) {
                        $brand = $row_brand["marka"];
                        $selected = ($brand == $filter_marka) ? 'selected' : '';
                        echo "<option value=\"$brand\" $selected>$brand</option>";
                    }
                    ?>
                </select>

                <label for="filter_city">Город:</label>
                <select name="filter_city" id="filter_city">
                    <option value="">Все города</option>
                    <?php
                    $sql_cities = "SELECT DISTINCT city FROM Info";
                    $result_cities = $conn->query($sql_cities);
                    while ($row_city = $result_cities->fetch_assoc()) {
                        $city = $row_city["city"];
                        $selected = ($city == $filter_city) ? 'selected' : '';
                        echo "<option value=\"$city\" $selected>$city</option>";
                    }
                    ?>
                </select>

                <label for="filter_color">Цвет:</label>
                <select name="filter_color" id="filter_color">
                    <option value="">Все цвета</option>
                    <?php
                    $sql_colors = "SELECT DISTINCT color FROM Info";
                    $result_colors = $conn->query($sql_colors);
                    while ($row_color = $result_colors->fetch_assoc()) {
                        $color = $row_color["color"];
                        $selected = ($color == $filter_color) ? 'selected' : '';
                        echo "<option value=\"$color\" $selected>$color</option>";
                    }
                    ?>
                </select>

                <div class="checkboxes">
                    <label>
                        <input type="checkbox" name="filter_document" value="1" <?php echo isset($_GET['filter_document']) && $_GET['filter_document'] == '1' ? 'checked' : ''; ?>>
                        Документы с проблемами или отсутствуют
                    </label>

                    <label>
                        <input type="checkbox" name="filter_repair" value="1" <?php echo isset($_GET['filter_repair']) && $_GET['filter_repair'] == '1' ? 'checked' : ''; ?>>
                        Требуется ремонт или не на ходу
                    </label>
                </div>

                <button class="filter__button" type="submit">Применить фильтры</button>
            </form>
        </div>

        <a href="./index.php" class="form__link">
            Разместить объявление
        </a>

        <?php
        // SQL-запрос
        $sql = "SELECT * FROM Info WHERE 1";

        // Добавление условий фильтрации
        if (!empty($filter_marka)) {
            $sql .= " AND marka = '$filter_marka'";
        }

        if (!empty($filter_city)) {
            $sql .= " AND city = '$filter_city'";
        }

        if (!empty($filter_color)) {
            $sql .= " AND color = '$filter_color'";
        }

        if ($filter_document !== '') {
            $sql .= " AND document = '$filter_document'";
        }

        if ($filter_repair !== '') {
            $sql .= " AND repair = '$filter_repair'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Вывод данных
            echo "<div class='container'>";
            while ($row = $result->fetch_assoc()) {
                echo "<a href='product.php?id=" . $row["id"] . "'>";
                echo "<article class='article__card'>";
                $photos = explode(",", $row["photos"]);
                foreach ($photos as $photo) {
                    echo "<img src='$photo' alt='Car Photo' style='width: 300px; max-height: 200px;'>";
                }

                echo "<div class='article__text'>";
                echo "<p><strong>Марка:</strong> " . $row["marka"] . "</p>";
                echo "<p><strong>Модель:</strong> " . $row["model"] . "</p>";
                echo "<p><strong>Город:</strong> " . $row["city"] . "</p>";
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

    </div>

</body>
</html>