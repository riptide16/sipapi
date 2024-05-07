<?php

namespace Tests\Feature\Admin;

use App\Models\FileDownload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

class FileDownloadTest extends TestCase
{
    public function test_index()
    {
        $files = FileDownload::factory()->count(3)->create();
        $response = $this->getJson(route('admin.file_downloads.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($files[0]->id, $sample['id']);
        $this->assertEquals($files[0]->filename, $sample['filename']);
        $this->assertEquals($files[0]->is_published, $sample['is_published']);
        $this->assertStringContainsString($files[0]->attachment, $sample['attachment']);
    }

    public function test_show()
    {
        $file = FileDownload::factory()->create();
        $response = $this->getJson(route('admin.file_downloads.show', [$file->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($file) {
                     $json->has('data', function ($json) use ($file) {
                              $json->where('id', $file->id)
                                   ->where('filename', $file->filename)
                                   ->where('is_published', $file->is_published)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'filename' => 'Judul 1',
            'attachment_file' => UploadedFile::fake()->image('file.jpg'),
            'is_published' => true,
        ];
        $response = $this->post(route('admin.file_downloads.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('filename', $param['filename'])
                                   ->where('is_published', $param['is_published'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('file_downloads', [
            'filename' => $param['filename'],
            'is_published' => $param['is_published'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'filename' => 'Judul 1',
            'attachment_file' => UploadedFile::fake()->image('file.jpg'),
        ];
        $response = $this->post(route('admin.file_downloads.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'is_published',
                     ]
                 ]);
    }

    public function test_update()
    {
        $file = FileDownload::factory()->create();
        $response = $this->put(
            route('admin.file_downloads.update',
            [$file->id]),
            ['filename' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($file) {
                     $json->has('data', function ($json) use ($file) {
                              $json->where('id', $file->id)
                                   ->where('filename', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('file_downloads', [
            'id' => $file->id,
            'filename' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        Storage::fake('public');
        $file = FileDownload::factory()->create();
        $response = $this->deleteJson(route('admin.file_downloads.destroy', [$file->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('file_downloads', [
            'id' => $file->id,
        ]);
        Storage::disk('public')->assertMissing($file->getRawOriginal('attachment'));
    }
}
