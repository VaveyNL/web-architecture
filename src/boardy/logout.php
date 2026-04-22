<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$_SESSION = [];
session_destroy();

setcookie('PHPSESSID', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

header('Location: /messages.php');
exit;