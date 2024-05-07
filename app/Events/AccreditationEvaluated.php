<?php

namespace App\Events;

use App\Models\Accreditation;
use App\Models\Evaluation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccreditationEvaluated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $accreditation;
    public $evaluation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Accreditation $accreditation, Evaluation $evaluation)
    {
        $this->accreditation = $accreditation;
        $this->evaluation = $evaluation;
    }
}
