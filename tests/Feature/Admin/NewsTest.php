<?php

namespace Tests\Feature\Admin;

use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

class NewsTest extends TestCase
{
    public function test_index()
    {
        $news = News::factory()->count(3)->create();
        $response = $this->getJson(route('admin.news.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($news[0]->id, $sample['id']);
        $this->assertEquals($news[0]->title, $sample['title']);
        $this->assertEquals($news[0]->body, $sample['body']);
        $this->assertStringContainsString($news[0]->image, $sample['image']);
    }

    public function test_show()
    {
        $news = News::factory()->create();
        $response = $this->getJson(route('admin.news.show', [$news->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($news) {
                     $json->has('data', function ($json) use ($news) {
                              $json->where('id', $news->id)
                                   ->where('title', $news->title)
                                   ->where('body', $news->body)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'title' => 'Judul 1',
            'image_file' => UploadedFile::fake()->image('news.jpg'),
            'body' => 'konten 1',
            'published_date' => '2021-01-01 05:00:00',
        ];
        $response = $this->post(route('admin.news.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('title', $param['title'])
                                   ->where('body', $param['body'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('news', [
            'title' => $param['title'],
            'body' => $param['body'],
            'published_date' => $param['published_date'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'title' => 'Judul 1',
            'image_file' => UploadedFile::fake()->image('news.jpg'),
            'published_date' => '2021-01-01 05:00:00',
        ];
        $response = $this->post(route('admin.news.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'body',
                     ]
                 ]);
    }

    public function test_update()
    {
        $news = News::factory()->create();
        $response = $this->put(
            route('admin.news.update',
            [$news->id]),
            ['title' => 'TEST', '_method' => 'PUT']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($news) {
                     $json->has('data', function ($json) use ($news) {
                              $json->where('id', $news->id)
                                   ->where('title', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        Storage::fake('public');
        $news = News::factory()->create();
        $response = $this->deleteJson(route('admin.news.update', [$news->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('news', [
            'id' => $news->id,
        ]);
        Storage::disk('public')->assertMissing($news->getRawOriginal('image'));
    }
}
