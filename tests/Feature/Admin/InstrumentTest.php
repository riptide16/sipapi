<?php

namespace Tests\Feature\Admin;

use App\Models\Instrument;
use Illuminate\Testing\Fluent\AssertableJson;

class InstrumentTest extends TestCase
{
    public function test_index()
    {
        $instruments = Instrument::get();
        $response = $this->getJson(route('admin.instruments.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_show()
    {
        $instrument = Instrument::first();
        $response = $this->getJson(route('admin.instruments.show', [$instrument->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($instrument) {
                     $json->has('data', function ($json) use ($instrument) {
                              $json->where('id', $instrument->id)
                                   ->where('category', $instrument->category)
                                   ->etc();
                          })
                          ->etc();
                 });
    }
}
