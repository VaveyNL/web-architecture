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

<div id="comments-app"
     data-post-id="{{ $post->id }}"
     data-user-name="{{ auth()->user()?->name ?? '' }}"></div>

<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
<script type="text/babel" data-type="module" data-presets="react" src="/js/comments.jsx"></script>

@endsection
