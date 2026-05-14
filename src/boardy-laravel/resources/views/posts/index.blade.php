@extends('layouts.app')
@section('title', 'Лента постов')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Лента</h1>
    @auth
        <a href="{{ route('posts.create') }}" class="btn btn-primary">Создать пост</a>
    @endauth
</div>

@forelse ($posts as $post)
    <article class="card mb-3">
        <div class="card-body">
            <h3 class="card-title">
                <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
            </h3>
            <p class="card-text">{{ Str::limit($post->body, 200) }}</p>
            <small class="text-muted">
                {{ $post->author->name }} · {{ $post->created_at->format('d.m.Y H:i') }}
            </small>
        </div>
    </article>
@empty
    <p>Постов пока нет.</p>
@endforelse

{{ $posts->links() }}

@endsection
