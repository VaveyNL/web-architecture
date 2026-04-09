<?php
require_once 'db.php';

$name    = $_POST['name'] ?? '';
$message = $_POST['message'] ?? '';

if ($name && $message) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE name = ?');
    $stmt->execute([$name]);
    $user = $stmt->fetch();

    if (!$user) {
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $name.'@boardy.local', 'temp']);
        $user_id = $pdo->lastInsertId();
    } else {
        $user_id = $user['id'];
    }

    $stmt = $pdo->prepare(
        'INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)'
    );
    $stmt->execute(['Сообщение', $message, $user_id]);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="utf-8"><title>Boardy</title>
<link rel="stylesheet" href="/css/style.css"></head>
<body>
<header><h1><a href="/">Boardy</a></h1></header>
<main>
<h2>Спасибо, <?= htmlspecialchars($name) ?>!</h2>
<p><a href="/">На главную</a> |
   <a href="/messages.php">Все сообщения</a></p>
</main>
</body></html>
