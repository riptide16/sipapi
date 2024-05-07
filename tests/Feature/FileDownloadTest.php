<?php

namespace Tests\Feature;

use App\Models\FileDownload;
use Tests\TestCase;

class FileDownloadTest extends TestCase
{
    public function test_index()
    {
        $published = FileDownload::factory()->count(2)->published()->create();
        $unpublished = FileDownload::factory()->create();
        $response = $this->getJson(route('public.file_downloads.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(2, count($data));
        $this->assertFalse(in_array($unpublished->id, [$data[0]['id'], $data[1]['id']]));
        $this->assertFalse(isset($data[0]->is_published));
    }

    public function test_show_published()
    {
        $published = FileDownload::factory()->published()->create();
        $response = $this->getJson(route('public.file_downloads.show', [$published->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_show_unpublished()
    {
        $published = FileDownload::factory()->create();
        $response = $this->getJson(route('public.file_downloads.show', [$published->id]));

        $response->assertStatus(404)
                 ->assertJson(['success' => false]);
    }
}
