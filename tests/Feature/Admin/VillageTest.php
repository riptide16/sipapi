<?php

namespace Tests\Feature\Admin;

use App\Models\Subdistrict;
use App\Models\Village;
use Illuminate\Testing\Fluent\AssertableJson;

class VillageTest extends TestCase
{
    public function test_index()
    {
        $villages = Village::factory()->count(3)->create();
        $response = $this->getJson(route('admin.villages.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($villages[0]->id, $sample['id']);
        $this->assertEquals($villages[0]->name, $sample['name']);
        $this->assertEquals($villages[0]->postal_code, $sample['postal_code']);
        $this->assertTrue(isset($sample['subdistrict']));
    }

    public function test_show()
    {
        $village = Village::factory()->create();
        $response = $this->getJson(route('admin.villages.show', [$village->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($village) {
                     $json->has('data', function ($json) use ($village) {
                              $json->where('id', $village->id)
                                   ->where('name', $village->name)
                                   ->where('postal_code', $village->postal_code)
                                   ->has('subdistrict')
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'name' => 'name',
            'postal_code' => '12122',
            'subdistrict_id' => Subdistrict::factory()->create()->id,
        ];
        $response = $this->postJson(route('admin.villages.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->where('postal_code', $param['postal_code'])
                                   ->has('subdistrict')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('villages', [
            'name' => $param['name'],
            'subdistrict_id' => $param['subdistrict_id'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'name' => 'Kota',
            'postal_code' => '101',
            'subdistrict_id' => Subdistrict::factory()->create()->id,
        ];
        $response = $this->postJson(route('admin.villages.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'postal_code',
                     ]
                 ]);
    }

    public function test_update()
    {
        $village = Village::factory()->create();
        $response = $this->putJson(
            route('admin.villages.update',
            [$village->id]),
            ['name' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($village) {
                     $json->has('data', function ($json) use ($village) {
                              $json->where('id', $village->id)
                                   ->where('name', 'TEST')
                                   ->has('subdistrict')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('villages', [
            'id' => $village->id,
            'name' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        $village = Village::factory()->create();
        $response = $this->deleteJson(route('admin.villages.update', [$village->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('villages', [
            'id' => $village->id,
        ]);
    }

    public function testSearchByKeyword()
    {
        $village = Village::factory()->create(['name' => 'Desa 1']);
        $notFound = Village::factory()->create(['name' => 'Not Found']);

        $response = $this->getJson(route('admin.villages.index', ['keyword' => 'desa']));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($village->id, $sample['id']);
        $this->assertEquals($village->name, $sample['name']);
        $this->assertEquals($village->postal_code, $sample['postal_code']);

        foreach ($response['data'] as $found) {
            $this->assertNotEquals($notFound->id, $found['id']);
        }
    }
}
