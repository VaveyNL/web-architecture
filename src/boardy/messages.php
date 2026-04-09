<?php
require_once 'db.php';

$stmt = $pdo->query(
    'SELECT posts.body, users.name, posts.created_at
     FROM posts
     JOIN users ON posts.author_id = users.id
     ORDER BY posts.created_at DESC'
);
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="utf-8"><title>Boardy — Сообщения</title>
<link rel="stylesheet" href="/css/style.css"></head>
<body>
<header><h1><a href="/">Boardy</a></h1></header>
<main>
<h2>Все сообщения</h2>
<?php if (empty($messages)): ?>
  <p>Сообщений пока нет.</p>
<?php else: ?>
  <table border="1" cellpadding="8"
         style="border-collapse:collapse;width:100%">
    <tr><th>Дата</th><th>Автор</th><th>Сообщение</th></tr>
    <?php foreach ($messages as $msg): ?>
    <tr>
      <td><?= htmlspecialchars($msg['created_at']) ?></td>
      <td><?= htmlspecialchars($msg['name']) ?></td>
      <td><?= htmlspecialchars($msg['body']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
<p style="margin-top:20px">
  <a href="/feedback.html">Написать</a> |
  <a href="/">На главную</a></p>
</main>
</body></html>
