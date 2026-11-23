<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['user:id,name,email,location,role'])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
        ]);

        $post = $request->user()->posts()->create($validated);
        $post->load(['user:id,name,email,location,role'])->loadCount(['comments', 'likes']);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        $post->load([
            'user:id,name,email,location,role',
            'comments.user:id,name,email,location,role',
        ])->loadCount(['comments', 'likes']);

        return response()->json($post);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorizeOwner($request, $post);

        $validated = $request->validate([
            'titre' => ['sometimes', 'required', 'string', 'max:255'],
            'contenu' => ['sometimes', 'required', 'string'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $post->update($validated);
        $post->load(['user:id,name,email,location,role'])->loadCount(['comments', 'likes']);

        return response()->json($post);
    }

    public function destroy(Request $request, Post $post)
    {
        $this->authorizeOwner($request, $post);
        $post->delete();

        return response()->json(null, 204);
    }

    private function authorizeOwner(Request $request, Post $post): void
    {
        $user = $request->user();
        if ($user->id !== $post->user_id && $user->role !== 'admin') {
            abort(403, 'You are not allowed to modify this post.');
        }
    }
}
