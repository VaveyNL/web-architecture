@extends('layouts.app')
@section('title', $post->title)
@section('content')

<article class="mb-4">
    <h1>{{ $post->title }}</h1>
    <p class="text-muted">
        {{ $post->author->name }} · {{ $post->created_at->format('d.m.Y H:i') }}
    </p>
    <div class="mb-3" style="white-space: pre-line">{{ $post->body }}</div>

    {{-- Кнопки Редактировать/Удалить вернутся после установки Breeze --}}
</article>

<hr>

<h3>Комментарии ({{ $post->comments->count() }})</h3>

@forelse ($post->comments as $comment)
    <div class="card mb-2">
        <div class="card-body">
            <strong>{{ $comment->author->name }}</strong>
            <small class="text-muted ms-2">{{ $comment->created_at->format('d.m.Y H:i') }}</small>
            <p class="mb-0 mt-1">{{ $comment->body }}</p>
        </div>
    </div>
@empty
    <p>Комментариев пока нет.</p>
@endforelse

{{-- Форма комментария вернётся после установки Breeze --}}
<p class="text-muted mt-3">Чтобы оставить комментарий, нужно войти. Кнопка входа появится после установки Breeze.</p>

@endsection
