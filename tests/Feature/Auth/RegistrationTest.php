<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $admin = User::factory()->create(['role' => 'Administrador']);

        $response = $this->actingAs($admin)->get('/register');

        $response->assertStatus(200);
    }

    public function test_guests_cannot_access_registration(): void
    {
        $this->get('/register')->assertRedirect('/login');

        $user = User::factory()->create();

        $this->actingAs($user)->get('/register')->assertForbidden();
    }

    public function test_new_users_can_register(): void
    {
        $admin = User::factory()->create(['role' => 'Administrador']);

        $response = $this->actingAs($admin)->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'Usuario',
        ]);
    }
}
