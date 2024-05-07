<?php

namespace Tests\Feature\Admin;

use App\Models\Instrument;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use App\Models\InstrumentComponent;
use Illuminate\Testing\Fluent\AssertableJson;

class InstrumentAspectTest extends TestCase
{
    protected $instrument;

    public function setUp(): void
    {
        parent::setUp();

        $this->instrument = Instrument::first();
    }

    public function test_index()
    {
        $aspects = InstrumentAspect::factory()->count(3)->create([
            'instrument_id' => $this->instrument->id,
        ])->each(function ($aspect) {
            InstrumentAspectPoint::factory()->create([
                'instrument_aspect_id' => $aspect->id,
            ]);
        });
        $response = $this->getJson(route('admin.instruments.aspects.index', [$this->instrument->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($aspects[0]->id, $sample['id']);
        $this->assertEquals($aspects[0]->aspect, $sample['aspect']);
        $this->assertEquals($aspects[0]->type, $sample['type']);
        $this->assertTrue(isset($aspects[0]->points));
    }

    public function test_bulk_store()
    {
        $param = [
            'aspects' => [
                [
                    'aspect' => 'Aspect Choice',
                    'type' => 'choice',
                    'instrument_component_id' => InstrumentComponent::factory()->sub2()->create()->id,
                    'order' => 1,
                    'points' => [
                        [
                            'statement' => 'Statement 1',
                            'order' => 1,
                        ],
                        [
                            'statement' => 'Statement 2',
                            'order' => 2,
                        ],
                        [
                            'statement' => 'Statement 3',
                            'order' => 3,
                        ],
                        [
                            'statement' => 'Statement 4',
                            'order' => 4,
                        ],
                        [
                            'statement' => 'Statement 5',
                            'order' => 5,
                        ],
                    ],
                ],
                [
                    'aspect' => 'Aspect Proof',
                    'type' => 'proof',
                    'instrument_component_id' => InstrumentComponent::factory()->sub1()->create()->id,
                    'order' => 1,
                    'points' => [
                        [
                            'statement' => 'Statement 1',
                            'order' => 1,
                        ],
                        [
                            'statement' => 'Statement 2',
                            'order' => 2,
                        ],
                        [
                            'statement' => 'Statement 3',
                            'order' => 3,
                        ],
                        [
                            'statement' => 'Statement 4',
                            'order' => 4,
                        ],
                        [
                            'statement' => 'Statement 5',
                            'order' => 5,
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->postJson(
            route('admin.instruments.aspects.bulk_store', [$this->instrument->id]),
            $param
        );

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(2, count($data));
        $this->assertEquals($param['aspects'][0]['type'], $data[0]['type']);
        $this->assertEquals($param['aspects'][0]['aspect'], $data[0]['aspect']);
        $this->assertEquals(5, count($data[0]['points']));
        $this->assertEquals($param['aspects'][0]['points'][0]['statement'], $data[0]['points'][0]['statement']);
        $this->assertEquals(5, $data[0]['points'][0]['value']);
        $this->assertEquals($param['aspects'][1]['type'], $data[1]['type']);
        $this->assertEquals($param['aspects'][1]['aspect'], $data[1]['aspect']);
        $this->assertEquals(5, count($data[1]['points']));
        $this->assertEquals($param['aspects'][1]['points'][0]['statement'], $data[1]['points'][0]['statement']);
        $this->assertEquals(null, $data[1]['points'][0]['value']);

        $this->assertDatabaseHas('instrument_aspects', [
            'aspect' => $data[0]['aspect'],
        ]);
        $this->assertDatabaseHas('instrument_aspect_points', [
            'statement' => $data[1]['points'][0]['statement'],
        ]);
    }

    public function test_bulk_update()
    {
        $choiceAspect = InstrumentAspect::factory()->choice()->create([
            'instrument_id' => $this->instrument->id,
        ]);
        $choicePoints = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $choiceAspect->id,
        ]);
        $param = [
            'aspects' => [
                [
                    'id' => $choiceAspect->id,
                    'aspect' => 'Aspect Choice Edited',
                    'type' => 'choice',
                    'instrument_component_id' => $choiceAspect->instrument_component_id,
                    'order' => 1,
                    'points' => [
                        [
                            'id' => $choicePoints[0]->id,
                            'statement' => 'Statement 1',
                            'order' => 1,
                        ],
                        [
                            'id' => $choicePoints[1]->id,
                            'statement' => 'Statement 2',
                            'order' => 2,
                        ],
                        [
                            'id' => $choicePoints[2]->id,
                            'statement' => 'Statement 3',
                            'order' => 3,
                        ],
                        [
                            'statement' => 'Statement baru',
                            'order' => 4,
                        ],
                        [
                            'statement' => 'Statement baru 2',
                            'order' => 5,
                        ],
                    ],
                ],
                [
                    'aspect' => 'Aspect Proof',
                    'type' => 'proof',
                    'instrument_component_id' => InstrumentComponent::factory()->sub1()->create()->id,
                    'order' => 1,
                    'points' => [
                        [
                            'statement' => 'Statement 1',
                            'order' => 1,
                        ],
                        [
                            'statement' => 'Statement 2',
                            'order' => 2,
                        ],
                        [
                            'statement' => 'Statement 3',
                            'order' => 3,
                        ],
                        [
                            'statement' => 'Statement 4',
                            'order' => 4,
                        ],
                        [
                            'statement' => 'Statement 5',
                            'order' => 5,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->putJson(
            route('admin.instruments.aspects.bulk_update', [$this->instrument->id]),
            $param
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(2, count($data));
        $this->assertEquals($param['aspects'][0]['type'], $data[0]['type']);
        $this->assertEquals($param['aspects'][0]['aspect'], $data[0]['aspect']);
        $this->assertEquals(5, count($data[0]['points']));
        $this->assertEquals($param['aspects'][0]['points'][0]['statement'], $data[0]['points'][0]['statement']);
        $this->assertEquals(5, $data[0]['points'][0]['value']);
        $this->assertEquals($param['aspects'][1]['type'], $data[1]['type']);
        $this->assertEquals($param['aspects'][1]['aspect'], $data[1]['aspect']);
        $this->assertEquals(5, count($data[1]['points']));
        $this->assertEquals($param['aspects'][1]['points'][0]['statement'], $data[1]['points'][0]['statement']);
        $this->assertEquals(null, $data[1]['points'][0]['value']);
        $this->assertEquals(5, $choiceAspect->refresh()->points->count());

        $this->assertDatabaseHas('instrument_aspect_points', [
            'id' => $choicePoints[1]->id,
            'statement' => $param['aspects'][0]['points'][1]['statement'],
        ]);
        $this->assertDatabaseMissing('instrument_aspect_points', [
            'id' => $choicePoints[3]->id,
        ]);
        $this->assertDatabaseHas('instrument_aspects', [
            'aspect' => $param['aspects'][1]['aspect'],
        ]);
        $this->assertDatabaseHas('instrument_aspect_points', [
            'statement' => $param['aspects'][1]['points'][3]['statement'],
        ]);
    }

    public function test_show()
    {
        $aspect = InstrumentAspect::factory()->create([
            'instrument_id' => $this->instrument->id,
        ]);
        $points = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $aspect->id,
        ]);
        $response = $this->getJson(route('admin.instruments.aspects.show', [$this->instrument->id, $aspect->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals($aspect->id, $data['id']);
        $this->assertEquals($aspect->aspect, $data['aspect']);
        $this->assertEquals($aspect->type, $data['type']);
        $this->assertEquals(5, count($data['points']));
    }

    public function test_destroy()
    {
        $aspect = InstrumentAspect::factory()->create([
            'instrument_id' => $this->instrument->id,
        ]);
        $points = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $aspect->id,
        ]);
        $response = $this->deleteJson(route('admin.instruments.aspects.destroy', [$this->instrument->id, $aspect->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertSoftDeleted($aspect);
        foreach ($points as $point) {
            $this->assertSoftDeleted($point);
        }
    }
}
