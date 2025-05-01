<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'token',
                 ]);
    }

    /** @test */
    public function user_cannot_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Invalid credentials',
                 ]);
    }

    /** @test */
    public function user_cannot_login_with_unregistered_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422); // Validation Error
        $response->assertJsonValidationErrors('email');
    }
}
