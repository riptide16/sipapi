<?php

namespace Tests\Feature;

use App\Models\Banner;
use Tests\TestCase;

class BannerTest extends TestCase
{
    public function test_index()
    {
        $active = Banner::factory()->count(2)->active()->create();
        $inactive = Banner::factory()->inactive()->create();
        $response = $this->getJson(route('public.banners.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(2, count($data));
        $this->assertFalse(in_array($inactive->id, [$data[0]['id'], $data[1]['id']]));
        $this->assertFalse(isset($data[0]->is_active));
    }
}
