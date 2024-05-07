<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use App\Models\Subdistrict;
use Illuminate\Testing\Fluent\AssertableJson;

class SubdistrictTest extends TestCase
{
    public function test_index()
    {
        $subdistricts = Subdistrict::factory()->count(3)->create();
        $response = $this->getJson(route('admin.subdistricts.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($subdistricts[0]->id, $sample['id']);
        $this->assertEquals($subdistricts[0]->name, $sample['name']);
        $this->assertTrue(isset($sample['city']));
    }

    public function test_show()
    {
        $subdistrict = Subdistrict::factory()->create();
        $response = $this->getJson(route('admin.subdistricts.show', [$subdistrict->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($subdistrict) {
                     $json->has('data', function ($json) use ($subdistrict) {
                              $json->where('id', $subdistrict->id)
                                   ->where('name', $subdistrict->name)
                                   ->has('city')
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'name' => 'name',
            'city_id' => city::factory()->create()->id,
        ];
        $response = $this->postJson(route('admin.subdistricts.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->has('city')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('subdistricts', [
            'name' => $param['name'],
            'city_id' => $param['city_id'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'name' => 'Kota',
            'city_id' => 'INVALID_ID',
        ];
        $response = $this->postJson(route('admin.subdistricts.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'city_id',
                     ]
                 ]);
    }

    public function test_update()
    {
        $subdistrict = Subdistrict::factory()->create();
        $response = $this->putJson(
            route('admin.subdistricts.update',
            [$subdistrict->id]),
            ['name' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($subdistrict) {
                     $json->has('data', function ($json) use ($subdistrict) {
                              $json->where('id', $subdistrict->id)
                                   ->where('name', 'TEST')
                                   ->has('city')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('subdistricts', [
            'id' => $subdistrict->id,
            'name' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        $subdistrict = Subdistrict::factory()->create();
        $response = $this->deleteJson(route('admin.subdistricts.update', [$subdistrict->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('subdistricts', [
            'id' => $subdistrict->id,
        ]);
    }
}
