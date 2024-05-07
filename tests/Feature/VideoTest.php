<?php

namespace Tests\Feature;

use App\Models\Video;
use Tests\TestCase;

class VideoTest extends TestCase
{
    public function test_index()
    {
        $videos = Video::factory()->count(3)->create();
        $response = $this->getJson(route('public.videos.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(3, count($data));
        foreach ($videos as $video) {
            $this->assertTrue(in_array($video->id, $videos->pluck('id')->toArray()));
        }
    }
}
