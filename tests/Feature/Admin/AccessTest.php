<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson;

class AccessTest extends TestCase
{
    public function test_save_permissions()
    {
        $role = Role::factory()->create();
        $permissions = Permission::take(3)->pluck('id')->toArray();

        $response = $this->putJson(route('admin.roles.permissions.save', [$role->id]), [
            'permission_ids' => implode(',', $permissions),
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($role) {
                     $json->has('data', function ($json) use ($role) {
                              $json->where('id', $role->id)
                                   ->where('name', $role->name)
                                   ->has('permissions')
                                   ->etc();
                          })
                          ->etc();
                 });

        $role->refresh();
        $this->assertEquals(3, $role->permissions()->count());
    }
}
