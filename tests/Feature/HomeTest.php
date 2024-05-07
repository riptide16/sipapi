<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    public function test_visit_home()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
