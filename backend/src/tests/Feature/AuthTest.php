<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'location' => 'Casablanca',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertCreated()
            ->assertJsonPath('user.email', 'test@example.com')
            ->assertJsonPath('user.name', 'Test User')
            ->assertJsonPath('user.location', 'Casablanca')
            ->assertJsonPath('user.role', 'user')
            ->assertJsonStructure(['user' => ['id', 'name', 'email', 'location', 'role'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_requires_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email);
    }

    public function test_guest_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_guest_cannot_logout(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertUnauthorized();
    }
}
