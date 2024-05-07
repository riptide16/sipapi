<?php

namespace App\Http\Resources;

class InstrumentAspectResource extends Resource
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
            'aspect' => $this->aspect,
            'type' => $this->type,
            'order' => $this->order,
            'points' => new InstrumentAspectPointCollection($this->whenLoaded('points')),
            'children' => new InstrumentAspectCollection($this->whenLoaded('children')),
            'instrument_id' => $this->instrument_id,
            'instrument' => new InstrumentResource($this->whenLoaded('instrument')),
            'instrument_component' => new InstrumentComponentResource($this->whenLoaded('instrumentComponent')),
            'answers' => new AccreditationContentCollection($this->whenLoaded('accreditationContents')),
            'simulation_answers' => new AccreditationContentCollection($this->whenLoaded('accreditationSimulationContents')),
        ];
    }
}
