<?php
$name    = $_POST['name'] ?? 'Аноним';
$message = $_POST['message'] ?? '';
$line    = date('Y-m-d H:i:s') . '|' . $name . '|' . $message . PHP_EOL;
file_put_contents('/var/www/boardy/data/messages.txt', $line, FILE_APPEND);
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="utf-8"><title>Boardy</title>
<link rel="stylesheet" href="/css/style.css"></head>
<body>
<header><h1><a href="/">Boardy</a></h1></header>
<main>
<h2>Спасибо, <?= htmlspecialchars($name) ?>!</h2>
<p>Ваше сообщение получено.</p>
<p><a href="/">На главную</a> |
   <a href="/messages.php">Все сообщения</a></p>
</main>
</body></html>
