<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Boardy')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('posts.index') }}">Boardy</a>
        <div>
            @auth
                <span class="text-light me-3">Привет, {{ Auth::user()->name }}!</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Выйти</button>
                </form>
            @else
                <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Вход</a>
                <a class="btn btn-light btn-sm" href="{{ route('register') }}">Регистрация</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</main>
</body>
</html>
