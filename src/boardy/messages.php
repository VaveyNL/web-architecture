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

$stmt = $pdo->query('
    SELECT p.id, p.body, p.created_at,
           u.name AS author_name
    FROM posts p
    JOIN users u ON p.author_id = u.id
    ORDER BY p.created_at DESC
');
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Boardy — Все посты';
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="posts">
    <h2>Все посты</h2>

    <?php if (empty($posts)): ?>
        <p>Постов пока нет.</p>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
            <div class="post">
                <span class="author"><?= htmlspecialchars($p['author_name']) ?></span>
                <span class="date"><?= htmlspecialchars($p['created_at']) ?></span>
                <div class="body"><?= htmlspecialchars($p['body']) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/foot.php'; ?>