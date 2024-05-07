<?php

namespace App\Http\Resources;

class AccreditationContentResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->aspectable_type . 'Resource';
        return [
            'id' => $this->id,
            'aspect' => $this->aspect,
            'statement' => $this->statement,
            'value' => $this->value,
            'file' => $this->file ? config('services.frontend.secure_storage_url').$this->file : null,
            'type' => $this->type,
            'url' => $this->url,
            'instrument_aspect_point_id' => $this->instrument_aspect_point_id,
            'aspectable_type' => $this->aspectable_type,
            'aspectable' => $this->resolveResource($this->aspectable_type, $this->aspectable),
            'main_component' => new InstrumentComponentResource($this->whenLoaded('mainComponent')),
            'instrument_aspect_point' => new InstrumentAspectPointResource($this->whenLoaded('instrumentAspectPoint')),
            'evaluation' => new EvaluationContentResource($this->whenLoaded('evaluationContent')),
        ];
    }
}
