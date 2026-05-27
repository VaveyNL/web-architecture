@extends('layouts.app')
@section('title', 'Лента постов')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Лента</h1>
    @auth
        <a href="{{ route('posts.create') }}" class="btn btn-primary">Создать пост</a>
    @endauth
</div>

<div id="posts-feed">
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
</div>

{{ $posts->links() }}

<script>
const wsUrl = 'ws://localhost/ws';

function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
}

function prependPost(post) {
    const feed = document.getElementById('posts-feed');
    if (!feed) return;
    const el = document.createElement('article');
    el.className = 'card mb-3';
    el.innerHTML = `
        <div class="card-body">
            <h3 class="card-title">${escapeHtml(post.title)}</h3>
            <p class="card-text">${escapeHtml(post.body)}</p>
            <small class="text-muted">${escapeHtml(post.author)} · только что</small>
        </div>`;
    feed.prepend(el);
}

function connect() {
    const ws = new WebSocket(wsUrl);
    ws.onopen    = () => console.log('WS connected');
    ws.onmessage = (e) => {
        const msg = JSON.parse(e.data);
        if (msg.type === 'new_post') prependPost(msg.post);
    };
    ws.onclose = () => {
        console.log('WS closed, reconnecting in 3s...');
        setTimeout(connect, 3000);
    };
    ws.onerror = (err) => console.error('WS error:', err);
}

connect();
</script>

@endsection
