<?php
$servername = "127.0.0.1:3306";
$username = "root";
$password = "";
$dbname = "List";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $sql = "SELECT * FROM Info WHERE id = '$id'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo $row["marka"] . " " . $row["model"];
            }
        } else {
            echo "Машина не найдена";
        }
        ?>
    </title>
</head>
<body>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        button {
            padding: 0;
            border: none;
            font: inherit;
            color: inherit;
            background-color: transparent;
            cursor: pointer;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .article__card {
            position: relative;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
            display: flex;
            gap: 50px;
        }

        .article__card img {
            max-width: 100%;
            height: auto;
        }

        .article__card p {
            margin: 10px 0;
        }

        .article__card strong {
            font-weight: bold;
            margin-right: 5px;
        }

        .article__card-back {
            position: absolute;
            top: 0;
            right: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .article__link {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .link__message {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .link__number {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #fff;
            background-color: #36B555;
            border-radius: 10px;
        }

        .link__number-text {
        }

        .link__number-telephone {
        }

        .list-reset {
            margin: 10px 0;
            padding: 0;
            text-indent: 0;
            list-style-type: none;
        }

        .article__description {
            max-width: fit-content;
            word-wrap: break-word;
        }
    </style>

    <?php
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sql = "SELECT * FROM Info WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='container'>";
            echo "<article class='article__card'>";
            echo "<a href='./list.php' class='article__card-back'><img src='./svg/back.svg'></a>";

            $photos = explode(",", $row["photos"]);
            foreach ($photos as $photo) {
                echo "<img src='$photo' alt='Car Photo' style='max-width: 600px; max-height: 600px;'>";
            }

            echo "<div class='article__content'>";
            echo "<p><strong>Имя:</strong> " . $row["name"] . "</p>";
            echo "<p><strong>Город:</strong> " . $row["city"] . "</p>";
            echo "<p><strong>Марка:</strong> " . $row["marka"] . "</p>";
            echo "<p><strong>Модель:</strong> " . $row["model"] . "</p>";
            echo "<p><strong>Цвет:</strong> " . $row["color"] . "</p>";

            if ($row["document"] == 1 || $row["repair"] == 1) {
                echo "<p><strong>Специальные отметки:</strong></p>";
                echo "<ul class='list-reset'>";
                if ($row["document"] == 1) {
                    echo "<li>Документы с проблемами или отсутствуют</li>";
                }
                if ($row["repair"] == 1) {
                    echo "<li>Требуется ремонт или не на ходу</li>";
                }
                echo "</ul>";
            }

            echo "<p><strong>Цена:</strong> " . number_format($row["price"], 0, '', ' ') . " ₽</p>";

            echo "<div class='article__link'>";
            echo "<button class='link__message'><img src='./svg/message.svg'><span class='link__message-text'>Написать сообщение</span></button>";
            echo "<a href='tel:" . $row["telephone"] . "' class='link__number'><span class='link__number-text'>Позвонить продавцу</span>
            <span class='link__number-telephone'>" . $row["telephone"] . "</span></a>";
            echo "</div>";
            echo "</div>";

            echo "</article>";

            echo "<div class='article__description'>";
            echo "<strong>Описание:</strong>";
            echo "<p class='article__description-text'>" . $row["description"] . "</p>";
            echo "</div>";

            echo "</div>";
        }
    } else {
        echo "Машина не найдена";
    }

    $conn->close();
    ?>
</body>
</html>
