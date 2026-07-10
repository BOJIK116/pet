<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Danil',
            'email' => 'danil@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'device_name' => 'MacBook',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                ],
                'token',
                'token_type',
            ])
            ->assertJsonPath('user.email', 'danil@example.com')
            ->assertJsonPath('token_type', 'Bearer');

        $this->assertDatabaseHas('users', [
            'email' => 'danil@example.com',
        ]);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'danil@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Danil',
            'email' => 'danil@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'danil@example.com',
            'password' => 'Password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'danil@example.com',
            'password' => 'Password123',
            'device_name' => 'MacBook',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                ],
                'token',
                'token_type',
            ])
            ->assertJsonPath('user.email', 'danil@example.com')
            ->assertJsonPath('token_type', 'Bearer');
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'danil@example.com',
            'password' => 'Password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'danil@example.com',
            'password' => 'WrongPassword123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_get_current_user(): void
    {
        $user = User::factory()->create([
            'email' => 'danil@example.com',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->getJson('/api/me', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.email', 'danil@example.com');
    }

    public function test_guest_cannot_get_current_user(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_logout(): void
{
    $user = User::factory()->create();

    $token = $user->createToken('test-token')->plainTextToken;

    $this->assertDatabaseCount('personal_access_tokens', 1);

    $response = $this->postJson('/api/logout', [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'message' => 'Logged out successfully.',
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
}

    public function test_token_does_not_work_after_logout(): void
{
    $user = User::factory()->create();

    $token = $user->createToken('test-token')->plainTextToken;

    $this->postJson('/api/logout', [], [
        'Authorization' => 'Bearer '.$token,
    ])->assertOk();

    $this->assertDatabaseCount('personal_access_tokens', 0);

    $this->refreshApplication();

    $this->getJson('/api/me', [
        'Authorization' => 'Bearer '.$token,
    ])->assertUnauthorized();
}}