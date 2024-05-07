<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\ClientRepository;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * @var \Laravel\Passport\Client
     */
    protected $client;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seedDatabase();

        $this->client = $this->createPasswordClient();

        $this->withHeaders([
            'accept' => 'application/json',
        ]);
    }

    protected function seedDatabase(): void
    {
        $this->seed('DatabaseSeeder');
    }

    protected function createPasswordClient()
    {
        return (new ClientRepository())->create(null, 'Password', 'users', 0, false, true);
    }
}
