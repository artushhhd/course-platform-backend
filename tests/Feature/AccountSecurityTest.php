<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_rejects_a_password_shorter_than_eight_characters(): void
    {
        $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'password' => 'abc12',
            'password_confirmation' => 'abc12',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'new-user@example.com']);
    }

    public function test_blocking_a_user_revokes_existing_access_tokens(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'StrongPassword123',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $target = User::create([
            'name' => 'Target User',
            'email' => 'target@example.com',
            'password' => 'StrongPassword123',
            'role' => 'user',
            'is_active' => true,
        ]);

        $target->createToken('test-token');

        $this->actingAs($admin)
            ->postJson('/api/admin/users/'.$target->id.'/toggle-block')
            ->assertOk()
            ->assertJsonPath('is_active', false);

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertFalse($target->fresh()->is_active);
    }
}
