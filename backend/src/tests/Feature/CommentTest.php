<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_post_comments(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->count(2)->for($post)->create();

        $response = $this->getJson('/api/posts/' . $post->id . '/comments');

        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => ['id', 'content', 'user_id', 'post_id', 'user'],
            ]);
    }

    public function test_authenticated_user_can_create_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts/' . $post->id . '/comments', [
            'content' => 'A new comment',
        ]);

        $response->assertCreated()
            ->assertJsonPath('content', 'A new comment')
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('post_id', $post->id);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'A new comment',
        ]);
    }

    public function test_guest_cannot_create_comment(): void
    {
        $post = Post::factory()->create();

        $response = $this->postJson('/api/posts/' . $post->id . '/comments', [
            'content' => 'Nope',
        ]);

        $response->assertUnauthorized();
    }

    public function test_create_comment_requires_content(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts/' . $post->id . '/comments', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_owner_can_update_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create(['content' => 'Old']);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/comments/' . $comment->id, [
            'content' => 'Updated',
        ]);

        $response->assertOk()
            ->assertJsonPath('content', 'Updated');
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'content' => 'Updated']);
    }

    public function test_non_owner_cannot_update_comment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $comment = Comment::factory()->for($owner)->create(['content' => 'Old']);
        Sanctum::actingAs($other);

        $response = $this->putJson('/api/comments/' . $comment->id, [
            'content' => 'Hacked',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'content' => 'Old']);
    }

    public function test_owner_can_delete_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/comments/' . $comment->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_non_owner_cannot_delete_comment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $comment = Comment::factory()->for($owner)->create();
        Sanctum::actingAs($other);

        $response = $this->deleteJson('/api/comments/' . $comment->id);

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }
}
