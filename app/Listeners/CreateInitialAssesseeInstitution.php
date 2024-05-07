<?php

namespace App\Listeners;

use App\Models\Institution;
use App\Events\AccountActivated;

class CreateInitialAssesseeInstitution
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AccountActivated $event)
    {
        if (is_null($event->user->institution)) {
            $institution = new Institution();
            $institution->user_id = $event->user->id;
            $institution->save();
        }
    }
}
