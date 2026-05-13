<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Boardy') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; font-family: system-ui, -apple-system, sans-serif; }
        .auth-card { max-width: 480px; margin: 60px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .auth-card h1 { font-size: 1.5rem; margin-bottom: 20px; text-align: center; }
        .auth-card .brand { text-align: center; margin-bottom: 20px; font-size: 1.8rem; font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="brand">Boardy</div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>
