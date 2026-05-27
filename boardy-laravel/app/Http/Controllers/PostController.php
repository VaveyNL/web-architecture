<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author')
            ->latest()
            ->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $this->authorize('create', Post::class);
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Post::class);
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body'  => 'required|string|max:5000',
        ]);
        $post = $request->user()->posts()->create($data);

        Redis::publish('new_post', json_encode([
            'id'         => $post->id,
            'title'      => $post->title,
            'body'       => $post->body,
            'author'     => $request->user()->name,
            'created_at' => $post->created_at->toISOString(),
        ]));

        return redirect()->route('posts.show', $post)
            ->with('success', 'Пост создан');
    }

    public function show(Post $post)
    {
        $post->load('author', 'comments.author');
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body'  => 'required|string|max:5000',
        ]);
        $post->update($data);
        return redirect()->route('posts.show', $post)
            ->with('success', 'Пост обновлён');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->route('posts.index')
            ->with('success', 'Пост удалён');
    }
}
