<?php
require_once 'db.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $fio = trim($_POST['fio'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Валидация
    if (!$login || !$password || !$fio || !$phone || !$email) {
        $error = "Все поля обязательны для заполнения";
    } elseif (strlen($login) < 3) {
        $error = "Логин должен содержать минимум 3 символа";
    } elseif (strlen($password) < 6) {
        $error = "Пароль должен содержать минимум 6 символов";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Некорректный формат email";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $mysqli->prepare("INSERT INTO users (login, password, fio, phone, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $login, $passwordHash, $fio, $phone, $email);
        
        if ($stmt->execute()) {
            $success = " Регистрация успешна! Теперь войдите.";
        } else {
            $errno = $stmt->errno;
            $error = ($errno == 1062) ? "❌ Такой логин уже занят" : "❌ Ошибка регистрации";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Регистрация — Мой Не Сам</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="mobile-container auth-page">
    <div class="auth-card">
        <h1 class="page-title"> Регистрация</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="fio">ФИО</label>
                <input type="text" id="fio" name="fio" required 
                       value="<?= htmlspecialchars($_POST['fio'] ?? '') ?>" placeholder="Иванов Иван Иванович">
            </div>
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone" required 
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="+7 (999) 123-45-67">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="example@mail.ru">
            </div>
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" required 
                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" placeholder="Придумайте логин">
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required placeholder="Минимум 6 символов">
            </div>
            <button type="submit" class="btn btn-success">Зарегистрироваться</button>
        </form>
        
        <p class="auth-link">
            Уже есть аккаунт? <a href="login.php">Войти</a>
        </p>
    </div>
</div>
</body>
</html>