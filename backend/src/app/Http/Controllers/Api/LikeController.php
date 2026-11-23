<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        $user = $request->user();
        $existing = $post->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
