<?php

namespace App\Listeners;

use App\Events\AccountActivated;
use App\Notifications\AccountActivatedEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAccountActivatedEmail
{
    /**
     * Handle the event.
     *
     * @param  AccountActivated  $event
     * @return void
     */
    public function handle(AccountActivated $event)
    {
        $event->user->notify(new AccountActivatedEmail());
    }
}
