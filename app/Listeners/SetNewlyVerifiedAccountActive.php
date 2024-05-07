<?php

namespace App\Listeners;

use App\Events\AccountActivated;
use Illuminate\Auth\Events\Verified;

class SetNewlyVerifiedAccountActive
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $event->user->status = $event->user::STATUS_ACTIVE;
        $event->user->activated_at = now();
        $event->user->save();

        event(new AccountActivated($event->user));
    }
}
