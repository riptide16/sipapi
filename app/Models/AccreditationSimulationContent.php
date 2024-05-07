<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccreditationSimulationContent extends AccreditationContent
{
    protected $fillable = [
        'accreditation_simulation_id',
        'aspectable_type',
        'aspectable_id',
        'main_component_id',
        'instrument_aspect_point_id',
        'type',
        'aspect',
        'statement',
        'value',
        'file',
        'url',
    ];

    public function accreditation()
    {
        return $this->belongsTo(AccreditationSimulation::class, 'accreditation_simulation_id');
    }
}
