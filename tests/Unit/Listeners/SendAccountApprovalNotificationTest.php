<?php

namespace Tests\Unit\Listeners;

use Illuminate\Auth\Events\Verified;
use App\Listeners\SendAccountApprovalNotification;
use App\Models\User;
use App\Notifications\ApproveNewlyVerifiedUser;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendAccountApprovalNotificationTest extends TestCase
{
    protected $listener;

    public function setUp(): void
    {
        parent::setUp();

        $this->listener = new SendAccountApprovalNotification();
    }

    public function test_send_notification()
    {
        Notification::fake();

        $notifiedUser = User::factory()->inactive()->create();
        $admins = User::factory()->count(3)->superAdmin()->create();

        $this->listener->handle(new Verified($notifiedUser));

        Notification::assertSentTo($admins, ApproveNewlyVerifiedUser::class);
    }
}
