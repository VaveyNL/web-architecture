<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Защита: без логина - редирект
if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = trim($_POST['body'] ?? '');
    if ($body !== '') {
        $stmt = $pdo->prepare(
            'INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)'
        );
        // title пустое - в форме только body (см. макет 5)
        $stmt->execute(['', $body, $_SESSION['user_id']]);
        header('Location: /messages.php');
        exit;
    }
}

$page_title = 'Boardy — Добавить пост';
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="post-form">
    <h2>Добавить пост</h2>
    <form method="POST" action="/submit.php">
        <textarea name="body" placeholder="Напиши что-нибудь..." required></textarea>
        <button type="submit" class="btn">Опубликовать</button>
    </form>
</div>

<?php include __DIR__ . '/partials/foot.php'; ?>