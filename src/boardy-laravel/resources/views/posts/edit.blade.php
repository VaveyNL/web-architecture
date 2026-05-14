@extends('layouts.app')
@section('title', 'Редактирование поста')
@section('content')

<h1>Редактирование</h1>

<form method="POST" action="{{ route('posts.update', $post) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Заголовок</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
        @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Текст</label>
        <textarea name="body" class="form-control" rows="6" required>{{ old('body', $post->body) }}</textarea>
        @error('body')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary">Сохранить</button>
    <a href="{{ route('posts.show', $post) }}" class="btn btn-link">Отмена</a>
</form>

@endsection
