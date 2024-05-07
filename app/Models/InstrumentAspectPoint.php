<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstrumentAspectPoint extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;

    protected $fillable = [
        'statement',
        'order',
        'value',
    ];

    public function aspect()
    {
        return $this->belongsTo(InstrumentAspectPoint::class, 'instrument_aspect_id');
    }
}
