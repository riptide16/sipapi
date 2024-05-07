<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use SoftDeletes;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;

    protected $fillable = [
        'subject',
        'body',
        'action_button',
    ];
}
