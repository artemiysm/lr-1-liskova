<?php
require_once 'db.php';
checkAuth();

$user = [
    'id' => $_SESSION['user_id'],
    'phone' => $_SESSION['user_phone']
];

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $date = $_POST['date'] ?? '';
    $type = $_POST['type'] ?? '';
    $payment = $_POST['payment'] ?? '';
    
    if (!$address || !$contact || !$date || !$type || !$payment) {
        $error = "Заполните все поля";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO requests (user_id, address, contact_info, service_date, service_type, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user['id'], $address, $contact, $date, $type, $payment);
        
        if ($stmt->execute()) {
            $success = " Заявка создана!";
            header("Refresh: 2; url=index.php");
        } else {
            $error = " Ошибка при создании заявки";
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
    <title>Новая заявка — Мой Не Сам</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="mobile-container form-page">
    
    <header class="page-header">
        <h1> Новая заявка</h1>
        <a href="index.php" class="btn-back">← Назад</a>
    </header>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST" class="request-form">
        <div class="form-group">
            <label for="address"> Адрес уборки</label>
            <input type="text" id="address" name="address" required 
                   placeholder="г. Москва, ул. Примерная, д. 1"
                   value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="contact"> Контакт для связи</label>
            <input type="tel" id="contact" name="contact" required 
                   value="<?= htmlspecialchars($_POST['contact'] ?? $user['phone']) ?>">
        </div>
        
        <div class="form-group">
            <label for="date"> Дата и время</label>
            <input type="datetime-local" id="date" name="date" required>
        </div>
        
        <div class="form-group">
            <label for="type"> Тип услуги</label>
            <select id="type" name="type" required>
                <option value="">— Выберите услугу —</option>
                <option value="general" <?= (($_POST['type'] ?? '') === 'general') ? 'selected' : '' ?>>Общий клининг</option>
                <option value="deep" <?= (($_POST['type'] ?? '') === 'deep') ? 'selected' : '' ?>>Генеральная уборка</option>
                <option value="post_build" <?= (($_POST['type'] ?? '') === 'post_build') ? 'selected' : '' ?>>Послестроительная уборка</option>
                <option value="dry_clean" <?= (($_POST['type'] ?? '') === 'dry_clean') ? 'selected' : '' ?>>Химчистка ковров и мебели</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="payment"> Способ оплаты</label>
            <select id="payment" name="payment" required>
                <option value="">— Выберите способ —</option>
                <option value="cash" <?= (($_POST['payment'] ?? '') === 'cash') ? 'selected' : '' ?>> Наличными</option>
                <option value="card" <?= (($_POST['payment'] ?? '') === 'card') ? 'selected' : '' ?>> Банковской картой</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary btn-large">Оформить заявку</button>
    </form>
    
</div>
</body>
</html>