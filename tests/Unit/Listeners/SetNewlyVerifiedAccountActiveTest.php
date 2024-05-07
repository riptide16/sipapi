<?php

namespace Tests\Unit\Listeners;

use App\Events\AccountActivated;
use App\Listeners\SetNewlyVerifiedAccountActive;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SetNewlyVerifiedAccountActiveTest extends TestCase
{
    protected $listener;

    public function setUp(): void
    {
        parent::setUp();

        $this->listener = new SetNewlyVerifiedAccountActive();
    }

    public function test_set()
    {
        $user = User::factory()->create();
        Event::fake();

        $this->listener->handle(new Verified($user));

        Event::assertDispatched(function (AccountActivated $event) use ($user) {
            return $event->user->id === $user->id;
        });

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => $user::STATUS_ACTIVE,
        ]);
    }
}
