<?php

namespace Tests\Feature\Admin;

use App\Models\Gallery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

class GalleryTest extends TestCase
{
    public function test_index()
    {
        $galleries = Gallery::factory()->count(3)->create();
        $response = $this->getJson(route('admin.galleries.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($galleries[0]->id, $sample['id']);
        $this->assertEquals($galleries[0]->title, $sample['title']);
        $this->assertEquals($galleries[0]->caption, $sample['caption']);
        $this->assertStringContainsString($galleries[0]->image, $sample['image']);
    }

    public function test_show()
    {
        $gallery = Gallery::factory()->create();
        $response = $this->getJson(route('admin.galleries.show', [$gallery->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($gallery) {
                     $json->has('data', function ($json) use ($gallery) {
                              $json->where('id', $gallery->id)
                                   ->where('title', $gallery->title)
                                   ->where('caption', $gallery->caption)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'title' => 'title',
            'image_file' => UploadedFile::fake()->image('gallery.jpg'),
            'caption' => 'caption 1',
            'published_date' => '2021-01-01 01:00:00',
            'album' => 'album 1',
        ];
        $response = $this->post(route('admin.galleries.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('title', $param['title'])
                                   ->where('caption', $param['caption'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('galleries', [
            'title' => $param['title'],
            'caption' => $param['caption'],
            'published_date' => $param['published_date'],
        ]);
        $this->assertDatabaseHas('gallery_albums', [
            'name' => $param['album'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'title' => 'title',
            'image_file' => UploadedFile::fake()->image('gallery.jpg'),
            'caption' => 'caption 1',
            'published_date' => '01:00:00',
            'album' => 'album 1',
        ];
        $response = $this->post(route('admin.galleries.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'published_date',
                     ]
                 ]);
    }

    public function test_update()
    {
        $gallery = Gallery::factory()->create();
        $response = $this->put(
            route('admin.galleries.update',
            [$gallery->id]),
            ['title' => 'TEST', '_method' => 'PUT']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($gallery) {
                     $json->has('data', function ($json) use ($gallery) {
                              $json->where('id', $gallery->id)
                                   ->where('title', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('galleries', [
            'id' => $gallery->id,
            'title' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        Storage::fake('public');
        $gallery = Gallery::factory()->create();
        $response = $this->deleteJson(route('admin.galleries.destroy', [$gallery->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('galleries', [
            'id' => $gallery->id,
        ]);
        Storage::disk('public')->assertMissing($gallery->image);
    }
}
