<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\AccreditationEvaluated;
use App\Notifications\InputAccreditationResult;
use App\Notifications\AccreditationEvaluated as EvaluatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class ProcessAccreditationPostEvaluation
{
    /**
     * Handle the event.
     *
     * @param  AccreditationEvaluated  $event
     * @return void
     */
    public function handle(AccreditationEvaluated $event)
    {
        $evaluation = $event->evaluation;
        if ($event->accreditation->hasBeenReviewed()) {
            return false;
        }

        $event->accreditation->setReviewed()->save();

        // Notify to admins
        $users = User::admins()
                     ->where('region_id', $event->accreditation->institution->region_id)
                     ->get();
        Notification::send($users, new InputAccreditationResult($event->accreditation));

        $event->accreditation->user->notify(new EvaluatedNotification($event->accreditation));
    }
}
