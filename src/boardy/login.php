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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, name, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && !empty($user['password_hash'])
        && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: /messages.php');
        exit;
    } else {
        // Одинаковое сообщение и для "email не найден", и для "пароль неверный"
        $error = 'Неверный email или пароль';
    }
}

$page_title = 'Boardy — Вход';
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="form-card">
    <h2>Вход</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login.php">
        <label>Email</label>
        <input type="email" name="email" placeholder="ivan@example.com" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label>Пароль</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn">Войти</button>
    </form>

    <div class="form-footer">
        Нет аккаунта? <a href="/register.php">Регистрация</a>
    </div>
</div>

<?php include __DIR__ . '/partials/foot.php'; ?>