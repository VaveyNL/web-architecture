@extends('layouts.app')
@section('title', 'Новый пост')
@section('content')

<h1>Новый пост</h1>

<form method="POST" action="{{ route('posts.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Заголовок</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Текст</label>
        <textarea name="body" class="form-control" rows="6" required>{{ old('body') }}</textarea>
        @error('body')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary">Опубликовать</button>
    <a href="{{ route('posts.index') }}" class="btn btn-link">Отмена</a>
</form>

@endsection
