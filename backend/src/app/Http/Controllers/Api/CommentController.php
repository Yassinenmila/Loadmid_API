<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with('user:id,name,email,location,role')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $comment = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => $request->user()->id,
        ]);

        $comment->load('user:id,name,email,location,role');

        return response()->json($comment, 201);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorizeOwner($request, $comment);

        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $comment->update($validated);
        $comment->load('user:id,name,email,location,role');

        return response()->json($comment);
    }

    public function destroy(Request $request, Comment $comment)
    {
        $this->authorizeOwner($request, $comment);
        $comment->delete();

        return response()->json(null, 204);
    }

    private function authorizeOwner(Request $request, Comment $comment): void
    {
        $user = $request->user();
        if ($user->id !== $comment->user_id && $user->role !== 'admin') {
            abort(403, 'You are not allowed to modify this comment.');
        }
    }
}
