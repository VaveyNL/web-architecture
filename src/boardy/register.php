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
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Заполните все поля';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не короче 6 символов';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email уже занят';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([$name, $email, $hash]);

            $_SESSION['user_id']   = (int)$pdo->lastInsertId();
            $_SESSION['user_name'] = $name;

            header('Location: /messages.php');
            exit;
        }
    }
}

$page_title = 'Boardy — Регистрация';
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="form-card">
    <h2>Регистрация</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/register.php">
        <label>Имя</label>
        <input type="text" name="name" placeholder="Иванов Иван" required
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

        <label>Email</label>
        <input type="email" name="email" placeholder="ivan@example.com" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label>Пароль</label>
        <input type="password" name="password" placeholder="Минимум 6 символов" required>

        <button type="submit" class="btn">Зарегистрироваться</button>
    </form>

    <div class="form-footer">
        Уже есть аккаунт? <a href="/login.php">Войти</a>
    </div>
</div>

<?php include __DIR__ . '/partials/foot.php'; ?>