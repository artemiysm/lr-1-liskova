<?php
require_once 'db.php';
checkAuth(); // Проверка авторизации

$user = [
    'id' => $_SESSION['user_id'],
    'fio' => $_SESSION['user_fio'],
    'phone' => $_SESSION['user_phone'],
    'email' => $_SESSION['user_email']
];

// Получение истории заявок
$stmt = $mysqli->prepare("SELECT * FROM requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Мои заявки — Мой Не Сам</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="mobile-container dashboard-page">
    
    <!-- Шапка -->
    <header class="page-header">
        <h1> <?= htmlspecialchars($user['fio']) ?></h1>
        <a href="logout.php" class="btn-logout">Выйти</a>
    </header>
    
    <!-- новоя заявка -->
    <a href="create_request.php" class="btn-new-request">+ Новая заявка</a>
    
    <!-- Список заявок -->
    <h2 class="section-title">История заявок</h2>
    
    <?php if (empty($requests)): ?>
        <p class="empty-state">У вас пока нет заявок</p>
    <?php else: ?>
        <?php 
        $statusConfig = [
            'new' => ['label' => 'Новая', 'class' => 'status-new'],
            'in_progress' => ['label' => 'В работе', 'class' => 'status-progress'],
            'done' => ['label' => 'Выполнено', 'class' => 'status-done'],
            'cancelled' => ['label' => 'Отменена', 'class' => 'status-cancelled']
        ];
        ?>
        <?php foreach ($requests as $req): ?>
        <div class="request-card">
            <div class="request-header">
                <span class="request-date"><?= date('d.m.Y', strtotime($req['service_date'])) ?></span>
                <span class="status-badge <?= $statusConfig[$req['status']]['class'] ?>">
                    <?= $statusConfig[$req['status']]['label'] ?>
                </span>
            </div>
            <div class="request-body">
                <p><strong>Услуга:</strong> <?= htmlspecialchars($req['service_type']) ?></p>
                <p><strong>Адрес:</strong> <?= htmlspecialchars($req['address']) ?></p>
                <p><strong>Оплата:</strong> <?= $req['payment_method'] === 'cash' ? 'Наличные' : ' Карта' ?></p>
                <?php if ($req['status'] === 'cancelled' && $req['cancel_reason']): ?>
                    <p class="cancel-reason">❌ Причина: <?= htmlspecialchars($req['cancel_reason']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
</div>
</body>
</html>