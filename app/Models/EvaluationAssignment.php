<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationAssignment extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;

    protected $fillable = [
        'accreditation_id',
        'scheduled_date',
        'method',
    ];

    public function assessors()
    {
        return $this->belongsToMany(User::class, 'evaluation_assignment_user');
    }

    public function accreditation()
    {
        return $this->belongsTo(Accreditation::class);
    }

    public static function methodList()
    {
        return [
            'Online',
            'Onsite',
            'Portofolio',
        ];
    }
}
