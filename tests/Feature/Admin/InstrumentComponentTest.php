<?php

namespace Tests\Feature\Admin;

use App\Models\InstrumentComponent;
use App\Models\InstrumentFirstSubcomponent;
use Illuminate\Testing\Fluent\AssertableJson;

class InstrumentComponentTest extends TestCase
{
    public function test_index_main()
    {
        $components = InstrumentComponent::factory()->count(3)->create();
        $response = $this->getJson(route('admin.instrument_components.index', [
            'type' => 'main'
        ]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($components[0]->id, $sample['id']);
        $this->assertEquals($components[0]->name, $sample['name']);
        $this->assertEquals($components[0]->weight, $sample['weight']);
    }

    public function test_show_main()
    {
        $component = InstrumentComponent::factory()->create();
        $response = $this->getJson(route('admin.instrument_components.show', [$component->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($component) {
                     $json->has('data', function ($json) use ($component) {
                              $json->where('id', $component->id)
                                   ->where('name', $component->name)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store_main()
    {
        $param = [
            'name' => 'name',
            'weight' => '2',
            'category' => 'Khusus',
            'type' => 'main',
            'order' => 1,
        ];
        $response = $this->postJson(route('admin.instrument_components.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('instrument_components', [
            'name' => $param['name'],
            'weight' => $param['weight'],
            'category' => $param['category'],
            'type' => 'main',
        ]);
    }

    public function test_store_main_with_parent()
    {
        $parent = InstrumentComponent::factory()->create();
        $param = [
            'name' => 'name',
            'weight' => '2',
            'category' => 'Khusus',
            'type' => 'main',
            'parent_id' => $parent->id,
            'order' => 1,
        ];
        $response = $this->postJson(route('admin.instrument_components.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('errors', function ($json) use ($param) {
                              $json->has('parent_id')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseMissing('instrument_components', [
            'name' => $param['name'],
            'weight' => $param['weight'],
            'category' => $param['category'],
            'type' => 'main',
        ]);
    }

    public function test_update_main()
    {
        $component = InstrumentComponent::factory()->create();
        $response = $this->putJson(
            route('admin.instrument_components.update',
            [$component->id]),
            ['name' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($component) {
                     $json->has('data', function ($json) use ($component) {
                              $json->where('id', $component->id)
                                   ->where('name', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('instrument_components', [
            'id' => $component->id,
            'name' => 'TEST',
        ]);
    }

    public function test_destroy_main()
    {
        $component = InstrumentComponent::factory()->create();
        $response = $this->deleteJson(route('admin.instrument_components.update', [$component->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertSoftDeleted($component);
    }

    public function test_failed_destroy_main_with_constraint_violation()
    {
        $component = InstrumentComponent::factory()->create();
        InstrumentComponent::factory()->sub1()->create([
            'parent_id' => $component->id,
        ]);
        $response = $this->deleteJson(route('admin.instrument_components.update', [$component->id]));

        $response->assertStatus(406)
                 ->assertJson(['success' => false])
                 ->assertJson(['code' => 'ERR4506']);
        $this->assertDatabaseHas('instrument_components', [
            'id' => $component->id,
        ]);
    }

    public function test_index_sub_1()
    {
        $components = InstrumentComponent::factory()->sub1()->count(3)->create();
        $response = $this->getJson(route('admin.instrument_components.index', [
            'type' => 'sub_1'
        ]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($components[0]->id, $sample['id']);
        $this->assertEquals($components[0]->name, $sample['name']);
        $this->assertEquals($components[0]->parent->id, $sample['parent']['id']);
    }

    public function test_show_sub_1()
    {
        $component = InstrumentComponent::factory()->sub1()->create();
        $response = $this->getJson(route('admin.instrument_components.show', [$component->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($component) {
                     $json->has('data', function ($json) use ($component) {
                              $json->where('id', $component->id)
                                   ->where('name', $component->name)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store_sub1()
    {
        $parent = InstrumentComponent::factory()->create();
        $param = [
            'name' => 'name',
            'weight' => '2',
            'category' => 'Khusus',
            'type' => 'sub_1',
            'parent_id' => $parent->id,
            'order' => 1,
        ];
        $response = $this->postJson(route('admin.instrument_components.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('instrument_components', [
            'name' => $param['name'],
            'weight' => $param['weight'],
            'category' => $param['category'],
            'type' => 'sub_1',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_store_sub_1_with_invalid_parent()
    {
        $parent = InstrumentComponent::factory()->sub2()->create();
        $param = [
            'name' => 'name',
            'weight' => '2',
            'category' => 'Khusus',
            'type' => 'sub_1',
            'parent_id' => $parent->id,
            'order' => 1,
        ];
        $response = $this->postJson(route('admin.instrument_components.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('errors', function ($json) use ($param) {
                              $json->has('parent_id')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseMissing('instrument_components', [
            'name' => $param['name'],
            'weight' => $param['weight'],
            'category' => $param['category'],
            'type' => 'sub_1',
        ]);
    }

    public function test_update_sub_1()
    {
        $main = InstrumentComponent::factory()->create();
        $component = InstrumentComponent::factory()->sub1()->create();
        $response = $this->putJson(
            route('admin.instrument_components.update',
            [$component->id]),
            ['name' => 'TEST', 'parent_id' => $main->id]
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($component) {
                     $json->has('data', function ($json) use ($component) {
                              $json->where('id', $component->id)
                                   ->where('name', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('instrument_components', [
            'id' => $component->id,
            'name' => 'TEST',
            'parent_id' => $main->id,
        ]);
    }

    public function test_destroy_sub_1()
    {
        $component = InstrumentComponent::factory()->sub1()->create();
        $response = $this->deleteJson(route('admin.instrument_components.update', [$component->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertSoftDeleted($component);
    }

    public function test_failed_destroy_sub_1_with_constraint_violation()
    {
        $component = InstrumentComponent::factory()->sub1()->create();
        InstrumentComponent::factory()->sub2()->create([
            'parent_id' => $component->id,
        ]);
        $response = $this->deleteJson(route('admin.instrument_components.update', [$component->id]));

        $response->assertStatus(406)
                 ->assertJson(['success' => false])
                 ->assertJson(['code' => 'ERR4506']);
        $this->assertDatabaseHas('instrument_components', [
            'id' => $component->id,
        ]);
    }

    public function test_index_sub_2()
    {
        $components = InstrumentComponent::factory()->sub2()->count(3)->create();
        $response = $this->getJson(route('admin.instrument_components.index', [
            'type' => 'sub_2'
        ]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($components[0]->id, $sample['id']);
        $this->assertEquals($components[0]->name, $sample['name']);
        $this->assertEquals($components[0]->parent->id, $sample['parent']['id']);
    }

    public function test_show_sub_2()
    {
        $component = InstrumentComponent::factory()->sub2()->create();
        $response = $this->getJson(route('admin.instrument_components.show', [$component->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($component) {
                     $json->has('data', function ($json) use ($component) {
                              $json->where('id', $component->id)
                                   ->where('name', $component->name)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store_sub_2()
    {
        $parent = InstrumentComponent::factory()->sub1()->create();
        $param = [
            'name' => 'name',
            'weight' => '2',
            'category' => 'Khusus',
            'type' => 'sub_2',
            'order' => 1,
            'parent_id' => $parent->id,
        ];
        $response = $this->postJson(route('admin.instrument_components.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('instrument_components', [
            'name' => $param['name'],
            'weight' => $param['weight'],
            'category' => $param['category'],
            'type' => 'sub_2',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_store_sub_2_with_invalid_parent()
    {
        $parent = InstrumentComponent::factory()->create();
        $param = [
            'name' => 'name',
            'weight' => '2',
            'category' => 'Khusus',
            'type' => 'sub_2',
            'order' => 1,
            'parent_id' => $parent->id,
        ];
        $response = $this->postJson(route('admin.instrument_components.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('errors', function ($json) use ($param) {
                              $json->has('parent_id')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseMissing('instrument_components', [
            'name' => $param['name'],
            'weight' => $param['weight'],
            'category' => $param['category'],
            'type' => 'sub_2',
        ]);
    }

    public function test_update_sub_2()
    {
        $parent = InstrumentComponent::factory()->sub1()->create();
        $component = InstrumentComponent::factory()->sub2()->create();
        $response = $this->putJson(
            route('admin.instrument_components.update',
            [$component->id]),
            ['name' => 'TEST', 'parent_id' => $parent->id]
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($component) {
                     $json->has('data', function ($json) use ($component) {
                              $json->where('id', $component->id)
                                   ->where('name', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('instrument_components', [
            'id' => $component->id,
            'name' => 'TEST',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_destroy_sub_2()
    {
        $component = InstrumentComponent::factory()->sub2()->create();
        $response = $this->deleteJson(route('admin.instrument_components.update', [$component->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertSoftDeleted($component);
    }
}
