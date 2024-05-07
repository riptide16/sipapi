<?php

namespace Tests\Feature\Admin;

use App\Models\Province;
use Illuminate\Testing\Fluent\AssertableJson;

class ProvinceTest extends TestCase
{
    public function test_index()
    {
        $provinces = Province::factory()->count(3)->create();
        $response = $this->getJson(route('admin.provinces.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($provinces[0]->id, $sample['id']);
        $this->assertEquals($provinces[0]->name, $sample['name']);
    }

    public function test_show()
    {
        $province = Province::factory()->create();
        $response = $this->getJson(route('admin.provinces.show', [$province->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($province) {
                     $json->has('data', function ($json) use ($province) {
                              $json->where('id', $province->id)
                                   ->where('name', $province->name)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'name' => 'name',
        ];
        $response = $this->postJson(route('admin.provinces.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('provinces', [
            'name' => $param['name'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $province = Province::factory()->create();

        $param = [
            'name' => $province->name,
        ];
        $response = $this->postJson(route('admin.provinces.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'name'
                     ]
                 ]);
    }

    public function test_update()
    {
        $province = Province::factory()->create();
        $response = $this->putJson(
            route('admin.provinces.update',
            [$province->id]),
            ['name' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($province) {
                     $json->has('data', function ($json) use ($province) {
                              $json->where('id', $province->id)
                                   ->where('name', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('provinces', [
            'id' => $province->id,
            'name' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        $province = Province::factory()->create();
        $response = $this->deleteJson(route('admin.provinces.update', [$province->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('provinces', [
            'id' => $province->id,
        ]);
    }
}
