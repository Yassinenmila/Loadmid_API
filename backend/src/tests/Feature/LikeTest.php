<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_like_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts/' . $post->id . '/like');

        $response->assertOk()
            ->assertJson(['liked' => true, 'likes_count' => 1]);

        $this->assertDatabaseHas('likes', ['post_id' => $post->id, 'user_id' => $user->id]);
    }

    public function test_authenticated_user_can_unlike_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $post->likes()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts/' . $post->id . '/like');

        $response->assertOk()
            ->assertJson(['liked' => false, 'likes_count' => 0]);

        $this->assertDatabaseMissing('likes', ['post_id' => $post->id, 'user_id' => $user->id]);
    }

    public function test_guest_cannot_like_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->postJson('/api/posts/' . $post->id . '/like');

        $response->assertUnauthorized();
    }
}
