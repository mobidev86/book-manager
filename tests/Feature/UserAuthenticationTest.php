<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testUserRegistration()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user'])
            ->assertJson(['message' => 'Registration successful']);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function testUserRegistrationValidation()
    {
        $userData = [
            // Missing 'name' field
            'email' => 'invalid_email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);

        // Assert that no user has been created in the database
        $this->assertDatabaseCount('users', 0);
    }

    /** @test */
    public function testUserLogin()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function testUserLoginInvalidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'wrong_password', // Incorrect password
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Assert that the user token is not returned
        $response->assertJsonMissing(['token']);
    }
}
