<?php

namespace Tests\Feature;

use App\Models\News;
use Tests\TestCase;

class NewsTest extends TestCase
{
    public function test_index()
    {
        $published = News::factory()->count(2)->create();
        $unpublished = News::factory()->unpublished()->create();
        $response = $this->getJson(route('public.news.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(2, count($data));
        $this->assertFalse(in_array($unpublished->id, [$data[0]['id'], $data[1]['id']]));
    }
}
