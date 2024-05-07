<?php

namespace Tests\Feature\Admin;

use App\Models\Faq;
use Illuminate\Testing\Fluent\AssertableJson;

class FaqTest extends TestCase
{
    public function test_index()
    {
        $faqs = Faq::factory()->count(3)->create();
        $response = $this->getJson(route('admin.faqs.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($faqs[0]->id, $sample['id']);
        $this->assertEquals($faqs[0]->title, $sample['title']);
        $this->assertEquals($faqs[0]->content, $sample['content']);
    }

    public function test_show()
    {
        $faq = Faq::factory()->create();
        $response = $this->getJson(route('admin.faqs.show', [$faq->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($faq) {
                     $json->has('data', function ($json) use ($faq) {
                              $json->where('id', $faq->id)
                                   ->where('title', $faq->title)
                                   ->where('content', $faq->content)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_store()
    {
        $param = [
            'title' => 'title',
            'content' => 'content 1',
            'order' => 1,
        ];
        $response = $this->post(route('admin.faqs.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($param) {
                     $json->has('data', function ($json) use ($param) {
                              $json->where('title', $param['title'])
                                   ->where('content', $param['content'])
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('faqs', [
            'title' => $param['title'],
            'content' => $param['content'],
            'order' => $param['order'],
        ]);
    }

    public function test_failed_validation_store()
    {
        $param = [
            'title' => 'title',
            'order' => 1,
        ];
        $response = $this->post(route('admin.faqs.store'), $param);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure([
                     'errors' => [
                         'content'
                     ]
                 ]);
    }

    public function test_update()
    {
        $faq = Faq::factory()->create();
        $response = $this->put(
            route('admin.faqs.update',
            [$faq->id]),
            ['title' => 'TEST', '_method' => 'PUT']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($faq) {
                     $json->has('data', function ($json) use ($faq) {
                              $json->where('id', $faq->id)
                                   ->where('title', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'title' => 'TEST',
        ]);
    }

    public function test_destroy()
    {
        $faq = Faq::factory()->create();
        $response = $this->deleteJson(route('admin.faqs.destroy', [$faq->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(['data' => []]);
        $this->assertDatabaseMissing('faqs', [
            'id' => $faq->id,
        ]);
    }
}
