@extends('layouts.app')
@section('title', $post->title)
@section('content')

<article class="mb-4">
    <h1>{{ $post->title }}</h1>
    <p class="text-muted">
        {{ $post->author->name }} · {{ $post->created_at->format('d.m.Y H:i') }}
    </p>
    <div class="mb-3" style="white-space: pre-line">{{ $post->body }}</div>

    @auth
        @can('update', $post)
            <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-secondary btn-sm">Редактировать</a>
        @endcan
        @can('delete', $post)
            <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline"
                  onsubmit="return confirm('Удалить пост?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">Удалить</button>
            </form>
        @endcan
    @endauth
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

@auth
<form method="POST" action="{{ route('comments.store') }}" class="mt-3">
    @csrf
    <input type="hidden" name="post_id" value="{{ $post->id }}">
    <div class="mb-2">
        <textarea name="body" class="form-control" rows="3" placeholder="Ваш комментарий" required></textarea>
        @error('body')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary">Отправить</button>
</form>
@else
    <p class="text-muted mt-3">
        <a href="{{ route('login') }}">Войдите</a>, чтобы оставить комментарий.
    </p>
@endauth

@endsection
