<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson;

class RoleTest extends TestCase
{
    public function test_index()
    {
        $response = $this->getJson(route('admin.roles.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', 4, function ($json) {
                     $json->where('name', 'super_admin')
                          ->etc();
                 })
                 ->etc();
        });
    }

    public function test_show()
    {
        $role = Role::factory()->create();
        $permissions = Permission::take(3)->pluck('id')->toArray();
        $role->permissions()->sync($permissions);

        $response = $this->getJson(route('admin.roles.show', [$role->id]));

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
    }

    // public function test_store()
    // {
    //     $param = [
    //         'display_name' => 'New Role',
    //     ];
    //     $response = $this->postJson(route('admin.roles.store'), $param);
    //
    //     $response->assertStatus(201)
    //              ->assertJson(['success' => true])
    //              ->assertJson(function (AssertableJson $json) use ($param) {
    //                  $json->has('data', function ($json) use ($param) {
    //                           $json->where('name', \Str::slug($param['display_name'], '_'))
    //                                ->where('display_name', $param['display_name'])
    //                                ->etc();
    //                       })
    //                       ->etc();
    //              });
    //     $this->assertDatabaseHas('roles', [
    //         'name' => \Str::slug($param['display_name'], '_'),
    //         'display_name' => $param['display_name'],
    //     ]);
    // }
    //
    // public function test_failed_validation_store()
    // {
    //     $param = [
    //         'name' => 'New Role',
    //     ];
    //     $response = $this->postJson(route('admin.roles.store'), $param);
    //
    //     $response->assertStatus(422)
    //              ->assertJson(['success' => false])
    //              ->assertJsonStructure([
    //                  'errors' => [
    //                      'display_name',
    //                  ]
    //              ]);
    // }
    //
    // public function test_update()
    // {
    //     $role = Role::factory()->create();
    //     $response = $this->putJson(
    //         route('admin.roles.update',
    //         [$role->id]),
    //         ['display_name' => 'TEST']
    //     );
    //
    //     $response->assertStatus(200)
    //              ->assertJson(['success' => true])
    //              ->assertJson(function (AssertableJson $json) use ($role) {
    //                  $json->has('data', function ($json) use ($role) {
    //                           $json->where('id', $role->id)
    //                                ->where('display_name', 'TEST')
    //                                ->where('name', $role->name)
    //                                ->etc();
    //                       })
    //                       ->etc();
    //              });
    //     $this->assertDatabaseHas('roles', [
    //         'id' => $role->id,
    //         'display_name' => 'TEST',
    //         'name' => $role->name,
    //     ]);
    // }
    //
    // public function test_destroy()
    // {
    //     $role = Role::factory()->create();
    //     $permissions = Permission::take(3)->pluck('id')->toArray();
    //     $role->permissions()->sync($permissions);
    //
    //     $response = $this->deleteJson(route('admin.roles.update', [$role->id]));
    //
    //     $response->assertStatus(200)
    //              ->assertJson(['success' => true])
    //              ->assertJson(['data' => []]);
    //     $this->assertSoftDeleted($role);
    // }
    //
    // public function test_destroy_admin()
    // {
    //     $role = Role::admins()->first();
    //
    //     $response = $this->deleteJson(route('admin.roles.update', [$role->id]));
    //
    //     $response->assertStatus(400)
    //              ->assertJson(['success' => false])
    //              ->assertJson(['code' => 'ERR4300']);
    //     $this->assertDatabaseHas('roles', [
    //         'id' => $role->id,
    //     ]);
    // }
}
