<?php

namespace App\Http\Resources;

class InstrumentComponentResource extends Resource
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
            'name' => $this->name,
            'category' => $this->category,
            'weight' => $this->weight,
            'type' => $this->type,
            'order' => $this->order,
            'parent' => new self($this->whenLoaded('parent')),
            'children' => new InstrumentComponentCollection($this->whenLoaded('children')),
            'aspects' => new InstrumentAspectCollection($this->whenLoaded('aspects')),
            'answers' => new AccreditationContentCollection($this->whenLoaded('accreditationContents')),
            'action_type' => $this->action_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen(isset($this->accreditation), [
                'accreditation' => $this->accreditation,
            ]),
        ];
    }
}
