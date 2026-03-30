<?php
$file = '/var/www/boardy/data/messages.txt';
$messages = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
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
    <tr><th>Дата</th><th>Имя</th><th>Сообщение</th></tr>
    <?php foreach ($messages as $msg):
      $parts = explode('|', $msg);
      if (count($parts) >= 3): ?>
      <tr>
        <td><?= htmlspecialchars($parts[0]) ?></td>
        <td><?= htmlspecialchars($parts[1]) ?></td>
        <td><?= htmlspecialchars($parts[2]) ?></td>
      </tr>
    <?php endif; endforeach; ?>
  </table>
<?php endif; ?>
<p style="margin-top:20px">
  <a href="/feedback.html">Написать</a> |
  <a href="/">На главную</a></p>
</main>
</body></html>
