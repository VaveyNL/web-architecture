<?php
/**
 * Выдаёт JWT по куке PHPSESSID.
 * React загружается на http://localhost/comments.html → fetch('/api/me.php')
 * → браузер прикрепляет PHPSESSID (тот же origin) → PHP читает $_SESSION
 * → подписывает токен общим секретом → отдаёт JSON {token: "eyJ..."}
 */

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => false,        // localhost без HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$secret_key = 'boardy-jwt-secret-2026-change-me-in-production';

function b64url(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generate_jwt(int $user_id, string $user_name, string $secret): string {
    $header = b64url(json_encode([
        'alg' => 'HS256',
        'typ' => 'JWT',
    ]));

    $payload = b64url(json_encode([
        'user_id' => $user_id,
        'name'    => $user_name,
        'iat'     => time(),
        'exp'     => time() + 3600,   // 1 час
    ]));

    $signature = b64url(hash_hmac(
        'sha256',
        "$header.$payload",
        $secret,
        true
    ));

    return "$header.$payload.$signature";
}

$jwt = generate_jwt(
    (int)$_SESSION['user_id'],
    $_SESSION['user_name'] ?? '',
    $secret_key
);

echo json_encode(['token' => $jwt]);