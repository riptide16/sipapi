<?php

namespace App\Models\Traits;

use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait Loggable
{
    use LogsActivity;

    protected static $logFillable = true;

    protected static $logOnlyDirty = true;

    public function tapActivity(Activity $activity, string $eventName)
    {
        $headers = request()->headers;

        $activity->ip_address = $headers->get(config('activitylog.ip_request_header'));
        $activity->user_agent = $headers->get(config('activitylog.user_agent_request_header'));
    }
}
