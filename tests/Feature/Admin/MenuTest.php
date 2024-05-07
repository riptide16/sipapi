<?php

namespace Tests\Feature\Admin;

class MenuTest extends TestCase
{
    public function test_index()
    {
        $response = $this->getJson(route('admin.menus.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}
