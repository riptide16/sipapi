<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Video;
use App\Models\ActivityLog;
use Illuminate\Testing\Fluent\AssertableJson;

class LogTest extends TestCase
{
    public function test_index()
    {
        $param = [
            'title' => 'title',
            'youtube_id' => 'tesid',
            'description' => 'tes deskripsi',
        ];
        $headers = [
            'Client-IP' => '123.123.123.123',
            'Client-User-Agent' => 'Mozilla',
        ];
        $videoResponse = $this->postJson(route('admin.videos.store'), $param, $headers);

        $response = $this->getJson(route('admin.logs.index', ['subject_type' => 'App\\\\Models\\\\Video']));
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals('created', $sample['description']);
        $this->assertEquals(Video::class, $sample['subject_type']);
        $this->assertEquals($param['title'], $sample['subject']['title']);
        $this->assertEquals($param['title'], $sample['changes']['attributes']['title']);
        $this->assertEquals(User::class, $sample['causer_type']);
        $this->assertEquals($this->superAdmin->id, $sample['causer']['id']);
        $this->assertEquals($headers['Client-IP'], $sample['ip_address']);
        $this->assertEquals($headers['Client-User-Agent'], $sample['user_agent']);
    }

    public function test_show()
    {
        $video = Video::factory()->create();

        $param = [
            'title' => 'edited title',
        ];
        $headers = [
            'Client-IP' => '123.123.123.123',
            'Client-User-Agent' => 'Mozilla',
        ];
        $videoResponse = $this->putJson(route('admin.videos.update', [$video->id]), $param, $headers);

        $log = ActivityLog::all()->last();

        $response = $this->getJson(route('admin.logs.show', [$log->id]));
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'];
        $this->assertEquals('updated', $sample['description']);
        $this->assertEquals(Video::class, $sample['subject_type']);
        $this->assertEquals($param['title'], $sample['subject']['title']);
        $this->assertEquals($param['title'], $sample['changes']['attributes']['title']);
        $this->assertEquals($video->title, $sample['changes']['old']['title']);
        $this->assertEquals(User::class, $sample['causer_type']);
        $this->assertEquals($this->superAdmin->id, $sample['causer']['id']);
        $this->assertEquals($headers['Client-IP'], $sample['ip_address']);
        $this->assertEquals($headers['Client-User-Agent'], $sample['user_agent']);
    }
}
