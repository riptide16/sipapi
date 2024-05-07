<?php

namespace App\Listeners;

use App\Notifications\AwaitingAccountApprovalEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAwaitingAccountApprovalEmail
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $event->user->notify(new AwaitingAccountApprovalEmail());
    }
}
