<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_accessing_protected_routes(): void
    {
        $protectedRoutes = [
            '/admin/dashboard',
            '/order',
            '/user/settings',
        ];

        foreach ($protectedRoutes as $route) {
            $this->get($route)->assertRedirect(route('login'));
        }
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name_user' => 'testuser',
            'email_user' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'User',
        ]);

        $response->assertRedirect(route('catalogue.index'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name_user' => 'testuser',
            'email_user' => 'testuser@example.com',
            'role' => 'User',
        ]);
    }

    public function test_public_registration_cannot_escalate_role_to_admin(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name_user' => 'attacker',
            'email_user' => 'attacker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Admin',
        ]);

        $response->assertRedirect(route('catalogue.index'));

        $this->assertDatabaseHas('users', [
            'name_user' => 'attacker',
            'email_user' => 'attacker@example.com',
            'role' => 'User',
        ]);
    }

    public function test_admin_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => 'Admin',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email_user' => $admin->email_user,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_regular_user_can_login_and_is_redirected_to_catalogue(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role' => 'User',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email_user' => $user->email_user,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('catalogue.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('catalogue.index'));
        $this->assertGuest();
    }

    public function test_non_admin_user_is_blocked_from_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'User']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertRedirect(route('catalogue.index'));
        $response->assertSessionHas('status', 'error');
    }
}
