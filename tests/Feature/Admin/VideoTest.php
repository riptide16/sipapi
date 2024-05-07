<?php

namespace Tests\Feature\Admin;

use App\Models\Video;
use Illuminate\Testing\Fluent\AssertableJson;

class VideoTest extends TestCase
{
    public function test_index()
    {
        $videos = Video::factory()->count(3)->create();
        $response = $this->getJson(route('admin.videos.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($videos[0]->id, $sample['id']);
        $this->assertEquals($videos[0]->title, $sample['title']);
        $this->assertEquals($videos[0]->youtube_id, $sample['youtube_id']);
    }

    public function test_show()
    {
        $video = Video::factory()->create();
        $response = $this->getJson(route('admin.videos.show', [$video->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($video) {
                     $json->has('data', function ($json) use ($video) {
                              $json->where('id', $video->id)
                                   ->where('title', $video->title)
                                   ->where('youtube_id', $video->youtube_id)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'title' => 'title',
            'youtube_id' => 'tesid',
            'description' => 'tes deskripsi',
        ];
        $response = $this->postJson(route('admin.videos.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('title', $param['title'])
                                   ->where('youtube_id', $param['youtube_id'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('videos', [
            'title' => $param['title'],
            'youtube_id' => $param['youtube_id'],
            'description' => $param['description'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'title' => '',
            'youtube_id' => 'youtubeid',
            'description' => 'tes deskripsi',
        ];
        $response = $this->postJson(route('admin.videos.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'title',
                     ]
                 ]);
    }

    public function test_update()
    {
        $video = Video::factory()->create();
        $response = $this->putJson(
            route('admin.videos.update',
            [$video->id]),
            ['title' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($video) {
                     $json->has('data', function ($json) use ($video) {
                              $json->where('id', $video->id)
                                   ->where('title', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'title' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        $video = Video::factory()->create();
        $response = $this->deleteJson(route('admin.videos.update', [$video->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('videos', [
            'id' => $video->id,
        ]);
    }
}
