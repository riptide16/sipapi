<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use App\Models\Province;
use Illuminate\Testing\Fluent\AssertableJson;

class CityTest extends TestCase
{
    public function test_index()
    {
        $cities = City::factory()->count(3)->create();
        $response = $this->getJson(route('admin.cities.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($cities[0]->id, $sample['id']);
        $this->assertEquals($cities[0]->name, $sample['name']);
        $this->assertEquals($cities[0]->type, $sample['type']);
        $this->assertTrue(isset($sample['province']));
    }

    public function test_show()
    {
        $city = City::factory()->create();
        $response = $this->getJson(route('admin.cities.show', [$city->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($city) {
                     $json->has('data', function ($json) use ($city) {
                              $json->where('id', $city->id)
                                   ->where('name', $city->name)
                                   ->has('province')
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'name' => 'name',
            'type' => City::typeList()[0],
            'province_id' => Province::factory()->create()->id,
        ];
        $response = $this->postJson(route('admin.cities.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->where('type', $param['type'])
                                   ->has('province')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('cities', [
            'name' => $param['name'],
            'type' => $param['type'],
            'province_id' => $param['province_id'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'name' => 'Kota',
            'type' => 'Invalid Type',
            'province_id' => Province::factory()->create()->id,
        ];
        $response = $this->postJson(route('admin.cities.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'type'
                     ]
                 ]);
    }

    public function test_update()
    {
        $city = City::factory()->create();
        $response = $this->putJson(
            route('admin.cities.update',
            [$city->id]),
            ['name' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($city) {
                     $json->has('data', function ($json) use ($city) {
                              $json->where('id', $city->id)
                                   ->where('name', 'TEST')
                                   ->has('province')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'name' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        $city = City::factory()->create();
        $response = $this->deleteJson(route('admin.cities.update', [$city->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('cities', [
            'id' => $city->id,
        ]);
    }
}
