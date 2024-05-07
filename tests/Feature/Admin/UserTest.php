<?php

namespace Tests\Feature\Admin;

use App\Events\AccountActivated;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

class UserTest extends TestCase
{
    public function test_index()
    {
        $users = User::factory()->count(3)->create();
        $response = $this->getJson(route('admin.users.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][1];
        $this->assertEquals($users[0]->id, $sample['id']);
        $this->assertEquals($users[0]->name, $sample['name']);
        $this->assertEquals($users[0]->email, $sample['email']);
        $this->assertFalse(isset($sample[1]->password));
    }

    public function test_show()
    {
        $user = User::factory()->create();
        $response = $this->getJson(route('admin.users.show', [$user->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($user) {
                     $json->has('data', function ($json) use ($user) {
                              $json->where('id', $user->id)
                                   ->where('name', $user->name)
                                   ->where('email', $user->email)
                                   ->missing('password')
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'email' => 'test@email.com',
            'name' => 'name',
            'username' => 'username',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => Role::first()->id,
        ];
        $response = $this->postJson(route('admin.users.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->where('email', $param['email'])
                                   ->missing('password')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('users', [
            'name' => $param['name'],
            'email' => $param['email'],
            'status' => User::STATUS_ACTIVE,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'causer_id' => $this->superAdmin->id,
            'subject_id' => $response['data']['id'],
            'subject_type' => User::class,
            'description' => 'created',
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'email' => 'test@email.com',
            'name' => 'name',
            'password' => 'password',
            'password_confirmation' => 'passsword',
        ];
        $response = $this->postJson(route('admin.users.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'password'
                     ]
                 ]);
    }

    public function test_update()
    {
        $user = User::factory()->create();
        $response = $this->putJson(route('admin.users.update', [$user->id]), ['name' => 'TEST']);

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($user) {
                     $json->has('data', function ($json) use ($user) {
                              $json->where('id', $user->id)
                                   ->where('name', 'TEST')
                                   ->where('email', $user->email)
                                   ->missing('password')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'TEST',
        ]);
    }

    public function test_update_user_to_active()
    {
        $user = User::factory()->inactive()->create();
        $response = $this->putJson(
            route('admin.users.update', [$user->id]),
            ['status' => User::STATUS_ACTIVE]
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($user) {
                     $json->has('data', function ($json) use ($user) {
                              $json->where('id', $user->id)
                                   ->where('status', $user::STATUS_ACTIVE)
                                   ->missing('password')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_destroy()
    {
        $user = User::factory()->create();
        $response = $this->deleteJson(route('admin.users.update', [$user->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertSoftDeleted($user);
    }
}
