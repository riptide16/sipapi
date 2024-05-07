<?php

namespace App\Http\Resources;

class EvaluationAssignmentResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'method' => $this->method,
            'scheduled_date' => $this->scheduled_date,
            'accreditation' => new AccreditationResource($this->whenLoaded('accreditation')),
            'assessors' => new UserCollection($this->whenLoaded('assessors')),
        ];
    }
}
