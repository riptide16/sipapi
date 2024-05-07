<?php

namespace Tests\Feature\Admin;

use App\Models\EmailTemplate;
use Illuminate\Testing\Fluent\AssertableJson;

class EmailTemplateTest extends TestCase
{
    public function test_index()
    {
        $templates = EmailTemplate::get();
        $response = $this->getJson(route('admin.email_templates.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        foreach ($templates as $i => $template) {
            $this->assertEquals($template->id, $data[$i]['id']);
            $this->assertEquals($template->slug, $data[$i]['slug']);
            $this->assertEquals($template->subject, $data[$i]['subject']);
        }
    }

    public function test_show()
    {
        $template = EmailTemplate::first();
        $response = $this->getJson(route('admin.email_templates.show', [$template->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($template) {
                     $json->has('data', function ($json) use ($template) {
                              $json->where('id', $template->id)
                                   ->where('subject', $template->subject)
                                   ->where('slug', $template->slug)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_update()
    {
        $template = EmailTemplate::first();
        $response = $this->putJson(
            route('admin.email_templates.update',
            [$template->id]),
            ['subject' => 'TEST']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($template) {
                     $json->has('data', function ($json) use ($template) {
                              $json->where('id', $template->id)
                                   ->where('subject', 'TEST')
                                   ->etc();
                          })
                          ->etc();
                 });
        $this->assertDatabaseHas('email_templates', [
            'id' => $template->id,
            'subject' => 'TEST',
        ]);
    }
}
