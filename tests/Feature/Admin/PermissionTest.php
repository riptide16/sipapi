<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Permission;

class PermissionTest extends TestCase
{
    public function test_index()
    {
        $response = $this->getJson(route('admin.permissions.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_show()
    {
        $permission = Permission::first();
        $response = $this->getJson(route('admin.permissions.show', [$permission->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_index_with_no_permission()
    {
        $assessee = User::factory()->assessee()->create();

        $response = $this->actingAsPassport($assessee)
                         ->getJson(route('admin.permissions.index'));

        $response->assertStatus(403)
                 ->assertJson(['success' => false]);
    }
}
