<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3> Диагностика</h3>";

// 1. Проверка db.php
echo "<p>1. Подключение db.php... ";
if (@include 'db.php') {
    echo "OK</p>";
} else {
    echo " ОШИБКА</p>";
    exit;
}

// 2. Проверка $mysqli
echo "<p>2. Переменная \$mysqli... ";
if (isset($mysqli) && $mysqli) {
    echo " OK (connect_id: " . $mysqli->connect_errno . ")</p>";
} else {
    echo " НЕ ОПРЕДЕЛЕНА</p>";
}

// 3. Проверка сессии
echo "<p>3. Сессия... ";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo " Активна</p>";
} else {
    echo " Не активна</p>";
}

// 4. Проверка файлов
echo "<p>4. Файлы:</p><ul>";
foreach (['index.php', 'admin.php', 'register.php'] as $file) {
    echo "<li>$file: " . (file_exists($file) ? '' : '') . "</li>";
}
echo "</ul>";

// 5. Проверка пользователей в БД
echo "<p>5. Пользователи в БД:</p>";
if ($mysqli) {
    $res = $mysqli->query("SELECT id, login, role FROM users LIMIT 5");
    if ($res && $res->num_rows > 0) {
        echo "<ul>";
        while ($row = $res->fetch_assoc()) {
            echo "<li>ID={$row['id']}, login=[{$row['login']}], role=[<b>{$row['role']}</b>]</li>";
        }
        echo "</ul>";
    } else {
        echo "<p> Таблица users пуста или не найдена</p>";
    }
}
?>