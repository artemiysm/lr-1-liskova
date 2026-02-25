<?php
$mysqli = new mysqli('127.127.126.26', 'root', '^ruS7]u56^£L', 'cleaning_portal');
$mysqli->set_charset('utf8mb4');
if ($mysqli->connect_error) {
    die(" Ошибка подключения к БД: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

// Сессия (если ещё не начата)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Проверка админа
function checkAdmin() {
    checkAuth();
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        header("Location: index.php");
        exit;
    }
}
?>