<?php

namespace Tests\Feature\Admin;

use App\Models\Region;
use App\Models\Province;
use Illuminate\Testing\Fluent\AssertableJson;

class RegionTest extends TestCase
{
    public function test_index()
    {
        $regions = Region::factory()->count(3)
                         ->has(Province::factory()->count(3), 'provinces')
                         ->create();

        $response = $this->getJson(route('admin.regions.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($regions[0]->id, $sample['id']);
        $this->assertEquals($regions[0]->name, $sample['name']);
        $this->assertTrue(!empty($sample['provinces']));
    }

    public function test_show()
    {
        $region = Region::factory()
                        ->has(Province::factory()->count(3), 'provinces')
                        ->create();
        $response = $this->getJson(route('admin.regions.show', [$region->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($region) {
                     $json->has('data', function ($json) use ($region) {
                              $json->where('id', $region->id)
                                   ->where('name', $region->name)
                                   ->has('created_by')
                                   ->has('provinces')
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $provinces = Province::factory()->count(3)->create();
        $param = [
            'name' => 'name',
            'province_ids' => implode(',', $provinces->pluck('id')->toArray()),
        ];
        $response = $this->postJson(route('admin.regions.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->has('provinces')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('regions', [
            'name' => $param['name'],
            'created_by' => $this->superAdmin->id,
        ]);

        foreach ($provinces as $province) {
            $this->assertDatabaseHas('province_region', [
                'region_id' => $response['data']['id'],
                'province_id' => $province->id,
            ]);
        }
    }

    public function test_failed_validation_store()
    {
        $param = [
            'name' => 'Kota',
            'province_ids' => '123123',
        ];
        $response = $this->postJson(route('admin.regions.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'province_ids'
                     ]
                 ]);
    }

    public function test_update()
    {
        $region = Region::factory()
                        ->has(Province::factory()->count(3), 'provinces')
                        ->create();
        $response = $this->putJson(
            route('admin.regions.update',
            [$region->id]),
            ['name' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($region) {
                     $json->has('data', function ($json) use ($region) {
                              $json->where('id', $region->id)
                                   ->where('name', 'TEST')
                                   ->has('created_by')
                                   ->has('provinces')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('regions', [
            'id' => $region->id,
            'name' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        $region = Region::factory()
                        ->has(Province::factory()->count(3), 'provinces')
                        ->create();
        $response = $this->deleteJson(route('admin.regions.update', [$region->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($region) {
                     $json->has('data', function ($json) use ($region) {
                              $json->where('id', $region->id)
                                   ->where('name', $region->name)
                                   ->has('created_by')
                                   ->has('provinces')
                                   ->has('deleted_at')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertSoftDeleted($region);
    }
}
