<?php

namespace Tests\Feature\Admin;

use App\Models\Testimony;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

class TestimonyTest extends TestCase
{
    public function test_index()
    {
        $testimonies = Testimony::factory()->count(3)->create();
        $response = $this->getJson(route('admin.testimonies.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($testimonies[0]->id, $sample['id']);
        $this->assertEquals($testimonies[0]->name, $sample['name']);
        $this->assertEquals($testimonies[0]->content, $sample['content']);
        $this->assertStringContainsString($testimonies[0]->photo, $sample['photo']);
    }

    public function test_show()
    {
        $testimony = Testimony::factory()->create();
        $response = $this->getJson(route('admin.testimonies.show', [$testimony->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($testimony) {
                     $json->has('data', function ($json) use ($testimony) {
                              $json->where('id', $testimony->id)
                                   ->where('name', $testimony->name)
                                   ->where('content', $testimony->content)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'name' => 'Judul 1',
            'photo_file' => UploadedFile::fake()->image('testimony.jpg'),
            'content' => 'konten 1',
        ];
        $response = $this->post(route('admin.testimonies.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('name', $param['name'])
                                   ->where('content', $param['content'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('testimonies', [
            'name' => $param['name'],
            'content' => $param['content'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'name' => 'Judul 1',
            'photo_file' => UploadedFile::fake()->image('testimony.jpg'),
        ];
        $response = $this->post(route('admin.testimonies.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'content',
                     ]
                 ]);
    }

    public function test_update()
    {
        $testimony = Testimony::factory()->create();
        $response = $this->put(
            route('admin.testimonies.update',
            [$testimony->id]),
            ['name' => 'TEST', '_method' => 'PUT']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($testimony) {
                     $json->has('data', function ($json) use ($testimony) {
                              $json->where('id', $testimony->id)
                                   ->where('name', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('testimonies', [
            'id' => $testimony->id,
            'name' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        Storage::fake('public');
        $testimony = Testimony::factory()->create();
        $response = $this->deleteJson(route('admin.testimonies.destroy', [$testimony->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('testimonies', [
            'id' => $testimony->id,
        ]);
        Storage::disk('public')->assertMissing($testimony->getRawOriginal('photo'));
    }
}
