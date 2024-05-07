<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;

    protected $filterable = [
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'ip_address',
        'user_agent',
    ];

    public function subject(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->setConnection(config('database.default'))->morphTo()->withTrashed();
        }

        return $this->setConnection(config('database.default'))->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->setConnection(config('database.default'))->morphTo();
    }

    public function getConnectionName()
    {
        return config('activitylog.database_connection');
    }
}
