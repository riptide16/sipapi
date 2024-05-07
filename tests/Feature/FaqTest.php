<?php

namespace Tests\Feature;

use App\Models\Faq;
use Tests\TestCase;

class FaqTest extends TestCase
{
    public function test_index()
    {
        $faqs = Faq::factory()->count(3)->create();
        $response = $this->getJson(route('public.faqs.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals(3, count($data));
        foreach ($faqs as $faq) {
            $this->assertTrue(in_array($faq->id, $faq->pluck('id')->toArray()));
        }
    }
}
