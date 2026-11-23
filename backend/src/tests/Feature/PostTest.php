<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_posts(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'titre', 'contenu', 'location', 'user_id', 'comments_count', 'likes_count', 'user'],
                ],
            ]);
    }

    public function test_guest_can_view_single_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->getJson('/api/posts/' . $post->id);

        $response->assertOk()
            ->assertJsonPath('id', $post->id)
            ->assertJsonPath('titre', $post->titre)
            ->assertJsonStructure(['id', 'titre', 'contenu', 'location', 'user', 'comments', 'comments_count', 'likes_count']);
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'titre' => 'My Post',
            'contenu' => 'Post content',
            'location' => 'Rabat',
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertCreated()
            ->assertJsonPath('titre', 'My Post')
            ->assertJsonPath('contenu', 'Post content')
            ->assertJsonPath('location', 'Rabat')
            ->assertJsonPath('user_id', $user->id);

        $this->assertDatabaseHas('posts', ['titre' => 'My Post', 'user_id' => $user->id]);
    }

    public function test_guest_cannot_create_post(): void
    {
        $response = $this->postJson('/api/posts', [
            'titre' => 'My Post',
            'contenu' => 'Post content',
            'location' => 'Rabat',
        ]);

        $response->assertUnauthorized();
    }

    public function test_create_post_requires_fields(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/posts', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['titre', 'contenu', 'location']);
    }

    public function test_owner_can_update_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/posts/' . $post->id, [
            'titre' => 'Updated title',
        ]);

        $response->assertOk()
            ->assertJsonPath('titre', 'Updated title');

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'titre' => 'Updated title']);
    }

    public function test_non_owner_cannot_update_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->for($owner)->create(['titre' => 'Original']);
        Sanctum::actingAs($other);

        $response = $this->putJson('/api/posts/' . $post->id, [
            'titre' => 'Hacked',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'titre' => 'Original']);
    }

    public function test_admin_can_update_any_post(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $post = Post::factory()->for($owner)->create();
        Sanctum::actingAs($admin);

        $response = $this->putJson('/api/posts/' . $post->id, [
            'titre' => 'Admin updated',
        ]);

        $response->assertOk()
            ->assertJsonPath('titre', 'Admin updated');
    }

    public function test_owner_can_delete_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/posts/' . $post->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_non_owner_cannot_delete_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->for($owner)->create();
        Sanctum::actingAs($other);

        $response = $this->deleteJson('/api/posts/' . $post->id);

        $response->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }
}
