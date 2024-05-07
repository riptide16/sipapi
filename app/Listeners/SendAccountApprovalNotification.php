<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\ApproveNewlyVerifiedUser;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAccountApprovalNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $users = User::admins()->get();
        Notification::send($users, new ApproveNewlyVerifiedUser($event->user));
    }
}
