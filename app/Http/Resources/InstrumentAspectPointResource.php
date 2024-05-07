<?php

namespace App\Http\Resources;

class InstrumentAspectPointResource extends Resource
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
            'statement' => $this->statement,
            'order' => $this->order,
            'value' => $this->value,
        ];
    }
}
