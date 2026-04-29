<?php

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
require_once __DIR__ . '/db.php';

$client_id     = 'Ov23liB9gZrEISo48n3d';
$client_secret = 'bd332e2925dce8c9984a863faf48abf8d755e5b8';

// --- 1. Проверка state (CSRF) ---
if (empty($_GET['state']) || empty($_SESSION['oauth_state'])
    || $_GET['state'] !== $_SESSION['oauth_state']) {
    http_response_code(400);
    die('Invalid state - возможная CSRF-атака');
}
unset($_SESSION['oauth_state']);

if (empty($_GET['code'])) {
    http_response_code(400);
    die('Code missing');
}

// --- 2. code → access_token ---
$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
        'code'          => $_GET['code'],
    ]),
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
$response = json_decode(curl_exec($ch), true);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    http_response_code(500);
    die('curl error: ' . htmlspecialchars($err));
}
if (empty($response['access_token'])) {
    http_response_code(500);
    die('No access_token in response: ' . htmlspecialchars(json_encode($response)));
}
$access_token = $response['access_token'];

// --- 3. access_token → профиль ---
$ch = curl_init('https://api.github.com/user');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER     => [
        "Authorization: Bearer $access_token",
        'User-Agent: Boardy',           // GitHub API без User-Agent отказывает
        'Accept: application/json',
    ],
    CURLOPT_RETURNTRANSFER => true,
]);
$profile = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($profile['id'])) {
    http_response_code(500);
    die('No profile id in response: ' . htmlspecialchars(json_encode($profile)));
}

// --- 4. Найти или создать пользователя ---
$github_id = (string)$profile['id'];
$github_login = $profile['login'] ?? ('github_user_' . $github_id);
$github_email = $profile['email'];   // может быть null если приватный

$stmt = $pdo->prepare('SELECT id, name FROM users WHERE github_id = ?');
$stmt->execute([$github_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Если email пустой - генерируем фейковый, чтобы не нарушить UNIQUE.
    $email_for_db = $github_email ?: ($github_login . '@github.local');

    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, github_id) VALUES (?, ?, ?)'
    );
    $stmt->execute([$github_login, $email_for_db, $github_id]);
    $user = [
        'id'   => (int)$pdo->lastInsertId(),
        'name' => $github_login,
    ];
}

// --- 5. Сессия + редирект ---
$_SESSION['user_id']   = (int)$user['id'];
$_SESSION['user_name'] = $user['name'];

header('Location: /messages.php');
exit;