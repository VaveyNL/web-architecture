<?php
$dsn  = 'mysql:host=localhost;dbname=boardy;charset=utf8mb4';
$user = 'boardy';
$pass = 'qwerty12345';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Ошибка подключения: ' . $e->getMessage());
}
