<?php

namespace App\Listeners;

use App\Events\AccreditationCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MakeInstitutionAccredited
{
    /**
     * Handle the event.
     *
     * @param  AccreditationCompleted  $event
     * @return void
     */
    public function handle(AccreditationCompleted $event)
    {
        $institution = $event->accreditation->institution;
        $institution->predicate = $event->accreditation->predicate;
        $institution->accredited_at = $event->accreditation->accredited_at;
        $institution->accreditation_expires_at = $event->accreditation->certificate_expires_at;
        $institution->save();
    }
}
