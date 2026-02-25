<?php
// отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();

require_once 'db.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($login && $password) {
        if (!isset($mysqli) || !$mysqli) {
            $error = " Ошибка: подключение к БД не установлено";
        } else {
            $stmt = $mysqli->prepare("SELECT * FROM users WHERE login = ?");
            
            if (!$stmt) {
                $error = " Ошибка подготовки запроса: " . $mysqli->error;
            } else {
                $stmt->bind_param("s", $login);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();
                
                if ($user) {
                    
                    if (password_verify($password, $user['password'])) {
                        // Сохраняем сессию
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_login'] = $user['login'];
                        $_SESSION['user_fio'] = $user['fio'];
                        $_SESSION['user_phone'] = $user['phone'];
                        $_SESSION['user_email'] = $user['email'];
                        
                        // нижний регистр
                        $role = strtolower(trim($user['role'] ?? 'user'));
                        $_SESSION['is_admin'] = ($role === 'admin');
                        
                        //  запись в лог
                        error_log("Login success: role=[$role], is_admin=[" . ($_SESSION['is_admin'] ? 'true' : 'false') . "]");
                        
                        // Определяем страницу для редиректа
                        $redirect = ($role === 'admin') ? 'admin.php' : 'index.php';
                        
                        //  проверяем, существует ли файл
                        if (!file_exists($redirect)) {
                            $error = " Файл $redirect не найден! Проверьте структуру папок.";
                        } else {
                            // Очищаем буфер и делаем редирект
                            ob_end_clean();
                            header("Location: $redirect");
                            exit;
                        }
                    } else {
                        $error = "Неверный пароль";
                    }
                } else {
                    $error = "Пользователь не найден";
                }
            }
        }
    } else {
        $error = "Заполните логин и пароль";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Вход — Мой Не Сам</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="mobile-container auth-page">
    <div class="auth-card">
        <h1 class="page-title"> Вход</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" required 
                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" 
                       placeholder="Введите логин">
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Введите пароль">
            </div>
            <button type="submit" class="btn btn-primary">Войти</button>
        </form>
        
        <p class="auth-link">
            Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
        </p>
    </div>
</div>
</body>
</html>