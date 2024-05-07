<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

class BannerTest extends TestCase
{
    public function test_index()
    {
        $banners = Banner::factory()->count(3)->create();
        $response = $this->getJson(route('admin.banners.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($banners[0]->id, $sample['id']);
        $this->assertEquals($banners[0]->name, $sample['name']);
        $this->assertEquals($banners[0]->is_active, $sample['is_active']);
        $this->assertStringContainsString($banners[0]->image, $sample['image']);
    }

    public function test_show()
    {
        $banner = Banner::factory()->create();
        $response = $this->getJson(route('admin.banners.show', [$banner->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($banner) {
                     $json->has('data', function ($json) use ($banner) {
                              $json->where('id', $banner->id)
                                   ->where('name', $banner->name)
                                   ->where('is_active', $banner->is_active)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'name' => 'name',
            'image_file' => UploadedFile::fake()->image('banner.jpg'),
            'order' => 3,
            'is_active' => 1,
            'url' => 'https://evalatore.com',
        ];
        $response = $this->post(route('admin.banners.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->where('order', $param['order'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('banners', [
            'name' => $param['name'],
            'order' => $param['order'],
            'is_active' => $param['is_active'],
            'url' => $param['url'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'name' => 'name',
            'image_file' => UploadedFile::fake()->image('banner.jpg'),
            'order' => 3,
            'is_active' => 1,
            'url' => 'not url',
        ];
        $response = $this->post(route('admin.banners.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'url',
                     ]
                 ]);
    }

    public function test_update()
    {
        $banner = Banner::factory()->create();
        $response = $this->put(
            route('admin.banners.update',
            [$banner->id]),
            ['name' => 'TEST', '_method' => 'PUT']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($banner) {
                     $json->has('data', function ($json) use ($banner) {
                              $json->where('id', $banner->id)
                                   ->where('name', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'name' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        Storage::fake('public');
        $banner = Banner::factory()->create();
        $response = $this->deleteJson(route('admin.banners.update', [$banner->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('banners', [
            'id' => $banner->id,
        ]);
        Storage::disk('public')->assertMissing($banner->getRawOriginal('image'));
    }
}
