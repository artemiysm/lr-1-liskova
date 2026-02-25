<?php
require_once 'db.php';
checkAdmin(); // Только для админов

// Обработка смены статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['req_id'])) {
    $req_id = (int)$_POST['req_id'];
    $status = $_POST['status'] ?? '';
    $reason = trim($_POST['cancel_reason'] ?? '');
    
    if (in_array($status, ['new', 'in_progress', 'done', 'cancelled'])) {
        $stmt = $mysqli->prepare("UPDATE requests SET status = ?, cancel_reason = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $reason, $req_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Получение всех заявок
$result = $mysqli->query("SELECT r.*, u.fio, u.phone FROM requests r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Админ-панель — Мой Не Сам</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="mobile-container admin-page">
    
    <header class="page-header admin-header">
        <h1> Админ-панель</h1>
        <a href="logout.php" class="btn-logout">Выйти</a>
    </header>
    
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Клиент</th>
                    <th>Услуга</th>
                    <th>Статус</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($req = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $req['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($req['fio']) ?></strong><br>
                        <small><?= htmlspecialchars($req['phone']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($req['service_type']) ?></td>
                    <td>
                        <span class="status-badge status-<?= $req['status'] ?>">
                            <?= $req['status'] ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" class="admin-form">
                            <input type="hidden" name="req_id" value="<?= $req['id'] ?>">
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <option value="new" <?= $req['status'] === 'new' ? 'selected' : '' ?>>Новая</option>
                                <option value="in_progress" <?= $req['status'] === 'in_progress' ? 'selected' : '' ?>>В работе</option>
                                <option value="done" <?= $req['status'] === 'done' ? 'selected' : '' ?>>Выполнено</option>
                                <option value="cancelled" <?= $req['status'] === 'cancelled' ? 'selected' : '' ?>>Отмена</option>
                            </select>
                            <?php if ($req['status'] === 'cancelled'): ?>
                                <input type="text" name="cancel_reason" 
                                       class="cancel-input" 
                                       placeholder="Причина отмены"
                                       value="<?= htmlspecialchars($req['cancel_reason'] ?? '') ?>">
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
</div>
</body>
</html>