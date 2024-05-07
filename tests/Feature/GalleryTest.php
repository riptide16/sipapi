<?php

namespace Tests\Feature;

use App\Models\Gallery;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    public function test_index()
    {
        $galleries = Gallery::factory()->count(3)->create();
        $response = $this->getJson(route('public.galleries.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(3, count($data));
        foreach ($galleries as $gallery) {
            $this->assertTrue(in_array($gallery->id, $gallery->pluck('id')->toArray()));
        }
    }
}
