<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_other_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $targetUser = User::factory()->create(['role' => 'User']);

        $response = $this->actingAs($admin)->put(route('users.update', $targetUser->id), [
            'name_user' => $targetUser->name_user,
            'email_user' => $targetUser->email_user,
            'role' => 'Admin',
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => 'Admin',
        ]);
    }

    public function test_admin_cannot_change_own_role_from_user_management_page(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)->put(route('users.update', $admin->id), [
            'name_user' => $admin->name_user,
            'email_user' => $admin->email_user,
            'role' => 'User',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'Admin',
        ]);
    }
}
