<!DOCTYPE html>
<html lang="ru">
<head><meta charset="utf-8"><title>Авторизация</title></head>
<body style="font-family:sans-serif;max-width:480px;margin:80px auto;">
    <h2>Boardy SPA запрашивает доступ</h2>
    <p>Приложение <b>{{ $client->name }}</b> хочет получить доступ к вашему аккаунту.</p>

    <form method="post" action="/oauth/authorize">
        @csrf
        <input type="hidden" name="state" value="{{ $request->state }}">
        <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
        <input type="hidden" name="auth_token" value="{{ $authToken }}">
        <button type="submit" style="padding:10px 20px;">Authorize</button>
    </form>

    <form method="post" action="/oauth/authorize">
        @csrf
        @method('delete')
        <input type="hidden" name="state" value="{{ $request->state }}">
        <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
        <input type="hidden" name="auth_token" value="{{ $authToken }}">
        <button type="submit" style="padding:10px 20px;margin-top:10px;">Отмена</button>
    </form>
</body>
</html>
