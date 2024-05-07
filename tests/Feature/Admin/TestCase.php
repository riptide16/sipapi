<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var User
     */
    protected $superAdmin;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->actingAsSuperAdmin();
    }

    protected function actingAsSuperAdmin()
    {
        return $this->actingAsPassport($this->superAdmin);
    }

    protected function actingAsPassport(User $user)
    {
        Passport::actingAs($user);

        return $this;
    }
}
