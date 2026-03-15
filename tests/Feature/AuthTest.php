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
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'jane123',
            'password_confirmation' => 'jane123'
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user',
                    'token',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com'
        ]);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('jane123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'jane123'
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user',
                    'token',
                ]
            ]);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('jane123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'david@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_protected_route(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertOk();
    }
}
