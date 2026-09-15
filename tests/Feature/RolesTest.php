<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class RolesTest extends TestCase
{
    public function test_user_admin_and_owner_roles_exist(): void
    {
        foreach (['user', 'admin', 'owner'] as $name) {
            $this->assertDatabaseHas('roles', [
                'name' => $name,
                'slug' => $name,
            ]);
        }
    }

    public function test_additional_named_role_can_be_created_and_assigned(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $member = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('roles.store'), ['name' => 'moderator'])
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', [
            'name' => 'moderator',
            'slug' => 'moderator',
        ]);

        $this->actingAs($admin)
            ->post(route('users.roles.store', $member), ['role' => 'moderator'])
            ->assertRedirect(route('roles.index'));

        $member = $member->fresh();
        $this->assertNotNull($member);
        $this->assertTrue($member->hasRole('moderator'));
        $this->assertDatabaseHas('role_user', [
            'user_id' => $member->id,
            'role_id' => Role::query()->where('name', 'moderator')->value('id'),
        ]);
    }
}
