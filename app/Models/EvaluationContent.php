<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationContent extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;

    protected $fillable = [
        'evaluation_id',
        'accreditation_content_id',
        'instrument_aspect_point_id',
        'statement',
        'value',
    ];

    public function accreditationContent()
    {
        return $this->belongsTo(AccreditationContent::class);
    }
}
