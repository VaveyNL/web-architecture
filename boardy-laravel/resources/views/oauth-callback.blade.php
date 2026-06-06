<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Boardy — вход…</title>
</head>
<body>
    <p style="font-family: sans-serif; padding: 40px;">Завершаем вход…</p>
    <script type="module">
        import { handleCallback } from '/js/auth.js';
        handleCallback().catch(e => {
            document.body.innerHTML =
                '<p style="font-family:sans-serif;padding:40px;color:red;">Ошибка входа: '
                + e.message + '</p>';
        });
    </script>
</body>
</html>

