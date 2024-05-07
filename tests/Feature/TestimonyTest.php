<?php

namespace Tests\Feature;

use App\Models\Testimony;
use Tests\TestCase;

class TestimonyTest extends TestCase
{
    public function test_index()
    {
        $testimonies = Testimony::factory()->count(3)->create();
        $response = $this->getJson(route('public.testimonies.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(3, count($data));
        foreach ($testimonies as $testimony) {
            $this->assertTrue(in_array($testimony->id, $testimony->pluck('id')->toArray()));
        }
    }
}
